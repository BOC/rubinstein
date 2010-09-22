<?php
/**
 * Base html block
 *
 * @category   Hr
 * @package    Hr_Catalog
 * @author     Xavier Muselet <xmuselet@brandonlinecommerce.com>
 */
class Hr_Catalog_Block_Product_View_Share extends Mage_Core_Block_Template
{
	/**
	 * Retrieve the value of the Share field
	 */
	public function getShareProduct($product)
	{
		return $product->getShare();
	}
}