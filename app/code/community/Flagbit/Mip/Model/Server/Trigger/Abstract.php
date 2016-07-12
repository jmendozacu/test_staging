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
abstract class Flagbit_Mip_Model_Server_Trigger_Abstract extends Varien_Object {

    protected $_paramsStartTag = '{{';
    protected $_paramsEndTag = '}}';

    protected $_adapter;
    protected $_processor;

    /**
     * input Data
     *
     * @param mixed $data
     * @return boolean
     */
    protected final function input($data) {

        $dataArray = $this->getAdapter()->input(
                        $this->getResource(),
                        $this->getAction(),
                        $data
                    );


        $result = $this->getHandler()->input(
                        $this->getResource(),
                        $this->getAction(),
                        $dataArray
                    );

        if($result){
            return true;
        }else{
            return false;
        }
    }

    /**
     * output Data
     *
     * @param array $params
     * @return string
     */
    protected final function output($params = array()) {

        $dataArray = $this->getHandler()->output(
                        $this->getResource(),
                        $this->getAction(),
                        $params
                    );

        return $this->getAdapter()->output(
                        $this->getResource(),
                        $this->getAction(),
                        $dataArray
                    );
    }


    /**
     * handle Params
     * replace Variable Placeholder with Values by handleVariables function
     *
     * @param array
     */
    public function handleParams($params){

        if(is_array($params) && count($params)){
            $paramsArray = array();

            foreach($params as $key => $value){
                $paramsArray[$key] = $this->handleVariables($value);
            }
        }else{
            $paramsArray = (array) $params;
            foreach ($paramsArray as &$param){
                $param = $this->handleVariables($param);
            }
        }
        return $paramsArray;
    }

    /**
     * replace Variable Placeholder with Values
     *
     * @param string $param
     * @return string
     */
    public function handleVariables($param)
    {
        return preg_replace_callback('#'.$this->_paramsStartTag.'(.*?)'.$this->_paramsEndTag.'#', array($this, '_handleVariablesGetDataCallback'), (string) $param );
    }

    /**
     * preg_replace_callback method for handleVariables to call the getData Method
     *
     * @param array $matches
     * @return mixed|null
     */
    protected function _handleVariablesGetDataCallback($matches)
    {
        if(!empty($matches[1])){
            return $this->getData($matches[1]);
        }
        return null;
    }

    /**
     * init Trigger
     *
     * @return Flagbit_Mip_Model_Server_Trigger_Abstract
     */
    public final function init(){

        // set Interface to Adapter
        $this->getAdapter()->setInterface($this->getInterface());

        // set Interface to Handler
        $this->getHandler()->setInterface($this->getInterface());

        return $this;
    }

    /**
     * Retrieve MIP controller
     *
     * @return Flagbit_Mip_Controller_Action
     */
    public function getController()
    {
        return $this->getData('controller');
    }

    /**
     * Retrieve MIP Adapter
     *
     * @return Flagbit_Mip_Model_Server_Adapter_Interface
     */
    public function getAdapter()
    {
        if(!$this->_adapter instanceof Flagbit_Mip_Model_Server_Adapter_Abstract){

            $adapter = $this->getAdapterName();
            $adapters = Mage::getSingleton('mip/config')->getActiveAdapters();

            if (empty($adapters[$adapter])) {
                Mage::throwException(Mage::helper('mip')->__('Invalid interface adapter specified'));
            }

            $this->_adapter = Mage::getModel((string) $adapters[$adapter]->model);

            /* @var $adapterModel Flagbit_Mip_Model_Server_Adapter_Interface */
            if (!$this->_adapter instanceof Flagbit_Mip_Model_Server_Adapter_Interface) {
                Mage::throwException(Mage::helper('mip')->__('Invalid interface adapter specified'));
            }
            $this->_adapter->setTrigger($this);
        }
        return $this->_adapter;
    }

    /**
     * get current Adapter Name
     *
     * @return string
     */
    public function getAdapterName(){

        if(! $this->_getDefinition()->getSettings('adapter')){
            return $this->getInterfaceConfig('adapter');
        }
        return $this->_getDefinition()->getSettings('adapter');
    }

    /**
     * get current Handler Name
     *
     * @return string
     */
    public function getHandlerName(){

        if(!$this->_getDefinition()->getSettings('handler')){
            return $this->getInterfaceConfig('handler');
        }
        return $this->_getDefinition()->getSettings('handler');
    }

    /**
     * get current Processor Name
     *
     * @return string
     */
    public function getProcessorName(){

        if(!$this->_getDefinition()->getSettings('processor')){
            return $this->getAdapterConfig('processor');
        }
        return $this->_getDefinition()->getSettings('processor');
    }

    /**
     * get Processor Instance
     *
     * @return Flagbit_Mip_Model_Server_Adapter_Processor_Interface
     */
    public function getProcessor(){

        if(!$this->_processor instanceof Flagbit_Mip_Model_Server_Adapter_Processor_Interface){

            // get the Handler and set it
            $processors = Mage::getSingleton('mip/config')->getProcessors();
            $processorName = $this->getProcessorName();

            if (!isset($processors[$processorName])) {
                Mage::throwException(Mage::helper('mip')->__('Invalid interface processor specified'));
            }

            $this->_processor = Mage::getModel((string) $processors[$processorName]->model);
        }
        return $this->_processor;
    }

    /**
     * Retrieve MIP Handler
     *
     * @return Flagbit_Mip_Model_Server_Handler_Interface
     */
    public function getHandler()
    {

        if(!$this->handler instanceof Flagbit_Mip_Model_Server_Handler_Abstract){

            // get the Handler and set it
            $handlers = Mage::getSingleton('mip/config')->getHandlers();

            if (!isset($handlers->{$this->getHandlerName()})) {
                Mage::throwException(Mage::helper('mip')->__('Invalid interface handler specified'));
            }

            $this->handler = Mage::getModel((string) $handlers->{$this->getHandlerName()}->model);
        }

        return $this->handler;
    }

    /**
     * get Interface Config Field by Key
     *
     * @param string $key
     * @return string
     */
    public function getInterfaceConfig($key){

        // get Interface and set it
        $interfaces = Mage::getSingleton('mip/config')->getInterfaces();
        $interface = $this->getInterface();

        if(isset($interfaces[$interface])){
            return (string) $interfaces[$interface]->{$key};
        }else{
            Mage::log($interfaces);
            Mage::log($interface);
            Mage::throwException(Mage::helper('mip')->__('Invalid Interface specified ('.$interface.')'));
        }
    }

    /**
     * get Adapter Config Field by key
     *
     * @param string $key
     * @return string
     */
    public function getAdapterConfig($key){

        $adapters = Mage::getSingleton('mip/config')->getAdapters();
        $adapter = $this->getAdapterName();

        if(isset($adapters[$adapter])){
            return (string) $adapters[$adapter]->{$key};
        }else{
            Mage::log($adapters);
            Mage::log($adapter);
            Mage::throwException(Mage::helper('mip')->__('Invalid adapter specified'));
        }
    }

    /**
     * get current definition
     *
     * @return Flagbit_Mip_Model_Config_Importexport_Interface
     */
    protected function _getDefinition()
    {
        return Flagbit_Mip_Model_Server::getConfig()->getCurrentDefinition();
    }

    /**
     * get current Direction Name
     *
     * @return string
     */
    public function getDirection(){
        return $this->_getDefinition()->getSettings('direction');
    }

    /**
     * get current Action Name
     *
     * @return string
     */
    public function getAction(){

        return $this->_getDefinition()->getSettings('action');
    }

    /**
     * get current Resource Name
     *
     * @return string
     */
    public function getResource(){

        return $this->_getDefinition()->getSettings('resource');
    }


    /**
     * return Interface
     *
     * @return string
     */
    public function getInterface(){
        return $this->_getDefinition()->getSettings('interface');
    }
}
