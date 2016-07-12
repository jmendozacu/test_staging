<?php
	class Flagbit_Bware_Model_Observer {
		public function generateNewOrderExports()
    {
        Mage::helper('mip/log')->getWriter($this)->info('START Order Export');

        $collection = Mage::getModel('sales/order')->getCollection()
            ->addFieldToFilter('status', array('in' => array('pending', 'processing')))
            ->addAttributeToFilter('transferred_to_bw', array('null' => 'transferred_to_bw'));

        foreach ($collection as $order) {
            /* @var $order Mage_Sales_Model_Order */

            $filename = 'order_' . $order->getIncrementId() . '.xml';
            $file = '/var/mip/bware/orders/' . $filename;

            /* @var Flagbit_Mip_Model_Config $definition */
            $definition = Mage::getSingleton('mip/config')->getRequest('orders');
            $definition->setSettings(array(
                'file' => $file,
                'params' => array('id' => $order->getIncrementId())
            ));

            /* @var Flagbit_Mip_Model_Server $server */
            $server = Mage::getSingleton('mip/server');
            $server->init($definition);
            $server->run(false);

            Mage::helper('mip/log')->getWriter($this)->info('transferred Order '.$order->getIncrementId().' to bw');

            // skip status update if export failed
            if (!$server->hasError() and !file_exists(Mage::getBaseDir().$file)) {
                continue;
            }

            // save transferred date
            $order->setTransferredToBw(now());
            $order->getResource()->saveAttribute($order, 'transferred_to_bw');

        }
        Mage::helper('mip/log')->getWriter($this)->info('FINISHED Order Export');
    }

}