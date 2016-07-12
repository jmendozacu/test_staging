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
class Flagbit_Mip_Model_Mysql4_Data_Queue extends Mage_Core_Model_Mysql4_Abstract
{
    /**
     * construct
     *
     */
    protected function _construct()
    {
        $this->_init('mip/data_queue', 'dataqueue_id');
    }

    /**
     * store Data Item in Queue
     *
     * @param string $resource
     * @param string $action
     * @param string $direction
     * @param array $data
     * @return boolean
     */
    public function storeQueueItem($interface, $resource, $action, $direction, $data, $definition){

        $wa = $this->_getWriteAdapter();

        $data = is_array($data) ? serialize($data) : $data;
        $hash = $this->getHash($data);

        $parentTaskId = Mage::getSingleton('mip/server')->getTaskModel()->save()->getId();

        // to not create duplicates
        if($this->getQueueItemExistsByHash($hash)){
            return false;
        }

        $definition->setSettings(array('parent_task_id' => $parentTaskId));

        return $wa->insert($this->getMainTable(), array(
            'parent_task_id' => $parentTaskId,
            'interface' => $interface,
            'resource' => $resource,
            'action' => $action,
            'direction' => $direction,
            'hash' => $hash,
            'data' => $data,
            'definition' => serialize($definition)
        ));
    }

    public function deleteEntriesByType(array $types){

        $this->_getWriteAdapter()->delete($this->getMainTable(), $this->_getWriteAdapter()->quoteInto('concat(`resource`,`action`,`direction`) IN(?)', $types));

    }


    /**
     * get an Item from Queue
     *
     * @param unknown_type $delete
     */
    public function getQueueItem($delete = true){

        $query = $this->getReadConnection()->select()->from($this->getMainTable())->limit(1)->order('dataqueue_id ASC');
        $item = $this->getReadConnection()->fetchRow($query);

        if(isset($item['dataqueue_id'])){

            if($delete == true){
                $this->_getWriteAdapter()->delete(
                    $this->getMainTable(),
                    $this->_getWriteAdapter()->quoteInto('dataqueue_id IN(?)', $item['dataqueue_id'])
                );
            }

            $item['data'] = unserialize($item['data']);
            $item['definition'] = unserialize($item['definition']);
            $item['definition']->setSettings(array('dataqueue_id' => $item['dataqueue_id']));

            return $item;
        }

        return false;
    }

    public function getQueueItemExistsByHash($hash){

        $query = $this->getReadConnection()->select()->from($this->getMainTable())->where('hash=?', $hash);

        return $this->getReadConnection()->fetchRow($query);
    }

    /**
     * generate Data Hash
     *
     * @param array|string $data
     * @return string
     */
    public function getHash($data){

        if(is_array($data)){
            $data = serialize($data);
        }

        return md5($data);
    }

}
