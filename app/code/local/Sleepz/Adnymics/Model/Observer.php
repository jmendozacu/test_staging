<?php

/**
 * @category   Sleepz
 * @package    Sleepz_Adnymics
 * @author     minfo@sleepz.com
 * @website    http://www.sleepz.com
 *
 * @method Sleepz_Adnymics_Model_Observer::printCommandSettle (Varien_Event_Observer $observer)
 */

/**
 * Class Sleepz_Adnymics_Model_Observer
 *
 * This Observer is calling after the Order progress (the endpoint of order progress)
 * Contain only the Event and Call the external adnymics service
 */
class Sleepz_Adnymics_Model_Observer extends Sleepz_Adnymics_Model_Abstract
{
    /**
     * Settle the Print Command to Adnymics Server over RESTful API Call
     *
     * @param Varien_Event_Observer $observer
     */
    public function printCommandSettle(Varien_Event_Observer $observer)
    {
        try {
            /** @var  $order contain the order object */
            $order = $observer->getEvent()->getOrder();
            /** Call the REST object to send order data to adymics */
            $this->call($order);
        } catch (Exception $e) {

        }
    }
}