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
class Flagbit_Mip_Model_Config_Importexport extends Varien_Object
{
    protected $_config = null;

    protected $_types = array('cron', 'event', 'request');

    protected function _construct()
    {
    }

    public function getAllItems($type = null)
    {
        $items = array();
        $types = $this->_types;
        if($type !== null){
            $types = array($type);
        }
        foreach($types as $typeName){
            foreach ($this->_createTypeObject($typeName)->getAllCodes() as $code){
                $items[] = $this->_createTypeObject($typeName)->loadByCode($code);
            }
        }
        return $items;
    }

    /**
     * get Type Configuration as Array by Type
     *
     * @param string $type
     * @return array | Flagbit_Mip_Model_Config_Importexport_Abstract
     */
    public function getTypeObject($type, $code = null)
    {
        $returnValue = null;
        if($code === null){
            $code = $this->_createTypeObject($type)->getAllCodes();
        }

        if(is_array($code)){
            $returnValue = array();
            foreach($code as $singleCode){
                $returnValue[] = $this->_createTypeObject($type)->loadByCode($singleCode);
            }
        }else{
            $returnValue = $this->_createTypeObject($type)->loadByCode($code);
        }

        return $returnValue;
    }

    /**
     * create Type Object
     *
     * @param string $type
     * @return Flagbit_Mip_Model_Config_Importexport_Abstract
     */
    protected function _createTypeObject($type)
    {
        return Mage::getModel('mip/config_importexport_'.$type);
    }

    /**
     * get Cronjobs
     *
     * @param string $code
     * @return Flagbit_Mip_Model_Config_Importexport_Cron | array
     */
    public function getCronjob($code = null)
    {
        return $this->getTypeObject(Flagbit_Mip_Model_Config_Importexport_Cron::TYPE, $code);
    }

    /**
     * get Events
     *
     * @param string $code
     * @return Flagbit_Mip_Model_Config_Importexport_Event | array
     */
    public function getEvent($code = null)
    {
        return $this->getTypeObject(Flagbit_Mip_Model_Config_Importexport_Event::TYPE, $code);
    }

    /**
     * get Request Config
     *
     * @param string $code
     * @return Flagbit_Mip_Model_Config_Importexport_Request | array
     */
    public function getRequest($code = null)
    {
        return $this->getTypeObject(Flagbit_Mip_Model_Config_Importexport_Request::TYPE, $code);
    }

}
