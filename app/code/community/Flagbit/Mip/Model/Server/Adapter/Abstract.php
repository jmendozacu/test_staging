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
abstract class Flagbit_Mip_Model_Server_Adapter_Abstract
    extends Varien_Object
    implements Flagbit_Mip_Model_Server_Adapter_Interface  {

    protected $_config = null;

    /**
     * input Data
     *
     * @param string $resource
     * @param string $action
     * @param string $data
     * @return array
     */
    public function input($resource, $action, $data) {

        Mage::dispatchEvent('mip_adapter_'.$resource.'_'.$action.'_input', array('data' => $data, 'adapter' => $this));

        return $data;
    }

    /**
     * output Data
     *
     * @param string $resource
     * @param string $action
     * @param string $data
     * @return string
     */
    public function output($resource, $action, $data)
    {
        Mage::dispatchEvent('mip_adapter_'.$resource.'_'.$action.'_output', array('data' => $data, 'adapter' => $this));

        return $data;
    }

    /**
     * get trigger object
     *
     * @return Flagbit_Mip_Model_Server_Trigger_Abstract
     */
    public function getTrigger() {

        return $this->getData('trigger');
    }

    /**
     * get Interface Path
     *
     * @return string
     */
    public function getPath() {

        $module = $this->getConfig('module');
        $interface = $this->getInterface();

        return Mage::getConfig()->getModuleDir(null , $module).DS.'interfaces'.DS.$interface.DS;
    }

    /**
     * get Handler Model
     *
     * @return Flagbit_Mip_Model_Server_Handler_Abstract
     */
    public function getHandler(){

        $handlerClass = $this->getHandlerModel();
        return Mage::getSingleton($handlerClass);
    }

    /**
     * post filter data throw Flagbit_Mip_Model_Filter
     *
     * @deprecated use postFilterData instead
     * @param string $data
     * @return string
     */
    public function filterData($data)
    {
        return $this->postFilterData($data);
    }

    /**
     * post filter data throw Flagbit_Mip_Model_Filter
     *
     * @param string $data
     * @return string
     */
    protected function postFilterData($data)
    {
        if(strpos($data, '{{') !== false){
            $filterModel = Mage::getSingleton('mip/filter');
            $filterModel->setDirectivePrefix('Post');
            $data = $filterModel->filter($data);
        }
        return $data;
    }

     /**
     * pre filter data throw Flagbit_Mip_Model_Filter
     *
     * @param string $data
     * @return string
     */
    protected function preFilterData($data)
    {
        if(strpos($data, '{{') !== false){
            $filterModel = Mage::getSingleton('mip/filter');
            $filterModel->setDirectivePrefix('Pre');
            $data = $filterModel->filter($data);
        }
        return $data;
    }


    /**
     * get Settings
     *
     * @param string|null $key
     * @return Mage_Core_Model_Config_Element | string
     */
    public function getSettings($key = null)
    {
        $value = Flagbit_Mip_Model_Server::getConfig()->getCurrentDefinition()->getSettings($key);
        if($key == 'delimiter' && $value == '\t'){
            $value = "\t";
        }
        return $value;
    }


    /**
     * get Interface Config value by key
     *
     * @param string $key
     * @return mixed
     */
    public function getConfig($key){

        if($this->_config === null){

            $interface = $this->getInterface();

            if(!$interface){
                Mage::throwException(Mage::helper('mip')->__('No interface specified'));
            }

            $interfaces = Mage::getSingleton('mip/config')->getInterfaces();
            $this->_config = (array) $interfaces[$interface];
        }

        return $key ? $this->_config[$key] : $this->_config;
    }

    /**
     * get Resource Model by Name
     *
     * @param string $key
     * @return Flagbit_Mip_Model_Resource_Abstract
     */
    protected function getResourceModel($key){

        $resources = Mage::getSingleton('mip/config')->getResources();
        $resourceModel = Mage::getModel((string) $resources[$key]->model);

        if($resourceModel instanceof Flagbit_Mip_Model_Resource_Abstract){
            return $resourceModel;
        }

        Mage::throwException(Mage::helper('mip')->__('Invalid resourcemodel specified'));
    }
}