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
class AW_Advancednewsletter_Block_Subscribeajax extends Mage_Core_Block_Template
{
    public function __construct()
    {
        parent::__construct();

        $preffix = Mage::helper('advancednewsletter')->getVersionPreffix();
        $this->setTemplate('advancednewsletter/subscribe_ajax/'.$preffix.'.phtml');
    }
    
    public function getSegments()
    { 
        $store=Mage::app()->getStore(true)->getId();
        $category = $this->getRequest()->getParam('id');
        return Mage::getModel('advancednewsletter/segmentsmanagment')->getSegments($store, $category);
    }

    public function getCategoryId()
    {
        $params = $this->getRequest()->getParams();
        if (isset($params['id'])) return $params['id'];
        else return 0;
    }
    public function checkSegmentforSelection($segment_id)
    {
        $params = $this->getRequest()->getParams();
        if (Mage::getModel('advancednewsletter/segmentsmanagment')->checkSegmentforSelection($segment_id, $params['id'])) return true;
        else return false;
    }
}
