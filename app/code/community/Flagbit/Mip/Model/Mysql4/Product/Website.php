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
class Flagbit_Mip_Model_Mysql4_Product_Website extends Mage_Core_Model_Mysql4_Abstract
{
    /**
     * Initialize connection and define resource table
     *
     */
    protected function _construct()
    {
        $this->_init('mip/product_website', 'product_sku');
    }

    /**
     * Get catalog product resource model
     *
     * @return Mage_Catalog_Model_Resource_Product
     */
    protected function _getProductResource()
    {
        return Mage::getResourceSingleton('catalog/product');
    }

    /**
     * cleanup
     *
     * @throws Exception
     */
    public function cleanUpRelations()
    {
        $this->_getWriteAdapter()->query('TRUNCATE '.$this->getMainTable());
        return $this;
    }

    /**
     * Add products to websites
     *
     * @param array $productSkuToWebsite
     * @return Mage_Catalog_Model_Resource_Product_Website
     * @throws Exception
     */
    public function addProducts($productSkuToWebsite)
    {
        $this->_getWriteAdapter()->beginTransaction();
        $_tableFields = array('product_sku', 'website_id');
        if(is_array($productSkuToWebsite) && count($productSkuToWebsite)){
            try {
                $query = 'REPLACE INTO ' . $this->getMainTable() . ' (' . implode(', ', $_tableFields) . ') VALUES ';
                $separator = '';
                $count = 0;
                foreach ($productSkuToWebsite as $productSku => $websiteId) {
                    $count++;
                    if(is_array($websiteId)){
                        foreach ($websiteId as $wId){
                            $rowString = $this->_getWriteAdapter()->quoteInto('(?)', array($productSku, $wId));
                            $query .= $separator . $rowString;
                            $separator = ', ';
                        }
                    }else{
                        $rowString = $this->_getWriteAdapter()->quoteInto('(?)', array($productSku, $websiteId));
                        $query .= $separator . $rowString;
                        $separator = ', ';
                    }

                    if($count > 100){
                        $this->_getWriteAdapter()->query($query);
                        $query = 'REPLACE INTO ' . $this->getMainTable() . ' (' . implode(', ', $_tableFields) . ') VALUES ';
                        $separator = '';
                        $count = 0;
                    }
                }
                $this->_getWriteAdapter()->query($query);

                $this->_getWriteAdapter()->commit();
            } catch (Exception $e) {
                $this->_getWriteAdapter()->rollBack();
                throw $e;
            }
        }
        return $this;
    }

    /**
     * Retrieve product(s) website ids.
     *
     * @param array $productSkus
     * @return array
     */
    public function getWebsites($productSkus = null)
    {
        $select = $this->_getReadAdapter()->select()
            ->from($this->getMainTable(), array('product_sku', 'website_id'));
        if($productSkus !== null){
            $select->where('product_sku IN (?)', $productSkus);
        }

        $rowset  = $this->_getReadAdapter()->fetchAll($select);

        $result = array();
        foreach ($rowset as $row) {
            $result[$row['product_sku']][] = $row['website_id'];
        }

        return $result;
    }
}
