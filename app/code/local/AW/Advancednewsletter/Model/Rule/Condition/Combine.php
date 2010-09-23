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

class AW_Advancednewsletter_Model_Rule_Condition_Combine extends Mage_Rule_Model_Condition_Combine
{
    public function __construct()
    {
		parent::__construct();
        $this->setType('advancednewsletter/rule_condition_combine');
    }

    public function getNewChildSelectOptions()
    {
        $conditions = parent::getNewChildSelectOptions();
        $conditions = array_merge_recursive($conditions, array(
            array('value'=>'advancednewsletter/rule_condition_combine', 'label'=>Mage::helper('advancednewsletter')->__('Conditions Combination')),
            array('value'=>'advancednewsletter/rule_condition_order_params|store', 'label'=>Mage::helper('advancednewsletter')->__('Store')),
            array('value'=>'advancednewsletter/rule_condition_order_params|category', 'label'=>Mage::helper('advancednewsletter')->__('Category')),
            array('value'=>'advancednewsletter/rule_condition_order_params|sku', 'label'=>Mage::helper('advancednewsletter')->__('Contains any of these SKUs')),
            array('value'=>'advancednewsletter/rule_condition_order_params|order_status', 'label'=>Mage::helper('advancednewsletter')->__('Order status')),
        ));
        return $conditions;
    }

	public function asHtml()
    {
        $html = $this->getTypeElement()->getHtml().
            Mage::helper('advancednewsletter')->__("If %s of these order conditions are %s",
              $this->getAggregatorElement()->getHtml(),
			  $this->getValueElement()->getHtml()
           );
           if ($this->getId()!='1') {
               $html.= $this->getRemoveLinkHtml();
           }

        return $html;
    }
}
