<?php
/**
 * This source file is subject to the Magento Integration Platform License
 * that is bundled with this package in the file LICENSE_MIP.txt.
 * It is also available through the world-wide-web at this URL:
 * http://www.flagbit.de/license/mip
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to magento@flagbit.de so we can send you a copy immediately.
 *
 * The Magento Integration Platform is a property of Flagbit GmbH & Co. KG.
 * It is NO part or deravative version of Magento and as such NOT published
 * as Open Source. It is NOT allowed to copy, distribute or change the
 * Magento Integration Platform or any of its parts. If you wish to adapt
 * the software to your individual needs, feel free to contact us at
 * http://www.flagbit.de or via e-mail (magento@flagbit.de) or phone
 * (+49 (0)800 FLAGBIT (3524248)).
 *
 *
 *
 * Dieser Quelltext unterliegt der Magento Integration Platform License,
 * welche in der Datei LICENSE_MIP.txt innerhalb des MIP Paket hinterlegt ist.
 * Sie ist außerdem über das World Wide Web abrufbar unter der Adresse:
 * http://www.flagbit.de/license/mip
 * Falls Sie keine Kopie der Lizenz erhalten haben und diese auch nicht über
 * das World Wide Web erhalten können, senden Sie uns bitte eine E-Mail an
 * magento@flagbit.de, so dass wir Ihnen eine Kopie zustellen können.
 *
 * Die Magento Integration Platform ist Eigentum der Flagbit GmbH & Co. KG.
 * Sie ist WEDER Bestandteil NOCH eine derivate Version von Magento und als
 * solche nicht als Open Source Softeware veröffentlicht. Es ist NICHT
 * erlaubt, die Software als Ganze oder in Einzelteilen zu kopieren,
 * verbreiten oder ändern. Wenn Sie eine Anpassung der Software an Ihre
 * individuellen Anforderungen wünschen, kontaktieren Sie uns unter
 * http://www.flagbit.de oder via E-Mail (magento@flagbit.de) oder Telefon
 * (+49 (0)800 FLAGBIT (3524248)).
 *
 *
 *
 * @package     Flagbit
 * @subpackage  Flagbit_Mip
 * @copyright   2009 by Flagbit GmbH & Co. KG
 * @author      Flagbit Magento Team <magento@flagbit.de>
 */


/**
 * @package     Flagbit
 * @subpackage  Flagbit_Mip
 */
class Flagbit_Mip_Model_Observer extends Mage_Cron_Model_Observer
{
    const CACHE_KEY_LAST_SCHEDULE_GENERATE_AT   = 'mip_last_schedule_generate_at';
    const CACHE_KEY_LAST_HISTORY_CLEANUP_AT     = 'mip_last_history_cleanup_at';

    const XML_PATH_SCHEDULE_GENERATE_EVERY  = 'system/cron/schedule_generate_freq';
    const XML_PATH_SCHEDULE_AHEAD_FOR       = 'system/cron/schedule_ahead_for';
    const XML_PATH_SCHEDULE_LIFETIME        = 'system/cron/schedule_lifetime';
    const XML_PATH_HISTORY_CLEANUP_EVERY    = 'system/cron/history_cleanup_every';
    const XML_PATH_HISTORY_SUCCESS          = 'system/cron/history_success_lifetime';
    const XML_PATH_HISTORY_FAILURE          = 'system/cron/history_failure_lifetime';

    protected $_pendingSchedules;

    /**
     * delete Product relations
     *
     * @param Varien_Event_Observer $observer
     */
    public function deleteProduct($observer)
    {
        Mage::getModel('mip/data_relation')
                ->deleteByDatarecord(
                        $observer->getProduct()->getId(),
                        Flagbit_Mip_Model_Resource_Product::RELATION_TYPE
                    );

    }

    /**
     * delete category relations
     *
     * @param Varien_Event_Observer $observer
     */
    public function deleteCategory($observer)
    {
        Mage::getModel('mip/data_relation')
                ->deleteByDatarecord(
                        $observer->getCategory()->getId(),
                        Flagbit_Mip_Model_Resource_Category::RELATION_TYPE
                    );

        // delete child Relation
        $childrenIds = $observer->getCategory()->getDeletedChildrenIds();
        foreach((array) $childrenIds as $id){
            Mage::getModel('mip/data_relation')
                    ->deleteByDatarecord(
                            $id,
                            Flagbit_Mip_Model_Resource_Category::RELATION_TYPE
                        );
        }
    }


    /**
     * add fields like Customer ID from external Systems
     *
     * @param Varien_Event_Observer $observer
     */
    public function addCustomerFields($observer)
    {
        /*@var $_customer Mage_Customer_Model */
        $_customer = $observer->getCustomer();

        if($_customer instanceof Mage_Core_Model_Abstract){

            /*@var $_relations Flagbit_Mip_Model_Mysql4_Data_Relation_Collection */
            $_relations = Mage::getResourceModel('mip/data_relation_collection');
            $_relations->addFieldToFilter('resource_type', array('eq' => Flagbit_Mip_Model_Resource_Customer::RELATION_TYPE))
                    ->addFieldToFilter('mage_type', array('eq' => Flagbit_Mip_Model_Resource_Customer::RELATION_TYPE))
                    ->addFieldToFilter('mage_id', array('eq' => $_customer->getId()));
            $_relations->load();

            foreach($_relations as $_relation){
                $_customer->setData('external_id_'.$_relation->getInterface(), $_relation->getExtId());
            }
        }
    }


    /**
     * add fields like Streetnumber to Customer Address
     *
     * @param Varien_Event_Observer $observer
     */
    public function addAddressFields($observer)
    {
        /** @var $_address Mage_Sales_Model_Order_Address */
        $_address = $observer->getAddress();

        if($_address instanceof Mage_Core_Model_Abstract){

            preg_match('/^(\D+)(\d.*)/', $_address->getData('street'), $matches);
            if(isset($matches[1])){
                $_address->setData('streetname', trim($matches[1]));
            }
            if(isset($matches[2])){
                $_address->setData('house_number', trim($matches[2]));
            }
        }
    }

    /**
     * delete customer relations
     *
     * @param Varien_Event_Observer $observer
     */
    public function deleteCustomer($observer)
    {
        if($observer->getCustomer() instanceof Mage_Customer_Model_Customer){
            Mage::getModel('mip/data_relation')
                    ->deleteByDatarecord(
                            $observer->getCustomer()->getId(),
                            Flagbit_Mip_Model_Resource_Customer::RELATION_TYPE
                        );
        }
    }

    /**
     * delete attribute relations
     *
     * @param Varien_Event_Observer $observer
     */
    public function deleteAttribute($observer)
    {
        Mage::getModel('mip/data_relation')
                ->deleteByDatarecord(
                        $observer->getAttribute()->getId(),
                        Flagbit_Mip_Model_Resource_Attribute::RELATION_TYPE
                    );
    }



    /**
     * handle Magento events throw MIP events
     *
     * @param Varien_Event_Observer $observer
     */
    public function handleEvents($observer)
    {
        $mipEvents = Mage::getResourceModel('mip/config_importexport_collection')
            ->addFieldToFilter('type', 'event')
            ->addFieldToFilter('code', array('like' => $observer->getEvent()->getName().'%'))
            ->load();

        if(!$mipEvents->count()){
            return;
        }
        Mage::helper('mip/log')->getWriter($this)->info('EVENT dispatch '.$observer->getEvent()->getName());

        foreach($mipEvents as $mipEvent){

            try{
                $server = Mage::getSingleton('mip/server');    // get Server
                $server->init($mipEvent); // init Server
                $server->getTrigger()->addData($observer->getData()); // add Observer Data to Trigger

                // run Server
                $server->run(false);

            }catch (Exception $e){
                Mage::helper('mip/log')->getWriter($this)->error('Error: '.$e->getMessage(). $e);
            }
        }
    }



    /**
     * Process cron queue
     * Geterate tasks schedule
     * Cleanup tasks schedule
     *
     * @param Varien_Event_Observer $observer
     */
    public function dispatch($observer)
    {

        Mage::helper('mip/log')->getWriter($this)->info('CRON dispatch cronjobs');

        $schedules = $this->getPendingSchedules();
        $scheduleLifetime = Mage::getStoreConfig(self::XML_PATH_SCHEDULE_LIFETIME) * 60;
        $now = time();
        $jobsRoot = $this->_getCronJobs();

        if($jobsRoot->count()){

            foreach ($schedules->getIterator() as $schedule) {

                $jobConfig = $jobsRoot->getItemsByColumnValue('code', $schedule->getJobCode());

                if(!isset($jobConfig[0])){
                    continue;
                }
                $jobConfig = $jobConfig[0];

                if (!($jobConfig instanceof Flagbit_Mip_Model_Config_Importexport_Cron) ) {
                    continue;
                }

                $time = strtotime($schedule->getScheduledAt());
                if ($time > $now) {
                    continue;
                }

                try {
                    $errorStatus = Mage_Cron_Model_Schedule::STATUS_ERROR;

                    if ($time < $now - $scheduleLifetime) {
                        $errorStatus = Mage_Cron_Model_Schedule::STATUS_MISSED;
                        #Mage::throwException(Mage::helper('cron')->__('Too late for the schedule'));
                    }

                    $schedule->setExecutedAt(strftime('%Y-%m-%d %H:%M:%S', time()));
                    $schedule->setStatus(Mage_Cron_Model_Schedule::STATUS_RUNNING)
                        ->save();

                    Mage::helper('mip/log')->getWriter($this)->trace('CRON run '.$jobConfig->getCode());
                    $this->runServer($jobConfig);

                    $schedule->setStatus(Mage_Cron_Model_Schedule::STATUS_SUCCESS)
                        ->setFinishedAt(strftime('%Y-%m-%d %H:%M:%S', time()));

                } catch (Exception $e) {
                    Mage::helper('mip/log')->getWriter($this)->error('CRON ERROR '.$e->getMessage(), $e);
                    $schedule->setStatus($errorStatus)
                        ->setMessages($e->getMessage());
                }
                $schedule->save();
            }
        }

        $this->generate();
        $this->cleanup();
    }

    /**
     * Generate cron schedule
     *
     * @return Mage_Cron_Model_Observer
     */
    public function generate()
    {

        /**
         * check if schedule generation is needed
         */
        $lastRun = Mage::app()->loadCache(self::CACHE_KEY_LAST_SCHEDULE_GENERATE_AT);
        if ($lastRun > time() - Mage::getStoreConfig(self::XML_PATH_SCHEDULE_GENERATE_EVERY)*60) {
            return $this;
        }

        $schedules = $this->getPendingSchedules();
        $exists = array();
        foreach ($schedules->getIterator() as $schedule) {
            $exists[$schedule->getJobCode().'/'.$schedule->getScheduledAt()] = 1;
        }

        /**
         * generate crontab jobs
         */

        /** @var $cronjobs Flagbit_Mip_Model_Mysql4_Config_Importexport_Collection */
        $cronjobs = $this->_getCronJobs();

        if ($cronjobs->count()) {
            $cronjobsConverted = array();
            foreach($cronjobs as $cronjob){
                $cronjobsConverted[$cronjob->getCode()] = $cronjob;
            }

            $this->_generateJobs($cronjobsConverted, $exists);
        }

        /**
         * save time schedules generation was ran with no expiration
         */
        Mage::app()->saveCache(time(), self::CACHE_KEY_LAST_SCHEDULE_GENERATE_AT, array('crontab'), null);

        return $this;
    }

    /**
     * @return Flagbit_Mip_Model_Mysql4_Config_Importexport_Collection
     */
    protected function _getCronJobs()
    {
        return Mage::getResourceModel('mip/config_importexport_collection')->addFieldToFilter('type', 'cron')->load();
    }

    /**
     * run MIP Server
     *
     * @param $settings
     */
    protected function runServer(Flagbit_Mip_Model_Config_Importexport_Interface $definition)
    {
        $server = Mage::getSingleton('mip/server');
        $server->init($definition);
        $server->run();
    }

}



