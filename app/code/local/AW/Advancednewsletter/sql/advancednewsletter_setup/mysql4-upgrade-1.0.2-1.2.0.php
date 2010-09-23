<?php

/**
 * aheadWorks Co.
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the EULA
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://ecommerce.aheadworks.com/AW-LICENSE-COMMUNITY.txt
 * 
 * =================================================================
 *                 MAGENTO EDITION USAGE NOTICE
 * =================================================================
 * This package designed for Magento COMMUNITY edition
 * aheadWorks does not guarantee correct work of this extension
 * on any other Magento edition except Magento COMMUNITY edition.
 * aheadWorks does not provide extension support in case of
 * incorrect edition usage.
 * =================================================================
 *
 * @category   AW
 * @package    AW_Advancednewsletter
 * @copyright  Copyright (c) 2009-2010 aheadWorks Co. (http://www.aheadworks.com)
 * @license    http://ecommerce.aheadworks.com/AW-LICENSE-COMMUNITY.txt
 */
$installer = $this;
$installer->startSetup();

$installer->run("

DROP TABLE IF EXISTS {$this->getTable('advancednewsletter/automanagement')};
CREATE TABLE IF NOT EXISTS {$this->getTable('advancednewsletter/automanagement')} (
  `rule_id` int(10) unsigned NOT NULL auto_increment,
  `title` varchar(100) NOT NULL,
  `status` int(11) NOT NULL,
  `conditions_serialized` mediumtext NOT NULL,
  `segments_cut` mediumtext NOT NULL,
  `segments_paste` mediumtext NOT NULL,
  PRIMARY KEY  (`rule_id`)
)  ENGINE=InnoDB DEFAULT CHARSET=utf8;

ALTER TABLE {$this->getTable('advancednewsletter/subscriptions')}
ADD `phone` varchar(100) default NULL,
ADD `salutation` varchar(100) default NULL;

ALTER TABLE {$this->getTable('advancednewsletter/segmentsmanagment')}
ADD UNIQUE (`code`);

");

$installer->endSetup();