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
	protected $_title, $_content;
	protected $_image = array('file' => '', 'alt' => '');
	protected $_link = array('label' => '', 'url' => '');
	
	public function setItemTitle($title='')
	{
		$this->_title = $title;
	}
	
	public function getItemTitle()
	{
		return $this->_title;
	}	
	
	public function setItemContent($text='')
	{
		$this->_content = $text;
	}
	
	public function getItemContent()
	{
		return $this->_content;
	}
	
	public function setItemImage($image='', $alt='')
	{
		$this->_image['file'] = $this->getSkinUrl('images').'/'.$image;
		$this->_image['alt'] = $alt;
	}
	
	public function getItemImageFile()
	{
		return $this->_image['file'];
	}
	
	public function getItemImageAlt()
	{
		return $this->_image['alt'];
	}
	
	public function setItemLink($link='', $url='')
	{
		$this->_link['label'] = $link;
		$this->_link['url'] = $url;
	}
	
	public function getItemLinkLabel()
	{
		return $this->_link['label'];
	}
	
	public function getItemLinkUrl()
	{
		return $this->_link['url'];
	}
}