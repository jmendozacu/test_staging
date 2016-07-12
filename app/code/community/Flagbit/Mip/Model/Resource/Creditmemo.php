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

class Flagbit_Mip_Model_Resource_Creditmemo extends Flagbit_Mip_Model_Resource_Abstract {

    const RELATION_TYPE = 'creditmemo';

    /**
     * Attributes map array per entity type
     *
     * @var google
     */
    protected $_attributesMap = array(
        'global'    => array()
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
     * Initialize attributes' mapping
     */
    public function __construct()
    {
        $this->_attributesMap['creditmemo'] = array(
            'creditmemo_id' => 'entity_id'
        );
        $this->_attributesMap['creditmemo_item'] = array(
            'item_id'    => 'entity_id'
        );
        $this->_attributesMap['creditmemo_comment'] = array(
            'comment_id' => 'entity_id'
        );
    }

    /**
     * Create new credit memo for order
     *
     * @param array $data
     */
    public function create($data)
    {
        if(empty($data['order_increment_id'])){
            Mage::throwException('no order increment id given');
        }
        /** @var $order Mage_Sales_Model_Order */
        $order = Mage::getModel('sales/order')->load($data['order_increment_id'], 'increment_id');
        if (!$order->getId()) {
            Mage::throwException('order '.$data['order_increment_id'].' not exists');
        }
        if (!$order->canCreditmemo()) {
            Mage::throwException('cannot create creditmemo');
        }

        $skuItemMapping = array();
        foreach ($order->getAllItems() as $item) {
            if(isset($skuItemMapping[$item->getSku()])){
                continue;
            }
            $skuItemMapping[$item->getSku()] = $item->getId();
        }
        $backToStock = array();
        $creditmemoData = $this->_prepareCreateData($data, $skuItemMapping, $backToStock);

        /** @var $service Mage_Sales_Model_Service_Order */
        $service = Mage::getModel('sales/service_order', $order);

        /** @var $creditmemo Mage_Sales_Model_Order_Creditmemo */
        $creditmemo = $service->prepareCreditmemo($creditmemoData);

        /**
         * Process back to stock flags
         */
        foreach ($creditmemo->getAllItems() as $creditmemoItem) {
            $orderItem = $creditmemoItem->getOrderItem();
            $parentId = $orderItem->getParentItemId();
            if (isset($backToStock[$orderItem->getId()])) {
                $creditmemoItem->setBackToStock(true);
            } elseif ($orderItem->getParentItem() && isset($backToStock[$parentId]) && $backToStock[$parentId]) {
                $creditmemoItem->setBackToStock(true);
            } elseif (empty($savedData)) {
                $creditmemoItem->setBackToStock(Mage::helper('cataloginventory')->isAutoReturnEnabled());
            } else {
                $creditmemoItem->setBackToStock(false);
            }
        }

        $creditmemo->setPaymentRefundDisallowed(true)->register();
        // add comment to creditmemo
        if (!empty($data['comment'])) {
            $creditmemo->addComment($data['comment'], !empty($data['notify_customer']));
        }

        Mage::getModel('core/resource_transaction')
            ->addObject($creditmemo)
            ->addObject($order)
            ->save();
        // send email notification
        $creditmemo->sendEmail(!empty($data['notify_customer']), (!empty($data['comment']) ? $data['comment'] : ''));

        return $creditmemo->getIncrementId();
    }


    /**
     * Adds comment to credit memo with additional possibility to send it to customer via email
     * and show it in customer account
     *
     * @param bool $notify
     * @param bool $visibleOnFront
     *
     * @return Mage_Sales_Model_Order_Creditmemo
     */
    public function addComment($comment, $notify=false, $visibleOnFront=false)
    {
        if (!($comment instanceof Mage_Sales_Model_Order_Creditmemo_Comment)) {
            $comment = Mage::getModel('sales/order_creditmemo_comment')
                ->setComment($comment)
                ->setIsCustomerNotified($notify)
                ->setIsVisibleOnFront($visibleOnFront);
        }
        $comment->setCreditmemo($this)
            ->setParentId($this->getId())
            ->setStoreId($this->getStoreId());
        if (!$comment->getId()) {
            $this->getCommentsCollection()->addItem($comment);
        }
        $this->_hasDataChanges = true;
        return $this;
    }

    /**
     * Hook method, could be replaced in derived classes
     *
     * @param array $data
     * @param array $skuItemMapping
     * @param array $backToStock
     * @return array
     */
    protected function _prepareCreateData($data, $skuItemMapping=array(), &$backToStock=array())
    {
        $data = isset($data) ? $data : array();

        if (isset($data['items']) && count($data['items'])) {
            $qtysArray = array();
            foreach ($data['items'] as $qKey => $qVal) {

                $id = null;
                if(isset($qVal['order_item_id'])){
                    $id = $qVal['order_item_id'];
                }elseif(isset($qVal['sku_product']) && isset($skuItemMapping[$qVal['sku_product']])){
                    $id = $skuItemMapping[$qVal['sku_product']];
                }

                if (isset($qVal['qty']) && $id !== null) {
                    $qtysArray[$id] = $qVal['qty'];
                }
                if (!empty($qVal['back_to_stock']) && $id !== null) {
                    $backToStock[$id] = true;
                }
            }
            $data['qtys'] = $qtysArray;
        }
        return $data;
    }

    /**
     * Retrieve credit memos by filters
     *
     * @param array|null $filter
     * @return array
     */
    public function items($filters = null)
    {
        $filter = $this->_prepareListFilter($filters);
        $result = array();
        /** @var $creditmemoModel Mage_Sales_Model_Order_Creditmemo */
        $creditmemoModel = Mage::getModel('sales/order_creditmemo');
        // map field name entity_id to creditmemo_id
        foreach ($creditmemoModel->getFilteredCollectionItems($filter) as $creditmemo) {
            $result[] = $this->_getAttributes($creditmemo, 'creditmemo');
        }

        return $result;
    }

    /**
     * Make filter of appropriate format for list method
     *
     * @param array|null $filter
     * @return array|null
     */
    protected function _prepareListFilter($filter = null)
    {
        // prepare filter, map field creditmemo_id to entity_id
        if (is_array($filter)) {
            foreach ($filter as $field => $value) {
                if (isset($this->_attributesMap['creditmemo'][$field])) {
                    $filter[$this->_attributesMap['creditmemo'][$field]] = $value;
                    unset($filter[$field]);
                }
            }
        }
        return $filter;
    }


    /**
     * Load CreditMemo by IncrementId
     *
     * @param mixed $incrementId
     * @return Mage_Core_Model_Abstract|Mage_Sales_Model_Order_Creditmemo
     */
    protected function _getCreditmemo($incrementId)
    {
        /** @var $creditmemo Mage_Sales_Model_Order_Creditmemo */
        $creditmemo = Mage::getModel('sales/order_creditmemo')->load($incrementId, 'increment_id');
        if (!$creditmemo->getId()) {
            Mage::throwException('creditmemo '.$incrementId.' not exists');
        }
        return $creditmemo;
    }

    /**
     * Retrieve credit memo information
     *
     * @param string $creditmemoIncrementId
     * @return array
     */
    public function info($id)
    {
        $creditmemo = $this->_getCreditmemo($id);
        // get credit memo attributes with entity_id' => 'creditmemo_id' mapping
        $result = $this->_getAttributes($creditmemo, 'creditmemo');
        $result['order_increment_id'] = $creditmemo->getOrder()->load($creditmemo->getOrderId())->getIncrementId();
        // items refunded
        $result['items'] = array();
        foreach ($creditmemo->getAllItems() as $item) {
            $result['items'][] = $this->_getAttributes($item, 'creditmemo_item');
        }
        // credit memo comments
        $result['comments'] = array();
        foreach ($creditmemo->getCommentsCollection() as $comment) {
            $result['comments'][] = $this->_getAttributes($comment, 'creditmemo_comment');
        }

        return $result;
    }

    /**
     * Retrieve entity attributes values
     *
     * @param Mage_Core_Model_Abstract $object
     * @param array $attributes
     * @return Mage_Sales_Model_Api_Resource
     */
    protected function _getAttributes($object, $type, array $attributes = null)
    {
        $result = array();

        if (!is_object($object)) {
            return $result;
        }

        foreach ($object->getData() as $attribute=>$value) {
            if ($this->_isAllowedAttribute($attribute, $type, $attributes)) {
                $result[$attribute] = $value;
            }
        }

        foreach ($this->_attributesMap['global'] as $alias=>$attributeCode) {
            $result[$alias] = $object->getData($attributeCode);
        }

        if (isset($this->_attributesMap[$type])) {
            foreach ($this->_attributesMap[$type] as $alias=>$attributeCode) {
                $result[$alias] = $object->getData($attributeCode);
            }
        }

        return $result;
    }

    /**
     * Check is attribute allowed to usage
     *
     * @param Mage_Eav_Model_Entity_Attribute_Abstract $attribute
     * @param string $entityType
     * @param array $attributes
     * @return boolean
     */
    protected function _isAllowedAttribute($attributeCode, $type, array $attributes = null)
    {
        if (!empty($attributes)
            && !(in_array($attributeCode, $attributes))) {
            return false;
        }

        if (in_array($attributeCode, $this->_ignoredAttributeCodes['global'])) {
            return false;
        }

        if (isset($this->_ignoredAttributeCodes[$type])
            && in_array($attributeCode, $this->_ignoredAttributeCodes[$type])) {
            return false;
        }

        return true;
    }

}