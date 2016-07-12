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
class Flagbit_Mip_Model_Observer_Cache {

    /**
     * flush Magento Cache
     *
     * @param Varien_Event_Observer $observer
     */
    public function deleteCache($observer)
    {
        $settings = $observer->getSettings();
        if(isset($settings['delete_cache'])){

            $cache = Mage::helper('mip')->trimExplode(',', (string) $settings['delete_cache']);
            foreach($cache as $type){
                Mage::helper('mip/log')->getWriter($this)->info('CACHE FLUSH "'.$type.'"');
                Mage::app()->getCacheInstance()->cleanType($type);
            }
        }
    }

    /**
     * disable automatic cache cleaning to improve import performance
     *
     * @param Varien_Event_Observer $observer
     */
    public function disableCacheCleaning($observer)
    {
        $settings = $observer->getSettings();
        if(isset($settings['disable_cache_cleaning'])){
            $cacheInstance = Mage::app()->getCacheInstance();
            if($cacheInstance instanceof Flagbit_Mip_Model_Cache){
                Mage::helper('mip/log')->getWriter($this)->info('CACHE CLEANING DISABLED');
                $cacheInstance->setCleaningDisabled();
            }else{
                Mage::helper('mip/log')->getWriter($this)->error('CACHE CLEANING: cannot disable Cache because core model was not rewritten');
            }
        }
    }

    /**
     * enable automatic cache cleaning to improve import performance
     *
     * @param Varien_Event_Observer $observer
     */
    public function enableCacheCleaning($observer)
    {
        $settings = $observer->getSettings();
        if(isset($settings['disable_cache_cleaning'])){
            $cacheInstance = Mage::app()->getCacheInstance();
            if($cacheInstance instanceof Flagbit_Mip_Model_Cache){
                Mage::helper('mip/log')->getWriter($this)->info('CACHE CLEANING ENABLED');
                $cacheInstance->setCleaningDisabled();
            }
        }
    }
}