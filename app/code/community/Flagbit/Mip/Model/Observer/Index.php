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
class Flagbit_Mip_Model_Observer_Index {

    protected $_disabledIndexes = array();

    /**
     * disable Magento Index
     *
     * @param Varien_Event_Observer $observer
     */
    public function disable($observer)
    {
        $settings = $observer->getSettings();

        if(isset($settings['disable_index'])){
            $this->_disabledIndexes = array();
            $indexArray = Mage::helper('mip')->trimExplode(',', (string) $settings['disable_index']);
            $processCollection = Mage::getSingleton('index/indexer')->getProcessesCollection();
            foreach ($processCollection as $process) {
                if((!in_array($process->getIndexerCode(), $indexArray)
                        && !in_array('all', $indexArray))
                    || $process->getMode() != Mage_Index_Model_Process::MODE_REAL_TIME) {
                    continue;
                }
                $this->_disabledIndexes[] = $process->getIndexerCode();
                Mage::helper('mip/log')->getWriter($this)->info('INDEX set MODE_MANUAL for "'.$process->getIndexerCode().'"');
                $process->setMode(Mage_Index_Model_Process::MODE_MANUAL)->save();
            }

        }
    }


    /**
     * enable Magento Index
     *
     * @param Varien_Event_Observer $observer
     */
    public function enable($observer)
    {
        $settings = new Varien_Object($observer->getSettings());
        if(isset($settings['disable_index'])){
            if(!count($this->_disabledIndexes)){
                $this->_disabledIndexes = Mage::helper('mip')->trimExplode(',', (string) $settings['disable_index']);
            }

            $processCollection = Mage::getSingleton('index/indexer')->getProcessesCollection();
            foreach ($processCollection as $process) {
                if(!in_array($process->getIndexerCode(), $this->_disabledIndexes)
                    && !in_array('all', $this->_disabledIndexes)){
                    continue;
                }
                Mage::helper('mip/log')->getWriter($this)->info('INDEX set MODE_REAL_TIME for "'.$process->getIndexerCode().'"');
                $process->setMode(Mage_Index_Model_Process::MODE_REAL_TIME)->save();
            }

        }
    }

    /**
     * build Magento Index
     *
     * @param Varien_Event_Observer $observer
     */
    public function reindex($observer)
    {
        if(!($observer->getResult() instanceof ArrayObject)) {
            return $this;
        }

        $settings = $observer->getSettings();
        if(isset($settings['rebuild_index'])){

            $indexArray = Mage::helper('mip')->trimExplode(',', (string) $settings['rebuild_index']);
            $processCollection = Mage::getSingleton('index/indexer')->getProcessesCollection();
            foreach ($processCollection as $process) {
                if(!in_array($process->getIndexerCode(), $indexArray)
                && !in_array('all', $indexArray)){
                    continue;
                }
                Mage::helper('mip/log')->getWriter($this)->info('REINDEX "'.$process->getIndexerCode().'"');
                $this->_doReindexAction($process->getIndexerCode());
            }
        }
    }

    /**
     * do reindexing
     *
     * @param string $action
     */
    protected function _doReindexAction($action)
    {
        if(Mage::helper('mip')->compareVersion('1.4', '1.6')){

            try {
                $indexer = Mage::getSingleton('index/indexer');
                $process = $indexer->getProcessByCode($action);
                if ($process) {
                    $process->reindexEverything();
                }
            } catch ( Exception $e ) {
                Mage::helper('mip/log')->getWriter($this)->error($e->getMessage(), $e);
            }

        }else{

            try {
                switch ($action) {
                    case 'catalog_index' :
                        Mage::getSingleton ( 'catalog/index' )->rebuild ();
                        break;

                    case 'layered_navigation' :
                        $flag = Mage::getModel ( 'catalogindex/catalog_index_flag' )->loadSelf ();
                        if ($flag->getState () == Mage_CatalogIndex_Model_Catalog_Index_Flag::STATE_RUNNING) {
                            $kill = Mage::getModel ( 'catalogindex/catalog_index_kill_flag' )->loadSelf ();
                            $kill->setFlagData ( $flag->getFlagData () )->save ();
                        }

                        $flag->setState ( Mage_CatalogIndex_Model_Catalog_Index_Flag::STATE_QUEUED )->save ();
                        Mage::getSingleton ( 'catalogindex/indexer' )->plainReindex ();
                        break;

                    case 'images_cache' :
                        Mage::getModel ( 'catalog/product_image' )->clearCache ();
                        break;

                    case 'catalog_url' :
                        Mage::getSingleton ( 'catalog/url' )->refreshRewrites ();
                        break;

                    case 'catalog_product_flat' :
                        Mage::getResourceModel ( 'catalog/product_flat_indexer' )->rebuild ();
                        break;

                    case 'catalog_category_flat' :
                        Mage::getResourceModel ( 'catalog/category_flat' )->rebuild ();
                        break;

                    case 'catalogsearch_fulltext' :
                        Mage::getSingleton ( 'catalogsearch/fulltext' )->rebuildIndex ();
                        break;

                    case 'cataloginventory_stock' :
                        Mage::getSingleton ( 'cataloginventory/stock_status' )->rebuild ();
                        break;
                }
            } catch ( Exception $e ) {
                Mage::helper('mip/log')->getWriter($this)->error($e->getMessage(), $e);
            }
        }
    }

}