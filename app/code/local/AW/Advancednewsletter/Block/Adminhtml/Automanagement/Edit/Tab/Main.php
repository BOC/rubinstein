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
class AW_Advancednewsletter_Block_Adminhtml_Automanagement_Edit_Tab_Main extends Mage_Adminhtml_Block_Widget_Form
{

    protected function _prepareForm()
    {
		$model = Mage::registry('automanagement_data');

		$form = new Varien_Data_Form();

        $form->setHtmlIdPrefix('rule_');

        $fieldset = $form->addFieldset('base_fieldset', array('legend'=>Mage::helper('advancednewsletter')->__('General Information')));

        if ($model->getId()) {
        	$fieldset->addField('rule_id', 'hidden', array(
                'name' => 'rule_id',
            ));
        }
		
    	$fieldset->addField('title', 'text', array(
            'name' => 'title',
            'label' => Mage::helper('advancednewsletter')->__('Rule Title'),
            'title' => Mage::helper('advancednewsletter')->__('Rule Title'),
            'required' => true,
        ));

    	$fieldset->addField('status', 'select', array(
            'label'     => Mage::helper('advancednewsletter')->__('Status'),
            'title'     => Mage::helper('advancednewsletter')->__('Status'),
            'name'      => 'status',
            'options'    => array(
                '1' => Mage::helper('advancednewsletter')->__('Active'),
                '0' => Mage::helper('advancednewsletter')->__('Inactive'),
            ),
        ));

        $form->setValues($model->getData());
        $this->setForm($form);
        return parent::_prepareForm();
    }
}
