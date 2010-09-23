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
class AW_Advancednewsletter_Block_Adminhtml_Newslettertemplates extends Mage_Adminhtml_Block_Newsletter_Template_Grid
{
    protected function _prepareCollection()
    {
        $collection = Mage::getResourceSingleton('newsletter/template_collection')
            ->useOnlyActual();

        /*
            SELECT *
            FROM `aw_advancednewsletter_smtp` AS s
            INNER JOIN aw_advancednewsletter_smtp_templates AS t ON s.smtp_id = t.smtp_id
            INNER JOIN newsletter_template AS n ON n.template_id = t.template_id
         */

        $table=Mage::getModel('advancednewsletter/templates')->getCollection()->getTable('advancednewsletter/templates');

        $native_table = Mage::getSingleton('core/resource')->getTableName('newsletter/template');

        $table_smtp=Mage::getModel('advancednewsletter/smtpconfiguration')->getCollection()->getTable('advancednewsletter/smtpconfiguration');
        $collection->getSelect()
            ->from('', array(
                'template_id' => $native_table.'.template_id',
                'smtp_title' => 'g.title'
            ))
            ->joinLeft(array('i' => $table), '`'.$native_table.'`.template_id = i.template_id', array('segments_ids', 'smtp_id'))
            ->joinLeft(array('g' => $table_smtp), 'g.smtp_id = i.smtp_id', array('smtp_title'=>'title'));

		$this->setCollection($collection);
        Mage_Adminhtml_Block_Widget_Grid::_prepareCollection();

        foreach ($collection as $key => $value)
        {
           	$value->setSegmentsIds(Mage::getModel('advancednewsletter/segmentsmanagment')->remakeSegmentsByIds($value->getSegmentsIds()));
			if (!$value->getSmtpTitle()) $value->setSmtpTitle("Local SMTP");
		}

        return;
    }

    protected function _prepareColumns()
    {
        $this->addColumn('template_id',
            array(
                 'header'=>Mage::helper('newsletter')->__('ID'),
                 'align'=>'center',
                 'index'=>'template_id',
				 'type' =>'number',
				 'filter_condition_callback' => array($this, 'filter_id_callback'),
        ));
        $this->addColumn('code',
            array(
                'header'=>Mage::helper('newsletter')->__('Template Name'),
               	'index'=>'template_code'
        ));

        $this->addColumn('added_at',
            array(
                'header'=>Mage::helper('newsletter')->__('Date Added'),
                'index'=>'added_at',
                'gmtoffset' => true,
                'type'=>'datetime'
        ));

        $this->addColumn('modified_at',
            array(
                'header'=>Mage::helper('newsletter')->__('Date Updated'),
                'index'=>'modified_at',
                'gmtoffset' => true,
                'type'=>'datetime'
        ));

        $this->addColumn('subject',
            array(
                'header'=>Mage::helper('newsletter')->__('Subject'),
                'index'=>'template_subject'
        ));

        $this->addColumn('sender',
            array(
                'header'=>Mage::helper('newsletter')->__('Sender'),
                'index'=>'template_sender_email',
                'renderer' => 'adminhtml/newsletter_template_grid_renderer_sender'
        ));


        $this->addColumn('type',
            array(
                'header'=>Mage::helper('newsletter')->__('Template Type'),
                'index'=>'template_type',
                'type' => 'options',
                'options' => array(
            		Mage_Newsletter_Model_Template::TYPE_HTML   => 'html',
            		Mage_Newsletter_Model_Template::TYPE_TEXT 	=> 'text'
                ),
        ));

        $this->addColumn('smtp_title',
            array(
                'header'=>Mage::helper('newsletter')->__('SMTP server'),
                'index'=>'smtp_title',
                'filter' => false
        ));

        $this->addColumn('segments_ids',
            array(
                'header'=>Mage::helper('newsletter')->__('Segments codes'),
                'index'=>'segments_ids',
                'filter' => false
        ));

        $this->addColumn('action',
            array(
                'header'    => Mage::helper('newsletter')->__('Action'),
                'index'     =>'template_id',
                'sortable' =>false,
                'filter'   => false,
                'no_link' => true,
                'width'	   => '170px',
                'renderer' => 'adminhtml/newsletter_template_grid_renderer_action'
        ));

        return $this;
    }

	 protected function filter_id_callback($collection, $column)
    {
        $fromTo = $column->getFilter()->getValue();

    	if(!@$fromTo['from'] && !@$fromTo['to']) return;
        $fromExpr = $toExpr = null;
        $cond = array();

        if(@$fromTo['from'])
            $cond[] = "newsletter_template.template_id >= '".$fromTo['from']."'";
        if(@$fromTo['to'])
            $cond[] = "newsletter_template.template_id <= '".$fromTo['to']."'";

        $collection->getSelect()->where("(".implode(' AND ', $cond).")");
    }
}