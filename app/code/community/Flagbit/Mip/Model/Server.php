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

class Flagbit_Mip_Model_Server extends Varien_Object
{

    private static $_instance = null;


    /**
     * Mip adapter
     *
     * @var Flagbit_Mip_Model_Server_Adapter_Interface
     */
    protected $_adapter;

    /**
     * last Exception
     *
     * @var Exception | null
     */
    protected $_lastException = null;

    /**
     * MIP Trigger
     *
     * @var Flagbit_Mip_Model_Server_Trigger_Abstract
     */
    protected $_trigger;

    protected $_instanceCheck = true;


    /**
     * is there already a mip server instance?
     *
     * @return bool
     */
    public static function getIfInstanceExists()
    {
        return (self::$_instance instanceof Flagbit_Mip_Model_Server);
    }

    /**
     * @throws Exception
     */
    public function __construct()
    {
        if(self::getIfInstanceExists()){
            throw new Exception('There is already a Instance, please use getSingleton to instantiate.');
        }
    }

    /**
     * init Server
     *
     * @param Flagbit_Mip_Controller_Action $controller
     * @param Flagbit_Mip_Model_Config_Importexport_Interface $definition
     * @return Flagbit_Mip_Model_Server
     */
    public function init(Flagbit_Mip_Model_Config_Importexport_Interface $definition)
    {
        // init Logger
        $logHelper = Mage::helper('mip/log');
        $logHelper->initMageLogger( $definition->getSettings('debug') ? Flagbit_Mip_Helper_Log::RUN_MODE_TRACE : Flagbit_Mip_Helper_Log::RUN_MODE_LOG);
        $logHelper->setNamePrefix($definition->getCode() ? $definition->getCode() : 'unknown');
        $logHelper->getWriter($this)->info('INIT MIP Server');

        // register shutdown Handler for Error Handling
        register_shutdown_function(array('Flagbit_Mip_Model_Server', 'shutdownHandler'));

        // get Triggers and set it
        $triggers = self::getConfig()->getTriggers();

        if(isset($triggers[$definition->getSettings('trigger')])) {

            // set definition Object
            self::getConfig()->setCurrentDefinition($definition);

            // instantiate Trigger Object
            $this->_trigger = Mage::getSingleton((string) $triggers[$definition->getSettings('trigger')]->model);

            // inject some Objects to Trigger
            Mage::helper('mip/object')->inject($this, $this->getTrigger());

            // init Trigger
            $this->getTrigger()->init();

            Mage::helper('mip/log')->getWriter($this)->info('INIT: '.$this->getTrigger()->getInterface().' -> '.substr(get_class($this->getTrigger()), 33).' -> '.substr(get_class($this->getTrigger()->getAdapter()), 33));

        }else{
            Mage::throwException(Mage::helper('mip')->__('Invalid Trigger ('.$definition->getSettings('trigger').') specified'));
        }

        return $this;
    }

    /**
     * get MIP Config Object
     *
     * @return Flagbit_Mip_Model_Config
     */
    public static function getConfig()
    {
        return Mage::getSingleton('mip/config');
    }

    /**
     * Hook to php shutdown handler
     *
     * This method is registered via register_shutdown_handler and
     * is used to grab fatal errors and handly them gracefully where
     * possible
     *
     * @return void
     */
    public static function shutdownHandler() {
       $error = error_get_last();
       if ($error && (
             ($error['type'] == E_ERROR) ||
             ($error['type'] == E_PARSE) ||
             ($error['type'] == E_RECOVERABLE_ERROR))) {
          $msg = $error['message'] . "\nLine: " . $error['line'] . ' - File: ' . $error['file'];

          if (class_exists('Mage')) {
            Mage::helper('mip/log')->getWriter( Mage::getSingleton('mip/server') )->fatal($msg);

            Mage::getSingleton('mip/task')
                ->setStatus('error')
                ->setMessages($msg)
                ->save();
          }
       }
    }

    /**
     * is Action queueable
     *
     * @return boolean
     */
    public function isQueueable()
    {
        $flag = false;
        if(self::getConfig()->getCurrentDefinition() instanceof Flagbit_Mip_Model_Config_Importexport_Interface){
            $flag = self::getConfig()->getCurrentDefinition()->isQueueable();
        }
        return $flag;
    }

    /**
     * Run MIP server
     */
    public function run($instanceCheck = true)
    {

        $this->_instanceCheck = $instanceCheck;

        if($this->isInstanceAlreadyRunning() && $instanceCheck === true && !$this->isQueueable()){

            Mage::helper('mip/log')->getWriter($this)->info('MIP Server is already running');

        }else{
            $oldStore = Mage::app()->getStore()->getId();
            Mage::app()->setCurrentStore(Mage::getModel('core/store')->load(Mage_Core_Model_App::ADMIN_STORE_ID));
            Mage::app()->loadArea(Mage_Core_Model_App_Area::AREA_ADMIN);
            Mage::app()->loadArea(Mage_Core_Model_App_Area::AREA_ADMINHTML);

            try{
                Mage::helper('mip/log')->getWriter($this)->info('RUN MIP Server');

                $this->getTaskModel()->setData(
                    array(
                        'status' => 'running',
                        'interface' => $this->getTrigger()->getInterface(),
                        'resource' => $this->getTrigger()->getResource(),
                        'action' => $this->getTrigger()->getAction(),
                        'direction' => $this->getTrigger()->getDirection()
                    )
                )->save();

                Mage::helper('mip/log')->getWriter($this)->info('instanceCheck: '.$instanceCheck);
                Mage::helper('mip/log')->getWriter($this)->info('isQueueable: '.$this->isQueueable());

                if($instanceCheck === true && !$this->isQueOnly()){
                    $this->setLock();
                }

                try{
                    Mage::register('mip_run', true, true);
                    set_time_limit(0);
                    $this->getTrigger()->run();
                    $this->getTaskModel()->setStatus('success')->save();

                }catch (Exception  $e){
                    try{
                        $this->getTaskModel()
                            ->setStatus('error')
                            ->setMessages($e->getMessage())
                            ->save();
                    }catch (Exception $e){}
                    Mage::helper('mip/log')->getWriter($this)->error($e->getMessage(), $e);
                    Mage::helper('mip/error')->handleException($e);
                    $this->_lastException = $e;
                }


                Mage::helper('mip/log')->getWriter($this)->info('STOP MIP Server');

            }catch (Exception $e){
                try{
                    $this->getTaskModel()
                        ->setStatus('error')
                        ->setMessages($e->getMessage())
                        ->save();
                }catch (Exception $e){}
                Mage::helper('mip/log')->getWriter($this)->error($e->message(), $e);
                $this->_lastException = $e;
                throw $e;
            }
            Mage::app()->setCurrentStore($oldStore);
        }
    }

    /**
     * The Process is only for queueing
     */
    public function isQueOnly(){
        $flag = false;
        if(self::getConfig()->getCurrentDefinition() instanceof Flagbit_Mip_Model_Config_Importexport_Interface){
            $flag = self::getConfig()->getCurrentDefinition()->getSetting('que_only');
        }
        return $flag;
    }

    /**
     * are there any Errors ?!?
     *
     * @return bool
     */
    public function hasError()
    {
        return $this->_lastException === NULL ? false : true;
    }

    /**
     * get the last Exception if there is one
     *
     * @return Exception|null
     */
    public function getLastException()
    {
        return $this->_lastException;
    }

    /**
     * get Task Model
     *
     * @return Flagbit_Mip_Model_Task
     */
    public function getTaskModel(){

        return Mage::getSingleton('mip/task');
    }

    /**
     * is the Server already running?
     */
    public function isInstanceAlreadyRunning()
    {
        if($this->_instanceCheck === false){
            return false;
        }

        if($this->getTaskModel()->getStatus() === null){

            return $this->getTaskModel()->countRunningLockTasks();
        }
        return $this->getTaskModel()->getStatus() == 'running' ? false : true;
    }

    /**
     * set Lock to prevent Running of multiple Instances
     *
     * @return Flagbit_Mip_Model_Server
     */
    public function setLock()
    {
        $this->getTaskModel()->setLock(true)->save();

        return $this;
    }

    /**
     * Retrieve MIP adapter
     *
     * @return Flagbit_Mip_Model_Server_Trigger_Abstract
     */
    public function getTrigger()
    {
        return $this->_trigger;
    }

    /**
     * Retrieve web service adapter
     *
     * @return Flagbit_Mip_Model_Server_Adapter_Interface
     */
    public function getAdapter()
    {
        return $this->_adapter;
    }
}