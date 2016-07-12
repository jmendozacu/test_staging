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

class RatePAY_Ratepaypayment_Model_Source_Currency
{
    /**
     * Define which Currencies are allowed for payment
     *
     * @return array
     */
    public function toOptionArray()
    {
        $currency = array(
            array(
                'label' => Mage::helper('core')->__('EUR'),
                'value' => 'EUR'
            ),
            array(
                'label' => Mage::helper('core')->__('CHF'),
                'value' => 'CHF'
            )
        );
        return $currency;
    }
}

