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
class Flagbit_Mip_Model_Resource_Balance extends Flagbit_Mip_Model_Resource_Abstract {

    const RELATION_TYPE = 'balance';

    protected $_mapAttributes = array(
        'balance_id' => 'entity_id'
    );

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
     * save or update CustomerBalance Items
     *
     * @param array $data
     */
    public function saveItems($data){

        $identifier = null;
        $i=0;
        $count = count($data);

        $websites = array();
        foreach(Mage::app()->getWebsites(false) as $website) {
            $websites[] = $website->getId();
        }

        foreach($data as $balance){

            try{
                set_time_limit(0);
                $i++;

                // define Identifier
                if(!empty($balance['balance_id'])){
                    $identifier = $balance['balance_id'];
                }
                else if(!empty($balance['customer_id']) && !empty($balance['website_id']) &&
                    $balanceId = Mage::getModel('enterprise_customerbalance/balance')
                        ->getCollection()
                        ->addFieldToFilter('customer_id' , array('eq' => $balance['customer_id']))
                        ->addFieldToFilter('website_id' , array('eq' => $balance['website_id']))
                        ->getLastItem()
                        ->getId())
                {
                    $identifier = $balanceId;
                }
                else if(!empty($balance['customer_id']) && !empty($balance['website_id']))
                {
                    $identifier = null;
                }
                else{
                    $this->_fault('no_identifier');
                }

                // set datahash Identifier
                $this->_datahashIdentifier = empty($balance['mip_datahash_id']) ? null: $balance['mip_datahash_id'];

                // check if balance amount is defined
                if(empty($balance['amount_delta']) && empty($balance['amount'])){
                    $this->_fault('no_amount', 'There is no CustomerBalance AmountDelta nor Amount specified');
                }
                else if(!Mage::getModel('customer/customer')->load($balance['customer_id'])->getId()) {
                    $this->_fault('customer_not_exist', 'The specified customer does not exist');
                }
                else if(empty($balance['website_id']) || !in_array($balance['website_id'], $websites)) {
                    $this->_fault('website_not_exist', 'The specified website does not exist');
                }

                // get Relation
                $relation = $this->getRelationbyResourceId($identifier);

                // Debug Log Output
                $this->countOutput = $i.'/'.$count.' - '.round(100/$count * $i, 0).'% ';

                // update Customer
                if($relation->getId()){
                    try{

                        $this->update($relation->getMageId(), $balance);

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
                        $this->create($balance);

                    }catch (Flagbit_Mip_Model_Exception $e){

                        if(!empty($balance['email']) && stristr($e->getCustomMessage(), 'mail')){
                            $balanceObj = Mage::getModel('customer/customer')->setData($balance)->loadByEmail($balance['email']);
                            if($balanceObj->getId()){
                                $this->update($balanceObj->getId(), $balance);
                                if($balanceObj->getId() && !empty($balance['customer_id'])){
                                    $relation = $this->createRelation($balance['customer_id'], $balanceObj->getId());
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
     * Create new customer
     *
     * @param array $balanceData
     * @return int
     */
    public function create($balanceData)
    {

        $balance = Mage::getModel('enterprise_customerbalance/balance')
            ->setId(null);

        $balance = $this->_prepareBalanceModel($balance, $balanceData);

        try {
            $balance->save();
        } catch (Mage_Core_Exception $e) {
            $this->_fault('data_invalid', $e->getMessage());
        }

        Mage::helper('mip/log')->getWriter($this)->info($this->countOutput.' CREATE CustomerBalance ('.$balance->getId().')');

        if($balance->getId() && !empty($balanceData['customer_id'])){

            $relation = $this->createRelation($balanceData['customer_id'], $balance->getId());

        }

        return $balance->getId();
    }

    /**
     * Retrieve balance data
     *
     * @param int $balanceId
     * @param array $attributes
     * @return array
     */
    public function info($id, $attributes = null)
    {
        $balance = Mage::getModel('enterprise_customerbalance/balance')->load($id);

        $result = array();

        if (!$balance->getId()) {
            /*
             * Disabling the errorhandling
             * cause the nature of this feature does not afford error codes into the mip task model
             */
            // $this->_fault('not_exists', 'CustomerBalance with ID '.$id.' does not exist');

            $result['error_message'] = 'CustomerBalance with ID '.$id.' does not exist';
        }

        if (!is_null($attributes) && !is_array($attributes)) {
            $attributes = array($attributes);
        }

        foreach ($this->_mapAttributes as $attributeAlias=>$attributeCode) {
            if($balance->hasData($attributeCode)) {
                $result[$attributeAlias] = $balance->getData($attributeCode);
            }
        }

        foreach (array_keys((array)$balance->getData()) as $attributeCode) {
            $result[$attributeCode] = $balance->getData($attributeCode);
        }

        return $result;
    }

    /**
     * Retrieve customerbalance data
     *
     * @param  array $filters
     * @return array
     */
    public function items($filters = null)
    {
        $collection = Mage::getModel('enterprise_customerbalance/balance')->getCollection()
            ->addFieldToSelect('*');

        $historyCollection = Mage::getModel('enterprise_customerbalance/balance_history')->getCollection()
            ->addFieldToSelect('*');


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
        foreach ($collection as $index => $balance) {

            $data = $balance->toArray();
            $row  = array();

            foreach ($this->_mapAttributes as $attributeAlias => $attributeCode) {
                $row[$attributeAlias] = (isset($data[$attributeCode]) ? $data[$attributeCode] : null);
            }

            foreach ($data as $attributeCode => $attribute) {
                if (isset($data[$attributeCode])) {
                    $row[$attributeCode] = $data[$attributeCode];
                }
            }

            $result[] = $row;
        }

        return $result;
    }

    /**
     * Update customerbalance data
     *
     * @param int $balanceId
     * @param array $balanceData
     * @return boolean
     */
    public function update($balanceId, $balanceData)
    {
        $balance = Mage::getModel('enterprise_customerbalance/balance')
            ->load($balanceId);

        if (!$balance->getId()) {
            $this->_fault('not_exists', 'CustomerBalance with ID '.$balanceId.' does not exist');
        }

        $balance = $this->_prepareBalanceModel($balance, $balanceData);

        Mage::helper('mip/log')->getWriter($this)->info($this->countOutput.' UPDATE CustomerBalance ('.$balanceId.')');

        $balance->save();
        return true;
    }

    /**
     * Fit the balance model on create or update
     *
     * @param Enterprise_CustomerBalance_Model_Balance $balance
     * @param array $balanceData
     * @return Enterprise_CustomerBalance_Model_Balance
     */
    protected function _prepareBalanceModel(Enterprise_CustomerBalance_Model_Balance $balance, $balanceData)
    {
        if($balanceData['amount_delta']) {
            $balance->setAmountDelta($balanceData['amount_delta']);
            unset($balanceData['amount_delta']);
            unset($balanceData['amount']);
        }
        elseif($balanceData['amount']) {
            $balance->setAmountDelta($this->_evaluateAmountDelta($balanceData['amount'], $balance));
            unset($balanceData['amount']);
        }

        if(isset($balanceData['comment'])) {
            $balance->setComment($balanceData['comment']);
            unset($balanceData['comment']);
        }

        if(isset($balanceData['updated_action_additional_info'])) {
            $balance->setUpdatedActionAdditionalInfo($balanceData['updated_action_additional_info']);
            unset($balanceData['updated_action_additional_info']);
        }

        if(isset($balanceData['customer_id'])) {
            $balance->setCustomerId($balanceData['customer_id']);
            unset($balanceData['customer_id']);
        }

        if(isset($balanceData['website_id'])) {
            $balance->setWebsiteId($balanceData['website_id']);
            unset($balanceData['website_id']);
        }

        if(isset($balanceData['history_action'])) {
            $order = Mage::getModel('sales/order');
            if(isset($balanceData['order_increment_id']))
            {
                $order->loadByIncrementId($balanceData['order_increment_id']);
                unset($balanceData['order_increment_id']);
            }

            $this->_setHistoryAction($balanceData['history_action'], $balance, $order);
            unset($balanceData['history_action']);
        }

        foreach ($balance->getData() as $attributeCode => $value) {
            if (isset($balanceData[$attributeCode]) && !empty($balanceData[$attributeCode])) {
                $balance->setData($attributeCode, $balanceData[$attributeCode]);
            }
        }

        if(isset($balanceData['notify_by_email']) && is_bool((boolean) $balanceData['notify_by_email'])) {
            $balance->setNotifyByEmail((boolean) $balanceData['notify_by_email'], $balance->getWebsiteId());
            unset($balanceData['notify_by_email']);
        }

        return $balance;
    }

    /**
     * Delete customerbalance
     *
     * @param int $balanceId
     * @return boolean
     */
    public function delete($balanceId)
    {
        $balance = Mage::getModel('enterprise_customerbalance/balance')->load($balanceId);

        if (!$balance->getId()) {
            $this->_fault('not_exists');
        }

        try {
            $balance->delete();
        } catch (Mage_Core_Exception $e) {
            $this->_fault('not_deleted', $e->getMessage());
        }

        return true;
    }

    /**
     * Evaluate the differene between current balance amount
     * and the given target balance amount
     *
     * @param float $amount
     * @param Enterprise_CustomerBalance_Model_Balance $balance
     * @return float
     */
    protected function _evaluateAmountDelta($amount, Enterprise_CustomerBalance_Model_Balance $balance = null)
    {
        if(!$balance)
        {
            return (float) $amount;
        }

        return ((float) $balance->getAmount() * -1) + $amount;
    }

    /**
     * Evaluate the differene between current balance amount
     * and the given target balance amount
     *
     * @param string $action
     * @param Enterprise_CustomerBalance_Model_Balance $balance
     * @return bool
     */
    protected function _setHistoryAction($action, Enterprise_CustomerBalance_Model_Balance $balance, Mage_Sales_Model_Order $order)
    {
        switch($action)
        {
            case 'ACTION_UPDATED':
                $balance->setHistoryAction( Enterprise_CustomerBalance_Model_Balance_History::ACTION_UPDATED );
                break;
            case 'ACTION_CREATED':
                $balance->setHistoryAction( Enterprise_CustomerBalance_Model_Balance_History::ACTION_CREATED );
                break;
            case 'ACTION_USED':
                $balance->setHistoryAction( Enterprise_CustomerBalance_Model_Balance_History::ACTION_USED );
                break;
            case 'ACTION_REFUNDED':
                $balance->setHistoryAction( Enterprise_CustomerBalance_Model_Balance_History::ACTION_REFUNDED );
                if($order->getId())
                {
                    $balance->setOrder($order);

                    /*
                     * TODO
                     * $creditmemo has to get evaluated properly
                     * not only grabbing for the last item in the collection
                     */
                    $creditmemo = Mage::getModel('sales/order_creditmemo')
                        ->getCollection()
                        ->addFieldToFilter('order_id', array('eq' => $order->getId()))
                        ->getLastItem();

                    if($creditmemo && $creditmemo->getId())
                    {
                        $balance->setCreditMemo($creditmemo);
                        break;
                    }

                    $this->_fault('No proper creditmemo available to refund order #' . $order->getId());
                }
            case 'ACTION_REVERTED':
                $balance->setHistoryAction( Enterprise_CustomerBalance_Model_Balance_History::ACTION_REVERTED );
                if($order->getId())
                {
                    $balance->setOrder($order);
                    break;
                }
            default:
                return false;
        }

        return true;
    }
}

