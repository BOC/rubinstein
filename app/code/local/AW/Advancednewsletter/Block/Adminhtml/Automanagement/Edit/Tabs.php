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
class AW_Advancednewsletter_Block_Adminhtml_Automanagement_Edit_Tabs extends Mage_Adminhtml_Block_Widget_Tabs
{

    public function __construct()
    {
        parent::__construct();
        $this->setId('rule_id');
        $this->setDestElementId('edit_form');
        $this->setTitle(Mage::helper('advancednewsletter')->__('Auto-management rules'));
    }

    protected function _beforeToHtml()
    {
        $this->addTab('main_section', array(
            'label'     => Mage::helper('advancednewsletter')->__('Rule Information'),
            'title'     => Mage::helper('advancednewsletter')->__('Rule Information'),
            'content'   => $this->getLayout()->createBlock('advancednewsletter/adminhtml_automanagement_edit_tab_main')->toHtml(),
            'active'    => true
        ));

        $this->addTab('conditions_section', array(
            'label'     => Mage::helper('advancednewsletter')->__('Conditions'),
            'title'     => Mage::helper('advancednewsletter')->__('Conditions'),
            'content'   => $this->getLayout()->createBlock('advancednewsletter/adminhtml_automanagement_edit_tab_conditions')->toHtml(),
        ));

        $this->addTab('actions_section', array(
            'label'     => Mage::helper('advancednewsletter')->__('Actions'),
            'title'     => Mage::helper('advancednewsletter')->__('Actions'),
            'content'   => $this->getLayout()->createBlock('advancednewsletter/adminhtml_automanagement_edit_tab_actions')->toHtml(),
        ));

        return parent::_beforeToHtml();
    }

}
