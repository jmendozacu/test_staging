<?php

/**
 * Magento
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@magentocommerce.com so we can send you a copy immediately.
 *
 * @category RatePAY
 * @package RatePAY_Ratepaypayment
 * @copyright Copyright (c) 2015 RatePAY GmbH (https://www.ratepay.com)
 * @license http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 */
class RatePAY_Ratepaypayment_Helper_Mapping extends Mage_Core_Helper_Abstract
{

    /**
     * Article preparations for PAYMENT_REQUEST, PAYMENT_CHANGE, CONFIRMATION_DELIVER
     *
     * @param Mage_Sales_Model_Quote|Mage_Sales_Model_Order|Mage_Sales_Model_Order_Invoice|Mage_Sales_Model_Order_Creditmemo $object
     * @return array
     */
    public function getArticles($object)
    {
        $articles = array();
        $articleDiscountAmount = 0;
        $objectItems = $object->getAllItems();

        foreach ($objectItems as $item) {
            if ($item instanceof Mage_Sales_Model_Order_Item ||
                $item instanceof Mage_Sales_Model_Quote_Item) {
                $orderItem = $item;
            } else {
                $orderItem = Mage::getModel('sales/order_item')->load($item->getOrderItemId());
            }

            $shopProduct = Mage::getModel('catalog/product')->load($orderItem->getProductId());

            if ((($orderItem->getProductType() !== 'bundle') || ($orderItem->getProductType() === 'bundle' && $shopProduct->getPrice() > 0)) && $orderItem->getRowTotal() > 0) {
                $article = array();
                $article['articleNumber'] = $item->getSku();
                $article['articleName'] = $item->getName();
                $article['quantity'] = ($object instanceof Mage_Sales_Model_Order) ? $item->getQtyOrdered() : $item->getQty();
                $article['unitPriceGross'] = $item->getPriceInclTax();
                $article['taxPercent'] = $orderItem->getTaxPercent();

                $article['discountId'] = '';
                if ($item->getDiscountAmount() > 0) {
                    $discount = array();
                    $discount['articleNumber'] = 'DISCOUNT-' . $item->getSku();
                    $discount['articleName'] = 'DISCOUNT - ' . $item->getName();
                    $discount['quantity'] = $article['quantity'];
                    $discount['unitPriceGross'] = (-1 * $item->getDiscountAmount()) / $article['quantity'];
                    $discount['discountId'] = $item->getSku();

                    $articleDiscountAmount = $articleDiscountAmount + $item->getDiscountAmount();
                }

                $articles[] = $article;
                if ($item->getDiscountAmount() > 0) { // only for sort reason
                    $articles[] = $discount;
                }
            }
        }

        if ($object instanceof Mage_Sales_Model_Order || $object instanceof Mage_Sales_Model_Order_Invoice || $object instanceof Mage_Sales_Model_Order_Creditmemo) {
            $shippingObject = $object;
            if ($object instanceof Mage_Sales_Model_Order_Creditmemo) {
                $articles = $this->addAdjustments($object, $articles);
            }
        } else {
            $shippingObject = $object->getShippingAddress();
        }

        if ($shippingObject->getShippingAmount() > 0) {

            if ($object instanceof Mage_Sales_Model_Order_Invoice || $object instanceof Mage_Sales_Model_Order_Shipment || $object instanceof Mage_Sales_Model_Order_Creditmemo) {
                $shippingDiscountAmount = $shippingObject->getDiscountAmount() - $articleDiscountAmount;
                $shippingDescription = $object->getOrder()->getShippingDescription();
            } else {
                $shippingDiscountAmount = $shippingObject->getShippingDiscountAmount();
                $shippingDescription = $shippingObject->getShippingDescription();
            }

            $article = array();
            $article['articleNumber'] = 'SHIPPING';
            $article['articleName'] = $shippingDescription;
            $article['quantity'] = '1';
            $article['unitPriceGross'] = $shippingObject->getShippingInclTax();
            $shippingTaxPercent = 0;
            if (($shippingObject->getShippingInclTax() - $shippingObject->getShippingAmount()) > 0) {
                $shippingTaxPercent = (($shippingObject->getShippingInclTax() - $shippingObject->getShippingAmount()) * 100) / $shippingObject->getShippingAmount();
            }
            $article['taxPercent'] = $shippingTaxPercent;
            $article['discountId'] = '';

            if ($shippingDiscountAmount > 0) {
                $discount = array();
                $discount['articleNumber'] = 'SHIPPINGDISCOUNT';
                $discount['articleName'] = 'Shipping - Discount';
                $discount['quantity'] = 1;
                $discount['unitPriceGross'] = -1 * $shippingObject->getShippingDiscountAmount();
                $discount['taxPercent'] = 0;
                $discount['discountId'] = 'SHIPPING';
            }

            $articles[] = $article;
            if ($shippingDiscountAmount > 0) { // only for sort reason
                $articles[] = $discount;
            }
        }
        return $articles;
    }

    /**
     * Add adjustment items to the article list
     * 
     * @param Mage_Sales_Model_Creditmemo $creditmemo
     * @param array
     */
    private function addAdjustments($creditmemo, $articles)
    {
        if ($creditmemo->getAdjustmentPositive() != 0) {
            array_push($articles, $this->addAdjustment((float) $creditmemo->getAdjustmentPositive() * -1, 'Adjustment Fee', 'adj-fee'));
        }
        if ($creditmemo->getAdjustmentNegative() != 0) {
            array_push($articles, $this->addAdjustment((float) $creditmemo->getAdjustmentNegative(), 'Adjustment Refund', 'adj-refgetRequestBasket'));
        }

        return $articles;
    }

    /**
     * Add merchant credit to artcile list
     * 
     * @param array $articles
     * @param float $amount
     * @return array
     */
    public function addAdjustment($amount, $name, $articleNumber)
    {
        $tempVoucherItem = array();
        $tempVoucherItem['articleName'] = $name;
        $tempVoucherItem['articleNumber'] = $articleNumber;
        $tempVoucherItem['quantity'] = 1;
        $tempVoucherItem['unitPriceGross'] = $amount;
        $tempVoucherItem['taxPercent'] = 0;
        $tempVoucherItem['discountId'] = 0;

        return $tempVoucherItem;
    }

    /**
     * Gets all needed Informations for Logging
     * 
     * @param Mage_Sales_Model_Quote|Mage_Sales_Model_Order $quoteOrOrder
     * @param string $methodCode
     * @return array
     */
    public function getLoggingInfo($quoteOrOrder, $methodCode = '')
    {
        $loggingInfo = array();
        if ($methodCode == '') {
            $methodCode = $quoteOrOrder->getPayment()->getMethod();
        }
        $loggingInfo['logging'] = $this->getHelper()->getRpConfigData($quoteOrOrder, $methodCode, 'logging');
        $loggingInfo['sandbox'] = $this->getHelper()->getRpConfigData($quoteOrOrder, $methodCode, 'sandbox');
        if ($quoteOrOrder instanceof Mage_Sales_Model_Order) {
            $loggingInfo['orderId'] = $quoteOrOrder->getRealOrderId();
        } else {
            $loggingInfo['orderId'] = $quoteOrOrder->getReservedOrderId();
        }
        $loggingInfo['transactionId'] = $quoteOrOrder->getPayment()->getAdditionalInformation('transactionId');
        switch ($methodCode) {
            case 'ratepay_rechnung':
                $loggingInfo['paymentMethod'] = 'INVOICE';
                break;
            case 'ratepay_rate0':
            case 'ratepay_rate':
                $loggingInfo['paymentMethod'] = 'INSTALLMENT';
                break;
            case 'ratepay_directdebit':
                $loggingInfo['paymentMethod'] = 'ELV';
                break;
        }
        $loggingInfo['firstName'] = $quoteOrOrder->getBillingAddress()->getFirstname();
        $loggingInfo['lastName'] = $quoteOrOrder->getBillingAddress()->getLastname();
        return $loggingInfo;
    }

    /**
     * Gets all needed Informations for the head Block of Requests for RatePAY
     *
     * @param Mage_Sales_Model_Quote|Mage_Sales_Model_Order $quoteOrOrder
     * @param string $subtype
     * @param string $methodCode
     * @return array
     */
    public function getRequestHead($quoteOrOrder, $subtype = '', $methodCode = '')
    {
        $head = array();
        if ($methodCode == '') {
            $configProfileId    = $this->getHelper()->getRpConfigData($quoteOrOrder, $quoteOrOrder->getPayment()->getMethod(), 'profile_id');
            $configSecurityCode = $this->getHelper()->getRpConfigData($quoteOrOrder, $quoteOrOrder->getPayment()->getMethod(), 'security_code');
        } else {
            $configProfileId    = $this->getHelper()->getRpConfigData($quoteOrOrder, $methodCode, 'profile_id');
            $configSecurityCode = $this->getHelper()->getRpConfigData($quoteOrOrder, $methodCode, 'security_code');
        }
        $orderProfileId    = $quoteOrOrder->getPayment()->getAdditionalInformation('profileId');
        $orderSecurityCode = $quoteOrOrder->getPayment()->getAdditionalInformation('securityCode');
        if (!empty($orderProfileId) && $orderProfileId <> $configProfileId) {
            $head['profileId']     = $orderProfileId;
            $head['securityCode']  = $orderSecurityCode;
        } else {
            $head['profileId']     = $configProfileId;
            $head['securityCode']  = $configSecurityCode;
        }

        $head['transactionId'] = ($quoteOrOrder->getPayment()->getAdditionalInformation('transactionId')) ? $quoteOrOrder->getPayment()->getAdditionalInformation('transactionId') : '';

        if ($quoteOrOrder instanceof Mage_Sales_Model_Order) {
            $head['orderId'] = $quoteOrOrder->getRealOrderId();
        } else {
            $head['orderId'] = $quoteOrOrder->getReservedOrderId();
        }
        if ($customerId = $quoteOrOrder->getCustomerId()) {
            $head['customerId'] = $customerId;
        }

        $head['subtype'] = $subtype;

        return $head;
    }

    /**
     * Gets all needed Informations for customer Block of the Request
     *
     * @param Mage_Sales_Model_Quote|Mage_Sales_Model_Order $quoteOrOrder
     * @return array
     */
    public function getRequestCustomer($quoteOrOrder)
    {
        $customer = array();
        $contacts = array();
        $billing = array();
        $shipping = array();

        $dob = new Zend_Date($quoteOrOrder->getCustomerDob());
        $customer['dob'] = $dob->toString("yyyy-MM-dd");
        $customer['gender'] = $this->getHelper()->getGenderCode($quoteOrOrder);
        $customer['firstName'] = $quoteOrOrder->getBillingAddress()->getFirstname();
        $customer['lastName'] = $quoteOrOrder->getBillingAddress()->getLastname();
        $customer['ip'] = Mage::helper('core/http')->getRemoteAddr(false);
        $customer['nationality'] = $quoteOrOrder->getBillingAddress()->getCountryId();
        $customer['company'] = $quoteOrOrder->getBillingAddress()->getCompany();
        $customer['vatId'] = $quoteOrOrder->getCustomerTaxvat();

        $contacts['email'] = $quoteOrOrder->getCustomerEmail();
        $contacts['phone'] = $quoteOrOrder->getBillingAddress()->getTelephone();

        if ($quoteOrOrder->getBillingAddress()->getFax() != '') {
            $contacts['fax'] = $quoteOrOrder->getBillingAddress()->getFax();
        }

        $billing['street'] = preg_replace('~[\r\n]+~', ' ', $quoteOrOrder->getBillingAddress()->getStreetFull());
        $billing['zipCode'] = $quoteOrOrder->getBillingAddress()->getPostcode();
        $billing['city'] = $quoteOrOrder->getBillingAddress()->getCity();
        $billing['countryId'] = $quoteOrOrder->getBillingAddress()->getCountryId();

        $shipping['firstName'] = $quoteOrOrder->getShippingAddress()->getFirstname();
        $shipping['lastName'] = $quoteOrOrder->getShippingAddress()->getLastname();
        $shipping['street'] = preg_replace('~[\r\n]+~', ' ', $quoteOrOrder->getShippingAddress()->getStreetFull());
        $shipping['zipCode'] = $quoteOrOrder->getShippingAddress()->getPostcode();
        $shipping['city'] = $quoteOrOrder->getShippingAddress()->getCity();
        $shipping['countryId'] = $quoteOrOrder->getShippingAddress()->getCountryId();
        if ($quoteOrOrder->getShippingAddress()->getCompany()) {
            $shipping['company'] = $quoteOrOrder->getShippingAddress()->getCompany();
        }

        $customer['contacts'] = $contacts;
        $customer['billing'] = $billing;
        $customer['shipping'] = $shipping;
        return $customer;
    }

    /**
     * Gets all needed Informations for shopping-basket Block of the Request
     *
     * @param Mage_Sales_Model_Quote|Mage_Sales_Model_Order|Mage_Sales_Model_Order_Invoice|Mage_Sales_Model_Order_Creditmemo $object
     * @param float $amount
     * @param array $articleList
     * @return array
     */
    public function getRequestBasket($object, $amount = '', $articleList = '')
    {
        $basket = array();

        if ($object instanceof Mage_Sales_Model_Order) {
            $basket['currency'] = $object->getOrderCurrencyCode();
        } else if ($object instanceof Mage_Sales_Model_Quote) {
            $basket['currency'] = $object->getQuoteCurrencyCode();
        } else {
            $basket['currency'] = $object->getOrder()->getOrderCurrencyCode();
        }

        if ($articleList != '') {
            $basket['items'] = $articleList;
        } else {
            $basket['items'] = $this->getArticles($object);
        }

        // If no positiv item is remained in basket clear basket
        if (!$this->_anyPositiveItems($basket['items'])) {
            $basket['items'] = array();
        }

        if (is_numeric($amount)) {
            $basket['amount'] = $amount;
        } else {
            if ($object->getGrandTotal() > 0) {
                $basket['amount'] = $object->getGrandTotal();
            } elseif (count($basket['items']) == 0) {
                $basket['amount'] = 0;
            } else {
                $basket['amount'] = $this->getAmountByItems($basket['items']);
            }

        }

        // Ensure that the basket amount is never less than zero
        // In certain cases (i.e. in case of maloperation) the amount can become < 0
        if ($basket['amount'] < 0) {
            $basket['amount'] = 0;
        }

        return $basket;
    }

    /**
     * Totalizes the amount of all items
     *
     * @param array $items
     * @return boolean
     */
    public function getAmountByItems($items) {
        $amount = 0;

        foreach ($items as $item) {
            $amount += $item['quantity'] * $item['unitPriceGross'];
        }

        return $amount;
    }

    /**
     * Check for any positive items
     *
     * @param array $items
     * @return boolean
     */
    public function _anyPositiveItems($items) {
        $anyPositiveItems = false;
        foreach ($items as $item) {
            if ($item['unitPriceGross'] >= 0) {
                $anyPositiveItems = true;
            }
        }
        return $anyPositiveItems;
    }

    /**
     * Gets all needed Informations for payment Block of the Request
     *
     * @param Mage_Sales_Model_Quote|Mage_Sales_Model_Order $object
     * @param float amount
     * @param string $request
     * @return array
     */
    public function getRequestPayment($object, $amount = '', $request = '')
    {
        $paymentMethod = $object->getPayment()->getMethod();
        $payment = array();
        switch ($paymentMethod) {
            case 'ratepay_rechnung':
                $payment['method'] = 'INVOICE';
                break;
            case 'ratepay_rate0':
            case 'ratepay_rate':
                if ($request == 'PAYMENT_REQUEST') {
                    $payment['installmentNumber'] = Mage::getSingleton('ratepaypayment/session')->{'get' . Mage::helper('ratepaypayment')->convertUnderlineToCamelCase($paymentMethod) . 'NumberOfRatesFull'}();
                    $payment['installmentAmount'] = Mage::getSingleton('ratepaypayment/session')->{'get' . Mage::helper('ratepaypayment')->convertUnderlineToCamelCase($paymentMethod) . 'Rate'}();
                    $payment['lastInstallmentAmount'] = Mage::getSingleton('ratepaypayment/session')->{'get' . Mage::helper('ratepaypayment')->convertUnderlineToCamelCase($paymentMethod) . 'LastRate'}();
                    $payment['interestRate'] = Mage::getSingleton('ratepaypayment/session')->{'get' . Mage::helper('ratepaypayment')->convertUnderlineToCamelCase($paymentMethod) . 'InterestRate'}();
                    if ($this->isDynamicDue()) {
                        $payment['paymentFirstDay'] = Mage::getSingleton('ratepaypayment/session')->getRatepayPaymentFirstDay();
                    } else {
                        $payment['paymentFirstDay'] = '28';
                    }
                }
                $payment['method'] = 'INSTALLMENT';
                if (Mage::getSingleton('ratepaypayment/session')->getDirectDebitFlag()) {
                    $payment['debitType'] = 'DIRECT-DEBIT';
                } else {
                    $payment['debitType'] = 'BANK-TRANSFER';
                }
                break;
            case 'ratepay_directdebit':
                $payment['method'] = 'ELV';
                break;
        }
        if ($object instanceof Mage_Sales_Model_Order) {
            $payment['currency'] = $object->getOrderCurrencyCode();
        } else {
            $payment['currency'] = $object->getQuoteCurrencyCode();
        }
        if (is_numeric($amount)) {
            $payment['amount'] = $amount;
        } else {
            $payment['amount'] = $object->getGrandTotal();
        }

        // Ensure that the basket amout is never less than zero
            // In certain cases (i.e. in case of maloperation) the amount can become < 0
        if ($payment['amount'] < 0) {
            $payment['amount'] = 0;
        }

        return $payment;
    }

    /**
     * Is due dynamic
     * 
     * @return boolean
     */
    public function isDynamicDue()
    {
        if ($this->getHelper()->getRpConfigData($this->getHelper()->getQuote(), 'ratepay_rate', 'dynamic_due') || $this->getHelper()->getRpConfigData($this->getHelper()->getQuote(), 'ratepay_rate0', 'dynamic_due')) {
            return true;
        }

        return false;
    }

    /**
     * Returns the payment method helper
     *
     * @return RatePAY_Ratepaypayment_Helper_Data
     */
    public function getHelper()
    {
        return Mage::helper('ratepaypayment');
    }

}