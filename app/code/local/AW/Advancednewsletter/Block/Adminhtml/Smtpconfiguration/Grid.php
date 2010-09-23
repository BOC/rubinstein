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
class AW_Advancednewsletter_Block_Adminhtml_Smtpconfiguration_Grid extends Mage_Adminhtml_Block_Widget_Grid
{
  public function __construct()
  {
      parent::__construct();
      $this->setId('smpconfigurationGrid');
      $this->setDefaultSort('smtp_id');
      $this->setDefaultDir('ASC');
      $this->setSaveParametersInSession(true);
  }

  protected function _prepareCollection()
  {
      $segments = Mage::getModel('advancednewsletter/smtpconfiguration')->getCollection();;
      $this->setCollection($segments);
      return parent::_prepareCollection();
  }

  protected function _prepareColumns()
  {
      $this->addColumn('smtp_id', array(
          'header'    => Mage::helper('advancednewsletter')->__('ID'),
          'align'     => 'left',
          'index'     => 'smtp_id',
      ));

      $this->addColumn('title', array(
          'header'    => Mage::helper('advancednewsletter')->__('Title'),
          'align'     => 'left',
          'index'     => 'title',
      ));

      $this->addColumn('server_name', array(
          'header'    => Mage::helper('advancednewsletter')->__('Server name'),
          'align'     => 'left',
          'index'     => 'server_name',
      ));

      $this->addColumn('user_name', array(
          'header'    => Mage::helper('advancednewsletter')->__('User Name'),
          'align'     => 'left',
          'index'     => 'user_name',
      ));

      $this->addColumn('port', array(
          'header'    => Mage::helper('advancednewsletter')->__('Port'),
          'align'     => 'left',
          'index'     => 'port',
      ));

      $this->addColumn('usessl', array(
          'header'    => Mage::helper('advancednewsletter')->__('Use ssl'),
          'align'     => 'left',
          'index'     => 'usessl',
		  'type'      => 'number',
      ));

      return parent::_prepareColumns();
  }

  protected function _prepareMassaction()
    {
        $this->setMassactionIdField('smtp_id');
        $this->getMassactionBlock()->setFormFieldName('smtp');

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
}