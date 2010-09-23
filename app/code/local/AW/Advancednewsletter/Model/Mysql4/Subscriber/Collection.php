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
class AW_Advancednewsletter_Model_Mysql4_Subscriber_Collection extends Mage_Newsletter_Model_Mysql4_Subscriber_Collection
{
    public function showSegments()
    {
        $table=Mage::getModel('advancednewsletter/subscriptions')->getCollection()->getTable('advancednewsletter/subscriptions');
        $this->getSelect()
             ->joinLeft(array('i' => $table), 'main_table.subscriber_email = i.email', array(
			 													'segments_codes', 
																'first_name',
																'last_name',
                                                                'phone',
																));															;
        return $this;
    }
}