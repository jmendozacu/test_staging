<?php

class Flagbit_Mip_Block_Dashboard_Process_Grid extends Mage_Adminhtml_Block_Dashboard_Grid
{

   public function __construct()
    {
        parent::__construct();
        $this->setDefaultSort('task_id');
        $this->setDefaultDir('desc');
        $this->setId('mipProcessGrid');
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

        $this->addColumn('task_id', array(
            'header'    => $this->__('ID'),
            'sortable'  => false,
            'index'     => 'task_id',
             'width'        => '25px',
            'default'   => $this->__('ID'),
        ));

        $this->addColumn('resource', array(
            'header'    => $this->__('Resoucre'),
            'sortable'  => false,
                'type'        => 'options',
                'options'    => $taskCollection->toOptionPairsArray('resource', 'resource'),
                'width'        => '30px',
                'index'        => 'resource',
        ));

       $this->addColumn('direction', array(
            'header'    => $this->__('Direction'),
            'sortable'  => false,
            'options'    => $taskCollection->toOptionPairsArray('direction', 'direction'),
            'width'        => '30px',
            'type'        => 'options',
            'index'        => 'direction',
        ));

        $this->addColumn('status', array(
            'header'    => $this->__('Status'),
            'options'    => $taskCollection->toOptionPairsArray('status', 'status'),
            'type'        => 'options',
            'width'        => '30px',
            'index'        => 'status',
            'frame_callback' => array($this, 'decorateStatus')
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

        $this->setFilterVisibility(false);
        $this->setPagerVisibility(false);

        return parent::_prepareColumns();
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
            case 'error' :
                $class = 'grid-severity-critical';
                break;
        }
        return '<span class="'.$class.'"><span>'.$value.'</span></span>';
    }

}
