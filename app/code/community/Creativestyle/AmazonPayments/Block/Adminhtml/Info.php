<?php

/**
 * This file is part of the official Amazon Payments Advanced extension
 * for Magento (c) creativestyle GmbH <amazon@creativestyle.de>
 * All rights reserved
 *
 * Reuse or modification of this source code is not allowed
 * without written permission from creativestyle GmbH
 *
 * @category   Creativestyle
 * @package    Creativestyle_AmazonPayments
 * @copyright  Copyright (c) 2014 creativestyle GmbH
 * @author     Marek Zabrowarny / creativestyle GmbH <amazon@creativestyle.de>
 */
class Creativestyle_AmazonPayments_Block_Adminhtml_Info extends Mage_Adminhtml_Block_System_Config_Form_Fieldset {

    protected function _getInfo() {
        if (!$this->getChild('seller_central_config')) {
            $this->setChild('seller_central_config', $this->getLayout()->createBlock('amazonpayments/adminhtml_sellerCentral')->setHtmlId('amazonpayments_plugin_info_seller_central'));
        }
        $this->setTemplate('creativestyle/amazonpayments/info.phtml');
        $output = $this->toHtml();
        return $output;
    }

    public function getSellerCentralConfigHtml() {
        if ($this->getChild('seller_central_config')) {
            return $this->getChild('seller_central_config')->toHtml();
        }
        return '';
    }

    public function getExtensionVersion() {
        return (string)Mage::getConfig()->getNode('modules/Creativestyle_AmazonPayments/version');
    }

    public function render(Varien_Data_Form_Element_Abstract $element) {
        return $this->_getInfo();
    }

}
