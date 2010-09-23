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

class AW_Advancednewsletter_Model_Rule_Condition_Order_Params extends Mage_Rule_Model_Condition_Abstract
{
    /**
     * Retrieve attribute object
     *
     * @return Mage_Catalog_Model_Resource_Eav_Attribute
     */
    
    public function __construct()
    {
        parent::__construct();
        $this->setType('advancednewsletter/rule_condition_order_params')
            ->setValue(null);
    }

	public function loadAttributeOptions()
    {
		$hlp = Mage::helper('advancednewsletter');
        $this->setAttributeOption(array(
            'store'  => $hlp->__('Store'),
            'category' => $hlp->__('Category'),
			'order_status' => $hlp->__('Order status'),
			'sku' => $hlp->__('Contains any of these SKUs'),
        ));
        return $this;
    }

	public function getValueSelectOptions()
    {
		$hlp = Mage::helper('advancednewsletter');
		if ($this->getAttribute()=='store')
		{
			$stores = Mage::helper('advancednewsletter')->getStoresForRule();
			$stores_options = array();
			foreach ($stores as $key => $store)
				$stores_options[] = array('label' => $store, 'value' => $key);

			$this->setData('value_select_options', $stores);
		}
		if ($this->getAttribute()=='category')
		{
			$categories = Mage::helper('advancednewsletter')->getCategoriesArray();

			foreach ($categories as $key => $category)
				$categories[$key]['label'] = str_replace('&nbsp;', '', $category['label']);

			$this->setData('value_select_options', $categories);
		}
		if ($this->getAttribute()=='order_status')
		{
			$this->setData('value_select_options',
				Mage::getSingleton('sales/order_config')->getStatuses());
		}

        return $this->getData('value_select_options');
    }

	public function loadOperatorOptions()
    {
		$this->setOperatorOption(array(
			'=='  => Mage::helper('advancednewsletter')->__('is'),
			'!='  => Mage::helper('advancednewsletter')->__('is not')
		));
        return $this;
    }

	public function asHtml()
    {
        if ($this->getAttribute()=='sku')
		{
			$html = $this->getTypeElement()->getHtml().
				Mage::helper('advancednewsletter')->__("%s %s",
				   $this->getAttributeElement()->getHtml(),
				   $this->getValueElement()->getHtml()
			   );
			   if ($this->getId()!='1') {
				   $html.= $this->getRemoveLinkHtml();
			   }
			return $html;
		}

		return parent::asHtml();
    }

	public function getAttributeElement()
    {
        $element = parent::getAttributeElement();
        $element->setShowAsText(true);
        return $element;
    }

	public function getValueElementType()
    {
        if ($this->getAttribute()=='store'||$this->getAttribute()=='category'||$this->getAttribute()=='order_status') return 'select';
		return 'text';
    }

	public function validate(Varien_Object $object)
    {
		if ($this->getAttribute()=='sku')
		{
			$sku = explode(',',$this->getValue());
			foreach($sku as $skuA)
			{
				foreach($object->getSku() as $skuB)
				{
					if ($skuA == $skuB) return true;
				}
			}
			return false;
		}

		if ($this->getAttribute()=='category')
			return $this->validateAttribute($object->getCategories());

		return parent::validate($object);
    }
}