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
class AW_Advancednewsletter_Block_Adminhtml_Smtpconfiguration_Edit_Form extends Mage_Adminhtml_Block_Widget_Form
{
  protected function _prepareForm()
  {
      $smtp = Mage::getModel('advancednewsletter/smtpconfiguration');

      $form = new Varien_Data_Form(array(
            'id' => 'edit_form',
            'action' => $this->getUrl('*/*/save', array('id' => $this->getRequest()->getParam('id'))),
            'method' => 'post',
        	'enctype' => 'multipart/form-data'
            )
      );

      $fieldset = $form->addFieldset('main_group', array('legend'=>Mage::helper('advancednewsletter')->__('Fields')));

        $fieldset->addField('title', 'text', array(
            'label'     => Mage::helper('advancednewsletter')->__('Title'),
            'name'      => 'title',
			'required'  => true,
        ));

        $fieldset->addField('server_name', 'text', array(
            'label'     => Mage::helper('advancednewsletter')->__('Server name'),
            'name'      => 'server_name',
			'required'  => true,
        ));

        $fieldset->addField('user_name', 'text', array(
            'label'     => Mage::helper('advancednewsletter')->__('User name'),
            'name'      => 'user_name',
			'required'  => true,
        ));

        $fieldset->addField('password', 'password', array(
            'label'     => Mage::helper('advancednewsletter')->__('Password'),
            'name'      => 'password',
			'required'  => true,
        ));

        $fieldset->addField('port', 'text', array(
            'label'     => Mage::helper('advancednewsletter')->__('Port'),
            'name'      => 'port',
			'required'  => true,
        ));

        $fieldset->addField('usessl', 'select', array(
            'label'     => Mage::helper('advancednewsletter')->__('Use SSL'),
            'name'      => 'usessl',
            'values' => array(
					array(
						'value' => 0,
						'label' => Mage::helper('advancednewsletter')->__('No'),
					),
					array(
						'value' => 1,
						'label' => Mage::helper('advancednewsletter')->__('Yes'),
					)
				)
        ));

        if ( Mage::getSingleton('adminhtml/session')->getSmtpconfigurationData() )
        {
            $form->setValues(Mage::getSingleton('adminhtml/session')->getSmtpconfigurationData());
            Mage::getSingleton('adminhtml/session')->getSmtpconfigurationData(null);
        } elseif ( Mage::registry('smtpconfiguration_data') ) {
            $form->setValues(Mage::registry('smtpconfiguration_data'));
        }

      $form->setUseContainer(true);
      $this->setForm($form);
      return parent::_prepareForm();
  }
}