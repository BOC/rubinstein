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
class AW_Advancednewsletter_Block_Customer_Account_Dashboard_Info extends Mage_Customer_Block_Account_Dashboard_Info
{
    public function _toHtml()
    {
        $this->setTemplate('advancednewsletter/info.phtml');
        return parent::_toHtml();;
    }

    public function showCustomerSegments()
    {
        $customer = Mage::getModel('customer/session')->getCustomer()->getData();
        if (($segments = Mage::getModel('advancednewsletter/subscriptions')->getCustomerSegments($customer['email'])))
        {
            $segmentstitles = array();
            foreach ($segments as $segment)
                $segmentstitles[] = $segment['title'];
            return implode(', ', $segmentstitles);
        }
        return;
    }
}