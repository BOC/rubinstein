<?php
class Magestore_Fbfanbox_Block_Fbfanbox extends Mage_Core_Block_Template
{
	public function _prepareLayout()
    {
		return parent::_prepareLayout();
    }
    
     public function getFbfanbox()     
     { 
        if (!$this->hasData('fbfanbox')) {
			$fbfanbox = array();
			
			$enable = Mage::getStoreConfig('fbfanbox/general/enable');
			$fb_page_id = Mage::getStoreConfig('fbfanbox/general/fb_page_id');
			$box_width = Mage::getStoreConfig('fbfanbox/general/box_width');
			$box_height = Mage::getStoreConfig('fbfanbox/general/box_height');
			$connection = Mage::getStoreConfig('fbfanbox/general/connection');
			$stream = Mage::getStoreConfig('fbfanbox/general/stream');
			$header = Mage::getStoreConfig('fbfanbox/general/header');
			
			if(!$connection)
			{
				$connection = 10;
			}
			
			$fbfanbox['enable'] =$enable;
			$fbfanbox['fb_page_id'] =$fb_page_id;
			$fbfanbox['box_width'] =$box_width;
			$fbfanbox['box_height'] =$box_height;
			$fbfanbox['connection'] =$connection;
			$fbfanbox['stream'] =$stream;
			$fbfanbox['header'] =$header;
			
            $this->setData('fbfanbox', $fbfanbox);
        }
        return $this->getData('fbfanbox');
        
    }
}