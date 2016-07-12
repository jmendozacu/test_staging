<?php

/**
 * @category   Sleepz
 * @package    Sleepz_Adnymics_Model_Abstract
 * @extends    Mage_Core_Model_Abstract
 * @author     minfo@sleepz.com
 * @website    http://www.sleepz.com
 *
 * @method Sleepz_Adnymics_Model_Observer::printCommandSettle (Varien_Event_Observer $observer)
 */

/** include the httpful class */
require('lib/httpful/bootstrap.php');

/**
 * Class Sleepz_Adnymics_Model_Abstract
 */
class Sleepz_Adnymics_Model_Abstract extends Mage_Core_Model_Abstract
{
    /** RESTFul API - TEST */
    const REST_API_SERVER = "https://s1.adnymics.com:8001",
        REST_API_USER = "SleepzTest",
        REST_API_PASSWORD = "ec2ei8hy5r",
        REST_API_SERVER_PATH = "generateBrochure";

    /** Adnymics ErrorLog File */
    const ADNYMICS_ERROR_FILE = "adnymics_errors.log";

    /** @var contain the REST Object */
    protected $_rest;

    /** @var The output URL for Adnymics */
    protected $_urlItems = array();

    /** @var $_multiFlag bool */
    protected $_multiFlag = false;

    /** @var mandatory fields which must Includes the stuck in the call to adnymics */
    protected $_mandatoryFields = array();

    /** @var contain the server and path sections combine to an URL */
    protected $_adnymics_server_path = "";

    /** @var contain the query string to sending the custoemr data to adnymics */
    protected $_urlquery = "";

    /**
     * Load the mandatory fields method
     */
    public function _construct()
    {
        $this->_setMandatoryFields();
        $this->_setAdnymicsUrl();
        parent::_construct();
    }

    /**
     * Contain the mandatory fields
     *
     * @internal global/default/adnymics/mandatory/fields see in the config.xml from global node
     */
    protected function _setMandatoryFields()
    {
        $this->_mandatoryFields = Mage::getConfig()
            ->loadModulesConfiguration('config.xml')
            ->getNode('global/default/adnymics/mandatory/fields')
            ->asArray();
    }

    /**
     * Is mandatory fields are set
     *
     * @see 160125_Integration_IT_english (PDF) / teamwork: 1631794
     * @return boolean
     */
    public function checkMandatoryFields()
    {
        /** @var  $mandatoryFields caontain the mandatories (field => '') */
        $mandatoryFields = $this->_getMandatoryFields();
        foreach ($mandatoryFields as $mandatoryField => $defaultValue) {
            if (false === array_key_exists($mandatoryField, $this->_urlItems)) {
                /** @todo log the specific error
                 * Sleepz_Adnymics_Model_Abstract::log();*/
                return false;
            }
        }
        return true;
    }

    /**
     * Get the mandatory fields array back
     *
     * @return array
     */
    protected function _getMandatoryFields()
    {
        return $this->_mandatoryFields;
    }

    /**
     * Set the Adnymics Server/Path- Url
     */
    protected function _setAdnymicsUrl()
    {
        $this->_adnymics_server_path = self::REST_API_SERVER . '/' . self::REST_API_SERVER_PATH . '?';
    }

    /**
     * @return string
     */
    protected function _getAdnymicsUrl()
    {
        return $this->_adnymics_server_path;
    }

    /**
     * connect with adnymics rest server
     *
     * @throws Exception $e
     */
    public function generateBrochure()
    {
        /** @var  $urlWithQuery generate the full url */
        $urlWithQuery = $this->_getAdnymicsUrl() . $this->_urlquery;
        try {
            /** @var  $state contain the state of this call to adynmics */
            $state = Httpful\Request::init()
                ->method(\Httpful\Http::GET)
                ->uri($urlWithQuery)
                ->withoutStrictSsl()
                ->authenticateWith(
                    self::REST_API_USER,
                    self::REST_API_PASSWORD)
                ->send();

            if ($state->code <> 200) {
                $collectErrorMsgs = array('http_req' => $state, 'adnymics_ulr' => rawurldecode($urlWithQuery));
                Sleepz_Adnymics_Model_Abstract::log($collectErrorMsgs);
            }

        } catch (Exception $e) {
            $collectErrorMsgs = array('exception' => $e, 'http_req' => $state, 'adnymics_ulr' => rawurldecode($urlWithQuery));
            Sleepz_Adnymics_Model_Abstract::log($collectErrorMsgs);
        }
    }

    /**
     * Write the Log- File
     *
     * @param $errors array
     */
    public static function log($errors)
    {
        $errorArrayToString = implode('::', $errors);
        Mage::log($errorArrayToString, null, self::ADNYMICS_ERROR_FILE);
    }

    /**
     * Generate the Params to send to REST server (broschure)
     * Validation of all incoming order data for redirect to adnymics
     *
     * @param $orderObject
     */
    public function call($orderObject)
    {
        try {
            /** saves the order id */
            if ($orderObject->getEntityId()) {
                $this->addItem('orderId', $orderObject->getEntityId());
            }

            /** Check If exist are Customer Id when not use the email address */
            if ($orderObject->getCustomerId()) {
                $this->addItem('customerId', $orderObject->getCustomerId());
            } else {
                $this->addItem('customerId', $orderObject->getCustomerEmail());
            }

            /** saves the firstname */
            if ($orderObject->getCustomerFirstname()) {
                $this->addItem('customerName', $orderObject->getCustomerFirstname());
            }

            /** saves the lastname */
            if ($orderObject->getCustomerLastname()) {
                $this->addItem('customerSurname', $orderObject->getCustomerLastname());
            }

            /** save the coupon wehen  */
            if ($orderObject->getCouponCode()) {
                $this->addItem('orderCouponCode', $orderObject->getCouponCode());
            }

            /**
             * Fast facts about the behavior of all users logged on or Registered or guest accounts:
             *
             * 1. Active users are in real terms stored in the user table (CUSTOMER)
             * 2. Registered users are almost identical to logged user but only
             * after the inquiry of your request in the user table available (Registered Customer before saving)
             * 3. Guest Additions are available only temporarily and are deleted ordering process after (USER)
             */
            if (Mage::getSingleton('customer/session')->isLoggedIn() || $orderObject->getCustomerId()) {
                $customer = Mage::getModel('customer/customer')->load($orderObject->getCustomerId());
                $defaultBillingAddress = $customer->getDefaultBillingAddress();
                $gender = Mage::helper('adnymics')->getGenderIdByOptionText($defaultBillingAddress->getPrefix());
                if ($gender) {
                    $this->addItem('customerGender', $gender['result']);
                }
            } elseif ($orderObject->getCustomerIsGuest()) {
                $gender = Mage::helper('adnymics')->getGenderIdByOptionText($orderObject->getCustomerPrefix());
                if ($gender) {
                    $this->addItem('customerGender', $gender['result']);
                }
            }

            /** saves the email address */
            if ($orderObject->getCustomerEmail()) {
                $this->addItem('customerEMail', $orderObject->getCustomerEmail());
            }

            /** Saves the Street data */
            if ($orderObject->getShippingAddress()->getStreet()) {
                $streetObject = array_shift($orderObject->getShippingAddress()->getStreet());
                if ($streetObject) {
                    /** @var  $streetObject use only the first entry of array */
                    $this->addItem('custAddrStreet', $streetObject);
                }
            }

            /** Saves the Postcode */
            if ($orderObject->getShippingAddress()->getPostcode()) {
                $this->addItem('custAddrZip', $orderObject->getShippingAddress()->getPostcode());
            }

            /** Saves the country id */
            if ($orderObject->getShippingAddress()->getCountryId()) {
                $this->addItem('custAddrCountry', $orderObject->getShippingAddress()->getCountryId());
            }

            /** @var  $orderData contain the products */
            $orderData = $orderObject->getAllItems();
            if ($orderData) {
                foreach ($orderData as $item) {
                    $this->addItem('productId', $item->getSku());
                }
            }

            /**  */
            if ($this->hasItems() && $this->checkMandatoryFields()) {
                $this->encodeItems();
                $this->runQuery();
                $this->generateBrochure();
            }
        } catch (Exception $e) {

        }
    }

    /**
     * Add an Item(s) to the Object
     *
     * @internal Allowed multiple Items with same name into the array
     * @example
     * @param    $key       string
     * @param    $value     mixed
     * @throws   Exception
     */
    public function addItem($key, $value)
    {
        if ('productId' == $key) {
            $this->_urlItems[] = array($key => $value);
        } else {
            if (isset($this->_urlItems[$key])) {
                throw new Exception("Key $key already use.");
            } else {
                $this->_urlItems[$key] = $value;
            }
        }
    }

    /**
     * Encode all Items with urlencode
     *
     * @see http://php.net/manual/en/function.urlencode.php
     * @see http://stackoverflow.com/questions/4744888/how-to-properly-url-encode-a-string-in-php
     *
     * based on the following information RFC 3986
     */
    public function encodeItems()
    {
        $allItems = $this->_urlItems;
        foreach ($allItems as $key => $value) {
            if (is_numeric($key)) continue;
            $this->_urlItems[$key] = rawurlencode($value);
        }
    }

    /**
     * Check has the _urlItems Object values
     *
     * @return bool
     */
    public function hasItems()
    {
        if (empty($this->_urlItems)) {
            return false;
        }
        return true;
    }

    /**
     * Execute the Array items in $this->_urlItems to URL Query
     */
    public function runQuery()
    {
        $urlItems = array();
        foreach ($this->_urlItems as $keyName => $item) {
            if (is_int($keyName)) {
                foreach ($item as $name => $value) {
                    $urlItems[] = "$name=$value";
                }
            } else {
                $urlItems[] = "$keyName=$item";
            }
        }
        $this->_urlquery = implode('&', $urlItems);
    }
}
