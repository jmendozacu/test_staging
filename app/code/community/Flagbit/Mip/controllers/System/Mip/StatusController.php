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

require_once 'Mage/Adminhtml/controllers/System/ConfigController.php';

/**
 * @package     Flagbit
 * @subpackage  Flagbit_Mip
 */
class Flagbit_Mip_System_Mip_StatusController extends Mage_Adminhtml_System_ConfigController {


    /**
     * Initialization of current view - add's breadcrumps and the current menu status
     *
     * @return Flagbit_Mip_ManagerController
     */
    protected function _initAction() {
        $this->_usedModuleName = 'mip';

        $this->loadLayout()
                ->_setActiveMenu('system/mip');

        return $this;
    }

    /**
     * Displays the new form
     *
     */
    public function indexAction() {

        $this->_forward('edit');
        return;

        $this->_initAction()
                ->_addBreadcrumb(Mage::helper('adminhtml')->__('System'), Mage::helper('adminhtml')->__('System'))
                ->_addLeft($this->getLayout()->createBlock('adminhtml/system_config_switcher')->setTemplate('mip/switcher.phtml'))
                ->_addContent($this->getLayout()->createBlock('mip/system_manager')->setData('action', $this->getUrl('adminhtml/system_mip_manager/save')))
                ->_addLeft($this->getLayout()->createBlock('mip/system_manager_tabs'))
                ->renderLayout();
    }


    /**
     * Enter description here...
     *
     */
    public function editAction()
    {
        $this->_title($this->__('System'))->_title($this->__('Configuration'));

        $current = $this->getRequest()->getParam('section');
        $website = $this->getRequest()->getParam('website');
        $store   = $this->getRequest()->getParam('store');

        $configFields = Mage::getSingleton('mip/submodule_config');

        $sections     = $configFields->getSections($current);
        $section      = $sections->$current;
        $hasChildren  = $configFields->hasChildren($section, $website, $store);
        if (!$hasChildren && $current) {
            #$this->_redirect('*/*/', array('website'=>$website, 'store'=>$store));
        }

        $this->loadLayout();

        $this->getLayout()->getBlock('left')
            ->append($this->getLayout()->createBlock('adminhtml/system_config_switcher')->setTemplate('mip/switcher.phtml'))
            ->append($this->getLayout()->createBlock('mip/system_manager_tabs'));

        if ($this->_isSectionAllowed($this->getRequest()->getParam('section'))) {
            $this->_addContent($this->getLayout()->createBlock('mip/system_manager_form')->initForm());

            $this->_addJs($this->getLayout()->createBlock('adminhtml/template')->setTemplate('system/shipping/ups.phtml'));
            $this->_addJs($this->getLayout()->createBlock('adminhtml/template')->setTemplate('system/config/js.phtml'));
            $this->_addJs($this->getLayout()->createBlock('adminhtml/template')->setTemplate('system/shipping/applicable_country.phtml'));

            $this->renderLayout();
        }
    }

    /**
     * @return Mage_Adminhtml_Controller_Action
     */
    protected function _addContent(Mage_Core_Block_Abstract $block)
    {
        $this->getLayout()->getBlock('content')->append($block);
        return $this;
    }


    /**
     * Simple access control
     *
     * @return boolean True if user is allowed to edit mip
     */
    protected function _isAllowed() {

        return Mage :: getSingleton('admin/session')->isAllowed('mip/status');
    }

    public function saveAction() {

        $configFields = array(
            'debug_email',
            'auth_type',
            'auth_user',
            'auth_pass',
            'devlog',
            'task_lifetime'
        );

        $relationFieldsModels = array(
            'clean_relations' => 'mip/data_relation',
            'clean_datahashes' => 'mip/data_hash',
            'clean_dataqueues' => 'mip/data_queue'
        );

        if ($data = $this->getRequest()->getPost()) {

            try{

                foreach($relationFieldsModels as $field => $modeName){
                    if(!isset($data[$field])
                        || (
                                count($data[$field]) == 1
                                && $data[$field][0] == '0'
                            )
                        ){
                            continue;
                    }
                    $model = Mage::getModel($modeName)->getResource()->deleteEntriesByType($data[$field]);
                }

                $groups = $this->getRequest()->getPost('groups');

                // custom save logic
                $this->_saveSection();
                $section = $this->getRequest()->getParam('section');
                $website = $this->getRequest()->getParam('website');
                $store   = $this->getRequest()->getParam('store');
                Mage::getModel('mip/submodule_config_data')
                    ->setSection($section)
                    ->setWebsite($website)
                    ->setStore($store)
                    ->setGroups($groups)
                    ->save();

                // reinit configuration
                Mage::getConfig()->reinit();
                Mage::app()->reinitStores();

                Mage::getSingleton('adminhtml/session')->addSuccess(Mage::helper('mip')->__('Settings successfully saved'));
            }
            catch (Exception $e){
                Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
            }
        }
        $this->_saveState($this->getRequest()->getPost('config_state'));
        $this->_redirect('*/*/edit', array('_current' => array('section', 'website', 'store')));
    }

    /**
     * Check if specified section allowed in ACL
     *
     * Will forward to deniedAction(), if not allowed.
     *
     * @param string $section
     * @return bool
     */
    protected function _isSectionAllowed($section)
    {
        return true;

    }


    /**
     * Set redirect into responce
     *
     * @param   string $path
     * @param   array $arguments
     */
    protected function _redirect($path, $arguments=array())
    {
        $this->_getSession()->setIsUrlNotice($this->getFlag('', self::FLAG_IS_URLS_CHECKED));
        $this->getResponse()->setRedirect($this->getUrl('*/*/index', $arguments));
        return $this;
    }


}