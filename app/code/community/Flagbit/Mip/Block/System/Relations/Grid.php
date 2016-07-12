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
class Flagbit_Mip_Block_System_Relations_Grid extends Mage_Adminhtml_Block_Widget_Grid
{

    protected $_dataType = Flagbit_Mip_Model_Resource_Customer::RELATION_TYPE;

    public function __construct()
    {
        parent::__construct();
        $this->setId('relationsGrid');
        $this->setDefaultSort('updated_at');
        $this->setDefaultDir('desc');
        $this->setSaveParametersInSession(true);
        //$this->setUseAjax(true);

    }


    protected function _prepareCollection()
    {
        /*@var $collection Flagbit_Mip_Model_Mysql4_Data_Relation_Collection */
        $collection = Mage::getResourceModel('mip/data_relation_collection');

        $this->setCollection($collection);
        parent::_prepareCollection();
    }


    protected function _prepareColumns()
    {
        $relationCollection = Mage::getModel('mip/data_relation')->getCollection();

        $this->addColumn('mage_type',
            array(
                'header'    => Mage::helper('mip')->__('Parent Type'),
                'type'        => 'options',
                'options'    => $relationCollection->toOptionPairsArray('mage_type', 'mage_type'),
                'index'        => 'mage_type',
        ));

        $this->addColumn('resource_type',
            array(
                'header'=> Mage::helper('mip')->__('Type'),
                'type'        => 'options',
                'options'    => $relationCollection->toOptionPairsArray('resource_type', 'resource_type'),
                'index'        => 'resource_type',
        ));

        $this->addColumn('mage_id',
            array(
                'header'=> Mage::helper('mip')->__('Parent ID'),
                'type'        => 'number',
                'index'        => 'mage_id',
        ));

        $this->addColumn('resource_id',
            array(
                'header'=> Mage::helper('mip')->__('ID'),
                'type'        => 'text',
                'index'        => 'resource_id',
        ));

        $this->addColumn('interface',
            array(
                'header'    => Mage::helper('mip')->__('Interface'),
                'options'    => $relationCollection->toOptionPairsArray('interface', 'interface'),
                'type'        => 'options',
                'index'        => 'interface',
        ));

        $this->addColumn('ext_id',
            array(
                'header'=> Mage::helper('mip')->__('External Identifier'),
                'type'        => 'text',
                'index'        => 'ext_id',
        ));



        $this->addColumn('created_at',
            array(
                'header'=> Mage::helper('mip')->__('Created At'),

                'type'  => 'datetime',
                'index' => 'created_at',
        ));

        $this->addColumn('updated_at',
            array(
                'header'=> Mage::helper('mip')->__('Updated At'),

                'type'  => 'datetime',
                'index' => 'updated_at',
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
            case 'error' :
                $class = 'grid-severity-critical';
                break;
        }
        return '<span class="'.$class.'"><span>'.$value.'</span></span>';
    }

    protected function _prepareMassaction()
    {
        $this->setMassactionIdField('relation_id');
        $this->getMassactionBlock()->setFormFieldName('ident');

        $this->setMassactionIdFieldOnlyIndexValue(true);


        $this->getMassactionBlock()->addItem('deletetasks', array(
             'label'=> Mage::helper('mip')->__('Delete Relations'),
             'url'  => $this->getUrl('*/*/massDeleteRelations'),
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
