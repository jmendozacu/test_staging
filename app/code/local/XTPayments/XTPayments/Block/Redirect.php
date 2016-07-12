<?php
//class Mage_XTPayments_Block_Redirect extends Mage_Core_Block_Template

class XTPayments_XTPayments_Block_Redirect extends Mage_Page_Block_Redirect
{

    public function getTargetURL ()
    {
        $standard = Mage::getModel('xtpayments/payment');
        return $standard->getPaymentUrl();
    }

    public function getFormId ()
    {
        return 'redirect_form_id';
    }

    public function getMethod ()
    {
        return 'POST';
    }

    public function getMessage ()
    {
        return $this->__('You will be redirected to G2S Checkout in a few seconds.');
    }
    
    public function getFormFields()
    {
        $standard = Mage::getModel('xtpayments/payment');
        return $standard->getPPPFormFields();
    }

    // PATCH: fixed issue with JavaScript error on Firefox
    public function getHtmlFormRedirect()
    {
        $html=parent::getHtmlFormRedirect();
        $html=preg_replace('/(\<script[^\>]+\>)(.+?)(\<\/script\>)/i','${1}setTimeout(function(){${2}});${3}',$html);
        return $html;
    }
}
