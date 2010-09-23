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
class AW_Advancednewsletter_Helper_Storesettings extends Mage_Core_Helper_Abstract
{
	const STORE_ASSOC    = 0;
	const SETTINGS_ASSOC = 1;
	
	public function getSettings($configPaths, $assoc = self::SETTINGS_ASSOC, $with_default = false)
	{
		if (!is_array($configPaths)) return false;
		
		$storeCollection = Mage::getSingleton('adminhtml/system_store')->getStoreCollection();
		$settingsA = array();
		foreach ($storeCollection as $store) {
			$store_id = $store->getStoreId();
			/*
			$store_name = $store->getName();
			$settings = array();
			
			foreach($keys as $key)
			$settings[$key] = Mage::getStoreConfig($key, $store_id);
			
			$stores_settings[$store_id] = array('store_name' => $store_name, 'settings' => $settings);
			*/
			$settingsA[$store_id] = array();
			foreach ($configPaths as $path) {
				$settingsA[$store_id][$path] = Mage::getStoreConfig($path, $store_id);
			}
		}

		if ($with_default)
			foreach ($configPaths as $path)
				$settingsA[0][$path] = Mage::getStoreConfig($path, 0);

		if ($assoc == self::SETTINGS_ASSOC) {
			$settingsB = array();
			foreach ($settingsA as $aKey => $aValue) {
				$__matchKey = null;
				foreach ($settingsB as $bKey => $bValue) {
					$__matched = true;
					foreach ($configPaths as $path) {
						if ($aValue[$path] != $bValue['SETTINGS'][$path]) {
							$__matched = false;
							break;
						}
					}
					if ($__matched) {
						$__matchKey = $bKey;
						break;
					}
				}
				if ($__matchKey) {
					$settingsB[$__matchKey]['STORE_IDS'][] = $aKey;
				}
				else {
					$settingsB[] = array(
						'SETTINGS' => $aValue,
						'STORE_IDS' => array($aKey)
					);
				}
			}
		}

		if ($assoc == self::SETTINGS_ASSOC) return $settingsB;
		else return $settingsA;
	}
}