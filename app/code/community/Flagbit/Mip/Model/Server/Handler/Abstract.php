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
abstract class Flagbit_Mip_Model_Server_Handler_Abstract extends Varien_Object
    implements Flagbit_Mip_Model_Server_Handler_Interface
{

    protected $_queued = false;


    /**
     * get Resource Model
     *
     * @param string $resource hallo
     *
     * @return Flagbit_Mip_Model_Resource_Abstract
     */
    protected function getResourceModel($resource)
    {
        $resourceModel = Mage::getModel((string)$this->getResourceConfig($resource)->model);
        if ($resourceModel instanceof Flagbit_Mip_Model_Resource_Interface) {
            return $resourceModel;
        }

        Mage::throwException(Mage::helper('mip')->__('Invalid resourcemodel specified'));
    }

    /**
     * get Resource Config
     *
     * @param string $resource
     * @return Mage_Core_Model_Config_Element
     */
    protected function getResourceConfig($resource)
    {

        $resources = Mage::getSingleton('mip/config')->getResources();

        if (!isset($resources[$resource])) {
            Mage::throwException('Resource-Config for ' . $resource . ' not found!');
        }

        return $resources[$resource];
    }

    /**
     * get real Action Name from Config
     *
     * @param string $actionAlias
     * @param string $resource
     * @param string $direction
     * @return string
     */
    protected function getActionName($actionAlias, $resource, $direction = 'input')
    {

        $method = (string)$this->getResourceConfig($resource)->methods->{$actionAlias}->{$direction . '_method'};
        if (empty($method)) {
            $this->fault('no_' . $direction . '_action_translation', 'No ' . $direction . ' Translation for Action ' . $actionAlias);
        }
        return $method;
    }

    /**
     * input Data to an Resource Models
     *
     * @param string $resource
     * @param string $action
     * @param array $data
     * @return boolan
     */
    public function input($resource, $action, $data)
    {

        return $this->doAction($resource, $action, 'input', $data);
    }

    /**
     * get Settings
     *
     * @param string|null $key
     * @return Mage_Core_Model_Config_Element | string
     */
    public function getSettings($key = null)
    {
        return Flagbit_Mip_Model_Server::getConfig()->getCurrentDefinition()->getSettings($key);
    }

    /**
     * get MIP Server
     *
     * @return Flagbit_Mip_Model_Server
     */
    protected function _getServer()
    {
        return Mage::getSingleton('mip/server');
    }

    /**
     * do Action
     *
     * @param string $resource
     * @param string $action
     * @param string $direction
     * @param array $data
     * @return array
     */
    protected function doAction($resource, $action, $direction, $data = array())
    {

        if (empty($resource)) {
            $this->fault('no_resource');
        }

        if (empty($action)) {
            $this->fault('no_action');
        }

        // get real Action Name
        $method = $this->getActionName($action, $resource, $direction);

        // get Resource Model Object
        $resourceModel = $this->getResourceModel($resource);

        // execute some before import functionality
        $this->_beforeImport($resource, $action, $direction, $data);

        // debugging
        if ($this->getSettings('debug')) {
            $this->_handleDebugOutput($data);
            return;
        }

        // remove up to date Data from import
        if ($this->getSettings('remove_up_to_date_data')) {
            Mage::helper('mip/log')->getWriter($this)->info('remove up to date Data');
            $data = $this->removeUpToDateData($data, $resource);
        }

        // organize Data in Function Call Params
        $params = $this->getActionCallParams($resourceModel, $method, $data, $direction);

        // split Data in parts
        if ($this->getSettings('data_parts_limit') !== null && $this->getSettings('data_parts_size') !== null) {
            $params = $this->handleLargeData($resource, $action, $direction, $params);
        }

        // when mip already runs and action is queueable, add it to queue else stop the action
        if ((
                $this->_getServer()->isInstanceAlreadyRunning()
                && $this->_getServer()->isQueueable()
            )
            || $this->getSettings('que_only') == 'true' && !empty($params['data'])
        ) {

            Mage::helper('mip/log')->getWriter($this)->info('QUEUE: ' . $resource . ' -> ' . $method);
            return Mage::getModel('mip/data_queue')->store($this->getInterface(), $resource, $action, $direction, $params, Flagbit_Mip_Model_Server::getConfig()->getCurrentDefinition());

        } elseif ($this->_getServer()->isInstanceAlreadyRunning()) {

            Mage::helper('mip/log')->getWriter($this)->warn('cannot do something');
            return array();
        }

        Mage::helper('mip/log')->getWriter($this)->info('CALL: ' . $resource . ' -> ' . $method);

        // start Resource
        try {
            $result = call_user_func_array(array($resourceModel, $method), (array)$params);
        } catch (Exception $e) {
            $this->_afterImport($resource, $action, $direction, $result);
            throw $e;
        }

        // execute some after import functionality
        $this->_afterImport($resource, $action, $direction, $result);

        return $result;
    }

    /**
     * handles the debug output
     *
     * @param $data
     */
    protected function _handleDebugOutput($data)
    {
        if (ini_get('xdebug.var_display_max_depth')) {
            ini_set('xdebug.var_display_max_depth', 15);
        }
        Mage::helper('mip/debug')->dump($data);
    }

    /**
     * execute some before import functionality
     *
     * @param $resource
     * @param $action
     * @param $direction
     * @param $data
     */
    protected function _beforeImport($resource, $action, $direction, &$data)
    {
        // dispatch Event for Data manipulation
        $data = $data && is_array($data) ? new ArrayObject($data) : new ArrayObject();
        Mage::helper('mip')->dispatchCustomEvents(Flagbit_Mip_Model_Server::getConfig()->getCurrentDefinition(), 'before', array('data' => $data, 'settings' => $this->getSettings(), 'handler' => $this));
        Mage::dispatchEvent('mip_before_' . $direction . '_' . $resource . '_' . $action, array('data' => $data, 'settings' => $this->getSettings(), 'handler' => $this));
        Mage::dispatchEvent('mip_before_' . $direction, array('data' => $data, 'settings' => $this->getSettings(), 'handler' => $this));
        $data = (array)$data;
    }

    /**
     * execute some after import functionality
     *
     * @param $resource
     * @param $action
     * @param $direction
     * @param $result
     */
    protected function _afterImport($resource, $action, $direction, &$result)
    {
        $result = $result && is_array($result) ? new ArrayObject($result) : new ArrayObject();

        // Dispatch after Action Event
        // if this a queued action than dispatch only after the last queue Item
        if ($this->_getServer()->getTrigger() instanceof Flagbit_Mip_Model_Server_Trigger_Queue) {
            if ($_parentTaskId = $this->getSettings('parent_task_id')) {
                if (!Mage::getResourceModel('mip/data_queue_collection')->filterByParentTaskId($_parentTaskId)->load()->count()) {
                    Mage::dispatchEvent('mip_after_' . $direction, array('result' => $result, 'settings' => $this->getSettings(), 'handler' => $this));
                    Mage::dispatchEvent('mip_after_' . $direction . '_' . $resource . '_' . $action, array('result' => $result, 'settings' => $this->getSettings(), 'handler' => $this));
                    Mage::helper('mip')->dispatchCustomEvents(Flagbit_Mip_Model_Server::getConfig()->getCurrentDefinition(), 'after', array('result' => $result, 'settings' => $this->getSettings(), 'handler' => $this));
                }
            }
        } elseif ($this->_queued === false) {
            Mage::dispatchEvent('mip_after_' . $direction, array('result' => $result, 'settings' => $this->getSettings()));
            Mage::dispatchEvent('mip_after_' . $direction . '_' . $resource . '_' . $action, array('result' => $result, 'settings' => $this->getSettings(), 'handler' => $this));
            Mage::helper('mip')->dispatchCustomEvents(Flagbit_Mip_Model_Server::getConfig()->getCurrentDefinition(), 'after', array('result' => $result, 'settings' => $this->getSettings(), 'handler' => $this));
        }
        $result = (array)$result;
    }

    /**
     * split large Date in parts
     *
     * @param string $resource
     * @param string $action
     * @param string $direction
     * @param array $data
     * @return array
     */
    protected function handleLargeData($resource, $action, $direction, $data)
    {

        $keys = $this->getLargeDataKeys($data);
        $return = array();

        if (!count($keys)) {
            return $data;
        }

        // get real Params
        $params = array_diff_key($data, $keys);

        $parts = array();
        foreach ($keys as $key => $size) {
            for ($i = 0; count($data[$key]); $i++) {

                // set Params
                $parts[$i] = $params;

                // split Data
                $parts[$i][$key] = array_splice($data[$key], 0, $this->getSettings('data_parts_size'));
            }
        }

        $partsCount = count($parts);
        for ($i = 0; $partsCount > $i; $i++) {
            if ($i == 0) {
                $return = $parts[$i];
                continue;
            }
            $result = Mage::getModel('mip/data_queue')->store($this->getInterface(), $resource, $action, $direction, $parts[$i], Flagbit_Mip_Model_Server::getConfig()->getCurrentDefinition());
            if ($result) {
                Mage::helper('mip/log')->getWriter($this)->info('QUEUE: store part ' . $i . ' of ' . ($partsCount - 1) . ', ' . strlen(serialize($parts[$i])) . ' bytes');
            }

        }
        $this->_queued = true;
        return $return;
    }

    /**
     * get Array key from sub arrays with an numeric Data array
     *
     * @param array $params
     * @return string
     */
    protected function getLargeDataKeys($params)
    {

        $keys = array();
        foreach ($params as $key => $data) {

            if (!is_array($data)) {
                continue;
            }

            $size = count($data);
            $keyString = implode(array_keys($data));
            if ($size > $this->getSettings('data_parts_limit')
                && (is_numeric($keyString)
                    or ctype_digit($keyString))
            ) {
                $keys[$key] = $size;
            }
        }
        return $keys;
    }


    /**
     * remove up to date Data from import due Hash compare
     *
     * @param array $data
     * @param string $resource
     * @return array
     */
    protected function removeUpToDateData(array $data, $resource)
    {

        foreach ($data as &$param) {

            // remove up to date Data from import
            $param = Mage::getSingleton('mip/data_hash')->setType($resource)->removeUpToDateData($param);
        }
        return $data;
    }


    /**
     * organize Data in Function Call Params
     *
     * @param string $resourceModel
     * @param string $action
     * @param array $data
     * @return array
     */
    protected function getActionCallParams($resourceModel, $action, array $data, $direction)
    {

        $reflector = new ReflectionMethod($resourceModel, $action);
        $params = array();

        // iterate method Params
        foreach ($reflector->getParameters() as $key => $value) {

            // if there only one Parameter with default value array than set all Data to it and return
            if ($reflector->getNumberOfParameters() == 1
                && $value->isOptional()
                && is_array($value->getDefaultValue())
            ) {

                return array($key => $data);
            }

            // if the key is 'data' and input direction so set all Data to it
            if ($key == 'data' && !isset($data[$value->name]) && $direction == 'input') {
                $params[$value->name] = $data;
                continue;
            }

            // set method Parameters for call
            $params[$value->name] = (isset($data[$value->name]) ? $data[$value->name] : null);
        }

        return $params;
    }


    /**
     * get Data from an Resource Model
     *
     * @param string $resource
     * @param string $action
     * @param array $params
     * @return array
     */
    public function output($resource, $action, $params)
    {
        return $this->doAction($resource, $action, 'output', $params);
    }

    /**
     * Dispatch webservice fault
     *
     * @param int $code
     * @param string $message
     */
    public function fault($code, $message = null)
    {
        throw new Flagbit_Mip_Model_Exception($code, $message);
    }
}