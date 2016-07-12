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
class Flagbit_Mip_Model_Server_Trigger_Url extends Flagbit_Mip_Model_Server_Trigger_Abstract {

    /**
     * run Trigger
     * determines Request Type Input / Output
     */
    public function run(){

        if($this->getDirection() == 'input'){

            if($this->getController()->getRequest()->getPost('data')){
                $params = $this->getController()->getRequest()->getPost('data');
            }else{
                $params = array();
                $params = $this->getController()->getRequest()->getParams();
                $params = array_merge($params, $this->handleParams($this->_getDefinition()->getSettings('params')));
                $params = array_diff_key((array) $params, array_flip(array('user', 'pass', 'resource', 'action', 'request', 'command')));
            }

            $response = $this->input($params);

        }else{
            $params = $this->getController()->getRequest()->getParams();
            $params = array_merge($params, $this->handleParams($this->_getDefinition()->getSettings('params')));
            $params = array_diff_key((array) $params, array_flip(array('user', 'pass', 'resource', 'action', 'request', 'command')));

            $response = $this->output($params);
        }

        $responseObject = $this->getController()->getResponse();

        if($this->getAdapterName() != 'xml'){
            $responseObject->setHeader('Content-Type', 'text/plain');
        }else{
            $responseObject->setHeader('Content-Type', 'text/xml');
        }
        $responseObject->setBody($response);
    }

    /**
     * inject Controller
     *
     * @param Mage_Core_Controller_Front_Action $controller
     */
    public function injectController($controller){
        $this->setController($controller);
    }

    /**
     * get current Adapter Name
     *
     * @return string
     */
    public function getAdapterName()
    {
        if($this->getController()->getRequest()->getParam('adapter', $this->_getDefinition()->getSettings('adapter'))){
            return $this->getController()->getRequest()->getParam('adapter', $this->_getDefinition()->getSettings('adapter'));
        }
        return parent::getAdapterName();
    }

    /**
     * get current Action Name
     *
     * @return string
     */
    public function getAction(){

        if($this->getController()->getRequest()->getParam('command')){
            return $this->getController()->getRequest()->getParam('command');
        }
        return parent::getAction();
    }

    /**
     * get current Resource Name
     *
     * @return string
     */
    public function getResource(){

        if($this->getController()->getRequest()->getParam('resource', $this->_getDefinition()->getSettings('resource'))){
            return $this->getController()->getRequest()->getParam('resource', $this->_getDefinition()->getSettings('resource'));
        }

        return parent::getResource();
    }

    /**
     * get current Direction
     *
     * @return string
     */
    public function getDirection(){

        if($this->getController()->getRequest()->getParam('direction', $this->_getDefinition()->getSettings('direction'))){
            return $this->getController()->getRequest()->getParam('direction', $this->_getDefinition()->getSettings('direction'));
        }

        if(parent::getDirection()){
            return parent::getDirection();
        }

        return $this->getController()->getRequest()->getPost() ? 'input' : 'output';
    }


    /**
     * return Interface
     *
     * @return string
     */
    public function getInterface()
    {
        $interface = $this->getController()->getRequest()->getActionName();

        if(!$interface or $interface == 'noRoute'){
            $interface = parent::getInterface();
        }
        return $interface;
    }
}