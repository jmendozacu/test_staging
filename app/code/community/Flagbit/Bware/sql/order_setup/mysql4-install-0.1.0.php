<?php
$installer = $this;
$installer->startSetup();

$installer->getConnection()
    ->addColumn($installer->getTable('sales/order'), 'transferred_to_bw', array(
        'TYPE'      => Varien_Db_Ddl_Table::TYPE_DATETIME,
        'NULLABLE'  => true,
        'COMMENT'   => 'Transferred to BW'
    ));

$installer->endSetup();
