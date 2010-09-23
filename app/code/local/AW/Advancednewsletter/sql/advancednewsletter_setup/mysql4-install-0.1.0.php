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

DROP TABLE IF EXISTS {$this->getTable('advancednewsletter/segmentsmanagment')};
CREATE TABLE IF NOT EXISTS {$this->getTable('advancednewsletter/segmentsmanagment')} (
  `segment_id` int(11) unsigned NOT NULL auto_increment,
  `title` varchar(255) NOT NULL,
  `code` varchar(255) NOT NULL,
  `default_store` smallint(5) NOT NULL,
  `default_category` varchar(255) NOT NULL,
  `display_in_store` varchar(255) NOT NULL,
  `display_in_category` varchar(255) NOT NULL,
  `display_order` int(11) unsigned NOT NULL,
  PRIMARY KEY  (`segment_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

DROP TABLE IF EXISTS {$this->getTable('advancednewsletter/subscriptions')};
CREATE TABLE IF NOT EXISTS {$this->getTable('advancednewsletter/subscriptions')} (
  `id` int(11) NOT NULL auto_increment,
  `segments_codes` text NOT NULL,
  `first_name` varchar(255) NOT NULL,
  `last_name` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  PRIMARY KEY  (`id`),
  UNIQUE KEY `email` (`email`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

DROP TABLE IF EXISTS {$this->getTable('advancednewsletter/templates')};
CREATE TABLE IF NOT EXISTS {$this->getTable('advancednewsletter/templates')} (
  `id` int(11) unsigned NOT NULL auto_increment,
  `segments_ids` text NOT NULL,
  `template_id` int(11) NOT NULL,
  `smtp_id` int(11) unsigned NOT NULL,
  PRIMARY KEY  (`id`),
  UNIQUE KEY `template_id` (`template_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

DROP TABLE IF EXISTS {$this->getTable('advancednewsletter/smtpconfiguration')};
CREATE TABLE IF NOT EXISTS {$this->getTable('advancednewsletter/smtpconfiguration')} (
  `smtp_id` int(11) unsigned NOT NULL auto_increment,
  `title` varchar(255) NOT NULL,
  `server_name` varchar(255) NOT NULL,
  `user_name` varchar(255) NOT NULL,
  `password` varchar(255) NOT NULL,
  `port` int(11) unsigned NOT NULL,
  `usessl` tinyint(1) NOT NULL,
  PRIMARY KEY  (`smtp_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
    ");

$subscribers = Mage::getResourceSingleton('newsletter/subscriber_collection')
        ->showCustomerInfo(true)
        ->getData();

foreach ($subscribers as $subscriber)
{
    try
    {
        Mage::getModel('advancednewsletter/subscriptions')
        ->setEmail($subscriber['subscriber_email'])
        ->setLastName($subscriber['customer_lastname'])
        ->setFirstName($subscriber['customer_firstname'])
		->setSegmentsCodes(AW_Advancednewsletter_Helper_Data::AN_SEGMENTS_ALL)
        ->save();
    }
    catch(Exception $e){continue;}
}

$installer->endSetup(); 