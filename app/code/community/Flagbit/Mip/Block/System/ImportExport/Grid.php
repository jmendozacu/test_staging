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
class Flagbit_Mip_Block_System_Importexport_Grid extends Mage_Adminhtml_Block_Widget_Grid
{

    public function __construct()
    {
        parent::__construct();
        $this->setId('importexportGrid');
        $this->setDefaultSort('name');
        $this->setDefaultDir('asc');
        $this->setSaveParametersInSession(true);
        $this->setFilterVisibility(false);
        //$this->setUseAjax(true);
        $this->setVarNameFilter('importexport_filter');

    }


    protected function _prepareCollection()
    {
        /*@var $collection Flagbit_Mip_Model_Mysql4_Config_Importexport_Collection */
        $collection = Mage::getResourceModel('mip/config_importexport_collection');
        $collection->setAddTaskData(true);
        $this->setCollection($collection);
        parent::_prepareCollection();
    }


    protected function _prepareColumns()
    {
        $collection = Mage::getResourceModel('mip/config_importexport_collection');
        $taskCollection = Mage::getModel('mip/task')->getCollection();

        $this->addColumn('name',
            array(
                'header'        => Mage::helper('mip')->__('Name'),
                'type'          => 'text',
                'index'         => 'code',
        ));
        $this->addColumn('type',
            array(
                'header'        => Mage::helper('mip')->__('Type'),
                'type'          => 'options',
                'options'       => $collection->getTypeOptionsPairs(),
                'index'         => 'type',
        ));

        $this->addColumn('interface',
            array(
                'header'        => Mage::helper('mip')->__('Interface'),
                'type'          => 'text',
                'index'         => 'interface',
        ));

        $this->addColumn('messages', array (
               'header'         => Mage::helper('mip')->__('Messages'),
               'index'          => 'messages',
               'frame_callback' => array($this, 'decorateMessages')
       ));

        $this->addColumn('queue_size',
            array(
                'header'        => Mage::helper('mip')->__('Queue'),
                'width'         => '50px',
                'type'          => 'int',
                'index'         => 'queue_size',
        ));

        $this->addColumn('status',
            array(
                'header'        => Mage::helper('mip')->__('Status'),
                'width'         => '50px',
                'options'       => array(
                            'running'   => $this->__('running'),
                            'success'   => $this->__('success'),
                            'error'     => $this->__('error'),
                            'expired'   => $this->__('expired'),
                        ),
                'type'          => 'options',
                'index'         => 'status',
                'frame_callback'=> array($this, 'decorateStatus')
        ));

        $this->addColumn('lead_time',
            array(
                'header'        => Mage::helper('mip')->__('Lead Time'),
                'css_property'  => 'width: 60px',
                'sortable'      => false,
                'filter'        => false,
                'renderer'      => 'mip/system_importexport_grid_renderer_leadtime'
        ));

        $this->addColumn('executed_at',
            array(
                'header'        => Mage::helper('mip')->__('Execution Time'),
                'type'          => 'datetime',
                'css_property'  => 'width: 50px',
                'index'         => 'executed_at',
        ));

        $this->addColumn('finished_at',
            array(
                'header'        => Mage::helper('mip')->__('Finishing Time'),
                'type'          => 'datetime',
                'css_property'  => 'width: 50px',
                'index'         => 'finished_at',
        ));


        return parent::_prepareColumns();
    }

    /**
    * Decorate message
    *
    * @param string $value
    * @param $row
    * @return string
    */
    public function decorateMessages($value, $row) {
        $return = '';
        if (!empty($value)) {
            $return .= '<a href="#" onclick="$(\'messages_'.$row->getId().'\').toggle(); return false;">'.$this->__('Message').'</a>';
            $return .= '<div class="schedule-message" id="messages_'.$row->getId().'" style="display: none; width: 300px; overflow: auto; font-size: small;"><pre>'.$value.'</pre></div>';
        }
        return $return;
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

    public function getGridUrl()
    {
        return $this->getUrl('*/*/index', array('_current'=>true));
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

}
