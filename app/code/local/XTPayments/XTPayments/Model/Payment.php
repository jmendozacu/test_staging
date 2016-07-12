<?php
//class Mage_XTPayments_Model_Payment extends Mage_Payment_Model_Method_Cc {
class XTPayments_XTPayments_Model_Payment extends Mage_Payment_Model_Method_Abstract {
    protected $_code  = 'xtpayments';
    protected $_formBlockType = 'xtpayments/form';

    protected $_isGateway               = false;
    protected $_canAuthorize            = false;
    protected $_canCapture              = false;
    protected $_canCapturePartial       = false;
    protected $_canRefund               = false;
    protected $_canVoid                 = true;
    protected $_canUseInternal          = true;
    protected $_canUseCheckout          = true;
    protected $_canUseForMultishipping  = false;
    protected $_canSaveCc               = false;

    public function canEdit()
    {
        return false;
    }

    public function isAvailable($quote=null)
    {
        return Mage::getStoreConfig('payment/xtpayments/active') > 0 && ! $this->getIsDisabled();
    }
    public function isFake($quote=null)
    {
        return Mage::getStoreConfig('payment/xtpayments/ppp_fake') > 0;
    }

    public function getOrderPlaceRedirectUrl()
    {

        return Mage::getUrl('xtpayments/payment/redirect',array('_secure' => true));
    }

    public function getPaymentUrl()
    {
         return Mage::getStoreConfig('payment/xtpayments/ppp_payment_url');
    }
    public function getPaymentMerchantID()
    {
         return Mage::getStoreConfig('payment/xtpayments/ppp_merchant_id');
    }
    public function getPaymentMerchantSiteID()
    {
         return Mage::getStoreConfig('payment/xtpayments/ppp_merchant_site_id');
    }
    public function getPaymentSecretKey()
    {
         return Mage::getStoreConfig('payment/xtpayments/ppp_merchant_secret_key');
    }

    public function getCheckout()
    {
        return Mage::getSingleton('checkout/session');
    }
    public function getCHQuote()
    {
        return $this->getCheckout()->getQuote();
    }

    public function getQuote() {
        $orderIncrementId = $this->getCheckout()->getLastRealOrderId();
        $order = Mage::getModel('sales/order')->loadByIncrementId($orderIncrementId);
        return $order;
    }


    public function getPPPFormFields()
    {
        $order_id = $this->getCheckout()->getLastRealOrderId();
        $order    = Mage::getModel('sales/order')->loadByIncrementId($order_id);
        $_G2SoneItem = false;

        if ($this->isFake()){
            foreach ( array( 'cancel', 'error', 'success' ) as $key => $value ) {
                printf( "\n<a href='%s'>%s</a>\n", Mage::getUrl('xtpayments/payment/'.$value,array('_secure' => false)), $value );
            }
            print "<pre><xmp>";
        }

        if ($this->getQuote()->getIsVirtual()) {
            $a = $this->getQuote()->getBillingAddress();
            $b = $this->getQuote()->getShippingAddress();
        } else {
            $a = $this->getQuote()->getShippingAddress();
            $b = $this->getQuote()->getBillingAddress();
        }

        $sArr = array();
        //$sArr_NO = array();

        $shipping = sprintf('%.2f', $this->getQuote()->getShippingAmount());
        /* Korrektur Discount */ 
        //$discount = sprintf('%.2f', abs($this->getQuote()->getBaseDiscountAmount()));
        $discount = 0.0; 


		//$total = sprintf('%.2f', ($this->getQuote()->getSubtotal() + $shipping - $discount) ); 
		//$total = sprintf('%.2f', ($this->getQuote()->getGrandTotal()- $discount) ); 
		/* $total = number_format($this->getQuote()->getGrandTotal()- $discount,2,'.', '');

        $sArr = array_merge($sArr, array
        (
          'currency'      => $this->getQuote()->getBaseCurrencyCode(),
          'total_amount'  => $total,
        ));


        $items = $this->getQuote()->getAllItems();
		$item_price=0;
        if ($items) {
           // if ($sArr["total_amount"] == number_format($this->getQuote()->getBaseGrandTotal(),2,'.', '')) {
              $i = 1;
              foreach($items as $item){
                  if ($item->getParentItem()) {
                      continue;
                  }
                  $sArr = array_merge($sArr, array(
                      'item_name_'.$i   => $item->getName(),
					  'item_number_'.$i   => $i,
                      'item_amount_'.$i => number_format($item->getPrice(),2,'.', ''),//sprintf('%.2f', ($item->getPrice())),
                      'item_quantity_'.$i    => sprintf('%.1d', ($item->getQtyOrdered())),
                  ));
				  
				  $item_price+= number_format(number_format($item->getPrice(),2,'.', '')*$item->getQtyOrdered(),2,'.', '');
                  $i++;
              }
              if ( $i > 2 ) $sArr['numberofitems'] = $i - 1;
			  
           /* }
			
            else {
              $_G2SoneItem = true;
              $storeName = Mage::app()->getStore()->getName();

              $sArr = array_merge($sArr, array(
                'item_name_1'     => $storeName." - #".$order_id,
				'item_number_1'   => "1",
                'item_amount_1'   => number_format($this->getQuote()->getBaseGrandTotal(),2,'.', ''),
                'item_quantity_1' => 1,
				'numberofitems'		=>1,
              ));
              $sArr["total_amount"] = number_format($this->getQuote()->getBaseGrandTotal(),2,'.', '');
              $sArr['numberofitems'] = 1;
			  

            }*/
        /* } */

        $sArr = array_merge($sArr, array
        (
          'currency'      => $this->getQuote()->getBaseCurrencyCode(),
          'total_amount'  => sprintf('%.2f', ($this->getQuote()->getSubtotal() + $shipping - $discount) ),
        ));


        $items = $this->getQuote()->getAllItems();
        if ($items) {
            if ($sArr["total_amount"] == number_format($this->getQuote()->getBaseGrandTotal(),2,'.', '')) {
              $i = 1;
              foreach($items as $item){
                  if ($item->getParentItem()) {
                      continue;
                  }
                  $sArr = array_merge($sArr, array(
                      'item_name_'.$i   => $item->getName(),
                      'item_number_'.$i   => $i,
                      'item_amount_'.$i => number_format($item->getPrice(),2,'.', ''),//sprintf('%.2f', ($item->getPrice())),
                      'item_quantity_'.$i    => sprintf('%.1d', ($item->getQtyOrdered())),
                  ));
                  $i++;
              }
              if ( $i > 2 ) $sArr['numberofitems'] = $i - 1;
            }
            else {
              $_G2SoneItem = true;
              $storeName = Mage::app()->getStore()->getName();

              $sArr = array_merge($sArr, array(
                'item_name_1'     => $storeName." - #".$order_id,
                'item_number_1'   => "1",
                'item_amount_1'   => number_format($this->getQuote()->getBaseGrandTotal(),2,'.', ''),
                'item_quantity_1' => 1,
                'numberofitems'     =>1,
              ));
              $sArr["total_amount"] = number_format($this->getQuote()->getBaseGrandTotal(),2,'.', '');
              $sArr['numberofitems'] = 1;
              

            }
        }

        /* Korrektur Discount Ende */
		
		$item_price_total = number_format($item_price,2,'.', '');
		
		list( $sec, $min, $hour, $mday, $mon, $year, $wday, $yday, $isdst ) = localtime( time() );
        $TimeStamp = date('Ymdhis');

        $sArr = array_merge($sArr, array
        (
            'first_name'        => $a->getFirstname(),
            'last_name'         => $a->getLastname(),
            'address1'          => $a->getStreet(1),
            'address2'          => $a->getStreet(2),
            'city'              => $a->getCity(),
            'state'             => $a->getRegionCode(),
            'country'           => $a->getCountry(),
            'zip'               => $a->getPostcode(),
            'phone1'            => $a->getTelephone(),
            'email'             => $order->getData('customer_email'),
            'encoding'          => 'utf8',
            'invoice_id'        => $order_id.'_'.$TimeStamp,
			'merchant_unique_id' =>$order_id.'_'.$TimeStamp,
            'version'           => '4.0.0',

        ));
		
        $sArr = array_merge($sArr, array
        (
            'shippingFirstName'         => $b->getFirstname(),
            'shippingLastName'          => $b->getLastname(),
            'shippingAddress'           => $b->getStreet(1),
            'shippingCity'              => $b->getCity(),
            'shippingState'             => $b->getRegionCode(),
            'shippingCountry'           => $b->getCountry(),
            'shippingZip'               => $b->getPostcode(),
            'shippingPhone'             => $b->getTelephone(),
            'shippingMail'              => $order->getData('customer_email'),
        ));

       
          $sArr = array_merge($sArr, array(
            'handling'  => number_format(($total-$item_price_total),2,'.', ''), //sprintf('%.2f', ($total-$item_price_total)),
            'discount'  => $discount));
       

        $SecretKey = $this->getPaymentSecretKey();
        $MerchantID = $this->getPaymentMerchantID();
        $MerchantSiteID = $this->getPaymentMerchantSiteID();

        $sArr = array_merge($sArr, array
        (
            'merchant_id'      			=> $MerchantID,
            'time_stamp'       			=> $TimeStamp,
			'merchant_site_id'		 	=> $MerchantSiteID,
			'success_url'            	=> Mage::getUrl('xtpayments/payment/success', array('transaction_id' => $order_id)),
			'pending_url'            	=> Mage::getUrl('xtpayments/payment/success', array('transaction_id' => $order_id)),
            'error_url'            		=> Mage::getUrl('xtpayments/payment/error', array('transaction_id' => $order_id)),
			'back_url'            		=> Mage::getUrl('xtpayments/payment/back', array('transaction_id' => $order_id)),
            'notify_url'            	=> Mage::getUrl('xtpayments/payment/status'),
        ));

		
		$JoinedInfo = join( '', array_values( $sArr ) );
		$CheckSum = md5( stripslashes($SecretKey).Mage::getSingleton('core/session')->getFormKey().$JoinedInfo );
		
		 $sArr = array_merge($sArr, array
        (
            'checksum'         => $CheckSum,
        ));
		
		
		/*
		echo "Secret word = ".stripslashes($SecretKey).$JoinedInfo."<br />";
		echo "checksum = ".$CheckSum;
		echo "<pre>";
		var_dump($sArr);die();
		*/
		 
        return $sArr;
    }


	
    public function getIsDisabled ()
    {
        $quote = $this->getCHQuote();
        foreach ( $quote->getAllVisibleItems() as $item ) {
            if ( $item->getProduct()->getDisableG2spayment() ) {
                return true;
            }
        }
        return false;
    }

}
