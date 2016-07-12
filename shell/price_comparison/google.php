<?php
require_once 'price_comparison/abstract.php';

class Mage_Shell_PriceComparison_Google extends Mage_Shell_PriceComparison_Abstract
{
    public $name = 'Google Shooping Exporter';
    protected $_code = 'google';
    protected $_csvDelimiter = '\t';
    protected $_csvEnclosure = '';

    const IN_STOCK = 'in stock';
    const OUT_OF_STOCK = 'out of stock';

    public function __construct(Mage_Shell_PriceComparison $shell)
    {
        $this->_csvDelimiter = chr(9);
        parent::__construct($shell);
    }

    function getFields()
    {
        $return_array = array(
            'id' => array('code' => 'sku'),
            'title' => array('code' => 'name'),
            'description' => array('function' => 'getDescription', 'fields' => array('description')),
            'brand' => array('function' => 'getManufacturer', 'fields' => array('ps_brand', 'manufacturer')),
            'EAN' => array('function' => 'getEan', 'fields' => array('ps_ean')),
            'identifier_exists' => array('function' => 'identifierExists'),
            'link' => array('function' => 'getUrl'),
            'image_link' => array('function' => 'get_ImgUrl'),
            'additional_image_link' => array('function' => 'getAdditionalImage'),
            'condition' => array('static' => 'new'),
            'availability' => array('function' => 'getAvailability'),
            'price' => array('function' => 'calcPrice'),
            'sale_price' => array('function' => 'calcSalePrice'),
            'product_type' => array('function' => 'getCategory'),
            'google_product_category' => array('static' => 'Heim & Garten'),
        );
        if ($this->_shell->currentStoreCode == "md_de") {
            $return_array['shipping'] = array('function' => 'calc_google_Shipping');
        }
        return $return_array;
    }

    /**
     * Return image for product
     *
     * @param unknown $_product
     * @return string
     */
    function getImageUrl($_product)
    {
        try {
            $_product->load('media_gallery');
            return Mage::helper('catalog/image')->init($_product, 'small_image');
        } catch (Exception $e) {
            return '';
        }
    }

    /**
     * Get Google availability for product
     *
     * @param unknown $_product
     */
    function getAvailability($_product)
    {
        $pid = $_product->getSku();
        $qty = $_product->getQty();
        $store = $this->_shell->currentStoreCode;
        $att = $_product->getAttributeSetId();
        $siz = $_product->getPsSizeValue();
        if (stripos($siz, "Sondergr") !== false) {
            $special_size = true;
        } else {
            $special_size = false;
        }
        $dlv = $_product->getDeliveryTime();//delivery_time
        $dlv_array = array("18-25 Werktage", "18-25 Werktagen", "4-6 Wochen", "8 - 9 Wochen");
        $delivery_quick = true;
        foreach ($dlv_array as $search_string) {
            if ($dlv == $search_string) {
                $delivery_quick = false;
            }
        }
        $discount = $this->check_price_discount($_product, 25);
        $return_value = "";
        if ($store == "ps_de" && $att == 9) {
            if ($qty > 1) {
                $return_value = self::IN_STOCK;
            } else {
                if ($special_size || $delivery_quick || $discount) {
                    $return_value = self::IN_STOCK;
                } else {
                    $return_value = self::OUT_OF_STOCK;
                }
            }
        } else {
            $return_value = self::IN_STOCK;
        }

        return $return_value;
        /*
        if($_product->getQty() > 0) {
            return self::IN_STOCK;
        }
        return self::OUT_OF_STOCK;
        */
        //return self::IN_STOCK;
    }

    function getEan($_product)
    {
        /*if ($_product->getPsEan() && $_product->getPsEan() != 0) return $_product->getPsEan();
        return '';*/
    }

    /**
     * Calc Price for Google
     * @see Mage_Shell_PriceComparison_Abstract::calcPrice()
     */
    function calcPrice($_product)
    {
        if ($_product->getTypeId() == "configurable") {
            $configurabel_priceValue = $this->getLowestConfigPrice($_product);
            $priceValue = $this->_shell->_taxHelper->getPrice($_product, $configurabel_priceValue, true);
            return number_format($priceValue, 2, '.', '') . ' ' . $this->_shell->currentStore->getCurrentCurrencyCode();
        }
        if (stripos($_product->getName(), 'tempur') !== false) {
            if (($_product->getMsrp() && $_product->getMsrp() > 1) && !($_product->getPrice() < $_product->getMsrp())) {
                return number_format($_product->getMsrp(), 2, '.', '') . ' ' . $this->_shell->currentStore->getCurrentCurrencyCode();
            } else {
                return number_format(parent::calcPrice($_product), 2, '.', '') . ' ' . $this->_shell->currentStore->getCurrentCurrencyCode();
            }
        } else {
            if (($_product->getMsrp() && $_product->getMsrp() > 1)) {
                return number_format($_product->getMsrp(), 2, '.', '') . ' ' . $this->_shell->currentStore->getCurrentCurrencyCode();
            } else {
                return number_format(parent::calcPrice($_product), 2, '.', '') . ' ' . $this->_shell->currentStore->getCurrentCurrencyCode();
            }
        }
    }

    function calcSalePrice($_product)
    {
        $sale_price = "";
        if (stripos($_product->getName(), 'tempur') !== false) {
            if (($_product->getMsrp() && $_product->getMsrp() > 1) && !($_product->getPrice() < $_product->getMsrp())) {
                $sale_price = number_format(parent::calcPrice($_product), 2, '.', '') . ' ' . $this->_shell->currentStore->getCurrentCurrencyCode();
            }
            if ($sale_price >= $this->calcPrice($_product)) {
                $sale_price = "";
            }
        } else {
            if (($_product->getMsrp() && $_product->getMsrp() > 1)) {
                $sale_price = number_format(parent::calcPrice($_product), 2, '.', '') . ' ' . $this->_shell->currentStore->getCurrentCurrencyCode();
            }
            if ($sale_price >= $this->calcPrice($_product)) {
                $sale_price = "";
            }
        }
        return $sale_price;
    }

    function getLowestConfigPrice($product)
    {
        $childProducts = Mage::getSingleton('catalog/product_type_configurable')->getUsedProducts(null, $product);
        $childPriceLowest = '';
        if ($childProducts) {
            foreach ($childProducts as $child) {
                $_child = Mage::getSingleton('catalog/product')->load($child->getId());
                if ($childPriceLowest == '' || $childPriceLowest > $_child->getPrice()) {
                    $childPriceLowest = $_child->getPrice();
                }
            }
        } else {
            $childPriceLowest = $product->getPrice();
        }
        return $childPriceLowest;
    }

    function check_price_discount($_product, $discount = 25)
    {
        $check = false;
        if ($this->calcSalePrice($_product)) {
            if ($this->calcSalePrice($_product) < ($this->calcPrice($_product) * $discount / 100)) {
                $check = true;
            }
        }
        return $check;
    }


    function identifierExists($_product)
    {
        if ($this->getManufacturer($_product) && $this->getEan($_product)) {
            return 'TRUE';
        }
        return 'FALSE';
    }

    function get_all_ImageUrl($_product)
    {
        $all_images = $_product->getMediaGalleryImages();
        $all_image_url = array();
        foreach ($all_images as $image) {
            $all_image_url[] = $image->getUrl();
        }
        return $all_image_url;
    }

    function get_ImgUrl($_product)
    {
        $all_img = $this->get_all_ImageUrl($_product);
        if (isset($all_img[0]) && !empty($all_img[0])) {
            return $all_img[0];
        } else {
            return "";
        }
    }

    function getAdditionalImage($_product)
    {
        $all_img = $this->get_all_ImageUrl($_product);
        $addition_img = "";
        $num = count($all_img);
        for ($i = 1; $i < $num; $i++) {
            if (!empty($all_img[$i])) {
                $addition_img .= $all_img[$i] . ",";
            }
        }
        $addition_img = rtrim($addition_img, ",");
        return $addition_img;
    }

    function calc_google_Shipping($_product)
    {
        $shipping_cost = parent::calcShipping($_product);
        if (empty($shipping_cost)) {
            return '0';
        } else {
            $shipping_cost_de = $shipping_cost . ' ' . $this->_shell->currentStore->getCurrentCurrencyCode();
            return $shipping_cost_de;
        }
    }

}
