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
class Flagbit_Mip_Model_Resource_Tax extends Flagbit_Mip_Model_Resource_Abstract {


    const RELATION_TYPE = 'tax';


    /**
     * getter method for the RELATION_TYPE constant
     *
     * @return string the Relation type of the Tax Resource
     */
    protected function getRelationType(){
        return self::RELATION_TYPE;
    }

    /**
     * customer taxes
     *
     * fetches the customer tax classes
     *
     * @param integer $storeId
     */
    public function customerTaxes($storeId = null){
        if ($storeId){
            Mage::app()->setCurrentStore(Mage::app()->getStore($storeId));
        }
        $result = array();
        /* @var $calc Mage_Tax_Model_Calculation */
        $calc = Mage::getSingleton('tax/calculation');
        /* @var $rates Mage_Tax_Model_Mysql4_Class_Collection */
        $rates = Mage::getModel('tax/class')->getCollection()
            ->addFieldToFilter('class_type', 'CUSTOMER')
            ->load();

        foreach ($rates as $rate) {
            $node = array(
                'classname' => $rate->getClassName(),
                'id' => $rate->getClassId(),
                'priority' => $rate->getPriority(),
                'taxrates' => $calc->getRatesByCustomerTaxClass($rate->getClassId())

            );
               $result['customerclass_'.$rate->getClassId()] = $node;
        }

        return $result;
    }

    /**
     * Customer product taxes
     *
     * fetches all tax rates by product and customer
     *
     * @param integer $storeId
     */
    public function customerProductTaxes($storeId = null){
        if ($storeId){
            Mage::app()->setCurrentStore(Mage::app()->getStore($storeId));
        }
        $result = array();
        /* @var $calc Mage_Tax_Model_Calculation */
        $calc = Mage::getSingleton('tax/calculation');
        /* @var $rates Mage_Tax_Model_Mysql4_Class_Collection */
        $productRates = Mage::getModel('tax/class')->getCollection()
            ->addFieldToFilter('class_type', 'PRODUCT')
            ->load();
        $customerRates = Mage::getModel('tax/class')->getCollection()
            ->addFieldToFilter('class_type', 'CUSTOMER')
            ->load();

        foreach ($productRates as $productRate) {
            $node = array(
                'classname' => $productRate->getClassName(),
                'id' => $productRate->getClassId()
            );
            foreach ($customerRates as $customerRate){
                $node['taxrates']['customerclass_'.$customerRate->getClassId()] = $calc->getRatesByCustomerAndProductTaxClasses($customerRate->getClassId(), $productRate->getClassId());
            }
               $result['productclass_'.$productRate->getClassId()] = $node;
        }
        return $result;
    }

}

?>