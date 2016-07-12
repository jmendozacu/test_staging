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
class Flagbit_Mip_Model_Resource_Customer extends Flagbit_Mip_Model_Resource_Abstract {


    protected $_mapAttributes = array(
        'customer_id' => 'entity_id'
    );

    const RELATION_TYPE = 'customer';
    const ADDRESS_RELATION_TYPE = 'customer_address';

    /**
     * Output Counter for Log
     *
     * @var string
     */
    public $countOutput = '';

    /**
     * get Resource Relation Type
     *
     * @return string
     */
    protected function getRelationType(){
        return self::RELATION_TYPE;
    }

    /**
     * save or update Customer Items
     *
     * @param array $data
     */
    public function saveItems($data){

        $identifier = null;
        $i=0;
        $count = count($data);
        foreach($data as $customer){

            try{

                set_time_limit(0);
                $i++;

                // define Identifier
                if(!empty($customer['customer_id'])){
                    $identifier = $customer['customer_id'];
                }elseif(!empty($customer['email'])){
                    $identifier = $customer['email'];
                }else{
                    $this->_fault('no_identifier');
                }

                // set datahash Identifier
                $this->_datahashIdentifier = empty($customer['mip_datahash_id']) ? null: $customer['mip_datahash_id'];

                // check if email address is defined
                if(empty($customer['email'])){
                    $this->_fault('no_email', 'There is no E-Mail Address specified');
                }

                // get Relation
                $relation = $this->getRelationByExtId($identifier);

                // Debug Log Output
                $this->countOutput = $i.'/'.$count.' - '.round(100/$count * $i, 0).'% ';

                // update Customer
                if($relation->getId()){
                    try{

                        $this->update($relation->getMageId(), $customer);

                    }catch(Flagbit_Mip_Model_Exception $e){
                        if($e->getCode() == 'not_exists'){
                            $relation->delete();
                        }else{
                            throw $e;
                        }
                    }

                // create Customer
                }else{
                    try{
                        $this->create($customer);

                    }catch (Flagbit_Mip_Model_Exception $e){

                        if(!empty($customer['email']) && stristr($e->getCustomMessage(), 'mail')){
                            $customerObj = Mage::getModel('customer/customer')->setData($customer)->loadByEmail($customer['email']);
                            if($customerObj->getId()){
                                $this->update($customerObj->getId(), $customer);
                                if($customerObj->getId() && !empty($customer['customer_id'])){
                                    $relation = $this->createRelation($customer['customer_id'], $customerObj->getId());
                                }
                                continue;
                            }
                        }
                        throw $e;
                    }
                }

            }catch (Flagbit_Mip_Model_Exception $e){
                Mage::helper('mip/log')->getWriter($this)->error($this->countOutput.' Customer ('.$identifier.') Import Error: '.$e->getMessage(), $e);
            }catch (Exception $e){
                Mage::helper('mip/log')->getWriter($this)->error($this->countOutput.' Customer ('.$identifier.') Import Error: '.$e->getMessage(), $e);
            }
        }
    }

    /**
     * save Customer Addresses
     *
     * @param int $customerId
     * @param array $addresses
     */
    public function saveAddresses($customerId, $addresses){

        if(!is_array($addresses) or !$customerId){
            return;
        }

        $comparsionArray = array('firstname', 'lastname', 'company', 'street', 'city', 'country_id', 'postcode');

        foreach ($addresses as $address){

            if(empty($address['address_id'])
                or empty($address['firstname'])
                or empty($address['lastname'])
                ){
                continue;
            }
            // get Relation
            $relation = $this->getRelationByExtId($address['address_id'], self::ADDRESS_RELATION_TYPE, self::RELATION_TYPE, $customerId);

            // Update Address
               if($relation->getId()){
                   if(!Mage::getModel('customer/address')->load($relation->getResourceId())->getId()){
                       $this->createAddress($customerId, $address);
                       $relation->delete();
                   }else{
                       $this->updateAddress($relation->getResourceId(), $address);
                   }

               // create Address
               }else{
                   // compare new Adress with current one to prevent clones
                   $currentAdresses = Mage::getResourceModel('customer/address_collection')->addAttributeToSelect($comparsionArray)->addAttributeToFilter('parent_id', $customerId)->load();

                   if(count($currentAdresses)){
                       $addressObject = new Varien_Object($address);
                       foreach($currentAdresses as $currentAddress){
                           // compare addresses
                        $result = array_diff_assoc($addressObject->__toArray($comparsionArray),  $currentAddress->__toArray($comparsionArray));

                        if(!count($result)){
                            $this->updateAddress($currentAddress->getId(), $address);
                            continue 2;
                        }
                    }
                   }
                   $this->createAddress($customerId, $address);
               }
        }
    }




    /**
     * Create new customer
     *
     * @param array $customerData
     * @return int
     */
    public function create($customerData)
    {
        try {

            $customer = Mage::getModel('customer/customer')
                ->setData($customerData)
                ->setId(null);

            if(isset($customerData['subscription'])) {
                $customer->setIsSubscribed(true);
            } else {
                $customer->setIsSubscribed(false);
            }

            $customer->setForceConfirmed(true);

            $customer->save();

            if($customer->getId() && !empty($customerData['address'])){
                $this->saveAddresses($customer->getId(), $customerData['address']);
            }

        } catch (Mage_Core_Exception $e) {
            $this->_fault('data_invalid', $e->getMessage());
        }

        Mage::helper('mip/log')->getWriter($this)->info($this->countOutput.' CREATE Customer ('.$customer->getId().')');

        if($customer->getId() && !empty($customerData['customer_id'])){

            $relation = $this->createRelation($customerData['customer_id'], $customer->getId());

        }

        return $customer->getId();
    }

    /**
     * Retrieve customer data
     *
     * @param int $customerId
     * @param array $attributes
     * @return array
     */
    public function info($id, $attributes = null)
    {
        $customer = Mage::getModel('customer/customer')->load($id);

        if (!$customer->getId()) {
            $this->_fault('not_exists', 'Customer with ID '.$id.' not exists');
        }

        if (!is_null($attributes) && !is_array($attributes)) {
            $attributes = array($attributes);
        }

        $result = array();

        foreach ($this->_mapAttributes as $attributeAlias=>$attributeCode) {
            $result[$attributeAlias] = $customer->getData($attributeCode);
        }

        foreach ($this->getAllowedAttributes($customer, $attributes) as $attributeCode=>$attribute) {
            $result[$attributeCode] = $customer->getData($attributeCode);
        }

        $result['address'] = $this->addressItems($id);

        return $result;
    }

    /**
     * Retrieve cutomers data
     *
     * @param  array $filters
     * @return array
     */
    public function items($filters = array())
    {
        $collection = Mage::getModel('customer/customer')->getCollection()
            ->addAttributeToSelect('*');


        if (is_array($filters)) {
            try {
                foreach ($filters as $field => $value) {
                    if (isset($this->_mapAttributes[$field])) {
                        $field = $this->_mapAttributes[$field];
                    }

                    $collection->addFieldToFilter($field, $value);
                }
            } catch (Mage_Core_Exception $e) {
                $this->_fault('filters_invalid', $e->getMessage());
            }
        }

        $result = array();
        foreach ($collection as $customer) {
            $data = $customer->toArray();
            $row  = array();

            foreach ($this->_mapAttributes as $attributeAlias => $attributeCode) {
                $row[$attributeAlias] = (isset($data[$attributeCode]) ? $data[$attributeCode] : null);
            }

            foreach ($this->getAllowedAttributes($customer) as $attributeCode => $attribute) {
                if (isset($data[$attributeCode])) {
                    $row[$attributeCode] = $data[$attributeCode];
                }
            }

            $row['address'] = $this->addressItems($customer->getData('entity_id'));

            $result[] = $row;
        }

        return $result;
    }


    /**
     * Retrieve customer addresses list
     *
     * @param int $customerId
     * @return array
     */
    public function addressItems($customerId)
    {
        $customer = Mage::getModel('customer/customer')
            ->load($customerId);
        /* @var $customer Mage_Customer_Model_Customer */

        if (!$customer->getId()) {
            $this->_fault('customer_not_exists');
        }

        $result = array();
        foreach ($customer->getAddresses() as $address) {
            $data = $address->toArray();
            $row  = array();

            foreach ($this->_mapAttributes as $attributeAlias => $attributeCode) {
                $row[$attributeAlias] = isset($data[$attributeCode]) ? $data[$attributeCode] : null;
            }

            foreach ($this->getAllowedAttributes($address) as $attributeCode => $attribute) {
                if (isset($data[$attributeCode])) {
                    $row[$attributeCode] = $data[$attributeCode];
                }
            }

            $row['is_default_billing'] = $customer->getDefaultBillingAddress() == $address->getId();
            $row['is_default_shipping'] = $customer->getDefaultShippingAddress() == $address->getId();

            $result[] = $row;

        }

        return $result;
    }

    /**
     * Create new address for customer
     *
     * @param int $customerId
     * @param array $addressData
     * @return int
     */
    public function createAddress($customerId, $addressData)
    {
        $customer = Mage::getModel('customer/customer')
            ->load($customerId);
        /* @var $customer Mage_Customer_Model_Customer */

        if (!$customer->getId()) {
            $this->_fault('customer_not_exists');
        }

        $address = Mage::getModel('customer/address');

        foreach ($this->getAllowedAttributes($address) as $attributeCode=>$attribute) {
            if (isset($addressData[$attributeCode])) {
                $address->setData($attributeCode, $addressData[$attributeCode]);
            }
        }

        if (isset($addressData['is_default_billing'])) {
            $address->setIsDefaultBilling($addressData['is_default_billing']);
        }

        if (isset($addressData['is_default_shipping'])) {
            $address->setIsDefaultShipping($addressData['is_default_shipping']);
        }

        $address->setId(null);
        $address->setCustomerId($customer->getId());

        $valid = $address->validate();

        if (is_array($valid)) {
            $this->_fault('data_invalid', implode("\n", $valid));
        }

        try {
            $address->save();
        } catch (Mage_Core_Exception $e) {
            $this->_fault('data_invalid', $e->getMessage());
        }

        if($address->getId() && !empty($addressData['address_id'])){

            $relation = $this->createRelation($addressData['address_id'], $address->getId(), self::ADDRESS_RELATION_TYPE, $customerId);
        }

        return $address->getId();
    }


    /**
     * Update address data
     *
     * @param int $addressId
     * @param array $addressData
     * @return boolean
     */
    public function updateAddress($addressId, $addressData)
    {
        $address = Mage::getModel('customer/address')
            ->load($addressId);

        if (!$address->getId()) {
            $this->_fault('address_not_exists');
        }

        foreach ($this->getAllowedAttributes($address) as $attributeCode=>$attribute) {
            if (isset($addressData[$attributeCode])) {
                $address->setData($attributeCode, $addressData[$attributeCode]);
            }
        }

        if (isset($addressData['is_default_billing'])) {
            $address->setIsDefaultBilling($addressData['is_default_billing']);
        }

        if (isset($addressData['is_default_shipping'])) {
            $address->setIsDefaultShipping($addressData['is_default_shipping']);
        }

        $valid = $address->validate();
        if (is_array($valid)) {
            $this->_fault('data_invalid', implode("\n", $valid));
        }

        try {
            $address->save();
        } catch (Mage_Core_Exception $e) {
            $this->_fault('data_invalid', $e->getMessage());
        }

        return true;
    }


    /**
     * Update customer data
     *
     * @param int $customerId
     * @param array $customerData
     * @return boolean
     */
    public function update($customerId, $customerData)
    {
        $customer = Mage::getModel('customer/customer')->load($customerId);

        if (!$customer->getId()) {
            $this->_fault('not_exists', 'Customer with ID '.$customerId.' not exists');
        }

        if(!empty($customerData['address'])){
            $this->saveAddresses($customerId, $customerData['address']);
        }

        foreach ($this->getAllowedAttributes($customer) as $attributeCode=>$attribute) {
            if (isset($customerData[$attributeCode])) {
                $customer->setData($attributeCode, $customerData[$attributeCode]);
            }
        }

        Mage::helper('mip/log')->getWriter($this)->info($this->countOutput.' UPDATE Customer ('.$customerId.')');

        if(isset($customerData['subscription'])) {
            $customer->setIsSubscribed(true);
        } else {
            $customer->setIsSubscribed(false);
        }

        $customer->save();
        return true;
    }

    /**
     * Delete customer
     *
     * @param int $customerId
     * @return boolean
     */
    public function delete($customerId)
    {
        $customer = Mage::getModel('customer/customer')->load($customerId);

        if (!$customer->getId()) {
            $this->_fault('not_exists');
        }

        try {
            $customer->delete();
        } catch (Mage_Core_Exception $e) {
            $this->_fault('not_deleted', $e->getMessage());
        }

        return true;
    }


    /**
     * Check is attribute allowed
     *
     * @param Mage_Eav_Model_Entity_Attribute_Abstract $attribute
     * @param array $attributes
     * @return boolean
     */
    protected function _isAllowedAttribute($attribute, array $filter = null)
    {
        if (!is_null($filter)
            && !( in_array($attribute->getAttributeCode(), $filter)
                  || in_array($attribute->getAttributeId(), $filter))) {
            return false;
        }

        return !in_array($attribute->getFrontendInput(), $this->_ignoredAttributeTypes)
               && !in_array($attribute->getAttributeCode(), $this->_ignoredAttributeCodes);
    }

    /**
     * Return list of allowed attributes
     *
     * @param Mage_Eav_Model_Entity_Abstract $entity
     * @param array $filter
     * @return array
     */
    public function getAllowedAttributes($entity, array $filter = null)
    {
        $attributes = $entity->getResource()
                        ->loadAllAttributes($entity)
                        ->getAttributesByCode();
        $result = array();
        foreach ($attributes as $attribute) {
            if ($this->_isAllowedAttribute($attribute, $filter)) {
                $result[$attribute->getAttributeCode()] = $attribute;
            }
        }

        return $result;
    }
}

