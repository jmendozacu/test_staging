<?php
/**
 * Shopgate GmbH
 *
 * URHEBERRECHTSHINWEIS
 *
 * Dieses Plugin ist urheberrechtlich geschützt. Es darf ausschließlich von Kunden der Shopgate GmbH
 * zum Zwecke der eigenen Kommunikation zwischen dem IT-System des Kunden mit dem IT-System der
 * Shopgate GmbH über www.shopgate.com verwendet werden. Eine darüber hinausgehende Vervielfältigung, Verbreitung,
 * öffentliche Zugänglichmachung, Bearbeitung oder Weitergabe an Dritte ist nur mit unserer vorherigen
 * schriftlichen Zustimmung zulässig. Die Regelungen der §§ 69 d Abs. 2, 3 und 69 e UrhG bleiben hiervon unberührt.
 *
 * COPYRIGHT NOTICE
 *
 * This plugin is the subject of copyright protection. It is only for the use of Shopgate GmbH customers,
 * for the purpose of facilitating communication between the IT system of the customer and the IT system
 * of Shopgate GmbH via www.shopgate.com. Any reproduction, dissemination, public propagation, processing or
 * transfer to third parties is only permitted where we previously consented thereto in writing. The provisions
 * of paragraph 69 d, sub-paragraphs 2, 3 and paragraph 69, sub-paragraph e of the German Copyright Act shall remain unaffected.
 *
 * @author Shopgate GmbH <interfaces@shopgate.com>
 */

/**
 * Class Shopgate_Framework_Model_Payment_Payone_Cc
 *
 * @author awesselburg <wesselburg@me.com>
 * @author Konstantin Kiritsenko <konstantin@kiritsenko.com>
 */
class Shopgate_Framework_Model_Payment_Payone_Cc 
    extends Shopgate_Framework_Model_Payment_Payone_Abstract 
    implements Shopgate_Framework_Model_Payment_Interface
{
    const PAYONE_CORE_MODEL_CONFIG_IDENTIFIER = 'payone_creditcard';
    const PAYMENT_MODEL                       = 'payone_core/payment_method_creditcard';
    const PAYMENT_IDENTIFIER                  = ShopgateOrder::PAYONE_CC;

    /**
     * @param Mage_Sales_Model_Order $order
     *
     * @return Mage_Sales_Model_Order
     * @throws Exception
     */
    public function manipulateOrderWithPaymentData($order)
    {
        $paymentInfo = $this->getShopgateOrder()->getPaymentInfos();

        $this->getOrder()->getPayment()->setCcType($this->_getConfigCode());
        $this->getOrder()->getPayment()->setCcOwner($paymentInfo['credit_card']['holder']);
        $this->getOrder()->getPayment()->setCcNumberEnc($paymentInfo['credit_card']['masked_number']);

        return parent::manipulateOrderWithPaymentData($order);
    }

    /**
     * @return bool|string
     */
    protected function _getConfigCode()
    {
        /**
         * @var array
         */
        $paymentInfo = $this->getShopgateOrder()->getPaymentInfos();

        /**
         * @var string $key
         * @var string $value
         */
        foreach ($this->getSystemConfig()->toSelectArray() as $key => $value) {
            if (strtolower($value) == $paymentInfo['credit_card']['type']) {
                return $key;
            }
        }

        return false;
    }

}