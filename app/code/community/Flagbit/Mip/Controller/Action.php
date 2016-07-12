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
class Flagbit_Mip_Controller_Action extends Mage_Core_Controller_Front_Action
{

    const XML_AUTH_TYPE_PATH        = 'mip_core/settings/auth_type';
    const XML_AUTH_USERNAME_PATH    = 'mip_core/settings/auth_user';
    const XML_AUTH_PASSWORD_PATH    = 'mip_core/settings/auth_pass';
    const XML_PATH_ALLOW_IPS        = 'mip_core/settings/ip_mask';

    protected $_mipAction = null;

    /**
     * handle Authentication
     */
    public function preDispatch()
    {

        $this->setFlag('', self::FLAG_NO_START_SESSION, 1); // Do not start standart session

        $username = Mage::getStoreConfig(self::XML_AUTH_USERNAME_PATH);
        $password = Mage::getStoreConfig(self::XML_AUTH_PASSWORD_PATH);

        switch (Mage::getStoreConfig(self::XML_AUTH_TYPE_PATH)){

            case 'httpauth':
                // HTTP Authentication
                if($this->getRequest()->getServer('PHP_AUTH_USER') != $username
                    or $this->getRequest()->getServer('PHP_AUTH_PW') != $password){

                    $this->getResponse()->setHeader('status', 'Unauthorized', true);
                    $this->getResponse()->setHeader('WWW-authenticate', 'basic realm="mip Interface"', true);
                    $this->getResponse()->sendHeaders();
                    Mage::helper('mip/log')->getWriter($this)->warn('Invalid login or password.');

                    $this->setFlag('', self::FLAG_NO_DISPATCH, true);
                }
                break;

            case 'getparams':
                if($this->getRequest()->getParam('user') != $username
                    or $this->getRequest()->getParam('pass') != $password){

                    Mage::helper('mip/log')->getWriter($this)->warn('Invalid login or password.');

                    $this->setFlag('', self::FLAG_NO_DISPATCH, true);
                }
                break;

            case 'ip':
                $allowedIps = Mage::getStoreConfig(self::XML_PATH_ALLOW_IPS);
                $remoteAddr = Mage::helper('core/http')->getRemoteAddr();
                if (!empty($allowedIps) && !empty($remoteAddr)) {
                    $allowedIps = preg_split('#\s*,\s*#', $allowedIps, null, PREG_SPLIT_NO_EMPTY);
                    if (array_search($remoteAddr, $allowedIps) === false
                        && array_search(Mage::helper('core/http')->getHttpHost(), $allowedIps) === false) {

                        Mage::helper('mip/log')->getWriter($this)->warn('Invalid IP ('.$remoteAddr.') allowed: '.implode(', ', $allowedIps));
                        $this->setFlag('', self::FLAG_NO_DISPATCH, true);
                    }
                }
                break;
        }

        return parent::preDispatch();
    }


    /**
     * Overwrite Action name to bypass EE 13 method_exists checks
     *
     * @param string $action
     * @return string
     */
    public function getActionMethodName($action)
    {
        $this->_mipAction = $action;
        $method = 'dummyAction';
        return $method;
    }

    public function dummyAction(){
        $this->__call($this->_mipAction, array());
    }

    /**
     * Retrive webservice server
     *
     * @return Mage_Mip_Model_Server
     */
    protected function _getServer()
    {
        return Mage::getSingleton('mip/server');
    }

    /**
     * translate URL Request to serverAction
     *
     * @param string $name
     * @param array $args
     */
    public function __call($interface, $args) {

        if($this->getRequest()->getParam('request')){

            $definition = Mage::getSingleton('mip/config')->getRequest($this->getRequest()->getParam('request'));

            if($definition instanceof Flagbit_Mip_Model_Config_Importexport_Interface){
                $server = $this->_getServer()->setController($this)->init($definition);
                $server->getTrigger()->setData('request_params',$this->getRequest()->getParams());
                return $server->run(($definition->getSettings('instancecheck') ? true : false));
            }else{
                Mage::throwException(Mage::helper('mip')->__('Invalid Request specified ('.$this->getRequest()->getParam('request').')'));
            }
        }else{
            $definition = Mage::getModel('mip/config_importexport_url');
            $definition->setSettings($this->getRequest()->getParams());
            $server = $this->_getServer()->setController($this)->init($definition);
            $server->getTrigger()->setData('request_params',$this->getRequest()->getParams());
            return $server->run(($definition->getSettings('instancecheck') ? true : false));
        }
    }



}