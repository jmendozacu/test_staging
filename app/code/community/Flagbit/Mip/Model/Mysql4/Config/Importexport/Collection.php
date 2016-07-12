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
class Flagbit_Mip_Model_Mysql4_Config_Importexport_Collection extends Varien_Data_Collection
{

    protected $_types = array('cron', 'request', 'event');
    protected $_collectedItems = array();
    protected $_addTaskData = false;

    /**
     * Filter rendering helper variables
     *
     * @see Varien_Data_Collection::$_filter
     * @see Varien_Data_Collection::$_isFiltersRendered
     */
    private $_filterIncrement = 0;
    private $_filterBrackets = array();
    private $_filterEvalRendered = '';



    public function __construct()
    {
        $this->setItemObjectClass('mip/config_importexport');
    }

    public function getTypeOptionsPairs()
    {
        $res = array();
        foreach ($this->_types as $item) {
            $res[$item] = $item;
        }
        return $res;
    }

    /**
     * Load data
     *
     * @return  Varien_Data_Collection
     */
    public function loadData($printQuery = false, $logQuery = false)
    {

        if(!$this->isLoaded()){

            $importexportModel = $this->getNewEmptyItem();

            foreach ($importexportModel->getAllItems() as $item){
                $this->_collectedItems[$item->getType().'_'.$item->getCode()] = $item;
            }
            $this->_generateAndFilterAndSort('_collectedItems');

                // calculate totals
            $this->_totalRecords = count($this->_collectedItems);
            $this->_setIsLoaded();

            // paginate and add items
            $from = ($this->getCurPage() - 1) * $this->getPageSize();
            $to = $from + $this->getPageSize() - 1;
            $isPaginated = $this->getPageSize() > 0;
            $cnt = 0;
            foreach ($this->_collectedItems as $row) {
                $cnt++;
                if ($isPaginated && ($cnt < $from || $cnt > $to)) {
                    continue;
                }
                $this->addItem($row);
            }
            if($this->_addTaskData === true){
                foreach($this->_items as &$item){
                    $item->addLastTaskData();
                }
            }
        }
        return $this;
    }

    public function setAddTaskData($boolean)
    {
        $this->_addTaskData = $boolean;
        return $this;
    }

    /**
     * With specified collected items:
     *  - generate data
     *  - apply filters
     *  - sort
     *
     * @param string $attributeName '_collectedItems'
     */
    private function _generateAndFilterAndSort($attributeName)
    {
        // apply filters on generated data
        if (!empty($this->_filters)) {
            foreach ($this->$attributeName as $key => $row) {
                if (!$this->_filterRow($row)) {
                    unset($this->{$attributeName}[$key]);
                }
            }
        }

        // sort (keys are lost!)
        if (!empty($this->_orders)) {
            usort($this->$attributeName, array($this, '_usort'));
        }
    }

    /**
     * Callback for sorting items
     * Currently supports only sorting by one column
     *
     * @param array $a
     * @param array $b
     * @return int
     */
    protected function _usort($a, $b)
    {
        foreach ($this->_orders as $key => $direction) {
            $result = $a[$key] > $b[$key] ? 1 : ($a[$key] < $b[$key] ? -1 : 0);
            return (self::SORT_ORDER_ASC === strtoupper($direction) ? $result : -$result);
            break;
        }
    }

    /**
     * Set select order
     * Currently supports only sorting by one column
     *
     * @param   string $field
     * @param   string $direction
     * @return  Varien_Data_Collection
     */
    public function setOrder($field, $direction = self::SORT_ORDER_DESC)
    {
        $this->_orders = array($field => $direction);
        return $this;
    }
    /**
     * Set a custom filter with callback
     * The callback must take 3 params:
     *     string $field       - field key,
     *     mixed  $filterValue - value to filter by,
     *     array  $row         - a generated row (before generaring varien objects)
     *
     * @param string $field
     * @param mixed $value
     * @param string $type 'and'|'or'
     * @param callback $callback
     * @param bool $isInverted
     * @return Varien_Data_Collection_Filesystem
     */
    public function addCallbackFilter($field, $value, $type, $callback, $isInverted = false)
    {
        $this->_filters[$this->_filterIncrement] = array(
            'field'       => $field,
            'value'       => $value,
            'is_and'      => 'and' === $type,
            'callback'    => $callback,
            'is_inverted' => $isInverted
        );
        $this->_filterIncrement++;
        return $this;
    }

    /**
     * The filters renderer and caller
     * Aplies to each row, renders once.
     *
     * @param array $row
     * @return bool
     */
    protected function _filterRow($row)
    {
        // render filters once
        if (!$this->_isFiltersRendered) {
            $eval = '';
            for ($i = 0; $i < $this->_filterIncrement; $i++) {
                if (isset($this->_filterBrackets[$i])) {
                    $eval .= $this->_renderConditionBeforeFilterElement($i, $this->_filterBrackets[$i]['is_and'])
                        . $this->_filterBrackets[$i]['value'];
                }
                else {
                    $f = '$this->_filters[' . $i . ']';
                    $eval .= $this->_renderConditionBeforeFilterElement($i, $this->_filters[$i]['is_and'])
                        . ($this->_filters[$i]['is_inverted'] ? '!' : '')
                        . '$this->_invokeFilter(' . "{$f}['callback'], array({$f}['field'], {$f}['value'], " . '$row))';
                }
            }
            $this->_filterEvalRendered = $eval;
            $this->_isFiltersRendered = true;
        }
        $result = false;
        if ($this->_filterEvalRendered) {
            eval('$result = ' . $this->_filterEvalRendered . ';');
        }
        return $result;
    }

    /**
     * Invokes specified callback
     * Skips, if there is no filtered key in the row
     *
     * @param callback $callback
     * @param array $callbackParams
     * @return bool
     */
    protected function _invokeFilter($callback, $callbackParams)
    {
        /** @var $row Varien_Object */
        list($field, $value, $row) = $callbackParams;
        if (!array_key_exists($field, $row->toArray())) {
            return false;
        }
        return call_user_func_array($callback, $callbackParams);
    }

    /**
     * Fancy field filter
     *
     * @param string $field
     * @param mixed $cond
     * @param string $type 'and' | 'or'
     * @see Varien_Data_Collection_Db::addFieldToFilter()
     * @return Varien_Data_Collection_Filesystem
     */
    public function addFieldToFilter($field, $cond, $type = 'and')
    {
        $inverted = true;

        // simply check whether equals
        if (!is_array($cond)) {
            return $this->addCallbackFilter($field, $cond, $type, array($this, 'filterCallbackEq'));
        }

        // versatile filters
        if (isset($cond['from']) || isset($cond['to'])) {
            $this->_addFilterBracket('(', 'and' === $type);
            if (isset($cond['from'])) {
                $this->addCallbackFilter($field, $cond['from'], 'and', array($this, 'filterCallbackIsLessThan'), $inverted);
            }
            if (isset($cond['to'])) {
                $this->addCallbackFilter($field, $cond['to'], 'and', array($this, 'filterCallbackIsMoreThan'), $inverted);
            }
            return $this->_addFilterBracket(')');
        }
        if (isset($cond['eq'])) {
            return $this->addCallbackFilter($field, $cond['eq'], $type, array($this, 'filterCallbackEq'));
        }
        if (isset($cond['neq'])) {
            return $this->addCallbackFilter($field, $cond['neq'], $type, array($this, 'filterCallbackEq'), $inverted);
        }
        if (isset($cond['like'])) {
            return $this->addCallbackFilter($field, $cond['like'], $type, array($this, 'filterCallbackLike'));
        }
        if (isset($cond['nlike'])) {
            return $this->addCallbackFilter($field, $cond['nlike'], $type, array($this, 'filterCallbackLike'), $inverted);
        }
        if (isset($cond['in'])) {
            return $this->addCallbackFilter($field, $cond['in'], $type, array($this, 'filterCallbackInArray'));
        }
        if (isset($cond['nin'])) {
            return $this->addCallbackFilter($field, $cond['nin'], $type, array($this, 'filterCallbackIn'), $inverted);
        }
        if (isset($cond['notnull'])) {
            return $this->addCallbackFilter($field, $cond['notnull'], $type, array($this, 'filterCallbackIsNull'), $inverted);
        }
        if (isset($cond['null'])) {
            return $this->addCallbackFilter($field, $cond['null'], $type, array($this, 'filterCallbackIsNull'));
        }
        if (isset($cond['moreq'])) {
            return $this->addCallbackFilter($field, $cond['moreq'], $type, array($this, 'filterCallbackIsLessThan'), $inverted);
        }
        if (isset($cond['gt'])) {
            return $this->addCallbackFilter($field, $cond['gt'], $type, array($this, 'filterCallbackIsMoreThan'));
        }
        if (isset($cond['lt'])) {
            return $this->addCallbackFilter($field, $cond['lt'], $type, array($this, 'filterCallbackIsLessThan'));
        }
        if (isset($cond['gteq'])) {
            return $this->addCallbackFilter($field, $cond['gteq'], $type, array($this, 'filterCallbackIsLessThan'), $inverted);
        }
        if (isset($cond['lteq'])) {
            return $this->addCallbackFilter($field, $cond['lteq'], $type, array($this, 'filterCallbackIsMoreThan'), $inverted);
        }
        if (isset($cond['finset'])) {
            $filterValue = ($cond['finset'] ? explode(',', $cond['finset']) : array());
            return $this->addCallbackFilter($field, $filterValue, $type, array($this, 'filterCallbackInArray'));
        }

        // add OR recursively
        foreach ($cond as $orCond) {
            $this->_addFilterBracket('(', 'and' === $type);
            $this->addFieldToFilter($field, $orCond, 'or');
            $this->_addFilterBracket(')');
        }
        return $this;
    }

    /**
     * Prepare a bracket into filters
     *
     * @param string $bracket
     * @param bool $isAnd
     * @return Varien_Data_Collection_Filesystem
     */
    protected function _addFilterBracket($bracket = '(', $isAnd = true)
    {
        $this->_filterBrackets[$this->_filterIncrement] = array(
            'value' => $bracket === ')' ? ')' : '(',
            'is_and' => $isAnd,
        );
        $this->_filterIncrement++;
        return $this;
    }

    /**
     * Render condition sign before element, if required
     *
     * @param int $increment
     * @param bool $isAnd
     * @return string
     */
    protected function _renderConditionBeforeFilterElement($increment, $isAnd)
    {
        if (isset($this->_filterBrackets[$increment]) && ')' === $this->_filterBrackets[$increment]['value']) {
            return '';
        }
        $prevIncrement = $increment - 1;
        $prevBracket = false;
        if (isset($this->_filterBrackets[$prevIncrement])) {
            $prevBracket = $this->_filterBrackets[$prevIncrement]['value'];
        }
        if ($prevIncrement < 0 || $prevBracket === '(') {
            return '';
        }
        return ($isAnd ? ' && ' : ' || ');
    }

    /**
     * Does nothing. Intentionally disabled parent method
     *
     * @return Varien_Data_Collection_Filesystem
     */
    public function addFilter($field, $value, $type = 'and')
    {
        return $this;
    }

    /**
     * Get all ids of collected items
     *
     * @return array
     */
    public function getAllIds()
    {
        return array_keys($this->_items);
    }

    /**
     * Callback method for 'like' fancy filter
     *
     * @param string $field
     * @param mixed $filterValue
     * @param array $row
     * @return bool
     * @see addFieldToFilter()
     * @see addCallbackFilter()
     */
    public function filterCallbackLike($field, $filterValue, $row)
    {
        $filterValueRegex = str_replace('%', '(.*?)', preg_quote($filterValue, '/'));
        return (bool)preg_match("/^{$filterValueRegex}$/i", $row[$field]);
    }

    /**
     * Callback method for 'eq' fancy filter
     *
     * @param string $field
     * @param mixed $filterValue
     * @param array $row
     * @return bool
     * @see addFieldToFilter()
     * @see addCallbackFilter()
     */
    public function filterCallbackEq($field, $filterValue, $row)
    {
        return $filterValue == $row[$field];
    }

    /**
     * Callback method for 'in' fancy filter
     *
     * @param string $field
     * @param mixed $filterValue
     * @param array $row
     * @return bool
     * @see addFieldToFilter()
     * @see addCallbackFilter()
     */
    public function filterCallbackInArray($field, $filterValue, $row)
    {
        return in_array($row[$field], $filterValue);
    }

    /**
     * Callback method for 'isnull' fancy filter
     *
     * @param string $field
     * @param mixed $filterValue
     * @param array $row
     * @return bool
     * @see addFieldToFilter()
     * @see addCallbackFilter()
     */
    public function filterCallbackIsNull($field, $filterValue, $row)
    {
        return null === $row[$field];
    }

    /**
     * Callback method for 'moreq' fancy filter
     *
     * @param string $field
     * @param mixed $filterValue
     * @param array $row
     * @return bool
     * @see addFieldToFilter()
     * @see addCallbackFilter()
     */
    public function filterCallbackIsMoreThan($field, $filterValue, $row)
    {
        return $row[$field] > $filterValue;
    }

    /**
     * Callback method for 'lteq' fancy filter
     *
     * @param string $field
     * @param mixed $filterValue
     * @param array $row
     * @return bool
     * @see addFieldToFilter()
     * @see addCallbackFilter()
     */
    public function filterCallbackIsLessThan($field, $filterValue, $row)
    {
        return $row[$field] < $filterValue;
    }

}
