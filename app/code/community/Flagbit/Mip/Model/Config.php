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
class Flagbit_Mip_Model_Config extends Varien_Simplexml_Config
{
    const CACHE_TAG         = 'config_mip';
    protected $_definition  = null;

    /**
     * Constructor
     *
     * @see Varien_Simplexml_Config
     */
    public function __construct($sourceData=null)
    {
        $this->setCacheId('config_mip');
        $this->setCacheTags(array(self::CACHE_TAG));

        parent::__construct($sourceData);
        $this->_construct();
    }

    /**
     * Init configuration for webservices api
     *
     * @return Flagbit_Mip_Model_Config
     */
    protected function _construct()
    {
        if (Mage::app()->useCache('config_mip')) {
            if ($this->loadCache()) {
                return $this;
            }
        }
        $mergeConfig = Mage::getModel('core/config_base');

        $config = Mage::getConfig();
        $modules = $config->getNode('modules')->children();

        // check if local modules are disabled
        $disableLocalModules = (string)$config->getNode('global/disable_local_modules');
        $disableLocalModules = !empty($disableLocalModules) && (('true' === $disableLocalModules) || ('1' === $disableLocalModules));

        $configFile = $config->getModuleDir('etc', 'Flagbit_Mip').DS.'mip.xml';


        if ($mergeConfig->loadFile($configFile)) {
            $config->extend($mergeConfig, true);
        }

        foreach ($modules as $modName=>$module) {
            if ($module->is('active')) {
                if (($disableLocalModules && ('local' === (string)$module->codePool)) || $modName=='Flagbit_Mip') {
                    //continue;
                }

                $configFile = $config->getModuleDir('etc', $modName).DS.'mip.xml';

                if ($mergeConfig->loadFile($configFile)) {
                    $config->extend($mergeConfig, true);
                }elseif(file_exists($configFile)){
                    Mage::helper('mip/log')->getWriter($this)->fatal('Cannot load Config File: '.$configFile);
                }
            }
        }

        $this->setXml($config->getNode('mip'));

        if (Mage::app()->useCache('config_mip')) {
            $this->saveCache();
        }
        return $this;

    }

    /**
     * get Cronjobs
     *
     * @param string $name
     * @return Flagbit_Mip_Model_Config_Importexport_Cron | array
     */
    public function getCron($code = null)
    {
        return Mage::getSingleton('mip/config_importexport')->getCronjob($code);
    }

    /**
     * get Request Config
     *
     * @param string $name
     * @return Flagbit_Mip_Model_Config_Importexport_Request | array
     */
    public function getRequest($code = null)
    {
        return Mage::getSingleton('mip/config_importexport')->getRequest($code);
    }

    /**
     * get Events
     *
     * @param string $name
     * @return Flagbit_Mip_Model_Config_Importexport_Event | array
     */
    public function getEvent($code = null)
    {
        return Mage::getSingleton('mip/config_importexport')->getEvent($code);
    }

    /**
     * @param Flagbit_Mip_Model_Config_Importexport_Interface $definition
     * @return Flagbit_Mip_Model_Config
     */
    public function setCurrentDefinition(Flagbit_Mip_Model_Config_Importexport_Interface $definition)
    {
        $this->_definition = $definition;
        return $this;
    }

    /**
     * @return Flagbit_Mip_Model_Config_Importexport_Interface
     */
    public function getCurrentDefinition()
    {
        return $this->_definition;
    }

    /**
     * get Interfaces
     *
     * @return array
     */
    public function getInterfaces(){

        $interfaces = array();
        $use = null;
        foreach ($this->getNode('interfaces')->children() as $interfaceName => $interface) {
            /* @var $interface Varien_SimpleXml_Element */
            if (isset($interface->use)) {
            	$use = $interface->use;
                $interface = $this->getNode('interfaces/' . (string) $use);
                $interface->name = (string) $use;
            }
            $interfaces[$interfaceName] = $interface;
        }
        return $interfaces;
    }


    /**
     * Retrieve all adapters
     *
     * @return array
     */
    public function getAdapters()
    {
        $adapters = array();
        foreach ($this->getNode('adapters')->children() as $adapterName => $adapter) {
            /* @var $adapter Varien_SimpleXml_Element */
            if (isset($adapter->use)) {
                $adapter = $this->getNode('adapters/' . (string) $adapter->use);
            }
            $adapters[$adapterName] = $adapter;
        }
        return $adapters;
    }

    /**
     * Retrieve all triggers
     *
     * @return array
     */
    public function getTriggers()
    {
        $triggers = array();
        foreach ($this->getNode('triggers')->children() as $triggerName => $trigger) {
            /* @var $trigger Varien_SimpleXml_Element */
            $triggers[$triggerName] = $trigger;
        }
        return $triggers;
    }

    /**
     * get Processors
     *
     * @return array
     */
    public function getProcessors(){
    	$processors = array();
        foreach ($this->getNode('processors')->children() as $processorName => $processor) {
         	if (isset($processor->use)) {
                $processor = $this->getNode('processor/' . (string) $processor->use);
            }
        	$processors[$processorName] = $processor;
        }
        return $processors;
    }

    /**
     * Retrieve all resources
     *
     * @return array
     */
    public function getResources()
    {
        $resources = array();
        foreach ($this->getNode('resources')->children() as $resourceName => $resource) {
            /* @var $trigger Varien_SimpleXml_Element */
            $resources[$resourceName] = $resource;
        }
        return $resources;
    }

    /**
     * Retrieve active adapters
     *
     * @return array
     */
    public function getActiveAdapters()
    {
        $adapters = array();
        foreach ($this->getAdapters() as $adapterName => $adapter) {
            if (!isset($adapter->active) || $adapter->active == '0') {
                continue;
            }

            if (isset($adapter->required) && isset($adapter->required->extensions)) {
                foreach ($adapter->required->extensions->children() as $extension=>$data) {
                    if (!extension_loaded($extension)) {
                        continue;
                    }
                }
            }

            $adapters[$adapterName] = $adapter;
        }

        return $adapters;
    }

    /**
     * Retrieve handlers
     *
     * @return Varien_Simplexml_Element
     */
    public function getHandlers()
    {
        return $this->getNode('handlers')->children();
    }


    /**
     * Retrieve resources alias
     *
     * @return Varien_Simplexml_Element
     */
    public function getResourcesAlias()
    {
        return $this->getNode('resources_alias')->children();
    }


    /**
     * Load Acl resources from config
     *
     * @param Mage_Api_Model_Acl $acl
     * @param Mage_Core_Model_Config_Element $resource
     * @param string $parentName
     * @return Mage_Api_Model_Config
     */
    public function loadAclResources(Mage_Api_Model_Acl $acl, $resource=null, $parentName=null)
    {
        if (is_null($resource)) {
            $resource = $this->getNode('acl/resources');
        } else {
            $resourceName = (is_null($parentName) ? '' : $parentName.'/').$resource->getName();
            $acl->add(Mage::getModel('api/acl_resource', $resourceName), $parentName);
        }

        $children = $resource->children();

        if (empty($children)) {
            return $this;
        }

        foreach ($children as $res) {
            if ($res->getName() != 'title' && $res->getName() != 'sort_order') {
                $this->loadAclResources($acl, $res, $resourceName);
            }
        }
        return $this;
    }

    /**
     * Get acl assert config
     *
     * @param string $name
     * @return Mage_Core_Model_Config_Element|boolean
     */
    public function getAclAssert($name='')
    {
        $asserts = $this->getNode('acl/asserts');
        if (''===$name) {
            return $asserts;
        }

        if (isset($asserts->$name)) {
            return $asserts->$name;
        }

        return false;
    }

    /**
     * Retrieve privilege set by name
     *
     * @param string $name
     * @return Mage_Core_Model_Config_Element|boolean
     */
    public function getAclPrivilegeSet($name='')
    {
        $sets = $this->getNode('acl/privilegeSets');
        if (''===$name) {
            return $sets;
        }

        if (isset($sets->$name)) {
            return $sets->$name;
        }

        return false;
    }

	/**
	 * get Faults by Resource Name
	 *
	 * @param $resourceName
	 * @return array
	 */
    public function getFaults($resourceName=null)
    {
        if (is_null($resourceName)
            || !isset($this->getResources()->$resourceName)
            || !isset($this->getResources()->$resourceName->faults)) {
            $faultsNode = $this->getNode('faults');
        } else {
            $faultsNode = $this->getResources()->$resourceName->faults;
        }
        /* @var $faultsNode Varien_Simplexml_Element */

        $translateModule = 'api';
        if (isset($faultsNode['module'])) {
           $translateModule = (string) $faultsNode['module'];
        }

        $faults = array();
        foreach ($faultsNode->children() as $faultName => $fault) {
            $faults[$faultName] = array(
                'code'    => (string) $fault->code,
                'message' => Mage::helper($translateModule)->__((string)$fault->message)
            );
        }

        return $faults;
    }

    /**
     * Retrieve cache object
     *
     * @return Zend_Cache_Frontend_File
     */
    public function getCache()
    {
        return Mage::app()->getCache();
    }

    /**
     * load Cache by ID
     *
     * @param string $id
     */
    protected function _loadCache($id)
    {
        return Mage::app()->loadCache($id);
    }

    /**
     * save Cache
     *
     * @param mixed $data
     * @param string $id
     * @param array $tags
     * @param int $lifetime
     */
    protected function _saveCache($data, $id, $tags=array(), $lifetime=false)
    {
        return Mage::app()->saveCache($data, $id, $tags, $lifetime);
    }

    /**
     * remove Cache by Id
     *
     * @param string $id
     */
    protected function _removeCache($id)
    {
        return Mage::app()->removeCache($id);
    }
}