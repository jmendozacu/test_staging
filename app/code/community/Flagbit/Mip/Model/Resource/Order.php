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
class Flagbit_Mip_Model_Resource_Order extends Flagbit_Mip_Model_Resource_Abstract {


    const RELATION_TYPE = 'order';

    protected static $_underscoreCache = array();
    protected static $_reflectionCache = array();


    /**
     * Class Constuctor
     *
     * @return void
     */
    public function __construct()
    {
        $this->_attributesMap['order']         = array('order_id' => 'entity_id');
        $this->_attributesMap['order_address'] = array('address_id' => 'entity_id');
        $this->_attributesMap['order_payment'] = array('payment_id' => 'entity_id');

    }

    /**
     * get Resource Relation Type
     *
     * @return string
     */
    protected function getRelationType(){
        return self::RELATION_TYPE;
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

        if($type == 'order_address' and is_callable(array($object, 'getStreet1'))){
            $result['street1'] = $object->getStreet1();
            $result['street2'] = $object->getStreet2();
            $result['street3'] = $object->getStreet3();
            $result['street4'] = $object->getStreet4();
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

    /**
     * Initialize basic order model
     *
     * @param mixed $orderIncrementId
     * @return Mage_Sales_Model_Order
     */
    protected function _initOrder($orderIncrementId)
    {
        $order = Mage::getModel('sales/order');

        /* @var $order Mage_Sales_Model_Order */

        $order->loadByIncrementId($orderIncrementId);

        if (!$order->getId()) {
            $this->_fault('not_exists', 'Order do not exists: '.$orderIncrementId);
        }

        return $order;
    }

    /**
     * Add comment to order
     *
     * @param string $orderIncrementId
     * @param string $status
     * @param string $comment
     * @param boolean $notify
     * @return boolean
     */
    public function addComment($orderid, $status, $comment = null, $notify = false)
    {
        $order = $this->_initOrder($orderid);

        $order->addStatusToHistory($status, $comment, $notify);


        try {
            if ($notify && $comment) {
                $oldStore = Mage::getDesign()->getStore();
                $oldArea = Mage::getDesign()->getArea();
                Mage::getDesign()->setStore($order->getStoreId());
                Mage::getDesign()->setArea('frontend');
            }

            $order->save();
            $order->sendOrderUpdateEmail($notify, $comment);
            if ($notify && $comment) {
                Mage::getDesign()->setStore($oldStore);
                Mage::getDesign()->setArea($oldArea);
            }

        } catch (Mage_Core_Exception $e) {
            $this->_fault('status_not_changed', $e->getMessage());
        }

        return true;
    }


    /**
     * Retrieve full order information
     *
     * @param string $orderIncrementId
     * @return array
     */
    public function info($id, $list=true, $status = null)
    {
        $order = $this->_initOrder($id);

        $result = $this->_getAttributes($order, 'order');

        // FIXME: Problem of Magento EE tax + grand total calculation
        $result['shipping_amount'] = round($order->getShippingAmount(), 2);
        $result['base_shipping_amount'] = round($order->getBaseShippingAmount(), 2);

        $result['grand_total'] = round($order->getGrandTotal(), 2);
        $result['base_grand_total'] = round($order->getBaseGrandTotal(), 2);

        $result['grand_total_without_tax'] = $result['grand_total'] - $order->getTaxAmount() + $order->getRewardCurrencyAmount();
        $result['base_grand_total_without_tax'] = $result['base_grand_total'] - $order->getBaseTaxAmount() + $order->getBaseRewardCurrencyAmount();

        $result['shipping_amount_tax'] = round($order->getShippingAmount() + $order->getShippingTaxAmount(), 2);
        $result['base_shipping_amount_tax'] = round($order->getBaseShippingAmount() + $order->getBaseShippingTaxAmount(), 2);

        if($order->getIsNotVirtual()){
            $result['shipping_address'] = $this->_getAttributes($order->getShippingAddress()->afterLoad(), 'order_address');
            $result['addresses_identical'] = $order->getShippingAddress()->format('html') == $order->getBillingAddress()->format('html');
        }
        $result['billing_address']  = $this->_getAttributes($order->getBillingAddress()->afterLoad(), 'order_address');

        $result['items'] = array();

        if(isset($result['gift_cards'])){
            $result['gift_cards'] = unserialize($result['gift_cards']);
        }

        // get all Order Items
        $i=0;
        foreach ($order->getAllItems() as $item) {

            $result['items'][$i] = $this->_getAttributes($item, 'order_item');
            $result['items'][$i]['price_incl_tax'] = Mage::helper('checkout')->getPriceInclTax($item);
            $result['items'][$i]['subtotal_incl_tax'] = Mage::helper('checkout')->getSubtotalInclTax($item);

            $result['items'][$i]['qty'] = ($item->getQtyOrdered() ? $item->getQtyOrdered() : ($item->getQty() ? $item->getQty() : 1));

            if ($item->getProductType() == Mage_Catalog_Model_Product_Type::TYPE_CONFIGURABLE) {
                $result['items'][$i]['sku_product'] = $item->getProductOptionByCode('simple_sku');
            }else{
                $result['items'][$i]['sku_product'] = $item->getSku();
            }

            if(isset($result['items'][$i]['product_options'])){
                $result['items'][$i]['product_options'] = unserialize($result['items'][$i]['product_options']);
                if(isset($result['items'][$i]['product_options']['info_buyRequest']['uenc'])){
                    //$result['items'][$i]['product_options']['info_buyRequest']['uenc'] = base64_decode($result['items'][$i]['product_options']['info_buyRequest']['uenc']);
                }
            }
            $i++;
        }

        // get Status History
        foreach($order->getStatusHistoryCollection() as $status){
            $result['history'][] = $this->_getAttributes($status, 'order_status_history');
        }

        $result['status_history'] = array();
        foreach ($order->getAllStatusHistory() as $history) {
            $result['status_history'][] = $this->_getAttributes($history, 'order_status_history');
        }

        // get Payment Data
        $result['payment'] = $order->getPayment()->getData();
        $paymentDataModel = $order->getPayment()->getMethodInstance();
        $paymentDataMethods = $this->_getClassGetMethods($paymentDataModel);

        foreach($paymentDataMethods as $paymentDataMethod){

            if(in_array($paymentDataMethod,array('getUrl','getSession','getCheckout','getQuote','getOrderPlaceRedirectUrl','getStandardCheckoutFormFields','getDebugFlag','getInfoInstance'))) {
                continue;
            }
            try {
                $paymentData = $paymentDataModel->{$paymentDataMethod}();
                if(is_object($paymentData)
                    or is_array($paymentData)
                ){
                    continue;
                }
                $result['payment']['data'][$this->_underscore($paymentDataMethod)] = $paymentData;

            }catch (Exception $e){
                continue;
            }
        }

        // get complete Customer
        if(!empty($result['customer_id'])){
            $result['customer'] = Mage::getModel('customer/customer')->load($result['customer_id'])->getData();
        }

        // get Tax
        $result['tax'] = $order->getFullTaxInfo();

        if($list){
            return array($result);
        }

        return $result;
    }

    /**
     * Converts field names for setters and geters
     *
     * $this->setMyField($value) === $this->setData('my_field', $value)
     * Uses cache to eliminate unneccessary preg_replace
     *
     * @param string $name
     * @return string
     */
    protected function _underscore($name)
    {
        if (isset(self::$_underscoreCache[$name])) {
            return self::$_underscoreCache[$name];
        }

        $result = strtolower(preg_replace('/(.)([A-Z])/', "$1_$2", $name));
        self::$_underscoreCache[$name] = $result;
        return $result;
    }

    /**
     * get all getter Methods from a Class
     *
     * @param object|string $class
     * @return array
     */
    protected function _getClassGetMethods($class){

        if(is_object($class)){
            $class = get_class($class);
        }

        if(!isset(self::$_reflectionCache[$class])){

            $reflection = new ReflectionClass($class);
            self::$_reflectionCache[$class] = array();
            foreach ($reflection->getMethods(ReflectionMethod::IS_PUBLIC) as $method) {
                if(substr($method->name, 0, 3) != 'get' or $method->getNumberOfParameters() > 0){
                    continue;
                }

                self::$_reflectionCache[$class][] = $method->name;
            }
        }
        return self::$_reflectionCache[$class];
    }

    /**
     * get Orders Collection
     *
     * @param array $filters
     * @return Mage_Sales_Model_Mysql4_Order_Collection
     */
    protected function _getOrdersCollection($filters = array()){

        /** @var $collection Mage_Sales_Model_Resource_Order_Collection */
        $collection = Mage::getResourceModel('sales/order_collection');

        if (is_array($filters)) {
            try {
                foreach ($filters as $field => $value) {
                    if (isset($this->_filtersMap[$field])) {
                        $field = $this->_filtersMap[$field];
                    }
                    if(strstr($value, ',')){
                        $collection->addFieldToFilter($field, array('in' => Mage::helper('mip')->trimExplode(',', $value)));
                    }else{

                        switch ($value) {
                            case 'null':
                                $value = array("null" => null);
                                break;

                            case 'notnull':
                                $value = array("notnull" => null);
                                break;
                        }

                        $collection->addFieldToFilter($field, $value);
                    }
                }

            } catch (Mage_Core_Exception $e) {
                $this->_fault('filters_invalid', $e->getMessage());
            }
        }

        return $collection;
    }


    /**
     * mass Order Status Update
     *
     * @param string $from
     * @param string $to
     */
    public function massStatusUpdate($from, $to){

        $collection = $this->_getOrdersCollection(array('status' => $from))->load();

        foreach($collection as &$order){
            $order->setState($to);
            $order->addStatusToHistory($to);
        }

        return $collection->save();
    }

    /**
     * mass Update Order (cancel / to shippment)
     * Add Tracking information
     *
     * @param array $data
     */
    public function massUpdate($data)
    {
        foreach((array) $data as $item){
            try{
                $this->update($item);
            }catch(Exception $e){
                Mage::helper('mip/log')->getWriter($this)->error('Order ('.(isset($data['orderid']) ? $data['orderid'] : 'unknown').') Import Error: '.$e->getMessage(), $e);
            }
        }
    }


    /**
     * Update Order (cancel / to shippment)
     * Add Tracking information
     *
     * @param array $data
     */
    public function update($data = array()){

        if(isset($data['data']) && count($data) == 1){
            $data = $data['data'];
        }

        if(isset($data['orderid'])){
            $data['order_id'] = $data['orderid'];
        }

        if(!isset($data['order_id'])){
            return;
        }

        $order = $this->_initOrder($data['order_id']);

        Mage::helper('mip/log')->getWriter($this)->info('update Order: '.$data['order_id']);

        if(!isset($data['action'])){
            $data['action'] = 'default';
        }

        Mage::helper('mip/log')->getWriter($this)->info('update Action: '.$data['action']);

        Mage::dispatchEvent('mip_resource_order_update_before', array('data' => $data, 'order' => $order));

        switch($data['action']){

            case 'cancel':
                $order->cancel();
                if(isset($data['status'])){
                    $order->setData('status', $data['status']);
                    $order->addStatusToHistory($data['status']);
                    $order->setState($data['status']);
                }
                $order->save();
                break;

            case 'to_shipment':

                $skuItemMapping = array();
                foreach ($order->getAllItems() as $item) {
                    if(isset($skuItemMapping[$item->getSku()])){
                        continue;
                    }
                    $skuItemMapping[$item->getSku()] = $item->getId();
                }

                try{
                    if (!$order->canShip() && !isset($data['force_shipment'])) {
                        $this->_fault('cannot_ship');
                    }

                    $savedQtys = array();
                    if (is_array($data['shipment']['items'])){
                         foreach ($data['shipment']['items'] as $item){
                             if(empty($item['sku_product']) || empty($item['qty_shipped'])){
                                 continue;
                             }
                             if(!isset($skuItemMapping[$item['sku_product']])){
                                 continue;
                             }
                             $savedQtys[$skuItemMapping[$item['sku_product']]] = $item['qty_shipped'];
                         }
                     }

                    /** @var Mage_Sales_Model_Service_Order $shipment */
                    $shipment = Mage::getModel('sales/service_order', $order)->prepareShipment(isset($data['force_shipment']) ? array() : $savedQtys);

                    // tracking codes
                    if (isset($data['shipment']['track'])
                        && $tracks = $data['shipment']['track']) {

                        foreach ($tracks as $tracking) {
                            $track = Mage::getModel('sales/order_shipment_track')
                            ->addData($tracking);
                            $shipment->addTrack($track);
                        }
                    }

                    $shipment->register();

                    $comment = '';
                    if (!empty($data['comment_text'])) {
                        $shipment->addComment($data['comment_text'], isset($data['comment_customer_notify']));
                        if (isset($data['comment_customer_notify'])) {
                            $comment = $data['comment_text'];
                        }
                    }

                    if (!empty($data['send_email'])) {
                        Mage::helper('mip/log')->getWriter($this)->info('send_email: true');
                        $shipment->setEmailSent(true);
                    }

                    $shipment->getOrder()->setCustomerNoteNotify(!empty($data['send_email']));
                    $shipment->getOrder()->setIsInProcess(true);

                    Mage::getModel('core/resource_transaction')
                        ->addObject($shipment)
                        ->addObject($shipment->getOrder())
                        ->save();

                    $shipment->sendEmail(!empty($data['send_email']), $comment);
                }catch (Exception $e){
                    $msg = $e->getMessage();
                    if(is_callable(array($e, 'getCustomMessage'))){ $msg .= ' '.$e->getCustomMessage(); }
                    Mage::helper('mip/log')->getWriter($this)->error('ERROR: ' . $msg, $e);
                }
                // set Invoice
                try{

                    $savedQtys = array();
                    if (isset($data['invoice']) && isset($data['invoice']['items']) && is_array($data['invoice']['items'])){
                        foreach ($data['invoice']['items'] as $item){
                            if(empty($item['sku_product']) || empty($item['qty_invoiced'])){
                                continue;
                            }
                            if(!isset($skuItemMapping[$item['sku_product']])){
                                continue;
                            }
                            $savedQtys[$skuItemMapping[$item['sku_product']]] = $item['qty_invoiced'];
                        }
                    }

                    $invoiceId = Mage::getModel('sales/order_invoice_api')
                    ->create($order->getIncrementId(), $savedQtys, 'Invoice Created (MIP)', false, true);

                    $invoice = Mage::getModel('sales/order_invoice')->loadByIncrementId($invoiceId);

                    /* @var $invoice Mage_Sales_Model_Order_Invoice */
                    if (!$invoice->getId()) {
                        $this->_fault('not_exists');
                    }

                    if (!$invoice->canCapture() && !isset($data['force_invoice'])) {
                        $this->_fault('Invoice cannot be captured.');
                    }


                    $invoice->capture();
                    $invoice->getOrder()->setIsInProcess(true);
                    Mage::getModel('core/resource_transaction')
                        ->addObject($invoice)
                        ->addObject($invoice->getOrder())
                        ->save();

                }catch (Exception $e){
                    $msg = $e->getMessage();
                    if(is_callable(array($e, 'getCustomMessage'))){ $msg .= ' '.$e->getCustomMessage(); }
                    Mage::helper('mip/log')->getWriter($this)->error('ERROR: ' . $msg, $e);
                }

                if(!empty($data['force_status']) && $order->getStatus() != $data['status']){
                    $order->addStatusHistoryComment($data['comment'], $data['status'])
                        ->setIsVisibleOnFront(true)
                        ->setIsCustomerNotified(false);
                    $order->save();
                }
                break;

            default:

                /** @var $order Mage_Sales_Model_Order  */
                if(isset($data['status']) && $order->getStatus() != Mage_Sales_Model_Order::STATE_COMPLETE){
                    $notify = isset($data['send_email']) ? $data['send_email'] : false;
                    $visible = false;
                    $comment = trim(strip_tags(empty($data['comment_text']) ? '' : $data['comment_text']));

                    $order->addStatusHistoryComment($comment, $data['status'])
                        ->setIsVisibleOnFront($visible)
                        ->setIsCustomerNotified($notify);

                    $order->save();
                    $order->sendOrderUpdateEmail($notify, $comment);
                }

        }
        Mage::dispatchEvent('mip_resource_order_update_after', array('data' => $data, 'order' => $order));

    }

     /**
     * Retrieve list of orders by filters
     *
     * @param array $filters
     * @return array
     */
    public function items($filters = array())
    {
        $collection = $this->_getOrdersCollection($filters)->load();
        $result = array();

        foreach ($collection as $order) {
            $result[] = $this->info($order->getIncrementId(), false);
        }

        return $result;
    }

    /**
     * create a order
     *
     * @param array $data
     */
    public function create($data = array()){

        Mage::helper('mip/log')->getWriter($this)->info('import external order #' . $data['ext_order_id']);

        /* @var $quote Mage_Sales_Model_Quote */
        $quote = Mage::getModel('sales/quote')->setStoreId(Mage::app()->getStore($data['store_id'])->getId());

        if( TRUE === isset($data['customer_is_guest']) && !$data['customer_is_guest'] ) {
            $customer = Mage::getModel('customer/customer')
                ->setWebsiteId( $data['website_id'] )
                ->loadByEmail(  $data['customer_email']  );
            $quote->assignCustomer($customer);
        } else {
            $quote->setCustomerEmail( $data['customer_email'] );
        }

        if( TRUE === isset($data['items'])) {
            foreach( $data['items'] as $item ) {
                /* @var $product Mage_Catalog_Model_Product */
                $product = Mage::getModel('catalog/product');

                $_product = $product->load( $product->getIdBySku( $item['sku_product'] ) );
                if(!$_product->getId()){
                    Mage::throwException('Product '.$item['sku_product'].' do not exists');
                }
                $_product->getStockItem()
                    ->setBackorders(1)
                    ->setIsInStock(1);

                $quoteItem = $quote->addProduct($_product, new Varien_Object($item));
                if(!is_object($quoteItem)){
                    Mage::throwException('Cannot add Product '.$item['sku_product'].' to cart.');
                }

                if( FALSE === empty($item['custom_price']) ) {
                    $quoteItem->setCustomPrice($item['custom_price'])->setOriginalCustomPrice($item['custom_price']);
                }

                // set additional attributes
                foreach($item as $itemKey => $itemValue){
                    if(in_array($itemKey, array('custom_price', 'sku_product', 'qty'))){
                        continue;
                    }
                    $quoteItem->setData($itemKey, $itemValue);
                }
            }
        }

        if( TRUE === isset($data['billing_address']) ) {
            $quote->getBillingAddress()->addData( $data['billing_address'] );
        }

        if( TRUE === isset($data['shipping_address'])) {
            $shippingAddress = $quote->getShippingAddress()->addData( $data['shipping_address'] );
        }

        if($data['shipping']['carrier'] == 'mip'){
            Flagbit_Mip_Model_Carrier_Fix::setMethodTitle($data['shipping']['title']);
            Flagbit_Mip_Model_Carrier_Fix::setPrice($data['shipping']['price']);
        }

        $carrier = Mage::getSingleton('shipping/config')->getCarrierInstance($data['shipping']['carrier']);
        $storeCode = Mage::app()->getStore($data['store_id'])->getCode();
        if ($data['shipping']['activate'] === "true" && $carrier->isActive() === false) {
            Mage::helper('mip/log')->getWriter($this)->info('enable carrier ' . $data['shipping']['carrier'] );
            $config = Mage::getConfig();
            $fullPath = 'stores/' . $storeCode . '/' . 'carriers';
            $carrierSettings = $config->getNode($fullPath);
            $carrierSettings->setNode($data['shipping']['carrier'].'/active', true);
        }

        $shippingAddress
            ->collectTotals()
            ->setCollectShippingRates(true)
            ->collectShippingRates()
            ->setShippingMethod( $data['shipping']['carrier'] . '_' .$data['shipping']['method'] )
            ->setPaymentMethod( $data['payment']['method'] );

        $quote->getPayment()->importData(array('method' => $data['payment']['method'] ));

        foreach($data as $dataKey =>$dataValue){
            if(in_array($dataKey, array('store_id', 'website_id', 'shipping', 'billing_address', 'shipping_address', 'items', 'payment'))){
                continue;
            }
            $quote->setData($dataKey, $dataValue);
        }


        $quote->collectTotals()->save();


        /** @var Mage_Sales_Model_Service_Quote $service */
        $service = Mage::getModel('sales/service_quote', $quote);
        $service->submitAll();

        $_order = $service->getOrder();

        if($_order->getExtOrderId() !== FALSE && $_order->getIncrementId() !== FALSE)
        {
            $_order
                ->setData('ext_order_id', $data['ext_order_id'])
                ->save();
            return;
        }

        Mage::helper('mip/log')->getWriter($this)->warn('nothing to to with import order #' . $data['ext_order_id'] );
    }

    /**
     * create orders
     *
     * @param $data
     */
    public function massCreate($data) {

        if( is_array($data) && !empty($data) ) {

            Mage::dispatchEvent('mip_resource_order_masscreate_before', array('data' => $data));
            $resultState = true;
            foreach( $data as $order ) {

                // skip if there is no external identifier
                if(!isset($order['ext_order_id']) || empty($order['ext_order_id'])){
                    Mage::helper('mip/log')->getWriter($this)->warn('skip order external order id empty or not set' );
                    continue;
                }

                // skip if there is no existing store
                if(!Mage::app()->getStore($order['store_id'])){
                    Mage::helper('mip/log')->getWriter($this)->warn('store_code '. $order['store_id'] .' empty or not found' );
                    continue;
                }

                try {
                    Mage::dispatchEvent('mip_resource_order_create_before', array('data' => $order, 'ext_id' => $order['ext_order_id'], 'state' => $resultState));

                    $_order = Mage::getModel('sales/order')
                        ->loadByAttribute('ext_order_id', $order['ext_order_id']);

                    if (!$_order->getId()) {
                        $this->create($order);
                    }

                } catch( Exception $e ) {
                    Mage::helper('mip/log')->getWriter($this)->error('#'. $order['ext_order_id'] .' '. $e->getMessage(),$e );
                    $resultState = false;
                    continue;
                }

                Mage::dispatchEvent('mip_resource_order_create_after', array('data' => $order, 'ext_id' => $order['ext_order_id'], 'state' => $resultState));
            }

            Mage::dispatchEvent('mip_resource_order_masscreate_after', array('data' => $data, 'state' => $resultState));

        } else {
            Mage::helper('mip/log')->getWriter($this)->info('que empty or data not set');
        }
    }
}
