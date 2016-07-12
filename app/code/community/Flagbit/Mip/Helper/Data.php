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
class Flagbit_Mip_Helper_Data extends Mage_Core_Helper_Abstract
{

    /**
     * returns true when script is executed from shell
     *
     * @return bool
     */
    public function isRunningFromShell()
    {
        return php_sapi_name() == 'cli';
    }

    /**
     * get MIP Submodule Configuration by module, groupKey and Fieldname
     *
     * @param string $module
     * @param string $groupKey
     * @param string $fieldName
     * @param int $storeId
     * @return string
     */
    public function getModuleConfig($module, $groupKey, $fieldName, $storeId = null)
    {
        return Mage::getStoreConfig($module.'/'.$groupKey.'/'.$fieldName, $storeId);
    }

    /**
     * trim Explode works like original PHP explode
     * and supplies dome extra Feature:
     *
     * remove Strip whitespace
     * remove empty Values (optional)
     *
     * @param string $delim The boundary string.
     * @param string $string The input string.
     * @param boolean $onlyNonEmptyValues removes empty Values, when set to true (default: true)
     * @return array
     */
    public function trimExplode($delim, $string, $onlyNonEmptyValues = true)
    {

        $array = explode($delim, $string);
                // for two perfomance reasons the loop is duplicated
                //  a) avoid check for $onlyNonEmptyValues in foreach loop
                //  b) avoid unnecessary code when $onlyNonEmptyValues is not set
        if ($onlyNonEmptyValues) {
                $new_array = array();
                foreach($array as $value) {
                        $value = trim($value);
                        if ($value != '') {
                                $new_array[] = $value;
                        }
                }
                        // direct return for perfomance reasons
                return $new_array;
        }

        foreach($array as &$value) {
                $value = trim($value);
        }

        return $array;
    }


    /**
     * compare Magento Version with current one
     * Supports Community and Enterprise Edition
     *
     * @param string $community Community Edition Version
     * @param string $enterprise Enterprise Edition Version
     * @param string $operator Compare Operator
     * @return boolean
     */
    public function compareVersion($community = null, $enterprise = null, $operator='>=')
    {
        return $this->isModuleActive('Enterprise_Enterprise')
            ? version_compare(Mage::getVersion(), !is_null($enterprise) ? $enterprise : $community, $operator)
            : version_compare(Mage::getVersion(), $community, $operator);
    }


    /**
     * returns Module Status by Module Code
     *
     * @param string $code Module Code
     * @return boolean
     */
    public function isModuleActive($code)
    {
        $module = Mage::getConfig()->getNode("modules/$code");
        $model = Mage::getConfig()->getNode("global/models/$code");
        return $module && $module->is('active') || $model;
    }

    /**
     * normalize Characters
     * Example: ü -> ue
     *
     * @param string $string
     * @return string
     */
    public function normalize($string)
    {
        $table = array(
            'Š'=>'S',  'š'=>'s',  'Đ'=>'Dj', 'đ'=>'dj', 'Ž'=>'Z',  'ž'=>'z',  'Č'=>'C',  'č'=>'c',  'Ć'=>'C',  'ć'=>'c',
            'À'=>'A',  'Á'=>'A',  'Â'=>'A',  'Ã'=>'A',  'Ä'=>'Ae', 'Å'=>'A',  'Æ'=>'A',  'Ç'=>'C',  'È'=>'E',  'É'=>'E',
            'Ê'=>'E',  'Ë'=>'E',  'Ì'=>'I',  'Í'=>'I',  'Î'=>'I',  'Ï'=>'I',  'Ñ'=>'N',  'Ò'=>'O',  'Ó'=>'O',  'Ô'=>'O',
            'Õ'=>'O',  'Ö'=>'Oe', 'Ø'=>'O',  'Ù'=>'U',  'Ú'=>'U',  'Û'=>'U',  'Ü'=>'Ue', 'Ý'=>'Y',  'Þ'=>'B',  'ß'=>'Ss',
            'à'=>'a',  'á'=>'a',  'â'=>'a',  'ã'=>'a',  'ä'=>'ae', 'å'=>'a',  'æ'=>'a',  'ç'=>'c',  'è'=>'e',  'é'=>'e',
            'ê'=>'e',  'ë'=>'e',  'ì'=>'i',  'í'=>'i',  'î'=>'i',  'ï'=>'i',  'ð'=>'o',  'ñ'=>'n',  'ò'=>'o',  'ó'=>'o',
            'ô'=>'o',  'õ'=>'o',  'ö'=>'oe', 'ø'=>'o',  'ù'=>'u',  'ú'=>'u',  'û'=>'u',  'ý'=>'y',  'ý'=>'y',  'þ'=>'b',
            'ÿ'=>'y',  'Ŕ'=>'R',  'ŕ'=>'r',  'ü'=>'ue',
        );

        return strtr($string, $table);
    }

    /**
     *    Dispatch custom Events
     *
     * @param Flagbit_Mip_Model_Config_Importexport_Interface
     * @param string $direction (input | output)
     * @param array $param
     */
    public function dispatchCustomEvents(Flagbit_Mip_Model_Config_Importexport_Interface $definition, $direction = null, $param = array())
    {
        if(!$direction || !is_string($direction) || !in_array($direction, array('before', 'after'))) {
            Mage::logException(new Exception('No valid direction set (have to be before or after)'));
        }

        $settings = $definition->getSettings();

        if( isset($settings['events'])
            && is_array($settings['events'])
            && count($settings['events'])
            && isset($settings['events'][$direction])) {

            $param['code'] = $definition->getCode();

            $events = explode(',', $settings['events'][$direction]);
            foreach($events as $event) {
                try{
                    Mage::helper('mip/log')->getWriter($this)->info('EVENT: dispatch custom event: mip_customevent_'.$direction.'_'.$event);
                    Mage::dispatchEvent('mip_customevent_'.$direction.'_'.$event, $param);
                }catch (Exception $e){
                    Mage::helper('mip/log')->getWriter($this)->error('EVENT ERROR: '.$e->getMessage(), $e);
                    Mage::logException($e);
                }
            }
        }
    }
}