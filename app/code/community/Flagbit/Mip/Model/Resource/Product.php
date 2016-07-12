<?php
/**
 * This source file is subject to the Magento Integration Platform License
 * that is bundled with this package in the file LICENSE_MIP.txt.
 * It is also available through the world-wide-web at this URL:
 * http://www.flagbit.de/license/mip
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to magento@flagbit.de so we can send you a copy immediately.
 *
 * The Magento Integration Platform is a property of Flagbit GmbH & Co. KG.
 * It is NO part or deravative version of Magento and as such NOT published
 * as Open Source. It is NOT allowed to copy, distribute or change the
 * Magento Integration Platform or any of its parts. If you wish to adapt
 * the software to your individual needs, feel free to contact us at
 * http://www.flagbit.de or via e-mail (magento@flagbit.de) or phone
 * (+49 (0)800 FLAGBIT (3524248)).
 *
 *
 *
 * Dieser Quelltext unterliegt der Magento Integration Platform License,
 * welche in der Datei LICENSE_MIP.txt innerhalb des MIP Paket hinterlegt ist.
 * Sie ist außerdem über das World Wide Web abrufbar unter der Adresse:
 * http://www.flagbit.de/license/mip
 * Falls Sie keine Kopie der Lizenz erhalten haben und diese auch nicht über
 * das World Wide Web erhalten können, senden Sie uns bitte eine E-Mail an
 * magento@flagbit.de, so dass wir Ihnen eine Kopie zustellen können.
 *
 * Die Magento Integration Platform ist Eigentum der Flagbit GmbH & Co. KG.
 * Sie ist WEDER Bestandteil NOCH eine derivate Version von Magento und als
 * solche nicht als Open Source Softeware veröffentlicht. Es ist NICHT
 * erlaubt, die Software als Ganze oder in Einzelteilen zu kopieren,
 * verbreiten oder ändern. Wenn Sie eine Anpassung der Software an Ihre
 * individuellen Anforderungen wünschen, kontaktieren Sie uns unter
 * http://www.flagbit.de oder via E-Mail (magento@flagbit.de) oder Telefon
 * (+49 (0)800 FLAGBIT (3524248)).
 *
 *
 *
 * @package     Flagbit
 * @subpackage  Flagbit_Mip
 * @copyright   2009 by Flagbit GmbH & Co. KG
 * @author      Flagbit Magento Team <magento@flagbit.de>
 */


/**
 * @package     Flagbit
 * @subpackage  Flagbit_Mip
 */
class Flagbit_Mip_Model_Resource_Product extends Flagbit_Mip_Model_Resource_Abstract {

    protected $_filtersMap = array(
        'product_id' => 'entity_id',
        'set'        => 'attribute_set_id',
        'type'       => 'type_id'
    );

    protected $_productLinkDefinition = array(
        'related_ids' => 'related_link_data',
        'up_sell_ids' => 'up_sell_link_data',
        'cross_sell_ids' => 'cross_sell_link_data',
        'grouped_ids' => 'grouped_link_data',
    );

    protected $_newRelations = array();

    protected $_optionRelations = array();


    const RELATION_TYPE = 'product';

    const RELATION_TYPE_CONF_ATTRIBUTE = 'conf_attribute';
    const RELATION_TYPE_PRODUCT_IMAGE = 'product_image';
    const RELATION_TYPE_BUNDLE_OPTION = 'bundle_option';
    const RELATION_TYPE_CUSTOM_OPTION = 'product_option';

    /**
     * Attribute code for media gallery
     *
     */
    const MEDIA_ATTRIBUTE_CODE = 'media_gallery';

    /**
     * Default ignored attribute codes
     *
     * @var array
     */
    protected $_ignoredAttributeCodes = array('entity_id', 'attribute_set_id', 'entity_type_id');

    /**
     * Default ignored attribute types
     *
     * @var array
     */
    protected $_ignoredAttributeTypes = array();

    /**
     * Field name in session for saving store id
     * @var string
     */
    protected $_storeIdSessionField   = 'store_id';

    /**
     * Output Counter for Log
     *
     * @var string
     */
    public $countOutput = '';

    /**
     * Produkt SKU to ID Mapping Array
     *
     * @var array
     */
    protected $_skuToIdMapping = null;

    /**
     * return Relation Type
     *
     * @return string
     */
    protected function getRelationType(){
        return self::RELATION_TYPE;
    }

    /**
     * Retrives store id from store code, if no store id specified,
     * it use seted session or admin store
     *
     * @param string|int $store
     * @return int
     */
    protected function _getStoreId($store = null)
    {
        if (is_null($store)) {
            $store = ($this->_getSession()->hasData($this->_storeIdSessionField)
                        ? $this->_getSession()->getData($this->_storeIdSessionField) : 0);
        }

        try {
            $storeId = Mage::app()->getStore($store)->getId();
        } catch (Mage_Core_Model_Store_Exception $e) {
            $this->_fault('store_not_exists');
        }

        return $storeId;
    }

    /**
     * Check is attribute allowed
     *
     * @param Mage_Eav_Model_Entity_Attribute_Abstract $attribute
     * @param array $attributes
     * @return boolean
     */
    protected function _isAllowedAttribute($attribute, $attributes = null, array $attributes = null)
    {
        if (is_array($attributes)
            && !( in_array($attribute->getAttributeCode(), $attributes)
                  || in_array($attribute->getAttributeId(), $attributes))) {
            return false;
        }

        return !in_array($attribute->getFrontendInput(), $this->_ignoredAttributeTypes)
               && !in_array($attribute->getAttributeCode(), $this->_ignoredAttributeCodes);
    }

    /**
     *  Return loaded product instance
     *
     *  @param    int|string $productId (SKU or ID)
     *  @param    int|string $store
     *  @return      object Mage_Catalog_Model_Product instance
     */
    protected function _getProduct ($productId, $store = null, $isSku = false, $datahash_identifier = null, $relation = null )
    {
        $product = Mage::getModel('catalog/product');

        //prevent options doublets
        $product->getOptionInstance()->unsetOptions();

        /* @var $product Mage_Catalog_Model_Product */
        if (!is_numeric($productId) or $isSku === true) {

            $idBySku = $product->getIdBySku($productId);

            if ($idBySku && $productId) {
                if($relation === null){
                    $this->createRelation($productId, $idBySku);
                }

                $productId = $idBySku;
            }
        }
        if ($store !== null) {
            $product->setStoreId($this->_getStoreId($store));
        }

        $product->load($productId);
        return $product;
    }

    /**
     * Set current store for catalog.
     *
     * @param string|int $store
     * @return int
     */
    public function currentStore($store=null)
    {
        if (!is_null($store)) {
            try {
                $storeId = Mage::app()->getStore($store)->getId();
            } catch (Mage_Core_Model_Store_Exception $e) {
                $this->_fault('store_not_exists');
            }

            $this->_getSession()->setData($this->_storeIdSessionField, $storeId);
        }

        return $this->_getStoreId();
    }

    /**
     * Constuctor
     */
    public function __construct()
    {
        $this->_storeIdSessionField = 'product_store_id';
        $this->_ignoredAttributeTypes[] = 'gallery';
        $this->_ignoredAttributeTypes[] = 'media_image';
    }

    /**
     * save Product Items
     *
     * @param $data array
     */
    public function saveItems($data){

        $i=0;
        $count = count($data);
        $orgCount = $count;
        $defaultStoreAdded = array();
        $errors = array();
        $store = null;
        $previousIdentifier = null;
        for($pi=0;$count > $pi && isset($data[$pi]);$pi++){
            $product = $data[$pi];
            $productResource = Mage::getModel('catalog/product')->getResource();

            try{
                set_time_limit(0);
                $i++;

                // get Product Identifier
                if(!empty($product['product_id'])){
                    $identifier = $product['product_id'];
                }elseif(!empty($product['sku'])){
                    $identifier = $product['sku'];
                }else{
                    $this->_fault('no_identifier');
                }

                // skip products with errors
                if(isset($errors[$identifier])){
                    continue;
                }

                // set Hash Identifier
                $this->_datahashIdentifier = empty($product['mip_datahash_id']) ? null: $product['mip_datahash_id'];

                // get Relation
                $relation = $this->getRelationByExtId($identifier);

                // handle Category Relations
                if(isset($product['category_ids']) && is_string($product['category_ids'])){
                    $product['category_ids'] = implode(',',
                        $this->getRelationModel()->getCollection()->convert2MageIds(
                            Mage::helper('mip')->trimExplode(',', $product['category_ids']),
                            Flagbit_Mip_Model_Resource_Category::RELATION_TYPE
                        )
                    );
                }
                if (isset($product['category_ids_default'])){
                    if(!empty($product['category_ids'])){
                        $product['category_ids'] .= ','.$product['category_ids_default'];
                    }else{
                        $product['category_ids'] = $product['category_ids_default'];
                    }
                }
                if(isset($product['category_ids']) && is_string($product['category_ids'])){
                    $product['category_ids'] = Mage::helper('mip')->trimExplode(',', $product['category_ids']);
                }

                $this->countOutput = $i.'/'.$count.' - '.round(100/$count * $i, 0).'% ';

                if( !isset($defaultStoreAdded[$identifier])
                    && $orgCount <= $count
                    && is_array($product['store_ids'])
                    && !in_array(Mage_Core_Model_App::ADMIN_STORE_ID, $product['store_ids'])
                    && ($relation->getId() && !$productResource->getAttributeRawValue($relation->getMageId(), 'status', Mage_Core_Model_App::ADMIN_STORE_ID)
                    || !$relation->getId())
                    && $previousIdentifier != $identifier ){
                    $product['store_ids'] = array_merge(array(Mage_Core_Model_App::ADMIN_STORE_ID), $product['store_ids']);
                    $defaultStoreAdded[$identifier] = true;
                }

                // get Store Ids
                $store = null;
                if(isset($product['store_ids'])){
                    if(is_array($product['store_ids']) && count($product['store_ids']) > 1){
                    foreach(array_slice($product['store_ids'], 1) as $additionalStoreId){
                        $productCopy = $product;
                        // unset Options
                        if(isset($productCopy['options'])){
                            unset($productCopy['options']);
                        }
                        $productCopy['store_ids'] = array($additionalStoreId);
                        $data[] = $productCopy;
                        $count++;
                    }
                    $product['store_ids'] = array($product['store_ids'][0]);
                    $store = $product['store_ids'][0];
                    }elseif(is_array($product['store_ids']) && count($product['store_ids']) == 1){
                        $store = $product['store_ids'][0];
                    }else{
                        $store = $product['store_ids'];
                    }
                    $product['store_id'] = $store;
                }
                try{

                    // update Product
                    if($relation->getId()){
                        $this->update($relation->getMageId(), $product, $store);

                    // create Product
                    }elseif(!empty($product['type']) && !empty($product['set'])){
                        $this->create($product['type'], $product['set'], $product['sku'], $product, $store);

                    // update Product without Relation
                    }else{
                        $productModel = $this->_getProduct($product['sku'], null, true, ($product['mip_datahash_id'] ? null : $product['mip_datahash_id']));
                        if($productModel->getId()){
                            $this->update($productModel->getId(), $product, $store);
                        }
                    }
                // handle Errors
                }catch (Exception $e){

                    // handle duplicate URL_KEY Error
                    if(strstr($e->getMessage(), 'url_key')
                        && strstr($e->getMessage(), 'exists')
                        && !empty($product['name'])){
                        $product['url_key'] = Mage::getModel('catalog/product_url')->formatUrlKey($product['name']).rand();
                        $data[] = $product;
                        $count++;
                        Mage::helper('mip/log')->getWriter($this)->info($identifier.': HANDLE URL KEY');
                        continue;
                    }

                    // handle duplicate SKU Error
                    if(strstr($e->getMessage(), 'SKU') || strstr($e->getMessage(), 'unique') || $this->_checkIfProductExists($identifier)){
                        $productModel = $this->_getProduct($product['sku'], null, true,  (empty($product['mip_datahash_id']) ? null : $product['mip_datahash_id']), $relation);
                        if($productModel->getId()){
                            $errors[$identifier] = $e->getMessage();
                            $this->update($productModel->getId(), $product, $store);
                        }else{
                            $this->_fault('product_not_found_by_sku');
                        }
                        continue;
                    }


                    // delete relations for Products which do not exists
                    if($e->getMessage() == 'product_not_exists'){
                        $relation->delete();
                    }
                    // import Product again if it was delete because of the wrong product type
                    if($e->getMessage() == 'wrong_product_type' && !empty($product['force_type'])){
                        $data[] = $product;
                        $count++;
                        continue;
                    }

                    $errors[$identifier] = $e->getMessage();

                    throw $e;
                }
                $previousIdentifier = $identifier;
                $this->saveStatus('ok');

            // report Errors
            }
            catch (Zend_Db_Exception $e){

                //remove product from hash table if a error happens
                if (isset($product['mip_datahash_id'])){
                    $datahash = Mage::getModel('mip/data_hash')->loadByIdentifier($product['mip_datahash_id'], 'product');
                    if ($datahash->hasData()){
                        $datahash->delete();
                    }
                }

                $this->saveStatus($e->getMessage());
                Mage::helper('mip/error')->handleException($e);
                Mage::helper('mip/log')->getWriter($this)->error($this->countOutput.' Product ('.$product['sku'].') Import Error: '.$e->getMessage(), $e);
            }
            catch (Exception $e){
                $this->saveStatus($e->getMessage());
                Mage::helper('mip/error')->handleException($e);
                Mage::helper('mip/log')->getWriter($this)->error($this->countOutput.' Product ('.$product['sku'].') Import Error: '.$e->getMessage(), $e);
            }
        }
    }

    /**
     * delete Product Items
     *
     * @param $data array
     */
    public function deleteItems($data){

        $i=0;
        $count = count($data);
        $store = null;
        foreach($data as $product){

            try{
                set_time_limit(0);
                $i++;

                // get Product Identifier
                if(!empty($product['product_id'])){
                    $identifier = $product['product_id'];
                }elseif(!empty($product['sku'])){
                    $identifier = $product['sku'];
                }else{
                    $this->_fault('no_identifier');
                }

                // set Hash Identifier
                $this->_datahashIdentifier = empty($product['mip_datahash_id']) ? null: $product['mip_datahash_id'];

                // get Relation
                $relation = $this->getRelationByExtId($identifier);

                $this->countOutput = $i.'/'.$count.' - '.round(100/$count * $i, 0).'% ';

                // get Store Ids
                $store = null;
                if(isset($product['store_ids'])){
                    if(is_array($product['store_ids']) && count($product['store_ids']) > 1){
                        for($si=0; count($product['store_ids']) > $si; $si++){
                            if(count($product['store_ids']) == $si-1){
                                $store = $product['store_ids'][$si];
                                break;
                            }else{
                                $productCopy = $product;
                                $productCopy['store_ids'] = $product['store_ids'][$si];
                                $data[] = $productCopy;
                            }
                        }
                    }elseif(is_array($product['store_ids']) && count($product['store_ids']) == 1){
                        $store = $product['store_ids'][0];
                    }else{
                        $store = $product['store_ids'];
                    }
                    $product['store_id'] = $store;
                }


                $this->delete($product['sku'], $relation, $store);
                Mage::helper('mip/log')->getWriter($this)->info( $this->countOutput . ' DELETE Product (' . $identifier . ')');
                $this->saveStatus('ok');

            // report Errors
            }
            catch (Zend_Db_Exception $e){

                //remove product from hash table if a error happens
                if (isset($product['mip_datahash_id'])){
                    $datahash = Mage::getModel('mip/data_hash')->loadByIdentifier($product['mip_datahash_id'], 'product');
                    if ($datahash->hasData()){
                        $datahash->delete();
                    }
                }

                $this->saveStatus($e->getMessage());
                Mage::helper('mip/error')->handleException($e);
                Mage::helper('mip/log')->getWriter($this)->error($this->countOutput.' Product ('.$product['sku'].') Delete Error: '.$e->getMessage(), $e);
            }
            catch (Exception $e){
                $this->saveStatus($e->getMessage());
                Mage::helper('mip/error')->handleException($e);
                Mage::helper('mip/log')->getWriter($this)->error($this->countOutput.' Product ('.$product['sku'].') Delete Error: '.$e->getMessage(), $e);
            }
        }
    }

    /**
     * Converts image to api array data
     *
     * @param array $image
     * @param Mage_Catalog_Model_Product $product
     * @return array
     */
    protected function _imageToArray(&$image, $product)
    {
        $result = array(
            'file'      => $image['file'],
            'label'     => $image['label'],
            'position'  => $image['position'],
            'exclude'   => $image['disabled'],
            'types'     => array()
        );

        foreach ($product->getMediaAttributes() as $attribute) {
            if ($product->getData($attribute->getAttributeCode()) == $image['file']) {
                $result['types'][] = $attribute->getAttributeCode();
            }
        }

        return $result;
    }

    /**
     * Retrieve gallery attribute from product
     *
     * @param Mage_Catalog_Model_Product $product
     * @return Mage_Catalog_Model_Resource_Eav_Mysql4_Attribute|boolean
     */
    protected function _getGalleryAttribute($product)
    {
        $attributes = $product->getTypeInstance()->getSetAttributes();

        if (!isset($attributes[self::MEDIA_ATTRIBUTE_CODE])) {

            $product->save();
            $product->load($product->getId());
            $attributes = $product->getTypeInstance()->getSetAttributes();

            if (!isset($attributes[self::MEDIA_ATTRIBUTE_CODE])) {

                $this->_fault('Attribute '.self::MEDIA_ATTRIBUTE_CODE.' not definied');
            }
        }

        return $attributes[self::MEDIA_ATTRIBUTE_CODE];
    }

    /**
     * Retrieve attributes for media gallery
     *
     * @return array
     */
    public function getMediaAttributes()
    {
        if (!$this->hasMediaAttributes()) {
            $mediaAttributes = array();
            foreach ($this->getAttributes() as $attribute) {
                if($attribute->getFrontend()->getInputType() == 'media_image') {
                    $mediaAttributes[$attribute->getAttributeCode()] = $attribute;
                }
            }
            $this->setMediaAttributes($mediaAttributes);
        }
        return $this->getData('media_attributes');
    }

    /**
     * get File Hash
     *
     * @param $file string
     */
    protected function _getFileHash($file){

        return md5_file($file);
    }

    /**
     * Create new product.
     *
     * @param string $type
     * @param int $set
     * @param array $productData
     * @return int
     */
    public function create($type, $set, $sku, $productData, $store = null)
    {

        if (!$type || !$set || !$sku) {
            $this->_fault('data_invalid', 'There is nor "type", "set" or "sku" set');
        }

        if (isset($productData['product_id'])){
            $orgProductId = $productData['product_id'];
            unset($productData['product_id']);
        }
        elseif (isset($productData['sku'])){
            $orgProductId = $productData['sku'];

        }

        $product = Mage::getModel('catalog/product');

        /* @var $product Mage_Catalog_Model_Product */
        $product->setStoreId($this->_getStoreId($store))
            ->setAttributeSetId($set)
            ->setTypeId($type)
            ->setSku($sku);


        // handle Attributes
        foreach ($product->getTypeInstance(true)->getEditableAttributes($product) as $attribute) {
            if ($this->_isAllowedAttribute($attribute)
                && isset($productData[$attribute->getAttributeCode()])) {
                $product->setData(
                    $attribute->getAttributeCode(),
                    $productData[$attribute->getAttributeCode()]
                );
            }
        }

        // throw validation Errors
        if (is_array($errors = $product->validate())) {
            $this->_fault('data_invalid', implode("\n", $errors));
        }

        // remove url key in sub storeviews to prevent url_key attribute already exists error
        if(empty($productData['url_key']) && $store !== null && $store != '0'){
            $product->unsUrlKey();
        }

        // try to save Product
        try {
            $this->_handleProductImages($product, $productData, $store);
            $this->_prepareDataForSave($product, $productData);
            $product->save();
            $this->_afterSave($product, $productData);
        } catch (Mage_Core_Exception $e) {
            $this->_fault('data_invalid', $e->getMessage());
        }

        // save Relation
        if($product->getId() && !empty($orgProductId)){
            $relation = $this->createRelation($orgProductId, $product->getId());
        }

        Mage::helper('mip/log')->getWriter($this)->info($this->countOutput.' CREATE Product ('.$sku.':'.$store.')');

        $id = $product->getId();
        $product->reset();
        return $id;
    }

    /**
     * get Product Website Ids for Update
     * extends Import ID with other already existing Relations
     *
     * @param int $productId
     * @param array $productData
     * @return array
     */
    protected function _getWebsiteIds($productId, $productData)
    {
        $websiteIds = array();

        if(isset($productId)){

            $productResource = Mage::getModel('catalog/product')->getResource();
            $readConnection = $productResource->getReadConnection();

            $select = $readConnection->select()
                                    ->from ($productResource->getTable ( 'catalog/product_website' ), array('website_id'))
                                    ->where ('product_id=?', $productId )
                                    ->group('website_id');

            $currentWebsiteIds = $readConnection->fetchCol($select);
            $productData['website_ids'] = array_merge((array) $productData['website_ids'], (array) $currentWebsiteIds);
            $websiteIds = array_unique($productData['website_ids']);
        }else{
            $websiteIds = (array) $productData['website_ids'];
        }

        return $websiteIds;
    }

    /**
     * get Product Category IDs
     * extends Import IDs with other already existing Relations
     *
     * @param Mage_Catalog_Model_Product $product
     * @param array $productData
     * @return
     */
    protected function _getCategoryIds($product, $productData)
    {

        $categoryIds = isset($productData['category_ids']) ? $productData['category_ids'] : array();

        if($product->getId()
            && !empty($productData['category_ids_overwrite'])
            && count($categoryIds)){

            $storeGroups = Mage::getResourceModel('core/store_group_collection')
                                ->setWithoutDefaultFilter()
                                ->addFieldToFilter('website_id', array("nin"=>$productData['website_ids']))
                                ->load();

            $filterCategories = array();
            foreach($storeGroups as $group){
                $filterCategories[] = '1/'.$group->getRootCategoryId().'/';
            }

            if(count($filterCategories)){
                $currentCategoryIds = Mage::getModel('catalog/category')->getCollection()
                            ->addIsActiveFilter()
                            ->addPathsFilter($filterCategories)
                            ->getAllIds();


                $categoryIds = array_merge($categoryIds, array_intersect($product->getCategoryCollection()->getAllIds(), (array) $currentCategoryIds));
            }
        }

        return $categoryIds;
    }


    /**
     * Update product data
     *
     * @param int|string $productId
     * @param array $productData
     * @param string|int $store
     * @return boolean
     */
    public function update($productId, $productData = array(), $store = null)
    {
        unset($productData['product_id']);

        /*@var $product Mage_Catalog_Model_Product*/
        $product = $this->_getProduct($productId, $store, false,  ($productData['mip_datahash_id'] ? null : $productData['mip_datahash_id']));


        // only update product when it exists
        if (!$product->getId()) {
            $this->_fault('product_not_exists');
        }

        // only update product when proper product type is set
        if(!empty($productData['type'])
            && $product->getTypeId() != $productData['type']){
            if(!empty($productData['force_type'])){
                $product->delete();
                $this->getRelationbyResourceId($productId)->delete();
            }
            $this->_fault('wrong_product_type');
        }

        // handle Attributes
        foreach ($product->getTypeInstance(true)->getEditableAttributes($product) as $attribute) {
            if ($this->_isAllowedAttribute($attribute)
                && isset($productData[$attribute->getAttributeCode()])) {
                $product->setData(
                    $attribute->getAttributeCode(),
                    $productData[$attribute->getAttributeCode()]
                );
            }
        }

        // remove url key in sub storeviews to prevent url_key attribute already exists error
        if(empty($productData['url_key']) && $store !== null && $store != '0'){
            $product->unsUrlKey();
        }

        // try and handle validation
        try {
            if (is_array($errors = $product->validate())) {
                $this->_fault('data_invalid', implode("\n", $errors));
            }
        } catch (Mage_Core_Exception $e) {
            $this->_fault('data_invalid', $e->getMessage());
        }

        // save Product
        try {
            $this->_handleProductImages($product, $productData, $store);
            $this->_prepareDataForSave($product, $productData);
            $product->save();
            $this->_afterSave($product, $productData);
        } catch (Mage_Core_Exception $e) {
            $this->_fault('data_invalid', $e->getMessage());
        }

        Mage::helper('mip/log')->getWriter($this)->info($this->countOutput.' UPDATE Product ('.(isset($productData['sku']) ? $productData['sku'] : $productId).':'.$store.')');

        $product->reset();

        return true;
    }


    /**
     * delete product
     *
     * @param int|string $productId
     * @param string|int $store
     * @return boolean
     */
    public function delete($sku, $relation, $store = NULL)
    {
        $productId = $this->_getProductIdBySku($sku);

        /*@var $product Mage_Catalog_Model_Product*/
        $product = $this->_getProduct($productId, $store, false,  $this->_datahashIdentifier, $relation);

        // only delete product when it exists
        if (!$product->getId()) {
            Mage::helper('mip/log')->getWriter($this)->warn($this->countOutput.' DELETE Product ('.$sku.':'.$store.') not exists - might have already been deleted');
            return false;
        }

        // delete Product
        try {
            $storeIds = $product->getStoreIds();
            $storeIdsActive = $this->_getStoreIdsOfActive($product);

            if ($store !== null) {
                if(!in_array($store, $storeIds)) {
                    Mage::helper('mip/log')->getWriter($this)->warn($this->countOutput.' DELETE Product ('.$sku.':'.$store.') product cant get deleted from store - its not set');
                    return false;
                }
                else if(!in_array($store, $storeIdsActive)) {
                    Mage::helper('mip/log')->getWriter($this)->warn($this->countOutput.' DELETE Product ('.$sku.':'.$store.') product cant get deleted from store - its already deactivated in the category');
                    return false;
                }

                unset($storeIdsActive[array_search($store, $storeIdsActive)]);
            }

            if($store && count($storeIdsActive)) {
                /*
                 * set the products status attribute onto deactive in the given storeview
                 * as long as there is given a specific storeview to delete in the product
                 * and there are different storeviews left where the product remains
                 */
                $product = Mage::getModel ( "catalog/product" )->setId ( $product->getId() );
                $product->setStoreId($store);
                $product->load($product->getId());

                /*
                 * setting the visibility to 'not visible'
                 * and remove all product <> category relations
                 */
                $product->setVisibility(1);
                $product->setCategoryIds(array());
                $product->save();
            }
            else {
                $product->delete();
            }
        } catch (Mage_Core_Exception $e) {
            $this->_fault('data_invalid', $e->getMessage());
            Mage::helper('mip/log')->getWriter($this)->error($this->countOutput.' DELETE Product ('.$sku.':'.$store.') '.$e->getMessage(), $e);
            return false;
        }

        Mage::helper('mip/log')->getWriter($this)->info($this->countOutput.' DELETE Product ('.$sku.':'.$store.')');

        return true;
    }

    /**
     * Retrieve a list of storeIds where the products are set on active status
     *
     * @param Mage_Catalog_Model_Product
     * @return array
     */
    protected function _getStoreIdsOfActive(Mage_Catalog_Model_Product $product) {
        $storeIds = $product->getStoreIds();
        $productId = $product->getId();
        $storeIdsActive = array();
        foreach($storeIds as $index => $storeId) {
            $product = Mage::getModel ( "catalog/product" )->setId ( $productId );
            $product->setStoreId($storeId);
            $product->load($productId);

            if($product->getStatus() == 1 && $product->getVisibility() != 1) {
                $storeIdsActive[] = $storeId;
             }
        }

        return $storeIdsActive;
    }

    /**
     * Retrieve media config
     *
     * @return Mage_Catalog_Model_Product_Media_Config
     */
    protected function _getMediaConfig()
    {
        return Mage::getSingleton('catalog/product_media_config');
    }


    /**
     * Gets all Duplicate entries for a given extId on imageRelations
     *
     * @param string $extId
     * @param array $pictureExists
     * @return mixed
     */
    protected function _getExtIdDuplicates($extId, $picturesExists)
    {
        $picturesExistsOrderedByExtId = array();
        $extIds = false;
        foreach($picturesExists as $key => $value) {
            $picturesExistsOrderedByExtId[$value][] = $key;
        }

        if(isset($picturesExistsOrderedByExtId[$extId])) {
            $extIds = $picturesExistsOrderedByExtId[$extId];
        }

        return $extIds;
    }

    /**
     *  handle Images
     *
     *  @param    Mage_Catalog_Model_Product $product
     *  @param    array $productData
     *  @return   object
     */
    protected function _handleProductImages (&$product, $productData, $store = 0)
    {

        $noPictures = false;
        $picturesExists = array();

            // handle Images
        if ($product instanceof Mage_Catalog_Model_Product
            && isset($productData['images']) && is_array($productData['images'])) {

            $productId = $product->getId();

            /* @var $gallery Mage_Catalog_Model_Resource_Eav_Mysql4_Attribute */
            $gallery = $this->_getGalleryAttribute($product);

            /* @var $galleryBackend Mage_Catalog_Model_Product_Attribute_Backend_Media */
            $galleryBackend = $gallery->getBackend();

            $galleryData = $product->getData(self::MEDIA_ATTRIBUTE_CODE);

            // delete existing Images with no Relation
            $imagesCur = array();
            if(isset($galleryData['images']) && is_array($galleryData['images'])){

                $importImageFiles = array_map(
                    create_function('$value', 'return Mage::getBaseDir().trim($value["file"]);'),
                    $productData['images']
                );

                foreach ($galleryData['images'] as &$image) {
                    $imageArray = $this->_imageToArray($image, $product);

                    $relation = $this->getRelationbyResourceId($imageArray['file'], self::RELATION_TYPE_PRODUCT_IMAGE, $productId, self::RELATION_TYPE);

                    if(!$relation->getId()){
                        $galleryBackend->removeImage($product, $imageArray['file']);
                    }elseif ($relation->getId() && !in_array($relation->getExtId(), $importImageFiles)) {
                        $galleryBackend->removeImage($product, $imageArray['file']);
                        $relation->delete();
                    }else{
                        $relationExists[] = $relation->getId();
                        $picturesExists[$relation->getResourceId()] = $relation->getExtId();
                    }
                }
            }

            // Import new Images
            $imageRelations = array();
            $file = null;
            foreach($productData['images'] as $image){

                // get complete file path
                $orgFile = Mage::getBaseDir().trim($image['file']);

                // continue if picture not exists
                if(!isset($image['file'])
                    or !file_exists($orgFile)
                    or !is_file($orgFile)){
                    continue;
                }

                // throw a Exeption when picture is not readable
                if(!is_readable($orgFile)){
                    $this->_fault('picture_not_readable', 'The Picture '.$orgFile.' is not readable');
                }

                // get Relation
                $relation = $this->getRelationByExtId($orgFile, self::RELATION_TYPE_PRODUCT_IMAGE, self::RELATION_TYPE, $productId);

                // remove picture when it has changed
                if($relation->getId()
                    && $relation->getResourceId()
                    && (!file_exists(Mage::getBaseDir('media').DS.'catalog'.DS.'product'.$relation->getResourceId())
                    OR md5_file(Mage::getBaseDir('media').DS.'catalog'.DS.'product'.$relation->getResourceId()) != md5_file($orgFile))){

                    $galleryBackend->removeImage($product, $relation->getResourceId());
                    $this->_deleteOldImageFileResource(Mage::getBaseDir('media').DS.'catalog'.DS.'product'.$relation->getResourceId());
                    unset($picturesExists[$relation->getResourceId()]);
                    $relation->delete();
                }

                // Skip Image if a ResourceId is already set for the given extId
                $extIdCount = 0;
                foreach($picturesExists as $key => $value) {
                    $extIdCount = ($value == $relation->getExtId() ? $extIdCount+1 : $extIdCount);
                    if($extIdCount > 1 && $value == $relation->getExtId()){
                        $galleryBackend->removeImage($product, $key);
                        $this->_deleteOldImageFileResource(Mage::getBaseDir('media').DS.'catalog'.DS.'product'.$key);

                        unset($picturesExists[$key]);
                        if($relation->getId()){
                            $relation->delete();
                        }
                    }
                }

                // import picture
                if(!$relation->getId() or empty($picturesExists[$relation->getResourceId()])){

                    if($extIdCount >= 1){
                        continue;
                    }

                    $ioAdapter = new Varien_Io_File();

                    $fileInfo = pathinfo($orgFile);

                    $newFileName = $this->getImageFileName((isset($image['handle']) ? $image['filename'] : ''), $productData, $fileInfo['extension']);

                    $ioAdapter->cp($orgFile, $newFileName);

                    if(!file_exists($newFileName)){
                        echo $orgFile. ' -> ' .$newFileName;
                        $this->_fault('picture_tmp_not_writeable', 'Cannot write Picture to '.$newFileName);
                    }

                    // Adding image to gallery
                    $file = $galleryBackend->addImage(
                        $product,
                        $newFileName,
                        null,
                        true,
                        false
                    );

                    // save Relation
                    $imageRelations[$file] = $orgFile;

                    $image['file'] = $file;
                    $galleryBackend->updateImage($product, $file, $image);
                }else{
                    // Update Image to change position if nothing else changed
                    $file = $relation->getResourceId();
                    $image['file'] = $file;
                    $galleryBackend->updateImage($product, $file, $image);
                }

                if (isset($image['types']) && $file !== null) {
                    $galleryBackend->setMediaAttribute($product, $image['types'], $file);
                }
            }

            // save Relations
              foreach ($imageRelations as $file => $orgFile){
                $this->_newRelations[self::RELATION_TYPE_PRODUCT_IMAGE][$productData['sku']][$orgFile] = $file;
            }
        }
    }

    protected function _deleteOldImageFileResource($file) {
        $ioAdapter = new Varien_Io_File();
        return $ioAdapter->rm($file);
    }

    /**
     * get a unique an valid filename
     *
     * @param string $name
     * @param array $productData
     * @param string $fileExt
     */
    protected function getImageFileName($name, $productData, $fileExt){

        if(!empty($name)){
            $fileName = $name;
        }elseif(!empty($productData['name'])){
            $fileName = $productData['name'];
        }else{
            $fileName = uniqid();
        }
        $fileName = str_replace(' ', '_', $fileName);
        $fileName = preg_replace('/([^a-zA-Z0-9-_])/', '', $fileName);

        $discretionPath = Mage_Core_Model_File_Uploader::getDispretionPath($fileName);
        $path = $this->_getMediaConfig()->getBaseTmpMediaPath().$discretionPath;
        if(!is_dir($path)){
            $io = new Varien_Io_File();
            $io->checkAndCreateFolder($path);
        }

        return $path.Varien_File_Uploader::getNewFileName($path.$fileName.'.'.$fileExt);
    }

    /**
     *  Set additional data before product saved
     *
     *  @param    Mage_Catalog_Model_Product $product
     *  @param    array $productData
     *  @return   object
     */
    protected function _prepareDataForSave (&$product, &$productData)
    {
        // handle Link Relations
        foreach($this->_productLinkDefinition as $xmlKey => $modelKey){
            if(!isset($productData[$xmlKey])){
                continue;
            }

            $product->setData($modelKey, array_flip(
                                $this->getRelationModel()->getCollection()->convert2MageIds(
                                    Mage::helper('mip')->trimExplode(',', $productData[$xmlKey]),
                                    self::RELATION_TYPE
                                )
                            )
                        );
        }


        // categories
        $_catecoryIds = $this->_getCategoryIds($product, $productData);
        if(count($_catecoryIds)){
            $product->setCategoryIds($_catecoryIds);
            unset($productData['category_ids']);
        }


        // set website Ids
        $product->setWebsiteIds(
            $this->_getWebsiteIds($product->getId(), $productData)
        );

        // stock
        if (isset($productData['stock_data']) && is_array($productData['stock_data'])) {
            $product->setStockData($productData['stock_data']);
        }

        // Initialize product options
        if (isset($productData['options']) && !$product->getOptionsReadonly()) {
            $options = (array) $productData['options'];

            /*@var $optionsCollection Mage_Catalog_Model_Resource_Eav_Mysql4_Product_Option_Collection */
            $optionsCollection = $product->getProductOptionsCollection();
            $currentOptionIds = array();
            foreach($options as &$optionItem){
                $identifier = !empty($optionItem['option_id']) ? $optionItem['option_id'] : $optionItem['title'];
                if(isset($optionItem['option_id'])){unset($optionItem['option_id']);}

                if($product->getId()){
                    $relation = $this->getRelationByExtId($identifier, self::RELATION_TYPE_CUSTOM_OPTION, self::RELATION_TYPE, $product->getId());
                    if($relation->getResourceId()){
                        $optionItem['option_id'] = $relation->getResourceId();

                        /** @var Mage_Catalog_Model_Product_Option $optionModel */
                        $optionModel = $optionsCollection->getItemById($optionItem['option_id']);
                        if($optionModel === null){
                            $relation->delete();
                            unset($optionItem['option_id']);
                        }else{
                            $usedOptionValueIds = array();
                            $currentOptionIds[] = $optionItem['option_id'];

                            // link import option values to existing options
                            foreach($optionItem['values'] as &$optionValue){
                                /** @value Mage_Catalog_Model_Product_Option_Value $valueModel */
                                foreach($optionModel->getValuesCollection() as $valueModel){
                                    if($optionValue['sku'] == $valueModel->getSku()
                                        && $optionValue['title'] == $valueModel->getTitle()){

                                        // overwrite magento options value with import values
                                        $optionValue = array_merge($valueModel->getData(), $optionValue);
                                        $usedOptionValueIds[] = $valueModel->getId();
                                        break;
                                    }

                                }
                            }
                            // delete options which exists in magento but not in the import
                            foreach($optionModel->getValuesCollection() as $valueModel){
                                if(in_array($valueModel->getId(), $usedOptionValueIds)){
                                    continue;
                                }
                                $valueModel->delete();
                            }
                        }
                    }else{
                        if(!isset($this->_newRelations[self::RELATION_TYPE_CUSTOM_OPTION][$productData['sku']])
                            || isset($this->_newRelations[self::RELATION_TYPE_CUSTOM_OPTION][$productData['sku']]) && !in_array($identifier, $this->_newRelations[self::RELATION_TYPE_CUSTOM_OPTION][$productData['sku']])){
                            $this->_newRelations[self::RELATION_TYPE_CUSTOM_OPTION][$productData['sku']][] = $identifier;
                        }
                    }
                }
                $optionItem['is_require'] = $optionItem['is_required'];
                $optionItem['price'] = empty($optionItem['price']) ? '0' : $optionItem['price'];
                $optionItem['price_type'] = empty($optionItem['price_type']) ? 'fixed' : $optionItem['price_type'];
            }
            // delete old options
            foreach($optionsCollection as $option){
                if(in_array($option->getOptionId(), $currentOptionIds)){
                    continue;
                }
                $options[] = array(
                    'option_id' => $option->getOptionId(),
                    'is_delete' => true,
                    'is_require' => '',
                    'price' => 0,
                    'price_type' => 'fixed'
                );
            }

            $product->setProductOptions($options);
            $product->setCanSaveCustomOptions(true);

            unset($productData['options']);
        }


        // bundles
        if(isset($productData['bundle_options_data']) && isset($productData['bundle_selections_data'])){

            Mage::register('product', $product, true);

            /* @var $bundle Mage_Bundle_Model_Product_Type */
            $bundle = $product->getTypeInstance();
            $currentOptionIds = $bundle->getOptionsIds($product);
            $importOptionsIds = array();

            $bundleSelectionsData = array();
            $bundleOptionsData = array();

            $optionIterator = 0;
            foreach ((array)$productData['bundle_selections_data'] as $optionId => $selection){
                foreach ($selection as $bundleProduct){

                    if( empty($bundleProduct['product_id']) ) {
                        $bundleProduct['product_id'] = $this->getRelationByExtId($bundleProduct['sku'])
                          ->getMageId();
                            }

                    $bundleProduct['delete'] = isset($bundleProduct['delete']) ? $bundleProduct['delete'] : '';

                    if(!$bundleProduct['product_id']){
                        continue;
                    }
                    $bundleSelectionsData[$optionId][] = $bundleProduct;
                }
                if(isset($bundleSelectionsData[$optionId])
                    && count($bundleSelectionsData[$optionId])){

                    $currentOption = $productData['bundle_options_data'][$optionId];

                    $currentOption['option_id'] = !empty($currentOption['option_id']) ? $currentOption['option_id'] : $productData['sku'].'_'.$currentOption['title'];
                    $currentOption['delete'] = isset($currentOption['delete']) ? $currentOption['delete'] : '';
                    $currentOption['position'] = isset($currentOption['position']) ? $currentOption['position'] : '';


                    $optionRelation = $this->getRelationByExtId(
                                            $currentOption['option_id'],
                                            self::RELATION_TYPE_BUNDLE_OPTION,
                                            self::RELATION_TYPE
                                        );

                    //set position if none defined
                    if (empty($currentOption['position'])) {
                        $currentOption['position'] = $optionIterator++;
                    }

                    // new Option
                    if (!$optionRelation->getResourceId()) {
                        $this->_newRelations[self::RELATION_TYPE_BUNDLE_OPTION][$currentOption['position']] = $currentOption['option_id'];
                        unset($currentOption['option_id']);
                    }
                    // Option Relation and Option exists
                    elseif($optionRelation->getResourceId() && in_array($optionRelation->getResourceId(), $currentOptionIds)){
                        $importOptionsIds[] = $optionRelation->getResourceId();
                        $currentOption['option_id'] = $optionRelation->getResourceId();
                    }
                    // Option Relation exits but Option do not exists
                    elseif($optionRelation->getResourceId() && !in_array($optionRelation->getResourceId(), $currentOptionIds)){
                        $optionRelation->delete();
                        unset($currentOption['option_id']);
                    }


                    $bundleOptionsData[$optionId] = $currentOption;
                }
            }

            // delete old options
            foreach($bundle->getOptionsCollection($product) as $currentOption){
                if(in_array($currentOption->getOptionId(), $importOptionsIds)){
                    continue;
                }
                $relation = $this->getRelationbyResourceId($currentOption->getOptionId(), self::RELATION_TYPE_BUNDLE_OPTION);
                if($relation->getd()){
                    $relation->delete();
                }
                $currentOption->setDelete(true);
                $bundleOptionsData[$currentOption->getOptionId()] = $currentOption->getData();

            }

            if(!$product->getCompositeReadonly()) {
                $product->setBundleOptionsData($bundleOptionsData);
            }

            if (isset($productData['affect_bundle_product_selections']) && !$product->getCompositeReadonly()) {
                $product->setBundleSelectionsData($bundleSelectionsData);
            }

            if ($product->getPriceType() == '0' && !$product->getOptionsReadonly()) {
                $product->setCanSaveCustomOptions(true);
                if ($customOptions = $product->getProductOptions()) {
                    foreach (array_keys($customOptions) as $key) {
                        $customOptions[$key]['is_delete'] = 1;
                    }
                    $product->setProductOptions($customOptions);
                }
            }

            $product->setCanSaveBundleSelections((bool)$productData['affect_bundle_product_selections'] && !$product->getCompositeReadonly());
        }

        // configurable product
        if(isset($productData['configurable_products_data'])){

            $configurableProductsData = array();
            foreach ($productData['configurable_products_data'] as $subProduct){

                if(!empty($subProduct['id'])){
                    $identifier = $subProduct['id'];
                }
                else{
                    $this->_fault('no_subproduct_identifier');
                }
                $relation = $this->getRelationByExtId($identifier);

                if ($relation->getId()){
                    $subProduct['id'] = $relation->getMageId();
                }else{
                    continue;
                }

                $configurableProductsData[$subProduct['id']] = $subProduct['values'];
            }

            $product->setConfigurableProductsData($configurableProductsData);
        }

        // build attribute data from product data if no attribute data exists
        if (isset($productData['configurable_products_data']) && !isset($productData['configurable_attributes_data'])){

            //format data for lated processing
            $confAttrData = array();
            foreach ($productData['configurable_products_data'] as $key => $simpleProduct){

                foreach ($simpleProduct['values'] as $values){
                    $confAttrData[$values['attribute_id']]['attribute_id'] = $values['attribute_id'];
                    $confAttrData[$values['attribute_id']]['attribute_code'] = $values['attribute_code'];
                    $confAttrData[$values['attribute_id']]['value_index'][$values['value_index']] = $values['value_index'];
                }
            }

            //set attribute data
            $productData['configurable_attributes_data'] = array();
            foreach ($confAttrData as $attrData){

                $currentAttribute = array();

                $id = $attrData['attribute_code'].$productData['sku'];
                $currentAttribute['id'] = $id;
                $currentAttribute['attribute_id'] = $attrData['attribute_id'];
                $currentAttribute['attribute_code'] = $attrData['attribute_code'];
                $currentAttribute['use_default'] = '1';
                $currentAttribute['label'] = '';

                foreach ($attrData['value_index'] as $value){
                    $currentAttribute['values'][] = array(
                        'value_index'                    => $value,
                        'pricing_value'                    => '',
                        'is_percent'                    => ''
                    );
                }
                $productData['configurable_attributes_data'][] = $currentAttribute;
            }
        }

        // handle configurable Product Attributes
        if (isset($productData['configurable_attributes_data'])){

            $configurableAttributesData = array();

            foreach ($productData['configurable_attributes_data'] as $key => $attribute){

                $identifier = $attribute['attribute_code'].$productData['sku'];

                $configurableAttributesData[$key] = $attribute;
                $relation = $this->getRelationByExtId($identifier, self::RELATION_TYPE_CONF_ATTRIBUTE);

                if (!$relation->getId()){
                    if($product->getId() && isset($attribute['attribute_id'])){
                        $superattribute = Mage::getResourceModel('catalog/product_type_configurable_attribute_collection')
                            ->setProductFilter($product)
                            ->addFieldToFilter('attribute_id', $attribute['attribute_id'])
                            ->load()
                            ->getFirstItem();
                        $relation->setResourceId($superattribute->getId());
                    }
                    $this->_newRelations[self::RELATION_TYPE_CONF_ATTRIBUTE][] = $identifier;
                }

                $configurableAttributesData[$key]['id'] = $relation->getResourceId();
            }
            $product->setConfigurableAttributesData($configurableAttributesData);
        }

    }

    /**
     * get Configurable Attribute Index
     *
     * @param $relationId
     */
    protected function _getConfigurableValueIndex($relationId){

        if (!isset($this->_optionRelations[$relationId])){

            $this->_optionRelations[$relationId] = $this->getRelationByExtId($relationId, Flagbit_Mip_Model_Resource_Attribute::OPTION_RELATION_TYPE, Flagbit_Mip_Model_Resource_Attribute::OPTION_RELATION_TYPE)->getResourceId();
        }

        if (!$this->_optionRelations[$relationId]){
            return $relationId;
        }

        return $this->_optionRelations[$relationId];
    }

    /**
     * Product after Save
     * handle Relation stuff
     *
     * @param $product Mage_Catalog_Model_Product
     * @param $productData array
     */
    protected function _afterSave($product, $productData){

        foreach ($this->_newRelations as $key => $data){

            if (empty($data)){
                continue;
            }

            switch ($key){
                case self::RELATION_TYPE_CONF_ATTRIBUTE:
                    if (isset($productData['configurable_attributes_data'])){
                        foreach ($productData['configurable_attributes_data'] as $attribute){
                            $identifier = $attribute['attribute_code'].$productData['sku'];
                            if (in_array($identifier, $data)){

                                /* @var $attributeCollection Mage_Catalog_Model_Resource_Eav_Mysql4_Product_Type_Configurable_Attribute_Collection */
                                $attributeCollection = $product
                                    ->getTypeInstance()
                                    ->getConfigurableAttributeCollection($product);

                                $createdAttribute = $attributeCollection->getItemByColumnValue('attribute_id', $attribute['attribute_id']);
                                $this->createRelation($identifier, $createdAttribute->getId(), self::RELATION_TYPE_CONF_ATTRIBUTE, $product->getId(), self::RELATION_TYPE);
                            }
                        }
                    }
                    break;

                case self::RELATION_TYPE_BUNDLE_OPTION:
                    foreach ($this->_getBundleOptions($product) as $option) {

                        if (empty($data[$option->getPosition()])) {
                            continue;
                        }
                        $this->createRelation(
                            $data[$option->getPosition()],
                            $option->getOptionId(),
                            self::RELATION_TYPE_BUNDLE_OPTION,
                            $product->getId(),
                            self::RELATION_TYPE
                        );
                    }
                    break;

                case self::RELATION_TYPE_CUSTOM_OPTION:
                    if(is_array($data[$productData['sku']])){
                        $i=0;
                        foreach ($product->getProductOptionsCollection() as $option) {
                            if(empty($data[$productData['sku']][$i])){
                                continue;
                            }
                            $identifier = $data[$productData['sku']][$i];
                            $i++;
                            $this->createRelation(
                                $identifier,
                                $option->getOptionId(),
                                self::RELATION_TYPE_CUSTOM_OPTION,
                                $product->getId(),
                                self::RELATION_TYPE
                            );

                        }
                    }
                    break;

                case self::RELATION_TYPE_PRODUCT_IMAGE:

                    if(is_array($data[$productData['sku']])){
                        foreach($data[$productData['sku']] as $orgFile => $file){
                            $file = $this->_getGalleryAttribute($product)->getBackend()->getRenamedImage($file);
                            $this->createRelation($orgFile, $file, self::RELATION_TYPE_PRODUCT_IMAGE, $product->getId(), self::RELATION_TYPE);
                        }
                        // set Type in Adminstore when it is empty
                        if($product->getStoreId() != Mage_Core_Model_App::ADMIN_STORE_ID){
                            foreach(array_keys($product->getMediaAttributes()) as $mediaAttribute) {

                                $attributeValue = $product->getResource()->getAttributeRawValue($product->getId(), $mediaAttribute, Mage_Core_Model_App::ADMIN_STORE_ID);
                                if(!$attributeValue){
                                    $orgStoreId = $product->getStoreId();
                                    $product->setStoreId(Mage_Core_Model_App::ADMIN_STORE_ID);
                                    $product->getResource()->saveAttribute ( $product, $mediaAttribute );
                                    $product->setStoreId($orgStoreId);
                                }
                            }
                        }
                    }
                    break;
            }

            unset($this->_newRelations[$key]);
        }
    }

    /**
     * get bundle options
     *
     * retrieves bundle option collection from a budled product
     * @param $product Mage_Catalog_Model_Product
     *
     * @return Mage_Core_Model_Mysql4_Collection_Abstract
     */
    protected function _getBundleOptions(Mage_Catalog_Model_Product $product)
    {
        $product->getTypeInstance(true)->setStoreFilter($product->getStoreId(), $product);

        $optionCollection = $product->getTypeInstance(true)->getOptionsCollection($product);

        $selectionCollection = $product->getTypeInstance(true)->getSelectionsCollection(
            $product->getTypeInstance(true)->getOptionsIds($product),
            $product
        );

        return $optionCollection->appendSelections($selectionCollection);
    }

    /**
     * Retrieve list of products without product loads
     *
     * @param array $filters
     * @return array
     */
    public function itemsFast($filters = array())
    {
        $store = null;
        $attributes = array();

        $collection = Mage::getResourceModel('catalog/product_collection');
        $this->_applyFiltersToCollection($collection, $filters, array('attributes'));

        if (is_array($filters) && isset($filters['attributes'])) {
            $attributes = explode(',', $filters['attributes']);
        }

        $result = array();
        foreach ($collection->getAllIds() as $productId) {
            $result[] = $this->infoFast($productId, $attributes);
        }

        Mage::dispatchEvent('mip_resource_product_listfast_after', array('data' => $result));

        return $result;
    }

    /**
     * helper function to apply filters to collections
     *
     * @param $collection
     * @param $filters
     * @param array $exclude
     * @throws Flagbit_Mip_Model_Exception
     */
    protected function _applyFiltersToCollection($collection, $filters, $exclude=array())
    {
        if (is_array($filters)) {
            try {
                foreach ($filters as $field => $value) {
                    if(in_array($field, $exclude)){
                        continue;
                    }
                    if($field == 'store'){
                        $collection->setStoreId($this->_getStoreId($value));
                        continue;
                    }
                    if (isset($this->_filtersMap[$field])) {
                        $field = $this->_filtersMap[$field];
                    }
                    if(strstr($value, ',')){
                        $collection->addFieldToFilter($field, array('in' => Mage::helper('mip')->trimExplode(',', $value)));
                    }else{
                        $collection->addFieldToFilter($field, $value);
                    }
                }
            } catch (Mage_Core_Exception $e) {
                $this->_fault('filters_invalid', $e->getMessage());
            }
        }
    }

    /**
     * Retrieve product info without product load
     *
     * @param int|string $id
     * @param array $attributes
     * @return array
     */
    public function infoFast($id, $attributes = array())
    {
        if(!is_array($attributes)){
            $this->_fault('attributes_not_set');
            return;
        }

        $result['product_id'] = $id;
        foreach($attributes as $attribute){

            if($attribute == 'stock'){
                $stock = Mage::getModel('Mage_CatalogInventory_Model_Stock');
                $stock_data = Mage::getModel('Mage_CatalogInventory_Model_Resource_Stock')->getProductsStock($stock, $id);

                if(!isset($stock_data[0])){
                    continue;
                }

                $result['stock_data'] = $stock_data[0];
            }
            else {
                $result[$attribute] = Mage::getModel('catalog/product')->getResource()->getAttributeRawValue($id, $attribute, Mage_Core_Model_App::ADMIN_STORE_ID);
            }
        }

        Mage::dispatchEvent('mip_resource_product_infofast_after', $result);

        return $result;
    }

    /**
     * Retrieve list of products with basic info (id, sku, type, set, name)
     *
     * @param array $filters
     * @param string|int $store
     * @param int $updatedAt
     * @return array
     */
    public function items($filters = array())
    {
        $store = null;
        $collection = Mage::getModel('catalog/product')->getCollection()
            ->addAttributeToSelect('name');

        $this->_applyFiltersToCollection($collection, $filters);

        $result = array();
        foreach ($collection as $product) {
            $result[] = $this->info($product->getId(), $store);
        }
        return $result;
    }


    /**
     * Retrieve product info
     *
     * @param int|string $productId
     * @param string|int $store
     * @param array $attributes
     * @return array
     */
    public function info($id, $store = null, $attributes = null)
    {
        /* @var $product Mage_Catalog_Model_Product */
        $product = $this->_getProduct($id, $store);


        if (!$product->getId()) {
            $this->_fault('product_not_exists');
        }
        $result = array( // Basic product data
            'product_id'    => $product->getId(),
               'parent_ids'    => $product->getParentProductIds(),
            'sku'            => $product->getSku(),
            'set'            => $product->getAttributeSetId(),
            'type'            => $product->getTypeId(),
            'categories'    => $product->getCategoryIds(),
            'website_ids'    => $product->getWebsiteIds(),
            'store_ids'        => $product->getStoreIds(),
            'stock_data'    => $product->getStockItem()->getData()
        );


        switch ($result['type']){

            // handle bundle products
            case 'bundle':
                $optionCollection = $product->getTypeInstance()->getOptionsCollection();
                $selectionCollection = $product->getTypeInstance()->getSelectionsCollection($product->getTypeInstance()->getOptionsIds());
                $options = $optionCollection->appendSelections($selectionCollection);
                foreach( $options as $option )
                {
                    $_selections = $option->getSelections();

                    $bundleOptionData = $option->getData();
                    unset($bundleOptionData['selections']);
                    $result['bundle_options_data'][] = $bundleOptionData;

                    foreach( $_selections as $selection )
                    {

                        $result['bundle_selections_data'][$option->getId()][] = array(
                            'product_id' => $selection->getData('product_id'),
                            'sku' => $selection->getSku(),
                            'option_id' => $option->getId(),
                            'position' => $selection->getData('position'),
                            'selection_price_type' =>  $selection->getData('selection_price_type'),
                            'selection_price_value' =>  $selection->getData('selection_price_value'),
                            'selection_qty' =>  $selection->getData('selection_qty'),
                            'selection_can_change_qty' =>  $selection->getData('selection_can_change_qty')
                        );
                    }
                }
                break;

            // handle configurable products
            case 'configurable':
                if(!$product->isConfigurable()) {
                    break;
                }
                $result['configurable_attributes_data'] = $product->getTypeInstance()->getConfigurableAttributesAsArray($product);

                $confAttributeIds = $product->getTypeInstance()->getUsedProductAttributeIds($product);
                $result['configurable_products_data'] = array();

                /*@var $subProduct Mage_Catalog_Model_Product */
                foreach($product->getTypeInstance()->getUsedProductCollection($product) as $subProduct){

                    $subProduct->load($subProduct->getId());
                    $values = array();
                    foreach ($subProduct->getAttributes() as $attribute){

                        if(!in_array($attribute->getId(), $confAttributeIds)){
                            continue;
                        }

                        $values[] = array(
                            'attribute_id' => $attribute->getId(),
                            'attribute_code' => $attribute->getData('attribute_code'),
                            'value_index' => $subProduct->getData($attribute->getData('attribute_code')),
                        );
                    }

                    $result['configurable_products_data'][] = array(
                        'id' => $subProduct->getId(),
                        'values' => $values
                    );
                }
                break;

        }

        foreach ($product->getTypeInstance()->getEditableAttributes() as $attribute) {
            if ($this->_isAllowedAttribute($attribute, $attributes)) {
                   $result[$attribute->getAttributeCode()] = $product->getData($attribute->getAttributeCode());
            }
        }

        return $result;
    }


    /**
     * saves product attributes in a faster way than saving the whole product
     *
     * @param array $data
     */
    public function saveAttributes($data){

        $ignoredKeys = array('product_id', 'mip_datahash_id', 'sku', 'store', 'stock_data', 'website_ids');

        $i=0;
        $count = count($data);
        $productResource = Mage::getResourceModel('mip/product_resource');
        $readConnection = $productResource->getReadConnection();

        foreach ( $data as $product ) {

            $i++;
            $productId = null;

            // get Product Identifier
            if (! empty ( $product ['product_id'] )) {
                $productId = $product ['product_id'];
                $identifier = 'ID:'.$product ['product_id'];
            } elseif (! empty ( $product ['sku'] )) {
                $identifier = $product ['sku'];
            } else {
                $this->_fault ( 'no_identifier' );
            }

            // set Debug Log Output
            $this->countOutput = $i . '/' . $count . ' - ' . round ( 100 / $count * $i, 0 ) . '% ';

            try {
                // get Relation by Relation Table or Product SKU
                if(!$productId){
                    $relation = $this->getRelationByExtId( $identifier );
                    if (!$relation->getId()) {
                        $productId = $this->_getProductIdBySku($identifier);
                        if($productId !== null){
                            $relation = $this->createRelation($identifier, $productId);
                        }else{
                            Mage::helper('mip/log')->getWriter($this)->warn( $this->countOutput . ' SKIP Product (' . $identifier . ' no Relation' );
                            continue;
                        }
                    }
                    $productId = $relation->getMageId ();
                }

                // check product exists
                if (! $readConnection->fetchOne ( $readConnection->select ()->from ( $productResource->getTable ( 'catalog/product' ) )->where ( $productResource->getIdFieldName () . '=?', $productId ) )) {
                    Mage::helper('mip/log')->getWriter($this)->warn ( $this->countOutput . ' SKIP Product (' . $identifier . ' not exists' );
                    continue;
                }

                // set Store
                $store = 0;
                if (isset ( $product ['store'] )) {
                    $store = $product ['store'];
                }

                //update attributes
                $productModel = Mage::getModel ( "catalog/product" )->setId ( $productId );
                $productModel->setEntityTypeId (
                                $productModel->getResource()
                                            ->getEntityType()
                                            ->getId ()
                                            )
                            ->setStoreId ( $store );

                // Iterate attributes
                foreach ( $product as $attributeCode => $value ) {

                    // pass ignored Attributes
                    if (in_array ( $attributeCode, $ignoredKeys )) {
                        continue;
                    }

                    $productModel->setData ( $attributeCode, $value );
                    $productModel->setData('_edit_mode', true);

                    /**
                     * handle Datetime Attributes is_formated Option
                     * And automated Price Indexer Option
                     */
                    if(substr($attributeCode, -12) == '_is_formated'
                        || $attributeCode == 'rebuild_price_index'){
                        continue;
                    }

                    $attribute = $productResource->getAttribute($attributeCode);
                    Mage::dispatchEvent('mip_product_resource_save_attribute', array('product' => $product, 'attribute' => $attribute, 'resource' => $this));

                    if(is_object($attribute)) {
                        if($attribute->getBackendModel()) {
                            $attribute->getBackend()->afterLoad($productModel);
                            $productModel->setData ( $attributeCode, $value );
                            $attribute->getBackend()->beforeSave($productModel);
                        }

                        $productResource->saveAttribute ( $productModel, $attributeCode );
                    }

                    // prevent calling of price backend model afterSave method
                    // because it can overwrites the prices of other storeviews
                    if($attributeCode != 'price' && $attributeCode != 'special_price' && is_object($attribute) && $attribute->getBackendModel()) {
                        $attribute->getBackend()->afterSave($productModel);
                    }

                    Mage::helper('mip/log')->getWriter($this)->info( $this->countOutput . ' UPDATE Product (' . $identifier . ':'.$store.') Attribute (' . $attributeCode . ') Value=' . $value );
                }

                //update stock data
                if (isset ( $product ['stock_data'] ) && is_array ( $product ['stock_data'] )) {

                    $stockItem = Mage::getModel ( 'cataloginventory/stock_item' );
                    $stockItem->setData('store_id', $store);
                    $stockItem->loadByProduct ( $productModel );
                    foreach($product['stock_data'] as $stockKey => $stockValue){
                        Mage::helper('mip/log')->getWriter($this)->info( $this->countOutput . ' UPDATE Product (' . $identifier . ':'.$store.') Stock '.$stockKey.'='.$stockValue );
                    }

                    $stockItem->addData ( $product ['stock_data'] );
                    $stockItem->save ();

                }

                if($productModel->getRebuildPriceIndex()){
                    $event = new Mage_Index_Model_Event();
                    $event->setNewData(array('reindex_price_product_ids' => array($productModel->getId())));
                    Mage::getResourceModel('catalog/product_indexer_price')->catalogProductMassAction($event);
                    Mage::helper('mip/log')->getWriter($this)->info( $this->countOutput . ' UPDATE Product (' . $identifier . ':'.$store.') rebuild price Index');
                }

            } catch ( Exception $e ) {
                Mage::helper ( 'mip/error' )->handleException ( $e );
                Mage::helper('mip/log')->getWriter($this)->error( $this->countOutput . ' Product (' . $identifier . ':'.$store.') Import Error: ' . $e->getMessage(), $e );
            }
        }
    }

    /**
     * check if product exists
     *
     * @param $sku
     * @param string $field
     * @return bool
     */
    protected function _checkIfProductExists($sku, $field = 'sku')
    {
        $productResource = Mage::getResourceModel('mip/product_resource');
        $readConnection = $productResource->getReadConnection();

        $query = $readConnection->select ()
                    ->from ( $productResource->getTable ( 'catalog/product' ) )
                    ->where ( $field.' =?', $sku );

        // check product exists
        if ($readConnection->fetchOne ( $query )) {
            return true;
        }
        return false;
    }


    /**
     * get Product by SKU faster than single lookup
     *
     * @param string $sku
     * @return int
     */
    protected function _getProductIdBySku($sku){

        if($this->_skuToIdMapping == null){

            $productCol = Mage::getResourceModel('catalog/product_collection');
            $idsSelect = clone $productCol->getSelect();
            $idsSelect->reset(Zend_Db_Select::ORDER);
            $idsSelect->reset(Zend_Db_Select::LIMIT_COUNT);
            $idsSelect->reset(Zend_Db_Select::LIMIT_OFFSET);
            $idsSelect->reset(Zend_Db_Select::COLUMNS);
            $idsSelect->from(null, array('e.sku', 'e.'.$productCol->getEntity()->getIdFieldName()));
            $idsSelect->resetJoinLeft();

            $this->_skuToIdMapping = $productCol->getConnection()->fetchPairs($idsSelect, array());

        }
        return isset($this->_skuToIdMapping[$sku]) ? $this->_skuToIdMapping[$sku] : null;
    }

}