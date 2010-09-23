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
class AW_Advancednewsletter_Block_Adminhtml_Subscriber_Edit extends Mage_Adminhtml_Block_Widget_Form_Container
{
    public function __construct()
    {
        parent::__construct();
                 
        $this->_objectId = 'subscriber_id';
        $this->_blockGroup = 'advancednewsletter';
        $this->_controller = 'adminhtml_subscriber';
        
        $this->_updateButton('save', 'label', Mage::helper('advancednewsletter')->__('Save Item'));
        $this->_updateButton('delete', 'label', Mage::helper('advancednewsletter')->__('Delete Item'));
		
        $this->_addButton('saveandcontinue', array(
            'label'     => Mage::helper('adminhtml')->__('Save And Continue Edit'),
            'onclick'   => 'saveAndContinueEdit()',
            'class'     => 'save',
        ), -100);

        $this->_formScripts[] = "
            function toggleEditor() {
                if (tinyMCE.getInstanceById('advancednewsletter_content') == null) {
                    tinyMCE.execCommand('mceAddControl', false, 'advancednewsletter_content');
                } else {
                    tinyMCE.execCommand('mceRemoveControl', false, 'advancednewsletter_content');
                }
            }

            function saveAndContinueEdit(){
                editForm.submit($('edit_form').action+'back/edit/');
            }
        ";
    }

    public function getHeaderText()
    {
		if( $cur_subscriber = Mage::registry('_current_subscriber'))
		{
			$cur_subscriber = $cur_subscriber->getData();
			if (isset($cur_subscriber['email']))
				return Mage::helper('advancednewsletter')->__("Edit Item '%s'", $this->htmlEscape($cur_subscriber['email']));
			else
				return Mage::helper('advancednewsletter')->__('Edit Item');
		}
		else
		{
            return Mage::helper('advancednewsletter')->__('Add Item');
        }
	}
}