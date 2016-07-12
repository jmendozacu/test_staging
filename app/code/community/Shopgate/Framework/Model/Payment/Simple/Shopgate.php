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
 * Handles SHOPGATE payment method orders
 *
 * @author Konstantin Kiritsenko <konstantin@kiritsenko.com>
 */
class Shopgate_Framework_Model_Payment_Simple_Shopgate
    extends Shopgate_Framework_Model_Payment_Abstract
    implements Shopgate_Framework_Model_Payment_Interface
{
    const PAYMENT_IDENTIFIER     = ShopgateOrder::SHOPGATE;
    const MODULE_CONFIG          = 'Shopgate_Framework';
    const PAYMENT_MODEL          = 'shopgate/payment_shopgate';
    const XML_CONFIG_STATUS_PAID = 'payment/shopgate/order_status';

    /**
     * If shipping is blocked, use Shopgate Payment config status.
     * If not blocked, set to Processing.
     *
     * @param Mage_Sales_Model_Order $magentoOrder
     *
     * @return Mage_Sales_Model_Order
     */
    public function setOrderStatus($magentoOrder)
    {
        if ($this->getShopgateOrder()->getIsShippingBlocked()) {
            return parent::setOrderStatus($magentoOrder);
        }

        $message = $this->_getHelper()->__('[SHOPGATE] Using default order status');
        $state   = Mage_Sales_Model_Order::STATE_PROCESSING;

        $magentoOrder->setState($state, true, $message);
        $magentoOrder->setShopgateStatusSet(true);

        return $magentoOrder;
    }

    /**
     * No need to check activation, just import!
     *
     * @return true
     */
    public function isEnabled()
    {
        return true;
    }
}