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
class Flagbit_Mip_Model_Server_Adapter_Processor_Xslt2 implements Flagbit_Mip_Model_Server_Adapter_Processor_Interface {



    /**
     * input object
     *
     * @var DOMdocument
     */
    protected $_input;


    protected $_ruleFile;


    /**
     *
     * @param string $xsltPath path of the xsl file
     */
    public function __construct($xsltPath = null) {

        if ($xsltPath) {
            $this->setRuleFile($xsltPath);
        }
    }

    /**
     * transform XML
     *
     * transforms XML File
     */
    public function transform() {

        $inputFile = $this->getTempBaseDir() . DS . Varien_File_Uploader::getNewFileName( $this->getTempBaseDir() . DS . uniqid(__CLASS__));

        $this->_input->save($inputFile);

        return $this->_runProcessor($inputFile);
    }


    /**
     * run Processor
     *
     * @param string $inputFile
     * @return string
     */
    protected function _runProcessor($inputFile){

        $commandString = $this->getSetting('exec_file') . ' ' .
            $this->getSetting('source_param') . $inputFile . ' ' .
            $this->getSetting('template_param') . $this->_ruleFile;

        $handle = popen(escapeshellcmd($commandString), 'r');

        $result = '';

        while (!feof($handle)){
            $result .= fread($handle, 1024);
        }
        pclose($handle);

        $file = new Varien_Io_File();
        $file->rm($inputFile);

        return $result;
    }

    /**
     * get Temp Base Directory
     *
     * @return string
     */
    protected function getTempBaseDir(){

        $path = Mage::getBaseDir('tmp') . DS . 'mip';
        $file = new Varien_Io_File();
        $file->setAllowCreateFolders(true);
        $file->createDestinationDir($path);

        return $path;
    }

    /**
     * set xsl file
     *
     * @param string $path path of the xsl file
     */
    public function setRuleFile($path) {

        $this->_ruleFile = $path;
        return $this;
    }

    /**
     * get Setting value by Key
     *
     * @param string $key
     * @return string
     */
    public function getSetting($key){
        $processors = Mage::getSingleton('mip/config')->getProcessors();
        $processorSettings = $processors['xslt2']->settings;

        return (string) $processorSettings->{$key};
    }


    /**
     * set Input Data
     *
     * @param mixed $input
     */
    public function setInput($input) {

        $xmlDom = null;
        if ($input instanceof DOMDocument){
            $xmlDom = $input;
        }
        elseif (is_string($input)) {
            $xmlDom = new DOMDocument();
            $xmlDom->preserveWhiteSpace = false;
            $xmlDom->loadXML($input);
        }

        $this->_input = $xmlDom;
        return $this;
    }

    /**
    * Register an Flagbit_Mip_Model_Server_Adapter_Processor_Xslt_Callback object instance
    *
    * @param Flagbit_Mip_Model_Server_Adapter_Processor_Xslt_Callback $callback The instance of the Flagbit_Mip_Model_Server_Adapter_Processor_Xslt_Callback to register
    */
    public function registerCallback(Flagbit_Mip_Model_Server_Adapter_Processor_Xslt_Callback $callback) {
           Mage::helper('mip/log')->getWriter($this)->warn(__CLASS__.' Callbacks not implemented!');
    }

}