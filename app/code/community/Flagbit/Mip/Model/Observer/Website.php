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
class Flagbit_Mip_Model_Observer_Website {

    protected $_skuToIdMapping = null;

    /**
     * flush Magento Cache
     *
     * @param Varien_Event_Observer $observer
     */
    public function collect($observer)
    {
        $settings = $observer->getSettings();
        if(isset($settings->websites)){

            $action = Mage::helper('mip')->trimExplode(',', (string) $settings->websites);
            if(in_array('collect', $action)){
                $data = (array) $observer->getData('data');
                $productWebsites = array();
                if(isset($data['data']) & is_array($data['data']) && count($data['data'])){
                    foreach ((array) $data['data'] as $product){
                        if(empty($product['sku'])
                            || empty($product['website_ids'])
                            || !is_array($product['website_ids'])){
                                continue;
                        }
                        foreach ($product['website_ids'] as $websiteId){
                            $productWebsites[$product['sku']][] = $websiteId;
                        }
                    }
                    Mage::getResourceSingleton('mip/product_website')->addProducts($productWebsites);
                }
            }
        }
    }

    /**
     * flush Magento Cache
     *
     * @param Varien_Event_Observer $observer
     */
    public function save($observer)
    {
        $settings = $observer->getSettings();
        if(isset($settings->websites)){
            $action = Mage::helper('mip')->trimExplode(',', (string) $settings->websites);
            if(in_array('save', $action)){

                $mipProductWebsiteModel = Mage::getResourceSingleton('mip/product_website');
                $productWebsiteModel = Mage::getModel('catalog/product_website');

                $allSkus = $this->_getAllProductSkus();
                $skuToWebsite = $mipProductWebsiteModel->getWebsites();
                $count = count($skuToWebsite);
                Mage::helper('mip/log')->getWriter($this)->info('OBSERVER START: WEBSITE Save Website Ids');

                $allWebsiteIds = Mage::getModel('core/website')->getCollection()->getAllIDs();
                foreach($skuToWebsite as $sku => $websiteIds){

                    $productIds = array($this->_getProductIdBySku($sku));
                    $productWebsiteModel->removeProducts($allWebsiteIds, $productIds);
                    $productWebsiteModel->addProducts($websiteIds, $productIds);
                }

                $mipProductWebsiteModel->cleanUpRelations();
                Mage::helper('mip/log')->getWriter($this)->info('OBSERVER END: WEBSITE Save Website Ids');
            }
        }
    }

    /**
     * get Product by SKU faster than single lookup
     *
     * @param string $sku
     * @return int
     */
    protected function _getAllProductSkus(){

        $productCol = Mage::getResourceModel('catalog/product_collection');
        $idsSelect = clone $productCol->getSelect();
        $idsSelect->reset(Zend_Db_Select::ORDER);
        $idsSelect->reset(Zend_Db_Select::LIMIT_COUNT);
        $idsSelect->reset(Zend_Db_Select::LIMIT_OFFSET);
        $idsSelect->reset(Zend_Db_Select::COLUMNS);
        $idsSelect->from(null, array('e.sku'));
        $idsSelect->resetJoinLeft();

        return $productCol->getConnection()->fetchCol($idsSelect, array());


    }

    /**
     * get Product by SKU faster than single lookup
     *
     * @param string $sku
     * @return int
     */
    protected function _getProductIdBySku($sku){

        if($this->_skuToIdMapping == null){

            $productCol = Mage::getResourceModel('catalog/product_collection');
            $idsSelect = clone $productCol->getSelect();
            $idsSelect->reset(Zend_Db_Select::ORDER);
            $idsSelect->reset(Zend_Db_Select::LIMIT_COUNT);
            $idsSelect->reset(Zend_Db_Select::LIMIT_OFFSET);
            $idsSelect->reset(Zend_Db_Select::COLUMNS);
            $idsSelect->from(null, array('e.sku', 'e.'.$productCol->getEntity()->getIdFieldName()));
            $idsSelect->resetJoinLeft();

            $this->_skuToIdMapping = $productCol->getConnection()->fetchPairs($idsSelect, array());

        }
        return isset($this->_skuToIdMapping[$sku]) ? $this->_skuToIdMapping[$sku] : null;
    }


}