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
class AW_Advancednewsletter_Block_Adminhtml_Automanagement_Edit_Tab_Actions extends Mage_Adminhtml_Block_Widget_Form
{

    protected function _prepareForm()
    {
		$model = Mage::registry('automanagement_data');

		$form = new Varien_Data_Form();

        $form->setHtmlIdPrefix('rule_');

        $fieldset = $form->addFieldset('action_fieldset', array('legend'=>Mage::helper('advancednewsletter')->__('Actions')));

		$segments_remove = array(array('label' => 'No change', 'value' => AW_Advancednewsletter_Helper_Data::RULES_NO_CHANGE));
		$segments_remove = array_merge($segments_remove, Mage::getModel('advancednewsletter/segmentsmanagment')->getSegmentList());

		$fieldset->addField('segments_cut', 'multiselect', array(
            'label'     => Mage::helper('advancednewsletter')->__('Remove from'),
            'name'      => 'segments_cut',
            'values'    => $segments_remove,
			'required' => true,
        ));

		$segments_move = array(array('label' => 'No change', 'value' => AW_Advancednewsletter_Helper_Data::RULES_NO_CHANGE));
		$segments_move = array_merge($segments_move, Mage::getModel('advancednewsletter/segmentsmanagment')->getSegmentList(true));
		$fieldset->addField('segments_paste', 'multiselect', array(
            'label'     => Mage::helper('advancednewsletter')->__('Add to'),
            'name'      => 'segments_paste',
            'values'    => $segments_move,
			'required' => true,
        ));
        
        $form->setValues($model->getData());

        /*if ($model->isReadonly()) {
            foreach ($fieldset->getElements() as $element) {
                $element->setReadonly(true, true);
            }
        }*/

        $this->setForm($form);

        return parent::_prepareForm();
    }
}
