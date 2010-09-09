<?php
/**
 * Dashboard neswletter info
 *
 * @category   Hr
 * @package    Hr_Customer
 * @author     Xavier Muselet <xmuselet@brandonlinecommerce.com>
 */
 
class Hr_Customer_Block_Account_Dashboard_Item extends Mage_Core_Block_Template
{
	protected $_image, $_alt, $_title, $_content, $_link;
	
	public function setItemImage($image, $alt)
	{
		$this->_image = $this->getSkinUrl('images').'/'.$image;
		$this->_alt = $alt;
	}
	
	public function getItemImage()
	{
		return $this->_image;
	}
	
	public function setItemTitle($title)
	{
		$this->_title = $title;
	}
	
	public function getItemTitle()
	{
		return $this->_title;
	}	
	
	public function setItemContent($text)
	{
		$this->_content = $text;
	}
	
	public function getItemContent()
	{
		return $this->_content;
	}
	
	public function setItemLink($link)
	{
		$this->_link = $link;
	}
	
	public function getItemLink()
	{
		return $this->_link;
	}
}