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
class AW_Advancednewsletter_Helper_Data extends Mage_Core_Helper_Abstract
{
    const AN_SEGMENTS_ALL = 'ALL_SEGMENTS';
	const RULES_NO_CHANGE = 'no_change';

	const MSS_PATH = 'http://ecommerce.aheadworks.com/market-segmentation-suite.html';

	public function search($str, $search_symbols)
    {
        $str_array = explode(',', $str);
        foreach ($str_array as $symb)
            if ($symb == $search_symbols) return true;
        return false;
    }

	public function getStoresForRule()
	{
		foreach (Mage::getModel('adminhtml/system_store')->getWebsiteCollection() as $website) {
            $websiteShow = false;
            foreach (Mage::getModel('adminhtml/system_store')->getGroupCollection() as $group) {
                if ($website->getId() != $group->getWebsiteId()) {
                    continue;
                }
                $groupShow = false;
                foreach (Mage::getModel('adminhtml/system_store')->getStoreCollection() as $store) {
                    if ($group->getId() != $store->getGroupId()) {
                        continue;
                    }
                    if (!$websiteShow) {
                        $options[] = array(
                            'label' => $website->getName(),
                            'value' => array()
                        );
                        $websiteShow = true;
                    }
                    if (!$groupShow) {
                        $groupShow = true;
                        $values    = array();
                    }
                    $values[] = array(
                        'label' => $store->getName(),
                        'value' => $store->getId()
                    );
                }
                if ($groupShow) {
                    $options[] = array(
                        'label' => '&nbsp;&nbsp;' . $group->getName(),
                        'value' => $values
                    );
                }
            }
        }
		return $options;
	}

	public function getCategoriesArray()
	{
		
		$categoriesArray = Mage::getModel('catalog/category')
                ->getCollection()
                ->addAttributeToSelect('name')
                ->addAttributeToSort('path', 'asc')
                ->load()
                ->toArray();
        $categories = array(
            array(
                'label' => '--- Any ---',
                'value' => 0,
            )
        );

		$nbsp = html_entity_decode('&#160;', ENT_NOQUOTES, 'UTF-8');

        foreach ($categoriesArray as $categoryID => $category) {
            if (isset($category['name'])) {
                if ($category['level'] < 1) $category['level'] = 1;
                $categories[] = array('label' => str_repeat($nbsp.$nbsp.$nbsp.$nbsp, $category['level'] - 1) . $category['name'], 'value' => $categoryID);
            }
        }

		return $categories;
	}

	public function extensionEnabled($extension_name)
	{
		$modules = (array)Mage::getConfig()->getNode('modules')->children();
		if (!isset($modules[$extension_name])
			|| $modules[$extension_name]->descend('active')->asArray()=='false'
			|| Mage::getStoreConfig('advanced/modules_disable_output/'.$extension_name)
		) return false;
		return true;
	}

    public function getVersionPreffix()
    {
        if (preg_match('/^1.3/', Mage::getVersion())) return 'mag13';
        if (preg_match('/^1.4/', Mage::getVersion())) return 'mag14';
        if (preg_match('/^1.8/', Mage::getVersion())) return 'mag18';
        return '1.8';
    }
}