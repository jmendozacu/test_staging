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
class Flagbit_Mip_Block_System_Manager_Tab_Runtime extends Mage_Adminhtml_Block_Widget_Form
{

    public function initForm()
    {

        $form = new Varien_Data_Form();
        $form->setHtmlIdPrefix('manager_');

        $fieldset1 = $form->addFieldset('server_fieldset', array('legend'=>Mage::helper('mip')->__('System'),'class'=>'fieldset-wide'));
        $fieldset1->addField('version', 'note', array(
            'name'      => 'version',
            'text'        => (string) Mage::getConfig()->getModuleConfig('Flagbit_Mip')->version,
            'label'     => Mage::helper('mip')->__('Version'),
        ));
        $fieldset1->addField('libxsl', 'note', array(
            'name'      => 'libxsl',
            'text'        => class_exists('XSLTProcessor', false) ? '<strong style="color: green">installed</strong>' : '<strong style="color: red">not installed</strong>',
            'label'     => Mage::helper('mip')->__('XSLTProcessor'),
        ));

        /*@var $scheduleCollection Mage_Cron_Model_Mysql4_Schedule_Collection */
        $scheduleCollection = Mage::getResourceModel('cron/schedule_collection');
        $scheduleCollection->getSelect()->order('created_at DESC')->limit(1);
        $cronjobsRunning = (strtotime ($scheduleCollection->getFirstItem()->getCreatedAt()) >= time() - Mage::getStoreConfig(Mage_Cron_Model_Observer::XML_PATH_SCHEDULE_GENERATE_EVERY)*60);

        $fieldset1->addField('cron', 'note', array(
            'name'      => 'cron',
            'text'        => $cronjobsRunning ? '<strong style="color: green">installed</strong>' : '<strong style="color: red">not installed</strong>',
            'label'     => Mage::helper('mip')->__('Cronjob'),
        ));

        $fieldset1->addField('memory_limit', 'note', array(
            'name'      => 'memory_limit',
            'text'        => ini_get('memory_limit'),
            'label'     => Mage::helper('mip')->__('PHP Memory Limit'),
        ));

        $fieldset1->addField('max_execution_time', 'note', array(
            'name'      => 'max_execution_time',
            'text'        => ini_get('max_execution_time'),
            'label'     => Mage::helper('mip')->__('PHP max Execution Time'),
        ));

        $fieldset2 = $form->addFieldset('relations_fieldset', array('legend'=>Mage::helper('mip')->__('Relations'),'class'=>'fieldset-wide'));

        $relationValues = array();
        $relationValues[] = array('label' => Mage :: helper('mip')->__('---'), 'value' => 0);

        $relations = Mage :: getModel('mip/data_relation')->getCollection();
        $relations
            ->getSelect()
            ->from(null, array('type_count' => 'COUNT(main_table.mage_type)'))
            ->group('mage_type');

        foreach($relations->getItems() as $relation) {
            $relationValues[] = array('label' => $relation->getMageType() . ' (' . $relation->getTypeCount() . ')', 'value' => $relation->getMageType());
        }

        $fieldset2->addField('clean_relations', 'multiselect',
                array (
                        'label' => Mage :: helper('mip')->__('Clean Relations'),
                        'title' => Mage :: helper('mip')->__('Clean Relations'),
                        'name' => 'clean_relations',
                        'required' => false,
                        'values' => $relationValues
                )
        )->setSize(5);

        $fieldset3 = $form->addFieldset('datahashes_fieldset', array('legend'=>Mage::helper('mip')->__('Datahashes'),'class'=>'fieldset-wide'));

        $datahashValues = array();
        $datahashValues[] = array('label' => Mage :: helper('mip')->__('---'), 'value' => 0);

        $datahashes = Mage :: getModel('mip/data_hash')->getCollection();
        $datahashes
            ->getSelect()
            ->from(null, array('type_count' => 'COUNT(main_table.type)'))
            ->group('type');

        foreach($datahashes->getItems() as $datahash) {
            $datahashValues[] = array('label' => $datahash->getType() . ' (' . $datahash->getTypeCount() . ')', 'value' => $datahash->getType());
        }

        $fieldset3->addField('clean_datahashes', 'multiselect',
                array (
                        'label' => Mage :: helper('mip')->__('Clean Datahashes'),
                        'title' => Mage :: helper('mip')->__('Clean Datahashes'),
                        'name' => 'clean_datahashes',
                        'required' => false,
                        'values' => $datahashValues
                )
        )->setSize(5);

        $fieldset4 = $form->addFieldset('dataqueue_fieldset', array('legend'=>Mage::helper('mip')->__('Dataqueue'),'class'=>'fieldset-wide'));

        $dataqueueValues = array();
        $dataqueueValues[] = array('label' => Mage :: helper('mip')->__('---'), 'value' => 0);

        $dataqueuesResource = Mage :: getModel('mip/data_queue')->getResource();
        $dataqueuesReadCon = $dataqueuesResource->getReadConnection();

        $select = $dataqueuesReadCon
            ->select()
            ->from($dataqueuesResource->getMainTable(),
                array(
                    'type_count' => 'COUNT(concat(`resource`,`action`,`direction`))',
                    'resource' => 'resource',
                    'action' => 'action',
                    'direction' => 'direction',
                )
            )
            ->group('concat(`resource`,`action`,`direction`)');

        $dataqueues = $dataqueuesReadCon->fetchAll($select);
        $dataqueuesCount = 0;

        foreach($dataqueues as $dataqueue) {
            $dataqueuesCount += $dataqueue['type_count'];
            $dataqueueValues[] = array('label' => $dataqueue['resource'].' - '.$dataqueue['action'].' - '.$dataqueue['direction'] . ' (' . $dataqueue['type_count'] . ')', 'value' =>$dataqueue['resource'].$dataqueue['action'].$dataqueue['direction']);
        }

        $fieldset4->addField('clean_dataqueues', 'multiselect',
                array (
                        'label' => Mage :: helper('mip')->__('Clean Dataqueue',$dataqueuesCount),
                        'title' => Mage :: helper('mip')->__('Clean Dataqueue'),
                        'name' => 'clean_dataqueues',
                        'required' => false,
                        'values' => $dataqueueValues
                )
        )->setSize(5);

        $this->setForm($form);
        $this->toHtml();

        return $this;
    }
}
