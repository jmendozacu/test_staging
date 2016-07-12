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
class Flagbit_Mip_Model_Task extends Mage_Core_Model_Abstract
{
    protected $_eventPrefix = 'mip_task';
    const XML_PATH_TASK_LIFETIME  = 'mip_core/settings/task_lifetime';

    protected function _construct()
    {
        $this->_init('mip/task');
    }

    /**
     * Processing object before save data
     *
     * @return Mage_Core_Model_Abstract
     */
    protected function _beforeSave()
    {
        if ($this->getStatus() == 'running' or $this->getExecutedAt() === null) {
            $this->setExecutedAt(now());
        }

        if($this->getStatus() != 'running'){
            $this->setFinishedAt(now());
        }

        $definition = Flagbit_Mip_Model_Server::getConfig()->getCurrentDefinition();
        if($definition instanceof Flagbit_Mip_Model_Config_Importexport_Interface){
            $this->setType($definition->getType());
            $this->setCode($definition->getCode());
            if($this->_getTrigger() instanceof Flagbit_Mip_Model_Server_Trigger_Queue){
                $this->setDataqueueId($definition->getSettings('dataqueue_id'));
                $this->setParentTaskId($definition->getSettings('parent_task_id'));
            }
        }

        return parent::_beforeSave();
    }

    /**
     * get Trigger
     *
     * @return Flagbit_Mip_Model_Server_Trigger_Abstract
     */
    protected function _getTrigger(){
        return Mage::getSingleton('mip/server')->getTrigger();
    }

    /**
     * count running lock Tasks
     *
     * @return int
     */
    public function countRunningLockTasks(){

        $lifetime = Mage::getStoreConfig(self::XML_PATH_TASK_LIFETIME);

        $counter = $this->getCollection()
            ->addFieldToFilter('main_table.status', array('eq' => 'running'))
            ->addFieldToFilter('main_table.lock', array('eq' => '1'));

        if ($lifetime){
            $counter->addFieldToFilter('map.executed', array('gt' => time() - $lifetime));
        }
        return $counter->count();
    }

    public function removeRunningTasks($msg = '')
    {
        /*@var $collection Flagbit_Mip_Model_Mysql4_Task_Collection */
        $collection = $this->getCollection();

        $collection->addFieldToFilter('main_table.status', array('eq' => 'running'));
        $collection->addFieldToFilter('main_table.lock', array('eq' => '1'));

        $collection->setDataToAll('status', 'expired');
        $collection->setDataToAll('messages', $msg);
        $collection->save();

        return $collection->count();
    }

    /**
     * remove expired Tasks
     *
     * @return Flagbit_Mip_Model_Task
     */
    public function removeExpiredTasks(){

        $lifetime = Mage::getStoreConfig(self::XML_PATH_TASK_LIFETIME);

        /*@var $collection Flagbit_Mip_Model_Mysql4_Task_Collection */
        $collection = $this->getCollection();

        if ($lifetime){
            $collection->addFieldToFilter('map.executed', array('lt' => time() - $lifetime));
            $collection->addFieldToFilter('main_table.status', array('eq' => 'running'));
            $collection->addFieldToFilter('main_table.lock', array('eq' => '1'));

            $collection->setDataToAll('status', 'expired');
            $collection->setDataToAll('messages', Mage::helper('mip')->__('automatically expired (TTL: %s)', $lifetime));
            $collection->save();
        }
        return $this;
    }
}