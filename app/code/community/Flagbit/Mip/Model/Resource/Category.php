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
class Flagbit_Mip_Model_Resource_Category extends Flagbit_Mip_Model_Resource_Abstract {

    protected $_filtersMap = array(
        'product_id' => 'entity_id',
        'set'        => 'attribute_set_id',
        'type'       => 'type_id'
    );

    /**
     * Output Counter for Log
     *
     * @var string
     */
    protected $countOutput = '';

    protected $total = 0;

    protected $current = 0;

    const RELATION_TYPE = 'category';

    /**
     * get Resource Relation Type
     *
     * @return string
     */
    protected function getRelationType(){
        return self::RELATION_TYPE;
    }

    /**
     * save Category Items
     *
     * @param $data
     */
    public function saveItems($data){

        // fix $_FILES error with dummy data
        $imageDummy = array (
                "name" => "",
                "type" => "",
                "tmp_name" => "",
                "error" => 4,
                "size" => 0,
        );
        $_FILES = array_merge( array('image' => $imageDummy),
                array('thumbnail' => $imageDummy),
                $_FILES
        );

        if(isset($data['children'])){

            // fix Category Positions
            //$this->_fixCategoryPositions($data);

            // Import Data
            $this->_createTreeFromArray($data['children'], $data['category_id']);

            // delete unused Categories
            if(!empty($data['delete_old_childs'])){
                $this->_deleteCategoriesFromTree($data);
            }
        }else{
            $this->_fault('no_data');
        }

    }

    /**
     * fix Category Positions
     *
     * @param $data
     * @return bool|int
     */
    protected function _fixCategoryPositions($categoryIds)
    {
        /* @var $resource Mage_Core_Model_Resource */
        $resource = Mage::getSingleton('core/resource');
        $connection = $resource->getConnection('core_write');

        if(count($categoryIds)){
            $result = $connection->query('SET @c := 0; UPDATE catalog_category_entity SET `position` = ( SELECT @c := @c + 10 ) where entity_id IN('.implode(',', $categoryIds).')  ORDER BY parent_id ASC, position ASC');
            return $result->rowCount();
        }
        return false;
    }

    /**
     * deletes old Categories from Magento
     *
     * @param array $newTree
     * @param array $mageTree
     */
    protected function _deleteCategoriesFromTree($newTree = null, $mageTree = null){

        if($newTree === null || $mageTree === null){

            $children = $newTree['children'];
            $parentId = $newTree['category_id'];
            $newTree = $this->_makeIdTree($children, true);
            $magentoCategoryTree = $this->items($parentId);
            $mageTree = $this->_makeIdTree($magentoCategoryTree['children']);
        }

        foreach($mageTree as $categoryId => $childCategories){

            if(!isset($newTree[$categoryId])){
                $this->delete($categoryId);
                continue;
            }

            $this->_deleteCategoriesFromTree($newTree[$categoryId], $childCategories);
        }

    }

    /**
     * generates a Category Tree from input Array with Magento Id´s from Relation Table
     * This is used to delete old categories in Method _deleteCategoriesFromTree
     *
     * @param array $categories
     * @param boolean $getMageIds
     */
    protected function _makeIdTree($categories, $getMageIds = false, $flat = false){

        $idArray = array();

        foreach ($categories as $category){
            if($getMageIds){
                $relation = $this->getRelationByExtId($category['category_id']);
                $category['category_id'] = $relation->getMageId();
            }

            if(isset($category['children']) && is_array($category['children']) && count($category['children'])){
                if($flat){
                    $idArray = array_merge($this->_makeIdTree($category['children'], $getMageIds, $flat), $idArray);
                }else{
                    $idArray[$category['category_id']] = $this->_makeIdTree($category['children'], $getMageIds, $flat);
                }
                continue;
            }
            if(!isset($idArray[$category['category_id']])){
                if($flat){
                    $idArray[] = $category['category_id'];
                }else{
                    $idArray[$category['category_id']] = array();
                }
            }
        }
        return $idArray;
    }

    /**
     * Category sorting function called by _createTreeFromArray -> uasort
     *
     * @param array $a
     * @param array $b
     * @return int
     */
    protected function __positionSort($a, $b){

        if(isset($a['position']) && isset($b['position'])){
            return $a['position'] > $b['position'] ? 1 : -1;
        }
        return 0;
    }

    /**
     * create or update Category Tree from Array
     *
     * @param array $categories
     * @param int $parentId
     * @param int $store
     */
    protected function _createTreeFromArray($categories, $parentId=1, $store=null){
        $previousCategoryId = null;

        if(!is_array($categories)){
            return;
        }

        // sort Categories by position attribute
        uasort($categories, array(&$this, '__positionSort'));

        $this->total += count($categories);
        foreach($categories as $arrayKey => $category){

                $this->current++;
                $this->countOutput = $this->current.'/'.$this->total.' - '.round(100/$this->total * $this->current, 0).'% ';

            if(isset($category['children']) && is_array($category['children']) && count($category['children'])){
                $children = $category['children'];
                unset($category['children']);
            }else{
                $children = array();
            }

            $this->_datahashIdentifier = empty($category['mip_datahash_id']) ? null: $category['mip_datahash_id'];

            // get Store Ids
            if(isset($category['store_ids'])){

                if(is_array($category['store_ids']) && count($category['store_ids']) > 1){
                    for($si=0; count($category['store_ids']) > $si; $si++){

                        if(count($category['store_ids']) == $si-1){
                            $store = $category['store_ids'][$si];
                            break;
                        }else{
                            $categoryCopy = $category;
                            $categoryCopy['store_ids'] = $category['store_ids'][$si];
                            $categories[] = $categoryCopy;
                        }
                    }

                }elseif(is_array($category['store_ids']) && count($category['store_ids']) == 1){
                    $store = $category['store_ids'][0];
                }else{
                    $store = $category['store_ids'];
                }
            }

            // get Relation
            $relation = $this->getRelationByExtId($category['category_id']);

            // set Product Sorting
            if (!empty($category['products'])) {

                foreach ($category['products'] as $product) {
                    $productRelation = $this->getRelationByExtId($product['product_sku'], Flagbit_Mip_Model_Resource_Product::RELATION_TYPE, Flagbit_Mip_Model_Resource_Product::RELATION_TYPE);
                    $productId = $productRelation->getMageId();

                    if ($productId) {
                        $category['posted_products'][$productId] = $product['product_sort'];
                    }
                }
                unset($category['products']);
            }

            // update Category
            if($relation->getId()){
                $this->update($relation->getMageId(), $category, $store);
                if(empty($category['do_not_move'])){
                    $this->move($relation->getMageId(), $parentId, $previousCategoryId);
                }
                $id = $relation->getMageId();

            // create Category
            }else{
                $id = $this->create($parentId, $category, $store);
            }

            $previousCategoryId = $id;

            // process Children
            if(count($children)){
                $this->_createTreeFromArray($children, $id, $store);
            }
        }
    }

    /**
     * Check is attribute allowed
     *
     * @param Mage_Eav_Model_Entity_Attribute_Abstract $attribute
     * @param array $attributes
     * @return boolean
     */
    protected function _isAllowedAttribute($attribute, $attributes = null)
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
     * Create new category
     *
     * @param int $parentId
     * @param array $categoryData
     * @return int
     */
    public function create($parentId, $categoryData, $store = null)
    {

        $parent_category = $this->_initCategory($parentId);

           /*@var $category Mage_Catalog_Model_Category*/
        $category = Mage::getModel('catalog/category')
            ->setStoreId($this->_getStoreId($store));

        $category->addData(array('path'=>implode('/',$parent_category->getPathIds())));

        $category->setAttributeSetId($category->getDefaultAttributeSetId());
        /* @var $category Mage_Catalog_Model_Category */

        foreach ($category->getAttributes() as $attribute) {
            if ($this->_isAllowedAttribute($attribute)
                && isset($categoryData[$attribute->getAttributeCode()])) {
                $category->setData(
                    $attribute->getAttributeCode(),
                    $categoryData[$attribute->getAttributeCode()]
                );
            }
        }

        $category->setParentId($parent_category->getId());

        //set product sorting data
        if (!empty($categoryData['posted_products'])){
            $category->setPostedProducts($categoryData['posted_products']);
        }
        try {
            $category->save();
            Mage::helper('mip/log')->getWriter($this)->info($this->countOutput.' CREATE Category ('.$category->getId().')');
        } catch (Mage_Core_Exception $e) {
            $this->_fault('data_invalid', $e->getMessage());
        }

        $this->createRelation($categoryData['category_id'], $category->getId());

        return $category->getId();
    }

    /**
     * Update category data
     *
     * @param int $categoryId
     * @param array $categoryData
     * @param string|int $store
     * @return boolean
     */
    public function update($categoryId, $categoryData, $store = null)
    {
        $category = $this->_initCategory($categoryId, $store);

        foreach ($category->getAttributes() as $attribute) {
            if ($this->_isAllowedAttribute($attribute)
                && isset($categoryData[$attribute->getAttributeCode()])) {

                $category->setData(
                    $attribute->getAttributeCode(),
                    $categoryData[$attribute->getAttributeCode()]
                );
            }
        }
        //set product sorting data
        if (!empty($categoryData['posted_products'])){
            $category->setPostedProducts($categoryData['posted_products']);
        }

        try {
            $category->unsPosition(); // delete Position, this is necessary because otherwise the categories are moved
            $category->save();
            Mage::helper('mip/log')->getWriter($this)->info($this->countOutput.' UPDATE Category ('.$category->getId().')');
        } catch (Mage_Core_Exception $e) {
            $this->_fault('data_invalid', $e->getMessage());
        }

        return true;
    }


    /**
     * Move category in tree
     *
     * @param int $categoryId
     * @param int $parentId
     * @param int $afterId
     * @return boolean
     */
    public function move($categoryId, $parentId, $afterId = null)
    {
        $category = $this->_initCategory($categoryId);
        $parent_category = $this->_initCategory($parentId);

        $origParentId = $category->getParentId();
        $origAfterId = null;

        $neighborIds = $category->getCollection()
            ->clear()
            ->addFieldToFilter('parent_id', $parentId)
            ->setOrder('position', Varien_Db_Select::SQL_ASC)
            ->load()
            ->getLoadedIds();

        $this->_fixCategoryPositions($neighborIds);

        foreach($neighborIds as $neighborId){
            if($neighborId == $categoryId){
                break;
            }
            $origAfterId = $neighborId;
        }

        // only move Categories when it is necessary
        if($origParentId != $parentId || ($afterId !== null && $origAfterId != $afterId)){
            Mage::helper('mip/log')->getWriter($this)->warn($this->countOutput.' MOVE Category ('.$categoryId.') Parent: '.$origParentId.' -> '.$parentId.', After: '.$origAfterId.' -> '.$afterId);

            // Magento newer than Version 1.4 CE or 1.9 EE
            if(Mage::helper('mip')->compareVersion('1.4', '1.9')){

                // if $afterId is null - move category to the down
                if ($afterId === null && $parent_category->hasChildren()) {
                    $parentChildren = $parent_category->getChildren();
                    $parentChildrenArray = explode(',', $parentChildren);
                    $afterId = array_pop($parentChildrenArray);
                }
                if( strpos($parent_category->getPath(), $category->getPath()) === 0) {
                    $this->_fault('not_moved', "Operation do not allow to move a parent category to any of children category");
                }

                try {
                    $category->move($parentId, $afterId);
                } catch (Mage_Core_Exception $e) {
                    $this->_fault('not_moved', $e->getMessage());
                }
                return true;

            // old Version
            }else{

                $tree = Mage::getResourceModel('catalog/category_tree')
                        ->load();

                $node           = $tree->getNodeById($category->getId());
                $newParentNode  = $tree->getNodeById($parent_category->getId());

                if (!$node || !$node->getId()) {
                    $this->getRelationByExtId($categoryId)->delete();
                    $this->_fault('not_exists category '.$categoryId);
                }

                // if $afterId is null - move category to the down
                if ($afterId === null && $parent_category->hasChildren()) {
                    $parentChildren = $parent_category->getChildren();
                    $parentsArray = explode(',', $parentChildren);
                    $afterId = array_pop($parentsArray);
                }

                $prevNode = $tree->getNodeById($afterId);

                if (!$prevNode || !$prevNode->getId()) {
                    $prevNode = null;
                }

                try {
                    $tree->move($node, $newParentNode, $prevNode);
                } catch (Mage_Core_Exception $e) {
                    $this->_fault('not_moved', $e->getMessage());
                }
            }
        }
        return true;
    }

    /**
     * Delete category
     *
     * @param int $categoryId
     * @return boolean
     */
    public function delete($categoryId)
    {
        $category = $this->_initCategory($categoryId);
        try {
            $category->delete();
            Mage::helper('mip/log')->getWriter($this)->info($this->countOutput.' DELETE Category ('.$categoryId.')');
        } catch (Mage_Core_Exception $e) {
            $this->_fault('not_deleted', $e->getMessage());
        }
        return true;
    }

    /**
     * Initilize and return category model
     *
     * @param int $categoryId
     * @param string|int $store
     * @return Mage_Catalog_Model_Category
     */
    protected function _initCategory($categoryId, $store = null)
    {
        $category = Mage::getModel('catalog/category')
            ->setStoreId($this->_getStoreId($store))
            ->load($categoryId);

        if (!$category->getId()) {
            try {
                Mage::getModel('mip/data_relation')
                    ->deleteByDatarecord(
                        $categoryId,
                        Flagbit_Mip_Model_Resource_Category::RELATION_TYPE
                    );
                $this->_fault('not_exists category '.$categoryId);
            }
            catch (Exception $e){
                throw $e;
            }
        }

        return $category;
    }

    /**
     * Retrieve category tree
     *
     * @param int $parent
     * @param string|int $store
     * @return array
     */
    public function items($parentId = null, $store = null)
    {

        if (is_null($parentId) && !is_null($store)) {
            $parentId = Mage::app()->getStore($this->_getStoreId($store))->getRootCategoryId();
        } elseif (is_null($parentId)) {
            $parentId = 1;
        }

        $tree = Mage::getResourceSingleton('catalog/category_tree')
            ->load();
        /* @var $tree Mage_Catalog_Model_Resource_Eav_Mysql4_Category_Tree */

        $root = $tree->getNodeById($parentId);

        if($root && $root->getId() == 1) {
            $root->setName(Mage::helper('catalog')->__('Root'));
        }

        $collection = Mage::getModel('catalog/category')->getCollection()
            ->setStoreId($this->_getStoreId($store))
            ->addAttributeToSelect('name')
            ->addAttributeToSelect('is_active');

        $tree->addCollectionData($collection, true);

        return $this->_nodeToArray($root);
    }

    /**
     * Convert node to array
     *
     * @param Varien_Data_Tree_Node $node
     * @return array
     */
    protected function _nodeToArray(Varien_Data_Tree_Node $node)
    {
        // Only basic category data
        $result = array();
        $result['category_id'] = $node->getId();
        $result['parent_id']   = $node->getParentId();
        $result['name']        = $node->getName();
        $result['is_active']   = $node->getIsActive();
        $result['position']    = $node->getPosition();
        $result['level']       = $node->getLevel();
        $result['children']    = array();

        foreach ($node->getChildren() as $child) {
            $result['children'][] = $this->_nodeToArray($child);
        }

        return $result;
    }
}
