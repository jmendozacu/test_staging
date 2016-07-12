<?php

class Flagbit_Mip_Model_Mysql4_Product_Resource extends Mage_Catalog_Model_Resource_Product
{

    /**
     * Save attribute
     *
     * @param Varien_Object $object
     * @param string $attributeCode
     * @return Mage_Eav_Model_Entity_Abstract
     */
    public function saveAttribute(Varien_Object $object, $attributeCode)
    {
        $attribute      = $this->getAttribute($attributeCode);
        $backend        = $attribute->getBackend();
        $table          = $backend->getTable();
        $entity         = $attribute->getEntity();
        $entityIdField  = $entity->getEntityIdField();
        $adapter        = $this->_getWriteAdapter();

        $row = array(
            'entity_type_id' => $entity->getTypeId(),
            'attribute_id'   => $attribute->getId(),
            $entityIdField   => $object->getData($entityIdField),
        );

        $newValue = $object->getData($attributeCode);
        if ($attribute->isValueEmpty($newValue)) {
            $newValue = null;
        }

        $whereArr = array();
        foreach ($row as $field => $value) {
            $whereArr[] = $adapter->quoteInto($field . '=?', $value);
        }
        $where = implode(' AND ', $whereArr);

        $adapter->beginTransaction();
        try {
            $select = $adapter->select()
                ->from($table, 'value_id')
                ->where($where);
            $origValueId = $adapter->fetchOne($select);

            if ($origValueId === false) {
                $this->_insertAttribute($object, $attribute, $newValue);
            } elseif ($origValueId !== false) {
                $this->_updateAttribute($object, $attribute, $origValueId, $newValue);
            }

            $this->_processAttributeValues();
            $adapter->commit();
        } catch (Exception $e) {
            $adapter->rollback();
            throw $e;
        }

        return $this;
    }

}