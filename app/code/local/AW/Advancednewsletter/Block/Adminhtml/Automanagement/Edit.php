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
class AW_Advancednewsletter_Block_Adminhtml_Automanagement_Edit extends Mage_Adminhtml_Block_Widget_Form_Container
{
    public function __construct()
    {
		$this->_objectId = 'id';
        $this->_blockGroup = 'advancednewsletter';
        $this->_controller = 'adminhtml_automanagement';

        parent::__construct();

        $this->_updateButton('save', 'label', Mage::helper('advancednewsletter')->__('Save Rule'));
        $this->_updateButton('delete', 'label', Mage::helper('advancednewsletter')->__('Delete Rule'));

        $filter = Mage::registry('automanagement_data');
    }

	public function getHeaderText()
    {
        $rule = Mage::registry('automanagement_data');
        if ($rule->getRuleId()) {
            return Mage::helper('advancednewsletter')->__("Edit Rule '%s'", $this->htmlEscape($rule->getTitle()));
        }
        else {
            return Mage::helper('advancednewsletter')->__('New Rule');
        }
    }
}
