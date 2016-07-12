<?php
class XTPayments_XTPayments_Block_Form extends Mage_Payment_Block_Form
{
    protected function _construct()
    {
        $this->setTemplate('xtpayments/form.phtml');
        parent::_construct();
    }
	
	public function getPaymentImageSrc($payment)
    {
	
        $imageFilename = Mage::getDesign()
            ->getFilename('images' . DS . 'xtpayments' . DS . $payment, array('_type' => 'skin'));
		
        if (file_exists($imageFilename . '.png')) {
		
            return $this->getSkinUrl('images/xtpayments/' . $payment . '.png');
        } else if (file_exists($imageFilename . '.gif')) {
            return $this->getSkinUrl('images/xtpayments/' . $payment . '.gif');
        }

        return false;
    }
}

?>
