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

class AW_Advancednewsletter_Block_Newsletter extends Mage_Customer_Block_Newsletter
{
    public function __construct()
    {
        Mage_Customer_Block_Account_Dashboard::__construct();
        $this->setTemplate('advancednewsletter/newsletter.phtml');
    }

    public function _toHtml()
    {
        return parent::_toHtml();
    }

    public function hasSubscriptions()
    {
        $customer = Mage::getModel('customer/customer')->load(Mage::getSingleton('customer/session')->getId());
        if (Mage::getModel('advancednewsletter/subscriptions')->hasSubscriptions($customer->getEmail())) return true;
        return false;
    }

    public function getCustomerSegments()
    {
        $customer = Mage::getModel('customer/customer')->load(Mage::getSingleton('customer/session')->getId());
        return Mage::getModel('advancednewsletter/subscriptions')->getCustomerSegments($customer->getEmail());
    }

    public function getUnsubscribeLink($segment_code)
    {
        $customer = Mage::getModel('customer/customer')->load(Mage::getSingleton('customer/session')->getId());
        return Mage::getModel('core/url')->getBaseURL()."advancednewsletter/index/unsubscribe/code/".$segment_code."/email/".$customer->getEmail();
    }

    public function getUnsubscribeAllLink()
    {
        $customer = Mage::getModel('customer/customer')->load(Mage::getSingleton('customer/session')->getId());
        return Mage::getModel('core/url')->getBaseURL()."advancednewsletter/index/unsubscribeall/email/".$customer->getEmail();
    }
}