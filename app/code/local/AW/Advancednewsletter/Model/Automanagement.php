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
class AW_Advancednewsletter_Model_Automanagement extends Mage_Rule_Model_Rule
{
    public function _construct()
    {
		parent::_construct();
        $this->_init('advancednewsletter/automanagement');
    }

	public function getConditionsInstance()
    {
        return Mage::getModel('advancednewsletter/rule_condition_combine');
    }

	public function checkRule($to_validate)
    {
		$rules = Mage::getModel('advancednewsletter/automanagement')->getCollection();
		foreach($rules as $rule)
		{
			if (!$rule->getStatus()) continue;
			$rule_validate = Mage::getModel('advancednewsletter/automanagement')->load($rule->getRuleId());
			if ($rule_validate->validate($to_validate))
				Mage::getModel('advancednewsletter/subscriptions')->updateSegments($to_validate->getCustomerEmail(), $rule_validate->getSegmentsCut(), $rule_validate->getSegmentsPaste());
		}
	}
}