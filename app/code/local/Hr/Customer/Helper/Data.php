<?php
/**
 * Customer Data Helper
 *
 * @category   Hr
 * @package    Hr_Customer
 * @author     Xavier Muselet <xmuselet@brandonlinecommerce.com>
 */
class Hr_Customer_Helper_Data extends Mage_Customer_Helper_Data
{

    /**
     * Retrieve customer info url
     *
     * @return string
     */
    public function getInfoUrl()
    {
        return $this->_getUrl('customer/account/info');
    }
    
    /**
     * Retrieve customer order url
     *
     * @return string
     */
     public function getOrderUrl()
     {
     	return $this->_getUrl('sales/order/history');
     }
     
     /**
      * Retrieve customer community url
      * 
      * @return string
      */
      public function getCommunityUrl()
      {
      	return $this->_getUrl('customer/account/community');
      }
}
