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
class AW_Advancednewsletter_Block_Adminhtml_Segmentsmanagment_Edit_Form extends Mage_Adminhtml_Block_Widget_Form
{
  protected function _prepareForm()
  {
      $segment = Mage::getModel('advancednewsletter/segmentsmanagment');

      $form = new Varien_Data_Form(array(
            'id' => 'edit_form',
            'action' => $this->getUrl('*/*/save', array('id' => $this->getRequest()->getParam('id'))),
            'method' => 'post',
        	'enctype' => 'multipart/form-data'
            )
      );

      $fieldset = $form->addFieldset('main_group', array('legend'=>Mage::helper('advancednewsletter')->__('Fields')));

        $fieldset->addField('title', 'text', array(
            'label'     => Mage::helper('advancednewsletter')->__('Segment Title'),
            'name'      => 'title',
			'required'  => true,
        ));

        $fieldset->addField('code', 'text', array(
            'label'     => Mage::helper('advancednewsletter')->__('Segment Code'),
            'name'      => 'code',
            'after_element_html' => '<p><small>'.Mage::helper('advancednewsletter')->__('Note that this field\'s text  is case-sensitive<br /> and must exactly correspond to the MailChimp text').'</small></p>',
			'required'  => true,
        ));

        $fieldset->addField('default_store', 'select', array(
            'label'     => Mage::helper('advancednewsletter')->__('Default Store'),
            'name'      => 'default_store',
            'values'    => Mage::getSingleton('adminhtml/system_store')->getStoreValuesForForm(false, true),
			'required'  => true,
        ));

        $fieldset->addField('default_category', 'select', array(
            'label'     => Mage::helper('advancednewsletter')->__('Default Category'),
            'name'      => 'default_category',
            'values' => Mage::helper('advancednewsletter')->getCategoriesArray(),
            'onchange'  => 'checkTarget()',
            'after_element_html' => '',
			'required'  => true,
        ));

        $fieldset->addField('display_in_store', 'multiselect', array(
            'label'     => Mage::helper('advancednewsletter')->__('Display in store'),
            'name'      => 'display_in_store',
            'values'    => Mage::getSingleton('adminhtml/system_store')->getStoreValuesForForm(false, true),
            'onchange'  => 'checkTarget()',
            'after_element_html' => '',
			'required'  => true,	
        ));

        $fieldset->addField('display_in_category', 'multiselect', array(
            'label'     => Mage::helper('advancednewsletter')->__('Display in category'),
            'name'      => 'display_in_category',
            'values' => Mage::helper('advancednewsletter')->getCategoriesArray(),
            'onchange'  => 'checkTarget()',
            'after_element_html' => '',
			'required'  => true,
        ));

        $fieldset->addField('display_order', 'text', array(
            'label'     => Mage::helper('advancednewsletter')->__('Display order'),
            'name'      => 'display_order',
        ));

        if ( Mage::getSingleton('adminhtml/session')->getSegmentsmanagmentData() )
        {
            $form->setValues(Mage::getSingleton('adminhtml/session')->getSegmentsmanagmentData());
            Mage::getSingleton('adminhtml/session')->getSegmentsmanagmentData(null);
        } elseif ( Mage::registry('segmentsmanagment_data') ) {
            $form->setValues(Mage::registry('segmentsmanagment_data'));
        }

      $form->setUseContainer(true);
      $this->setForm($form);
      return parent::_prepareForm();
  }
}