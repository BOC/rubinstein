<?php

/**
 * Customer login form block
 *
 * @category   Hr
 * @package    Hr_Customer
 * @author     Xavier Muselet <xmuselet@brandonlinecommerce.com>
 */
class Hr_Customer_Block_Form_Login extends Mage_Customer_Block_Form_Login
{
	protected function _prepareLayout()
    {
    	$title = $this->getLayout()->getBlock('head')->getTitle();
    	parent::_prepareLayout();
    	$this->getLayout()->getBlock('head')->setTitle($title);
        return $this;
    }
}
