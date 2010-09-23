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
 */class AW_Advancednewsletter_Model_Observer extends Mage_Core_Block_Abstract
{
	public function subscribeCustomer($observer)
	{
		$customer = $observer->getEvent()->getCustomer();
		if (($customer instanceof Mage_Customer_Model_Customer))
		{
			if ($customer->getIsSubscribed())
			{
				$storesegments = Mage::getModel('advancednewsletter/segmentsmanagment')->getStoreDefaultSegments(Mage::app()->getStore()->getId());

				$subscribe_segments = array();
				if ($storesegments)
				{
					foreach ($storesegments as $segment)
						$subscribe_segments[] = $segment['value'];
				}
				else
					$subscribe_segments[] = AW_Advancednewsletter_Helper_Data::AN_SEGMENTS_ALL;

				Mage::getModel('advancednewsletter/subscriptions')->subscribeCustomer($customer, $subscribe_segments);
			}
			elseif ($customer->getIsSubscribed()===false)
			{
				Mage::getModel('advancednewsletter/subscriptions')->unsubscribeall($customer->getEmail());
			}
		}
		return $this;
	}

	public function orderStatusChanged($observer)
	{
		$data = $observer->getEvent()->getData();
		$order = Mage::getModel('sales/order')->load($data['order']->getId());

		$customer_email = $order->getCustomerEmail();
		$store_id = $order->getStoreId();
		$order_status = $order->getStatus();

		$sku = array();
		$categoryids = array();
		$product = Mage::getModel('catalog/product');

		foreach($order->getAllItems() as $item)
		{
			$product->load($item->getProductId());

			$productCategoriesWithParents = $product->getCategoryIds();
			foreach ($productCategoriesWithParents as $productCategory)
				$productCategoriesWithParents = array_merge($productCategoriesWithParents, Mage::getModel('catalog/category')->load($productCategory)->getParentIds());
			$categoryids[] = array_unique($productCategoriesWithParents);
			$sku[] = $item->getSku();
		}

		$allcategories = array();
		foreach ($categoryids as $categoryid)
		{
			foreach ($categoryid as $item)
			{
				$flag = false;
				foreach ($allcategories as $allcategory)
					if ($allcategory == $item) $flag = true;

				if (!$flag) $allcategories[] = $item;
			}
		}

		$to_validate = new Varien_Object();
		$to_validate->setData('categories', $allcategories);
		$to_validate->setData('sku', $sku);
		$to_validate->setData('store', $store_id);
		$to_validate->setData('order_status', $order_status);
		$to_validate->setData('customer_email', $customer_email);

		Mage::getModel('advancednewsletter/automanagement')->checkRule($to_validate);
	}
}
