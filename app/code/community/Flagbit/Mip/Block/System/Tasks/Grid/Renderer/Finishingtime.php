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
class Flagbit_Mip_Block_System_Tasks_Grid_Renderer_Finishingtime extends Mage_Adminhtml_Block_Widget_Grid_Column_Renderer_Datetime {


    /**
     * Renders grid column
     *
     * @param   Varien_Object $row
     * @return  string
     */
    public function render(Varien_Object $row) {

        /**
         * TODO: implement time left
         *
         *
         * time left =
         * ((time needed for all now imported split packages with the child ids) / (count of all now imported split packages)) * all packages in dataqueue with parent id)
         *
         * currentId = $id
         * $curDataColl: SELECT * FROM_ mip_task WHERE parent_task_id = $id
         * $curQueueColl: SELECT * FROM mip_dataqueue WHERE patend_task_id = $id
         *
         * Bis jetzt benötigte Zeit: $curDataColl[lastElem][finished_at] - $curQueueColl[firstElem][executed_at]
         **/

        if ($row->getParentTaskId() && $row->getStatus() == 'success'){
            return parent::render($row);
         }

        if (!$row->getParentTaskId() && $row->getStatus() == 'running'){
            return $this->__('n/a');
        }

        if (!$row->getParentTaskId() ){
            return parent::render($row);

        } else {

            /* @var $parallels Flagbit_Mip_Model_Mysql4_Task_Collection */
            $parallels = Mage::getResourceModel('mip/task_collection');
            $parallels
                ->addFieldToFilter('parent_task_id', array('eq' => $row->getParentTaskId(), 'neq' => $row->getTaskId()))
                ->addFieldToFilter('task_id', array('neq' => $row->getTaskId()))
                ->setOrder('finished_at', 'DESC');

            $parallels->load();

            $parent = Mage::getResourceModel('mip/task_collection');
            $parent->addFieldToFilter('task_id', array('eq' => $row->getParentTaskId()));
            $parent->getSelect();
            $parent->getFirstItem();

            $parallelsCount = $parallels->count();

            if (!$parallelsCount){
                $parallelsCount = 1;
            }

            $lastParallelsChild = $parallels->getFirstItem();


            $timeNeededAll = strtotime($lastParallelsChild->getFinishedAt()) - strtotime($parent->getFirstItem()->getExecutedAt());
            $timeNeeded = round($timeNeededAll / ($parallelsCount + 1), 0);

            $row->setFinishedAt(date('Y-m-d H:i:s',strtotime($row->getExecutedAt()) + $timeNeeded));
            $finishingTime = '~ ' . parent::render($row);

            return $finishingTime;

        }
    }
}
