<?php

require_once 'abstract.php';

/**
 * This is the FEED API from sleepz to adnymics
 *
 * @package shell
 * @author André Manikofski <manikofski@sleepz.com>
 * @version 1.0.0.1
 */


/**
 * Class Sleepz2AdnymicsApi
 *
 * @internal When using this Skirpt , accepting this command line command
 *
 * php -f shell/sleepz2adnymics_api.php -- --export products
 *
 * thereafter is ps_de_product_feed.csv eautomatisch created and placed in the following directory:
 *
 * adnymics/export
 */
class Sleepz2AdnymicsApi extends Mage_Shell_Abstract
{
    /** @var */
    protected $_io;

    /** @var */
    protected $_dr;

    /** @var */
    protected $_config;

    /** @var */
    protected $_limit = 1000;

    /** @var */
    protected $_page = 1;

    /** @var */
    protected $_arrMappFields = array();

    /** @var */
    protected $_arrAdnymics = array();

    /** @var */
    protected $_additionalFieldsToSelect = array('ps_brand' => true);

    /** @var array contain the csv line items */
    protected $_items = array();

    /** @var */
    protected $_defaultExportPath;

    /** @var */
    protected $_defaultArchivePath;

    /** @var string */
    protected $_defaultFileName = "";

    /** @var $_productModel Mage_Catalog_Model_Product */
    protected $_productModel = null;

    /** @var $_productConfigModel Mage_Catalog_Model_Product_Type_Configurable */
    protected $_productConfigModel = null;

    /** @var mapper from Store ID to lang abbreviation */
    protected $_storeMapper = array(
        1 => "DE", // ps
        2 => "DE"  // md
    );

    /** Storage the lowest price simple products entity_ids  */
    protected $_simpleProductWithAbPrice = array();

    /** @var the magento TAX object - for calculation */
    protected $_tax = null;

    /** @var string Contain the current Domain name */
    protected $_currentDomain = "";

    /** @const set the default store view */
    const DEFAULT_STORE_VIEW = 1;

    /** @const set the export format */
    const DEFAULT_EXPORT_FORMAT = "csv";

    /** @const The delimiter of csv file */
    const DEFAULT_DELIMITER = ',';

    /** @const The enclosure character */
    const DEFAULT_ENCLOSURE = '"';

    /** @const XML config Path */
    const DEFAULT_XML_CONFIG_FILE = "adnymics.xml";

    /** @const The XML mail node */
    const DEFAULT_XML_NODE = "export";

    /** @const the string for mark as ab- price product */
    const DEFAULT_AB_PRICE_ENABLE = "ab";

    /** @const ist the default value for generic_string_1 field */
    const DEFAULT_AB_PRICE_DISABLE = "";

    /**
     * _construct / _init
     *
     * @return Mage_Shell_Abstract
     */
    public function _construct()
    {
        try {
            $this->_phpIniSettings();
            /** @method _setExportPath() set the export/archive path of the csv file */
            $this->_setExportPath();
            $this->_setArchivePath();
            /** @var  _config */
            $this->_config = new Zend_Config_Xml(self::DEFAULT_XML_CONFIG_FILE, self::DEFAULT_XML_NODE);
            /** @var  _io */
            $this->_io = new Varien_Io_File();
            /**  */
            $this->_io->setAllowCreateFolders(true);
            /** @var  _dr */
            $this->_dr = new Varien_Directory_Collection($this->_getExportPath());
            /** shifts the predecessor files in the archive   */
            $this->_moveOldExportFileToArchive();
            /** initialize the XML parts */
            $this->_init();
        } catch (Exception $e) {

        }
        return parent::_construct();
    }

    /**
     * am not sure whether an effect on the executing php instance but not harm it does indeed!
     *
     * memory_limt 4 GB
     * max_execution_time 0
     */
    protected function _phpIniSettings()
    {
        ini_set('memory_limit', '4095M');
        ini_set('max_execution_time', 0);
    }

    /**
     * Initialize the file / xml e.g.
     */
    protected function _init()
    {
        /** @var  _arrMappFields */
        $this->_arrMappFields = $this->_getConfig('magento')->toArray();
        /** @var  _arrAdnymics */
        $this->_arrAdnymics = $this->_getConfig('adnymics')->toArray();
        /** Open the file descriptor */
        $this->_io->open($this->_getExportPath());
        /** Set the savable filename */
        $this->_setFileName();
        /** @var  $file contain the complet path and file name */
        $file = $this->_getExportPath() . DS . $this->_getFileName();
        $this->_io->streamOpen($file, 'w+');
        $this->_io->streamLock(true);
        $this->_addHeadOfItems();
        /** Load the Mage_Catalog_Model_Product Model */
        $this->_productModel = Mage::getSingleton('catalog/product');
        /** Load the configurable product model */
        $this->_productConfigModel = Mage::getSingleton('catalog/product_type_configurable');
        /** Set the current Domain are use */
        $this->_currentDomain = rtrim(Mage::getBaseUrl(Mage_Core_Model_Store::URL_TYPE_WEB), '/');
        /** Init tax object */
        $this->_tax = Mage::helper('tax');
    }

    /**
     * shifts the predecessor files in the archive and renames the file ,
     * with the additional of the predecessor date
     */
    protected function _moveOldExportFileToArchive()
    {
        if (count($this->_dr->getItems()) > 0) {
            $diretoryContent = $this->_dr->getItems();
            foreach ($diretoryContent as $item) {
                $mvFile = $item->getFileName();
            }
            /** Move the old CSV File into the adnymics/archive folder */
            $this->_io->mv(
                $this->_defaultExportPath . DS . $mvFile,
                $this->_defaultArchivePath . DS . str_replace('.csv', '_' . date('d-m-Y') . '.csv', $mvFile));
        }
    }

    /**
     * Initialize the relationship of Store ID to abbreviation code
     */
    protected function _setStoreMapper()
    {
        foreach (Mage::app()->getWebsites() as $website) {
            foreach ($website->getGroups() as $group) {
                $stores = $group->getStores();
                foreach ($stores as $store) {
                    $this->_storeMapper[$store->getId()] = $store->getCode();
                }
            }
        }
    }

    /**
     * Get the Store Code by ID
     *
     * @param  $id int
     * @return string
     */
    protected function _getStoreMapperById($id = self::DEFAULT_STORE_VIEW)
    {
        if (!isset($this->_storeMapper[$id])) {
            return false;
        }
        return $this->_storeMapper[$id];
    }

    /**
     * Set the export folder path
     */
    protected function _setExportPath()
    {
        $this->_defaultExportPath = Mage::getBaseDir('base') . DS . 'adnymics' . DS . 'export';
    }

    /**
     * set the archive path
     */
    protected function _setArchivePath()
    {
        $this->_defaultArchivePath = Mage::getBaseDir('base') . DS . 'adnymics' . DS . 'archive';
    }

    /**
     * Gets the export folder path
     *
     * @return $this->_defaultExportPath string
     */
    protected function _getExportPath()
    {
        return $this->_defaultExportPath;
    }

    /**
     * Set the File and Format of the export file
     */
    protected function _setFileName()
    {
        $this->_defaultFileName = $this
                ->_config
                ->products
                ->filename
            . '.' . self::DEFAULT_EXPORT_FORMAT;
    }

    /**
     * @return $this->_defaultFileName string
     */
    protected function _getFileName()
    {
        return $this->_defaultFileName;
    }

    /**
     * Provide de_PS Products for Adnymics as a CSV over HTTP AUTH Basic
     */
    protected function _exportProducts()
    {
        try {
            /** Start the productfeed progress */
            do {
                $productsCollection = Mage::getModel('catalog/product')
                    ->getCollection()
                    ->addAttributeToSelect($this->_getFieldsForSelect())
                    ->addStoreFilter(self::DEFAULT_STORE_VIEW)
                    ->addAttributeToFilter('status', Mage_Catalog_Model_Product_Status::STATUS_ENABLED)
                    ->addAttributeToFilter('type_id', array(
                        array(Mage_Catalog_Model_Product_Type::TYPE_SIMPLE,
                            Mage_Catalog_Model_Product_Type::TYPE_CONFIGURABLE)
                    ))
                    ->addTaxPercents()
                    ->joinField('qty',
                        'cataloginventory/stock_item',
                        'qty',
                        'product_id=entity_id',
                        '{{table}}.stock_id=1',
                        'left'
                    )
                    ->setPage($this->_page,
                        $this->_limit)
                    ->load();

                /** Fill the csv file with content: line-by-line */
                foreach ($productsCollection as $product) {
                    $this->_restItems();

                    /** When Entity Id already been exists continue to the next one */
                    if ($this->_hasAbPriceSet($product->getId())) {
                        continue;
                    }

                    /** @var  $abPriceFlag set the flag of ab- price default ist empty string "" */
                    $abPriceFlag = self::DEFAULT_AB_PRICE_DISABLE;
                    /** Is a configurable product in the list? */
                    if ($product->getTypeId() == 'configurable') {
                        /** @var  $hasChildren if a configurable product load set the ab- price- flag */
                        $hasChildren = $this->_getLowestConfigPrice($product);
                        if (false !== $abPriceFlag) {
                            /** @var  $product load the lowest price product from the configurable list */
                            $this->_clearInstance($product);
                            $product = $this->_productModel->load($hasChildren);
                            $this->_setAbPriceEntityId($hasChildren);
                            $abPriceFlag = self::DEFAULT_AB_PRICE_ENABLE;
                        }
                    }
                    /** Start progress */
                    $this->_addItem($product->getSku());
                    $this->_addItem($product->getName());
                    $this->_addItem($this->_formatPrice($product, 'price'));
                    $this->_addItem($this->_getPSValueName($product));
                    $this->_addItem($this->_getCategoryPath($product));
                    $this->_addItem($this->_getProductUrl($product));
                    $this->_addItem($this->_getMediaUrl($product));
                    $this->_addItem($product->getDescription());
                    $this->_addItem($this->_getStoreMapperById());
                    $this->_additem($abPriceFlag);
                    /** Write the content into the file */
                    $this->_writeItem();
                    /** Solve the memory execution problem */
                    $this->_clearInstance($product);
                }

                $this->_page++;
            } while (count($productsCollection) == $this->_limit);
        } catch (Exception $e) {

        }
    }

    protected function _clearInstance(Mage_Catalog_Model_Product &$loadProductObject)
    {
        $loadProductObject->clearInstance();
        unset($loadProductObject);
    }

    /**
     * If Ab- Price- Flag already set
     *
     * @param $eid
     * @return boolean
     */
    protected function _hasAbPriceSet($eid)
    {
        if ($this->_getAbPriceEntityId($eid)) {
            return true;
        }
        return false;
    }

    /**
     * Get the Ab- Price- Flag item (when already been exists)
     *
     * @param $eid integer
     * @return integer
     */
    protected function _getAbPriceEntityId($eid)
    {
        foreach ($this->_simpleProductWithAbPrice as $num => $item) {
            if ($item[$eid] == true) {
                return $eid;
            }
        }
        return false;
    }

    /**
     * Set the Entity Id into the cantainer of already exists ab- price- flag entity ids
     *
     * @param $eid
     */
    protected function _setAbPriceEntityId($eid)
    {
        $this->_simpleProductWithAbPrice[] = array($eid => true);
    }

    /**
     * Check if a prodcut a configurable product and has children ()
     *
     * @param  Mage_Catalog_Model_Product $product
     * @return boolean or integer $id
     */
    protected function _getLowestConfigPrice(Mage_Catalog_Model_Product &$product)
    {
        $childProducts = $this->_productConfigModel->getUsedProducts(null, $product);
        $childPriceLowest = '';

        if ($childProducts) {
            foreach ($childProducts as $child) {
                $_child = $this->_productModel->load($child->getId());
                if ($childPriceLowest == '' || $childPriceLowest > $_child->getPrice()) {
                    $childPriceLowest = $_child->getPrice();
                    $id = $_child->getId();
                }
            }
        } else {
            return false;
        }
        return $id;
    }

    /**
     * Make the media url of a product
     *
     * @param  Mage_Catalog_Model_Product $product
     * @return string
     */
    protected function _getMediaUrl(Mage_Catalog_Model_Product &$product)
    {
        $mediaUrl = $this->_currentDomain;
        $mediaUrl .= "/media/catalog/product";
        $mediaUrl .= $product->getSmallImage();
        return $mediaUrl;
    }

    /**
     * Format the Price without currency symbol and include the tax
     *
     * @param Mage_Catalog_Model_Product $product
     * @param $price_type string
     * @return float
     */
    protected function _formatPrice(Mage_Catalog_Model_Product &$product, $price_type = 'price')
    {
        switch ($price_type) {
            case 'price':
                $productInstanz = $this->_productModel->load($product->getId());
                $priceIncludeTax = $this->_tax->getPrice($productInstanz, $product->getPrice(), true);
                $priceWithTaxAndSmybol = Mage::helper('core')->currency($priceIncludeTax, true, false);
                $priceIncludeTaxForCsv = Mage::getModel('directory/currency')->format($priceWithTaxAndSmybol, array('display' => Zend_Currency::NO_SYMBOL), false);
                return $priceIncludeTaxForCsv;
                break;
        }
    }

    /**
     * Join the Categgry- IDs to a string (From array(1,2,3) to 1/2/3)
     *
     * @param Mage_Catalog_Model_Product $product
     * @return string
     */
    protected function _getCategoryPath(Mage_Catalog_Model_Product &$product)
    {
        return implode('/', $product->getCategoryIds());
    }

    /**
     * Make the URL of this line
     *
     * @param  Mage_Catalog_Model_Product $product
     * @return string $url
     */
    protected function _getProductUrl(Mage_Catalog_Model_Product &$product)
    {
        $url = $this->_currentDomain .
            "/catalog/product/view/id/" . // The URL segment that refers to the product
            $product->getId() . // Product id (entity_id)
            '/s/' . // URL segment that initiates because human readable part
            $product->getUrlKey() . // The human -readable part of the url
            '/';                            // Important Backslash , otherwise forwarding the product does not work
        return $url;
    }

    /**
     * Add the content into the Object prospect
     *
     * @param $value string
     */
    protected function _addItem($value)
    {
        $this->_items[] = trim($value);
    }

    /**
     * Write the adding data from $this->_items into the CSV File
     */
    protected function _writeItem()
    {
        $this->_io->streamWriteCsv(
            $this->_getItems(),
            self::DEFAULT_DELIMITER,
            self::DEFAULT_ENCLOSURE);
    }

    /**
     * Prepare the CSV File with a header
     */
    protected function _addHeadOfItems()
    {
        $columns = $this->_arrAdnymics;
        $data = array();
        foreach ($columns as $column => $n) {
            $data[] = $column;
        }
        $this->_io->streamWriteCsv($data, self::DEFAULT_DELIMITER, self::DEFAULT_ENCLOSURE);
    }

    /**
     * Rest the $this->_items object
     */
    protected function _restItems()
    {
        unset($this->_items);
        $this->_items = array();
    }

    /**
     * Retrieve the data of $ths->_items object
     *
     * @return array array
     */
    protected function _getItems()
    {
        return $this->_items;
    }

    /**
     * Retrieve the XML config data
     *
     * Fields and mapped fields importent for export to CSV
     *
     * @param $node string
     * @return xml fields
     */
    protected function _getConfig($node)
    {
        if (is_null($this->_config->products->csv->{$node}->fields)) {
            return false;
        }
        return $this->_config->products->csv->{$node}->fields;
    }

    /**
     * Prepare the string for the product collection
     *
     * @return string
     */
    protected function _getFieldsForSelect()
    {
        $ret = array();
        $toKeyArr = $this->_getConfig('magento')->toArray();
        foreach ($toKeyArr as $mageKey => $adnKey) {
            $ret[$mageKey] = true;
        }
        return array_keys($ret);
    }

    /**
     * Give the ps_*- attribute value name
     *
     * @param  Mage_Catalog_Model_Product $product
     * @param  string $attrName
     * @return string
     */
    protected function _getPSValueName(Mage_Catalog_Model_Product &$product, $attrName = 'ps_brand')
    {
        $label = '';
        $attr = $product->getResource()->getAttribute($attrName);
        if ($attr->usesSource()) {
            $label = $attr->getSource()->getOptionText($product->getPsBrand());
        }
        return $label;
    }

    /**
     * Clear the adnymcis/export folder
     *
     * @deprecated
     * @return boolean
     */
    protected function _clearExportFolder()
    {
        if (count($this->_dr->getItems()) > 0) {
            foreach ($this->_dr->getItems() as $item) {
                $this->_io->rm($item->getPathname());
            }
            return true;
        }
        return false;
    }

    /**
     * Load the main application
     */
    public function run()
    {
        if (isset($this->_args['export'])) {
            try {
                switch ($this->_args['export']) {
                    case 'products':
                        $this->_exportProducts();
                        break;
                }
            } catch (Exception $e) {

            }
        }
    }
}

/** @var  $shell  start point */
$shell = new Sleepz2AdnymicsApi();
$shell->run();