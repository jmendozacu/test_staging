<?php
/**
 * Shopgate GmbH
 *
 * URHEBERRECHTSHINWEIS
 *
 * Dieses Plugin ist urheberrechtlich geschützt. Es darf ausschließlich von Kunden der Shopgate GmbH
 * zum Zwecke der eigenen Kommunikation zwischen dem IT-System des Kunden mit dem IT-System der
 * Shopgate GmbH über www.shopgate.com verwendet werden. Eine darüber hinausgehende Vervielfältigung, Verbreitung,
 * öffentliche Zugänglichmachung, Bearbeitung oder Weitergabe an Dritte ist nur mit unserer vorherigen
 * schriftlichen Zustimmung zulässig. Die Regelungen der §§ 69 d Abs. 2, 3 und 69 e UrhG bleiben hiervon unberührt.
 *
 * COPYRIGHT NOTICE
 *
 * This plugin is the subject of copyright protection. It is only for the use of Shopgate GmbH customers,
 * for the purpose of facilitating communication between the IT system of the customer and the IT system
 * of Shopgate GmbH via www.shopgate.com. Any reproduction, dissemination, public propagation, processing or
 * transfer to third parties is only permitted where we previously consented thereto in writing. The provisions
 * of paragraph 69 d, sub-paragraphs 2, 3 and paragraph 69, sub-paragraph e of the German Copyright Act shall remain unaffected.
 *
 *  @author Shopgate GmbH <interfaces@shopgate.com>
 */

/**
 * User: Peter Liebig
 * Date: 24.01.14
 * Time: 17:00
 * E-Mail: p.liebig@me.com
 */

/**
 * model for config product types
 *
 * @author      Shopgate GmbH, 35510 Butzbach, DE
 * @package     Shopgate_Framework
 */
class Shopgate_Framework_Model_System_Config_Source_Product_Types
{
    /**
     * Options getter
     *
     * @return array
     */
    public function toOptionArray()
    {
        return array(
            array(
                'value' => Mage_Catalog_Model_Product_Type::TYPE_CONFIGURABLE,
                'label' => Mage::helper('shopgate')->__("Configurable")
            ),
            array(
                'value' => Mage_Catalog_Model_Product_Type::TYPE_GROUPED,
                'label' => Mage::helper('shopgate')->__("Grouped")
            ),
            array(
                'value' => Mage_Catalog_Model_Product_Type::TYPE_BUNDLE,
                'label' => Mage::helper('shopgate')->__("Bundle")
            ),
            array(
                'value' => Mage_Catalog_Model_Product_Type::TYPE_SIMPLE,
                'label' => Mage::helper('shopgate')->__("Simple")
            ),
            array(
                'value' => Mage_Catalog_Model_Product_Type::TYPE_VIRTUAL,
                'label' => Mage::helper('shopgate')->__("Virtual")
            )

        );
    }
}