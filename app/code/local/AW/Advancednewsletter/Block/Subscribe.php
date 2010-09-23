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
class AW_Advancednewsletter_Block_Subscribe extends Mage_Newsletter_Block_Subscribe
{
    protected function _tohtml()
    {
        $preffix = Mage::helper('advancednewsletter')->getVersionPreffix();
        if (Mage::getStoreConfig('advancednewsletter/formconfiguration/formstyle')=='in_block')
           $this->setTemplate("advancednewsletter/subscribe/".$preffix.".phtml");
        if (Mage::getStoreConfig('advancednewsletter/formconfiguration/formstyle')=='ajax_layer')
            $this->setTemplate("advancednewsletter/subscribe_link/".$preffix.".phtml");
        $html = parent::_toHtml();
        return $html;
    }

    public function getSegments()
    { 
        $store=Mage::app()->getStore(true)->getId();
        if (Mage::registry('current_category')) $catid = Mage::registry('current_category')->getId();
        else $catid=0;
        return Mage::getModel('advancednewsletter/segmentsmanagment')->getSegments($store, $catid);
    }

    public function getCategoryId()
    {
        if (Mage::registry('current_category')) $catid = Mage::registry('current_category')->getId();
        else $catid=0;

        return $catid;
    }

    public function checkSegmentforSelection($segment_id)
    {
        if (Mage::registry('current_category')) $catid = Mage::registry('current_category')->getId();
        else return false;
        if (Mage::getModel('advancednewsletter/segmentsmanagment')->checkSegmentforSelection($segment_id, $catid)) return true;
        else return false;
    }
}