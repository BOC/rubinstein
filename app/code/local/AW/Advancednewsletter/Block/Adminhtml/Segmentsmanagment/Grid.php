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
class AW_Advancednewsletter_Block_Adminhtml_Segmentsmanagment_Grid extends Mage_Adminhtml_Block_Widget_Grid
{
  public function __construct()
  {
      parent::__construct();
      $this->setId('segmentsmanagmentGrid');
      $this->setDefaultSort('segment_id');
      $this->setDefaultDir('ASC');
      $this->setSaveParametersInSession(true);
  }

  protected function _prepareCollection()
  {
      $segments = Mage::getModel('advancednewsletter/segmentsmanagment')->getCollection();;
      
      $this->setCollection($segments);
      parent::_prepareCollection();

       foreach ($segments as $key => $value)
       {
           $value->setDisplayInStore(Mage::getModel('advancednewsletter/segmentsmanagment')->remakeStoresByIds($value->getDisplayInStore()));
           $value->setDisplayInCategory(Mage::getModel('advancednewsletter/segmentsmanagment')->remakeCategoriesByIds($value->getDisplayInCategory()));
       }
     return;
  }

  protected function _prepareColumns()
  {
      $this->addColumn('segment_id', array(
          'header'    => Mage::helper('advancednewsletter')->__('ID'),
          'index'     => 'segment_id',
      ));

      $this->addColumn('title', array(
          'header'    => Mage::helper('advancednewsletter')->__('Title'),
          'index'     => 'title',
      ));

      $this->addColumn('code', array(
          'header'    => Mage::helper('advancednewsletter')->__('Segment code'),
          'index'     => 'code',
      ));

      $this->addColumn('default_store', array(
          'header'    => Mage::helper('advancednewsletter')->__('Default store'),
          'index'     => 'default_store',
          'type'      => 'options',
          'options'   => $this->_getStoreOptions()
      ));

      $this->addColumn('default_category', array(
          'header'    => Mage::helper('advancednewsletter')->__('Default category'),
          'index'     => 'default_category',
          'type'      => 'options',
          'options'   => $this->_getCategoriesOptions()
      ));

      $this->addColumn('display_in_store', array(
          'header'    => Mage::helper('advancednewsletter')->__('Display in store'),
          'index'     => 'display_in_store',
          'filter' => false,
          'sortable' => false,
      ));

      $this->addColumn('display_in_category', array(
          'header'    => Mage::helper('advancednewsletter')->__('Display in category'),
          'index'     => 'display_in_category',
          'filter' => false,
          'sortable' => false,
      ));

      $this->addColumn('display_order', array(
          'header'    => Mage::helper('advancednewsletter')->__('Display order'),
          'index'     => 'display_order',
      ));

      return parent::_prepareColumns();
  }

  protected function _prepareMassaction()
    {
        $this->setMassactionIdField('segment_id');
        $this->getMassactionBlock()->setFormFieldName('segment');

        $this->getMassactionBlock()->addItem('delete', array(
             'label'    => Mage::helper('advancednewsletter')->__('Delete'),
             'url'      => $this->getUrl('*/*/massDelete'),
             'confirm'  => Mage::helper('advancednewsletter')->__('Are you sure?')
        ));

        return $this;
    }

  public function getRowUrl($row)
  {
      return $this->getUrl('*/*/edit', array('id' => $row->getId()));
  }

  protected function _getStoreOptions()
  {
      $options = Mage::getModel('adminhtml/system_store')->getStoreOptionHash();
      $options[0]='Any';
      return $options;
  }

  protected function _getCategoriesOptions()
  {
      $options = Mage::getModel('advancednewsletter/segmentsmanagment')->getCategoriesOptionHash();
      $options[0]='Any';
      return $options;
  }
}