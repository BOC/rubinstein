<?php

/**
 * One page checkout status
 *
 * @category   Hr
 * @package    Hr_Checkout
 * @author     Xavier Muselet <xmuselet@brandonlinecommerce.com>
 */
class Hr_Checkout_Block_Onepage_Login extends Mage_Checkout_Block_Onepage_Login
{
    protected function _construct()
    {
    	parent::_construct();
        $this->getCheckout()->setStepData('login', array('label'=>Mage::helper('checkout')->__('Connection/registration'), 'allow'=>true));
    }
}
