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
class Flagbit_Mip_Model_Filter extends Varien_Filter_Template {

    protected $_directivePrefix = 'Post';
    protected $_storeId = null;

    public function setStoreId($storeId)
    {
        $this->_storeId = $storeId;
        return $this;
    }

    /**
     * Filter the string as template.
     *
     * @param string $value
     * @return string
     */
    public function filter($value)
    {

        if(preg_match_all(self::CONSTRUCTION_PATTERN, $value, $constructions, PREG_SET_ORDER)) {
            foreach($constructions as $index=>$construction) {
                $replacedValue = '';
                $callback = array($this, $construction[1].$this->_directivePrefix.'Directive');
                if(!is_callable($callback)) {
                    continue;
                }
                try {
                    $replacedValue = call_user_func($callback, $construction);
                } catch (Exception $e) {
                    throw $e;
                }
                $value = str_replace($construction[0], $replacedValue, $value);
            }
        }
        return $value;
    }

    /**
     * set Filter directive prefix
     *
     * @param string $prefix
     */
    public function setDirectivePrefix($prefix)
    {
        $this->_directivePrefix = $prefix;
        return $this;
    }

    /**
     * get Tools Model
     *
     * @return Flagbit_Mip_Model_Tools
     */
    protected function _getToolsModel()
    {
        return Mage::getSingleton('mip/tools');
    }

    /**
     * get ID Filter
     * can hanlde products, attributes and attributesets
     *
     * @param array $construction
     */
    public function getidPostDirective($construction)
    {
        $params = $this->_getIncludeParameters($construction[2]);
        return $this->_getToolsModel()->getId($params['type'], $params['field'], $params['value']);
    }

    /**
     * get config
     *
     * @param array $construction
     */
    public function configPreDirective($construction)
    {
        $params = $this->_getIncludeParameters($construction[2]);
        $return = null;

        if (empty($params['field'])) {
            $params['field'] = null;
        }
        if (empty($params['group'])) {
            $params['group'] = null;
        }
        if (empty($params['module'])) {
            $params['module'] = null;
        }
        if(empty($params['store'])){
            $params['store'] = $this->_storeId;
        }

        return $this->_getToolsModel()->getConfigValue($params['field'], $params['group'], $params['module'], $store = null);
    }

    /**
     * get filetime
     *
     * @param array $construction
     */
    public function filetimePostDirective($construction)
    {
        $params = $this->_getIncludeParameters($construction[2]);
        return empty($params['file']) ? '' : $this->_getToolsModel()->getFiletime($params['file']);
    }

    /**
     * get Attribute Values
     *
     * @param array $construction
     * @return array
     */
    public function attributePostDirective($construction)
    {
        $params = $this->_getIncludeParameters($construction[2]);

        return $this->_getToolsModel()->getAttributeOptionValues(
            isset($params['attribute']) ? $params['attribute'] : '',
            isset($params['text']) ? $params['text'] : '',
            isset($params['default']) ? $params['default'] : null,
            isset($params['store']) ? $params['store'] : 0,
            isset($params['compare']) ? $params['compare'] : '',
            isset($params['log']) ? $params['log'] : false,
            isset($params['multi']) ? $params['multi'] : false
        );
    }
}
