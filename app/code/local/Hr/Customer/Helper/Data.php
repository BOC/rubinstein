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
     * Retrieve customer logout url
     *
     * @return string
     */
    public function getInfoUrl()
    {
        return $this->_getUrl('customer/account/info');
    }
}
