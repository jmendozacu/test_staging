<?php
class XTPayments_XTPayments_PaymentController extends Mage_Core_Controller_Front_Action
{

    protected $_order = null;

    public function getModel()
    {
        return Mage::getSingleton('xtpayments/payment');
    }

    public function redirectAction()
    {

        $session = Mage::getSingleton('checkout/session');
        $session->setXTPaymentsQuoteId($session->getQuoteId());
        $content = $this->getLayout()->createBlock('xtpayments/redirect')->getRedirectOutput();
        $this->getResponse()->setBody( $content );
        $session->unsQuoteId();
    }
	
	public function SetTransactionType($trnType)
	{
		switch ($trnType)
		{
			case 'Sale': 
				return Mage::helper('xtpayments')->__('Sale');
			break;		
			case 'Auth': 
				return Mage::helper('xtpayments')->__('Auth');
			break;	
			case 'Credit': 
				return Mage::helper('xtpayments')->__('Credit');
			break;
			case 'Void': 
				return Mage::helper('xtpayments')->__('Void');
			break;
			case 'Chargeback': 
				return Mage::helper('xtpayments')->__('Chargeback');
			break;
			case 'Modification': 
				return Mage::helper('xtpayments')->__('Modification');
			break;
		}
	}
	
	public function SaveStatus($id,$status)
	{
		$request = $this->getRequest();
		$order = Mage::getModel('sales/order')->loadByIncrementId($id);
		$PPP_TransactionID = $request->getParam('PPP_TransactionID');
		$GW_TransactionID = $request->getParam('TransactionID');
		
		$transactionType = $request->getParam('transactionType');
		
		if ( ($transactionType=='Void') || ($transactionType=='Chargeback') || ($transactionType=='Credit') )
		{
				$status = 'HOLDED';
		}
		
		switch ($status)
		{
			case 'HOLDED':
				$msg = Mage::helper('xtpayments')->__('Payment status changed to:'.$this->SetTransactionType($request->getParam('transactionType')).'. PPP_TransactionID = '.$PPP_TransactionID.", Status = ".$status.', GW_TransactionID = '.$GW_TransactionID);
				$order->setState(Mage_Sales_Model_Order::STATE_HOLDED, true, $msg)->save();	
			break;
			
			case 'APPROVED':
				$msg = Mage::helper('xtpayments')->__('The amount has been authorized and captured by XT Payments. PPP_TransactionID = '.$PPP_TransactionID.", Status = ".$status.", TransactionType = ".$this->SetTransactionType($request->getParam('transactionType')).', GW_TransactionID = '.$GW_TransactionID);
				$order->setState(Mage_Sales_Model_Order::STATE_PROCESSING, true, $msg)->save();	
			break;
			
			case 'DECLINED':
				$msg = Mage::helper('xtpayments')->__('Payment failed. PPP_TransactionID = '.$PPP_TransactionID.", Status = ".$status.", Error code = ".$request->getParam('ErrCode').", TransactionType = ".$this->SetTransactionType($request->getParam('transactionType')).', GW_TransactionID = '.$GW_TransactionID);
				$order->setState(Mage_Sales_Model_Order::STATE_CANCELED, true, $msg)->save();	
			break;
			
			case 'ERROR':
				$msg = Mage::helper('xtpayments')->__('Payment failed. PPP_TransactionID = '.$PPP_TransactionID.", Status = ".$status.", Error code = ".$request->getParam('ErrCode').", Message = ".$request->getParam('message').", TransactionType = ".$this->SetTransactionType($request->getParam('transactionType')).', GW_TransactionID = '.$GW_TransactionID);
				$order->setState(Mage_Sales_Model_Order::STATE_CANCELED, true, $msg)->save();	
			break;
			
			case 'PENDING':
				$msg = Mage::helper('xtpayments')->__('Payment is still pending '.$PPP_TransactionID.", Status = ".$status.", TransactionType = ".$this->SetTransactionType($request->getParam('transactionType')).', GW_TransactionID = '.$GW_TransactionID);
				$order->setState(Mage_Sales_Model_Order::STATE_PENDING_PAYMENT, true, $msg)->save();	
			break;
		}
	}
	
	public function backAction()
    {
		$session = Mage::getSingleton('checkout/session');
        if ($quoteId = $session->getXTPaymentsQuoteId()) {
            $quote = Mage::getModel('sales/quote')->load($quoteId);
            if ($quote->getId()) {
                $quote->setIsActive(true)->save();
                $session->setQuoteId($quoteId);
            }
        }
		
        $this->_redirect('checkout/cart');
		
	}
	
	public function errorAction()
    {
		$request = $this->getRequest();
		$id = $request->getParam('transaction_id');
		$this->SaveStatus($id,$request->getParam('Status'));
		$session = Mage::getSingleton('checkout/session');
		
        if ($quoteId = $session->getXTPaymentsQuoteId()) {
            $quote = Mage::getModel('sales/quote')->load($quoteId);
            if ($quote->getId()) {
                $quote->setIsActive(true)->save();
                $session->setQuoteId($quoteId);
            }
        }
		
		if ($request->getParam('ppp_status')=='CANCEL') $message = Mage::helper('xtpayments')->__('Payment cancelled by user');
		else $message = Mage::helper('xtpayments')->__('Payment failed. '.$request->getParam('Reason'));
		$session->addError($message);
		$this->_redirect('checkout/cart');
       
    }
	
	public function statusAction()
    {
		
         $request = $this->getRequest();
		 echo "<PRE>";
		 var_dump($request);
		 
		 $invoice_id= explode("_",$request->getParam('invoice_id'));
		 $id = $invoice_id[0];
		
		
		 $order = Mage::getModel('sales/order')->loadByIncrementId($id);
		 if ($this->generateDMNResponseChecksum($request) == $request->getParam('responsechecksum') ) 
		 { //check if DMN call

            if ($order->getId()) 
			{
				$this->SaveStatus($id,$request->getParam('Status'));
				$order->sendNewOrderEmail();
            }
	    else {
                throw new Exception(sprintf('Wrong order ID: "%s".', $id));
            }

            Mage::getSingleton('checkout/session')->getQuote()->setIsActive(false)->save();
		} 
		else
		{
			$msg = Mage::helper('xtpayments')->__('Invalid DMNResponseChecksum from G2S. Order not processed.');
            $order->setState(Mage_Sales_Model_Order::STATE_PENDING_PAYMENT, true, $msg)->save();	
		}
		
    }
	
	
   
   public function successAction()
    {
        $request = $this->getRequest();
		
		$session = Mage::getSingleton('checkout/session');
		$session->setQuoteId($session->getXTPaymentsQuoteId(true));
		
		if ($session->getLastRealOrderId()) {
               // $order = Mage::getModel('sales/order')->loadByIncrementId($session->getLastRealOrderId());
				$id = $request->getParam('transaction_id');;
				$order = Mage::getModel('sales/order')->loadByIncrementId($id);
                if ($order->getId()) {
					$msg = Mage::helper('xtpayments')->__('Payment process completed. Waiting for transaction status from G2S. PPP_TransactionID = '.$request->getParam('PPP_TransactionID').'GW_TransactionID = '.$request->getParam('TransactionID'));
					if ($order->getState()== Mage_Sales_Model_Order::STATE_COMPLETE)
						$order->setState(Mage_Sales_Model_Order::STATE_PENDING_PAYMENT, true,$msg)->save();
                    $order->sendNewOrderEmail();
                }
            }
			
		Mage::getSingleton('checkout/session')->getQuote()->setIsActive(false)->save();
		$this->_redirect('checkout/onepage/success', array('_secure'=>true));
 	       
    }
	
	
    private function generateResponseChecksum($request) {
    	$mc =	$this->getModel()->getPaymentSecretKey().
    			$request->getParam('totalAmount').
    			$request->getParam('currency').
    			$request->getParam('responseTimeStamp').
          $request->getParam('PPP_TransactionID').
    			$request->getParam('Status').
          $request->getParam('productId');
    	return md5($mc);
    }
    
    private function generateDMNResponseChecksum($request) {

       $mc =   $this->getModel()->getPaymentSecretKey().
          $request->getParam('ppp_status').
          $request->getParam('PPP_TransactionID');

       return md5($mc);
    }
    
}
