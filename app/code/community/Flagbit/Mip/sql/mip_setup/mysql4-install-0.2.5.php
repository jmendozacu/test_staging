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
 * @version        $Id$
 */

$installer = $this;
/* @var $installer Mage_Core_Model_Resource_Setup */

$installer->startSetup();

$installer->run("

DROP TABLE IF EXISTS {$this->getTable('mip/data_relation')};
CREATE TABLE IF NOT EXISTS `{$this->getTable('mip/data_relation')}` (
  `relation_id` int(10) unsigned NOT NULL auto_increment,
  `mage_id` int(10) unsigned NOT NULL default 0,
  `mage_type` varchar(255) NOT NULL default '',
  `resource_type` varchar(255) NOT NULL default '',
  `resource_id` varchar(255) NOT NULL default '',
  `ext_id` varchar(255) NOT NULL default '',
  `interface` varchar(30) NOT NULL default '',
  `datahash_identifier` varchar(30) NOT NULL default '',
  `status` varchar(30) NOT NULL default '',
  `created_at` datetime NOT NULL default '0000-00-00 00:00:00',
  `updated_at` datetime NOT NULL default '0000-00-00 00:00:00',


  PRIMARY KEY  (`relation_id`),
  KEY `relation_mage` (`resource_type`,`mage_id`),
  KEY `relation_ext` (`mage_type`,`ext_id`),
  KEY `id_type` (`mage_id`,`mage_type`),
  UNIQUE KEY `relation_ext_unique` (`mage_type`, `resource_type`, `resource_id`)
)
ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='Mip Relations' AUTO_INCREMENT=1;


DROP TABLE IF EXISTS {$this->getTable('mip/data_hash')};
CREATE TABLE IF NOT EXISTS `{$this->getTable('mip/data_hash')}` (
  `datahash_id` int(10) unsigned NOT NULL auto_increment,
  `identifier` varchar(30) NOT NULL default '',
  `hash` varchar(32) NOT NULL default '',
  `type` varchar(30) NOT NULL default '',

  PRIMARY KEY  (`datahash_id`),
  KEY `datahash_hash_type` (`type`, `identifier`)
)
ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='Mip Data Hash' AUTO_INCREMENT=1;


DROP TABLE IF EXISTS {$this->getTable('mip/data_queue')};
CREATE TABLE IF NOT EXISTS `{$this->getTable('mip/data_queue')}` (
  `dataqueue_id` int(10) unsigned NOT NULL auto_increment,
  `interface` varchar(30) NOT NULL default '',
  `resource` varchar(30) NOT NULL default '',
  `action` varchar(30) NOT NULL default '',
  `direction` varchar(30) NOT NULL default '',
  `hash` varchar(32) NOT NULL default '',
  `data` LONGBLOB NOT NULL,
  `parent_task_id`  int(10) unsigned NOT NULL default 0,

  PRIMARY KEY  (`dataqueue_id`),
  KEY `hash` (`hash`)
)
ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='Mip Data Queue' AUTO_INCREMENT=1;


DROP TABLE IF EXISTS {$this->getTable('mip/task')};
CREATE TABLE IF NOT EXISTS `{$this->getTable('mip/task')}` (
  `task_id` int(10) unsigned NOT NULL auto_increment,
  `interface` varchar(30) NOT NULL default '',
  `resource` varchar(30) NOT NULL default '',
  `action` varchar(30) NOT NULL default '',
  `direction` varchar(30) NOT NULL default '',
  `dataqueue_id` int(10) unsigned NOT NULL default 0,
  `status` enum('running','success','error', 'expired') NOT NULL default 'running',
  `messages` text,
  `executed_at` datetime NOT NULL default '0000-00-00 00:00:00',
  `finished_at` datetime NOT NULL default '0000-00-00 00:00:00',
  `lock` tinyint(2) NOT NULL default '0',
  `parent_task_id`  int(10) unsigned NOT NULL default 0,

  PRIMARY KEY  (`task_id`),
  KEY `status` (`status`,`lock`)
)
ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='Mip Tasks' AUTO_INCREMENT=1;


DROP TABLE IF EXISTS {$this->getTable('mip/product_website')};
CREATE TABLE IF NOT EXISTS `{$this->getTable('mip/product_website')}` (
`product_sku` VARCHAR( 64 ) NOT NULL,
`website_id` SMALLINT( 5 ) NOT NULL,
  PRIMARY KEY (`product_sku`,`website_id`)
) ENGINE = InnoDB CHARACTER SET utf8 COLLATE utf8_unicode_ci;

");

if($installer->getConnection()->isTableExists($this->getTable('sales_order_status'))){
    $installer->run("
      INSERT INTO {$this->getTable('sales_order_status')} VALUES ('mip_transmitted', 'Transmitted');
    ");
}

$installer->endSetup();