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
class Flagbit_Mip_Model_Resource_Attribute extends Flagbit_Mip_Model_Resource_Abstract {


    const RELATION_TYPE = 'attribute';
    const OPTION_RELATION_TYPE = 'attribute_option';

    protected $countOutput = '';

    protected $_attributes = array();

    protected $_attributeOptions = array();

    protected $_entityTypeId = null;

    protected function getRelationType(){
        return self::RELATION_TYPE;
    }

    /**
     * fetchs all visible catalogue product attributes
     *
     * @return array
     */
    public function items() {

        $collection = Mage :: getResourceModel('eav/entity_attribute_collection')
        ->setEntityTypeFilter(Mage :: getModel('eav/entity')->setType('catalog_product')->getTypeId());

        return $collection->getData();
    }

    /**
     * fetchs a detailed list of catalogue product attributes
     *
     * @return array
     */
    public function attributes(){

        $collection = Mage::getResourceModel('catalog/product_attribute_collection')
            ->setEntityTypeFilter( Mage::getModel('eav/entity')->setType('catalog_product')->getTypeId() )
            ->addVisibleFilter();

        $result = array();
        foreach ($collection as $item){

            $node = $item->toArray();

            if ($item->frontend_input == 'select' || $item->frontend_input == 'multiselect'){

                $optionCollection = Mage::getResourceModel('eav/entity_attribute_option_collection')
                    ->setAttributeFilter($item->attribute_id)
                    ->setPositionOrder('desc', true)
                    ->load();

                foreach ($optionCollection as $option){

                    $options = array(
                        'option_id' => $option->getOptionId(),
                        'value' => $option->getValue()
                    );
                    $node['attribute_values'][] = $options;
                }

            }

            $result[] = $node;
        }

        return $result;
    }

    public function attributeSet()
    {
        $attributeSetCollection = Mage::getResourceModel('eav/entity_attribute_set_collection');
        $attributeGroupCollection = null;
        $attributeCollection = null;

        $result = array();

        foreach( $attributeSetCollection->load() as $attributeSet )
        {
            $result[$attributeSet->getAttributeSetName()] = array(
                                'arribute_set_name' => $attributeSet->getAttributeSetName(),
                                'attribute_set_id' => $attributeSet->getAttributeSetId(),
            );

            $attributeGroupCollection = Mage::getResourceModel('eav/entity_attribute_group_collection')
            ->setAttributeSetFilter($attributeSet->getAttributeSetId())
            ->load();

            foreach( $attributeGroupCollection as $attributeGroup )
            {

                $result[$attributeSet->getAttributeSetName()][$attributeGroup->getAttributeGroupName()] = array(
                                    'arribute_group_name' => $attributeGroup->getAttributeGroupName(),
                                    'attribute_group_id' => $attributeGroup->getAttributeGroupId(),
                );

                $attributeCollection = Mage::getResourceModel('catalog/product_attribute_collection')
                ->setEntityTypeFilter( Mage::getModel('eav/entity')->setType('catalog_product')->getTypeId() )
                ->addVisibleFilter();
                foreach( $attributeCollection as $attribute )
                {

                    $tmp = &$result[$attributeSet->getAttributeSetName()][$attributeGroup->getAttributeGroupName()]['attributes'][$attribute->getAttributeCode()];
                    $tmp = $attribute->toArray();
                    $tmp['options'] = $this->_getAttributeOptionsByAttribute( $attribute );
                }

            }
        }

        return $result;
    }


    /**
     *
     * @param Catalog_Product_Attribute_Collection $attribute
     * @return array $options
     */
    protected function _getAttributeOptionsByAttribute( $attribute )
    {
        $options = array();

        if ($attribute->frontend_input == 'select' || $attribute->frontend_input == 'multiselect'){

            $stores = Mage::getResourceModel('core/store_collection')->load();

            foreach( $stores as $store )
            {
                $optionCollection = Mage::getResourceModel('eav/entity_attribute_option_collection')
                ->setAttributeFilter($attribute->attribute_id)
                ->setStoreFilter($store->getStoreId(), false)
                ->load();

                foreach ($optionCollection as $option){

                    $options[$store->getName()][] = array(
                                        'option_id' => $option->getOptionId(),
                                        'value' => $option->getValue()
                    );
                }
            }

        }
        return $options;
    }


    /**
     * instert/update an attribute
     *
     * @param array $data attribute data
     */
    public function saveItems($data){

        $i=0;
        $count = count($data);

        foreach($data as $attribute){

            set_time_limit(0);
            $i++;

            if(!empty($attribute['attribute_id'])){

                $identifier = $attribute['attribute_id'];
                $identifierType = 'id';

            }elseif(!empty($attribute['attribute_code'])){

                $identifier = $attribute['attribute_code'];
                $identifierType = 'code';
            }else{
                $this->_fault('no_attribute_identifier');
            }

            $this->_datahashIdentifier = empty($attribute['mip_datahash_id']) ? null: $attribute['mip_datahash_id'];

            $attribute['is_user_defined'] = 1;
            $this->countOutput = $i.'/'.$count.' - '.round(100/$count * $i, 0).'% ';

            try{

                $relation = $this->getRelationByExtId($identifier);

                if (empty($attribute['entity_type_id'])){
                    $attribute['entity_type_id'] = $this->getEntityTypeId();
                }

                if (isset($attribute['option'])){
                    $attribute['option'] = $this->_parseAttributeOptions($attribute, $relation->getMageId());
                }

                $attribute = $this->_prepareDataForSave($attribute);

                if($relation->getId()){

                    if (isset($attribute['attribute_id'])){
                        unset($attribute['attribute_id']);
                    }

                    $this->update($relation->getMageId(), $attribute);
                }
                else{
                    try {
                        $this->create($attribute);
                    }
                    catch (Mage_Core_Exception $e){

                        /*@var $model Mage_Catalog_Model_Entity_Attribute */
                        $model = Mage::getModel('catalog/entity_attribute');
                        //if the attribute with the identifier exists create a relation and make an update
                        if ($identifierType == 'id'){
                            $model->load($identifier);
                        }
                        elseif ($identifierType == 'code'){
                            $model->loadByCode($attribute['entity_type_id'], $identifier);
                        }

                        if ($model->getId()){

                            $this->createRelation($identifier, $model->getId());
                            $attribute['option'] = $this->_parseAttributeOptions($attribute, $model->getId());

                            $this->update($model->getId(), $attribute);
                            continue;
                        }
                        throw $e;
                    }
                }

                // report Errors
            }catch (Exception $e){
                Mage::helper('mip/log')->getWriter($this)->error($this->countOutput.'  Attribute ('.$identifier.') Import Error: '.$e->getMessage(), $e );
            }
        }
    }



    /**
     * prepares the attribute codes for save
     *
     * @param array $attributeData
     * @return array modified data
     */
    protected function _prepareDataForSave ($attributeData){
        if (isset($attributeData['attribute_code'])){
            $attributeData['attribute_code'] = str_replace(
                array('ä','ö','ü', 'ß'),
                array('ae','oe','ue', 'ss'),
                strtolower($attributeData['attribute_code'])
            );
        }
        return $attributeData;
    }


    /**
     * update
     *
     * updates an attribute by the given id
     *
     * @param integer $id
     * @param array $attributeData
     */
    public function update($id, $attributeData){
        $model = Mage::getModel('catalog/resource_eav_attribute');

        $model->load($id);
        $model->addData($attributeData);
        $model->save();

        Mage::helper('mip/log')->getWriter($this)->info($this->countOutput.' UPDATE Attribute ('.$model->getAttributeCode().')');
    }


    /**
     * creates a new attribute
     *
     * @param array $attributeData
     */
    public function create($attributeData){

        if(!empty($attributeData['attribute_id'])){
            $identifier = $attributeData['attribute_id'];

            unset($attributeData['attribute_id']);
        }elseif(!empty($attributeData['attribute_code'])){
            $identifier = $attributeData['attribute_code'];

        }else{
            $this->_fault('no_attribute_identifier');
        }

        if (!isset($attributeData['source_model'])){
            $attributeData['source_model'] = '';
        }

        if (!isset($attributeData['backend_type'])){
            $attributeData['backend_type'] = 'varchar';
        }

        /* @var $model Mage_Catalog_Model_Entity_Attribute */
        $model = Mage::getModel('catalog/resource_eav_attribute');
        $model->addData($attributeData);

        $model->save();

        $this->createRelation($identifier, $model->getId());

        Mage::helper('mip/log')->getWriter($this)->info($this->countOutput.' CREATE Attribute ('.$model->getAttributeCode().')');
    }



    /**
     * returns if an attribute is default
     *
     * @param string $code
     * @param  Flagbit_Mip_Model_Data_Relation $relation
     * @return boolean
     */
    protected function _isDefaultAttribute($code, $relation){

        $hasAttribute = Mage::getModel('catalog/entity_attribute')
        ->loadByCode('catalog_product', $code)
        ->hasData();

        $hasRelation = $relation->hasMageId();

        return $hasAttribute & !$hasRelation;
    }


    /**
     * parse attribute options
     *
     * converts the attribute options into the magento format
     *
     * @param array $attribute
     * @param integer $attributeId
     * @return array
     */
    protected function _parseAttributeOptions($attribute, $attributeId = null){

        if (!is_array($attribute['option'])){
            return array();
        }
        $options = $attribute['option'];

        $returnArray = array(
            'value' => array(),
            'order' => array(),
            'delete' => array(),
        );
        $count = 0; //count($options);

        foreach ($options as $option){

            if(!empty($option['option_id'])){
                $identifier = $option['option_id'];
            }else{
                Mage::helper('mip/log')->getWriter($this)->warn('Attribute ('.$attribute['attribute_code'].') no attribute option identifier '.print_r($option['name'], true));
                continue;
            }

            $key = null;
            $value = $this->_parseOptionValues($option['name'], $key, $attributeId);
            if(is_null($key)){
                $key = 'option_' . $count++;
            }
            if (isset($attributeId) && isset($value[0])){
                $this->_attributeOptions[$attributeId][0][$value[0]] = $key;
            }

            $returnArray['value'][$key] = $value;
            $returnArray['order'][$key] = $option['order'];
        }

        if (!is_null($attributeId)){
        //@todo option delete
        }
        return $returnArray;
    }


    /**
     * parse option values
     * converts the option values into magento format
     *
     * @param array $optionValue
     * @param integer $key
     * @param integer $attributeId
     * @return array
     */
    protected function _parseOptionValues($optionValues, &$key, $attributeId = null){
        $returnArray = array();

        $existingOptions = $this->getAttributeOptions($attributeId);

        $defaultValueSet = false;
        foreach ($optionValues as $name){

            if ($name['store_id'] == 0){
                if (isset($existingOptions[$name['value']])) {
                    $key = $existingOptions[$name['value']];
                }

                $defaultValueSet = true;
            }
            $returnArray[$name['store_id']] = $name['value'];
        }

        //set the first value as admin value if no admin value exists
        if (!$defaultValueSet){
            $returnArray[0] = $optionValues[0]['value'];
        }

        return $returnArray;
    }



    /**
     * get Attribute Options
     *
     * @param integer $id
     * @param integer $store
     * @return array
     */
    protected function getAttributeOptions($id, $store=0){

        if (is_null($id)){
            return array();
        }

        if(!isset($this->_attributeOptions[$id][$store])){

            if($store !== null){

                $optionCollection = Mage::getResourceModel('eav/entity_attribute_option_collection')
                ->setAttributeFilter($id)
                ->setStoreFilter($store, false)
                ->load();
            }else{

                $optionCollection = Mage::getResourceModel('eav/entity_attribute_option_collection')
                ->setAttributeFilter($id)
                ->load();
            }

            $text2values = array();
            foreach($optionCollection as $option){
                $text2values[$option->getValue()] = $option->getOptionId();
            }

            $this->_attributeOptions[$id][$store] = $text2values;
        }
        return $this->_attributeOptions[$id][$store];
    }


    /**
     * get entity type id
     *
     * returns the entity id of the catalog product tyoe
     *
     * @return integer
     */
    protected function getEntityTypeId(){
        if (is_null($this->_entityTypeId)){
            $this->_entityTypeId = Mage::getModel('eav/entity_type')->loadByCode('catalog_product')->getId();
        }
        return $this->_entityTypeId;
    }
}