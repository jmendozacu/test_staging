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
class Flagbit_Mip_Block_System_Tasks_Grid extends Mage_Adminhtml_Block_Widget_Grid
{

    protected $_dataType = Flagbit_Mip_Model_Resource_Customer::RELATION_TYPE;

    public function __construct()
    {
        parent::__construct();
        $this->setId('tasksGrid');
        $this->setDefaultSort('executed_at');
        $this->setDefaultDir('desc');
        $this->setSaveParametersInSession(true);
        //$this->setUseAjax(true);
        $this->setVarNameFilter('tasks_filter');

    }


    protected function _prepareCollection()
    {
        /*@var $collection Flagbit_Mip_Model_Mysql4_Task_Collection */
        $collection = Mage::getResourceModel('mip/task_collection');

        $collection->getSelect()->from(
            null,
            array(
                'finished_at'    => 'IF(main_table.finished_at=\'0000-00-00 00:00:00\', NULL, main_table.finished_at)',
                'difference'    => 'IF(main_table.finished_at=\'0000-00-00 00:00:00\', NULL, main_table.finished_at - main_table.executed_at)'
            )
        );

        $this->setCollection($collection);
        parent::_prepareCollection();
    }


    protected function _prepareColumns()
    {
        $taskCollection = Mage::getModel('mip/task')->getCollection();

        $this->addColumn('task_id',
            array(
                'header'    => Mage::helper('mip')->__('Id'),
                'width'        => '50px',
                'type'        => 'number',
                'index'        => 'task_id',
        ));
        $this->addColumn('interface',
            array(
                'header'    => Mage::helper('mip')->__('Interface'),
                'width'        => '50px',
                'type'        => 'options',
                'options'    => $taskCollection->toOptionPairsArray('interface', 'interface'),
                'index'        => 'interface',
        ));

        $this->addColumn('resource',
            array(
                'header'=> Mage::helper('mip')->__('Resource'),
                'width'     => '50px',
                'type'        => 'options',
                'options'    => $taskCollection->toOptionPairsArray('resource', 'resource'),
                'index'        => 'resource',
        ));

        $this->addColumn('action',
            array(
                'header'=> Mage::helper('mip')->__('Action'),
                'width'        => '50px',
                'options'    => $taskCollection->toOptionPairsArray('action', 'action'),
                'type'        => 'options',
                'index'        => 'action',
        ));

        $this->addColumn('direction',
            array(
                'header'    => Mage::helper('mip')->__('Direction'),
                'width'        => '50px',
                'options'    => $taskCollection->toOptionPairsArray('direction', 'direction'),
                'type'        => 'options',
                'index'        => 'direction',
        ));

        $this->addColumn('status',
            array(
                'header'=> Mage::helper('mip')->__('Status'),
                'width'        => '50px',
                'options'    => $taskCollection->toOptionPairsArray('status', 'status'),
                'type'        => 'options',
                'index'        => 'status',
                'frame_callback' => array($this, 'decorateStatus')
        ));

        $this->addColumn('messages',
            array(
                'header'=> Mage::helper('mip')->__('Messages'),
                'width' => '80px',
                'type'  => 'longtext',
                'index' => 'messages',
        ));


        $this->addColumn('executed_at',
            array(
                'header'=> Mage::helper('mip')->__('Execution Time'),
                'width' => '50px',
                'type'  => 'datetime',
                'index' => 'executed_at',
        ));

        $this->addColumn('finished_at',
            array(
                'header'        => Mage::helper('mip')->__('Finishing Time'),
                'width'            => '120px',
                'sortable'        => false,
                'filter'        => false,
                'index'            => 'finished_at',
                'renderer'        => 'mip/system_tasks_grid_renderer_finishingtime'
        ));


        $this->addColumn('lead_time',
            array(
                'header'        => Mage::helper('mip')->__('Lead Time'),
                'width'            => '50px',
                'sortable'        => false,
                'filter'        => false,
                'renderer'        => 'mip/system_tasks_grid_renderer_leadtime'
        ));

        $this->addColumn('lock',
            array(
                'header'=> Mage::helper('mip')->__('Lock'),
                'type'  => 'options',
                'index' => 'lock',
                'filter_index' => 'main_table.lock',
                'width'    => '30px',
                'options'=>array('0' => Mage::helper('mip')->__('No'), '1' => Mage::helper('mip')->__('Yes'), )
        ));

        $this->addColumn('parent_task_id',
            array(
                'header'=> Mage::helper('mip')->__('Parent'),
                'renderer'        => 'mip/system_tasks_grid_renderer_parent',
                'filter'        => 'mip/system_tasks_grid_filter_parent',
                'index' => 'parent_task_id',
                'filter_index' => 'main_table.parent_task_id',
                'width'    => '30px'
        ));


        return parent::_prepareColumns();
    }

    /**
     * Decorate status column values
     *
     * @return string
     */
    public function decorateStatus($value, $row, $column, $isExport)
    {
        $class = '';
        switch ($row->getStatus()) {
            case 'success' :
                $class = 'grid-severity-notice';
                break;
            case 'running' :
                $class = 'grid-severity-major';
                break;
            case 'expired' :
            case 'error' :
                $class = 'grid-severity-critical';
                break;
        }
        return '<span class="'.$class.'"><span>'.$value.'</span></span>';
    }

    protected function _prepareMassaction()
    {
        $this->setMassactionIdField('task_id');
        $this->getMassactionBlock()->setFormFieldName('ident');

        $this->setMassactionIdFieldOnlyIndexValue(true);


        $this->getMassactionBlock()->addItem('deletetasks', array(
             'label'=> Mage::helper('mip')->__('Delete Tasks'),
             'url'  => $this->getUrl('*/*/massDeleteTasks'),
             'confirm' => Mage::helper('mip')->__('Are you sure?')
        ));

        return $this;
    }

    /**
     * Return row url for js event handlers
     *
     * @param Mage_Catalog_Model_Product|Varien_Object
     * @return string
     */
    public function getRowUrl($item)
    {
        return '#';
    }


    public function getGridUrl()
    {
        return $this->getUrl('*/*/index', array('_current'=>true));
    }

}
