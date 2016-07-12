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
class Flagbit_Mip_Report_Mip_LogController extends Mage_Adminhtml_Controller_Action {
    /**
     * Initialization of current view - add's breadcrumps and the current menu status
     *
     * @return Flagbit_Glossary_GlossaryController
     */
    protected function _initAction() {
        $this->_usedModuleName = 'mip';

        $this->_publicActions[] = 'tail';

        $this->loadLayout()
                ->_setActiveMenu('report/mip/log')
                ->_addBreadcrumb($this->__('Reports'), $this->__('CMS'))
                ->_addBreadcrumb($this->__('Glossary'), $this->__('Glossary'));

        return $this;
    }

    /**
     * Sends the ajax response for the console output
     */
    public function tailAction()
    {

         $startPos = $this->getRequest()->getParam('position');
         $filename = Mage::getBaseDir('var').'/log/mip.log';

         if(!file_exists($filename)){
             return '';
         }

         $handle = fopen($filename, 'r');
         $filesize = filesize($filename);

         $firstTime = 0;

         if($startPos == 0) {
             $firstTime = 1;
             $lengthBefore = 1000;
             fseek($handle, -$lengthBefore, SEEK_END);
             $text = fread($handle, $filesize);

             $updates = '[...]' . substr($text, strpos($text, "\n"), strlen($text));
             $newPos = ftell($handle);
         } else {
             fseek($handle, $startPos, SEEK_SET);
             $updates = fread($handle, $filesize);
             $newPos = ftell($handle);
         }

         if($updates != NULL) {
            $response = Zend_Json::encode(array('text' => $updates, 'position' => $newPos, 'firsttime' => $firstTime));
            print $response;
         }

    }

    /**
     * Validate Secret Key
     *
     * @return bool
     */
    protected function _validateSecretKey()
    {
        return true;
    }

    /**
     * Displays the new glossary item form
     *
     */
    public function indexAction() {

        $this->_forward('log');
    }


    /**
     * Displays the glossary overview grid.
     *
     */
    public function logAction() {

        $this->_initAction()
                ->_addContent($this->getLayout()->createBlock('mip/report_log'))
                ->renderLayout();
    }

    /**
     * Simple access control
     *
     * @return boolean True if user is allowed to edit glossary
     */
    protected function _isAllowed() {

        return Mage :: getSingleton('admin/session')->isAllowed('admin/mip/log');
    }




}
