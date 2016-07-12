<?php
class Netresearch_Billsafe_Block_Onepage_Payment_Methods extends Mage_Checkout_Block_Onepage_Payment_Methods
{
    /**
     * Return image tag for billsafe logo
     * 
     * @return string
     */
    public function getBillsafeLogo()
    {
        $url = Mage::getStoreConfig('payment/billsafe/billsafe_logo');
        return sprintf('<p><img src="%s" width="75px" /></p>', $url);
    }

    /**
     * Returns extended billsafe text
     * 
     * @return string
     */
    public function getBillsafeText()
    {
        $label = Mage::getStoreConfig('payment/billsafe/title');
        $helper = Mage::helper('billsafe');
        $text = $helper->__('Pay with our strong partner BillSAFE.');
        $feeText = "";
        if (Mage::getModel('billsafe/config')->isPaymentFeeEnabled()) {
            $fee = Mage::helper('paymentfee')->getUpdatedFeeProduct();
            if ($fee instanceof Mage_Catalog_Model_Product && $fee->getPrice() > 0) {
                $feeText = $helper->__(
                    'Using BillSAFE will cause an additional payment Fee of %s.',
                    $fee->getCheckoutDescription()
                );
            }
        }
        $msg = '<p style="margin-left: 20px"><b>%s</b><br/>%s<br/>%s</p>';
        return sprintf($msg, $label, $text, $feeText);
    }
    
    public function getNotAvailableBillsafeTest() {
    	try {
    		if($this->getQuote()->getShippingAddress()->getData('same_as_billing') != '1') { 
    			return Mage::helper('billsafe')->__('You have selected the payment method "bill". The shipping and billing address differ. The payment method "bill" is only available when matching invoice and delivery address. Choose an alternative payment method or correct your delivery and billing address.');
    		}
    	}catch (Exception $e) {
    		// Silent
    	}
    	return Mage::helper('billsafe')->__('BillSAFE payment is not available for this order');
    }
}
