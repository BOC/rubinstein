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
class AW_Advancednewsletter_Model_Segmentsmanagment extends Mage_Core_Model_Abstract
{
    public function _construct()
    {
        parent::_construct();
        $this->_init('advancednewsletter/segmentsmanagment');
    }

    public function getCategoriesOptionHash() {
        $categoriesArray = Mage::getModel('catalog/category')
                ->getCollection()
                ->addAttributeToSelect('name')
                ->addAttributeToSort('path', 'asc')
                ->load()
                ->toArray();

        $categories = array();
        foreach ($categoriesArray as $categoryID => $category)
            if (isset($category['name']))
                $categories[$categoryID]=$category['name'];

        return $categories;
    }

    public function getSegments($cur_store=null, $cur_category=null)
    {
        $segments = array();
        $collection = $this->getCollection();
        $collection->getSelect()
                   ->order('display_order');
        $collection->load();

        if ($cur_store!=null&&$cur_category!=null)
        {
            foreach ($collection as $key=>$values)
            {
                $segments_info=array(
                    'label' => $values->getTitle(),
                    'value' => $values->getCode(),
                    'stores' => $values->getDisplayInStore(),
                    'categories' => $values->getDisplayInCategory()
                );

                if ((Mage::helper('advancednewsletter')->search($segments_info['stores'], $cur_store)||$segments_info['stores']==0)&&(Mage::helper('advancednewsletter')->search($segments_info['categories'], $cur_category)||$segments_info['categories']==0))
                    $segments[]=array('label'=>$values->getTitle() ,'value'=>$values->getCode());
            }
       }
       else if ($cur_store!=null)
        {
            foreach ($collection as $key=>$values)
            {
                $segments_info=array(
                    'label' => $values->getTitle(),
                    'value' => $values->getCode(),
                    'stores' => $values->getDisplayInStore(),
                    'categories' => $values->getDisplayInCategory()
                );

                if ((Mage::helper('advancednewsletter')->search($segments_info['stores'], $cur_store)||$segments_info['stores']==0)&&$segments_info['categories']==0)
                    $segments[]=array('label'=>$values->getTitle() ,'value'=>$values->getCode());
            }
       }
       else if ($cur_category!=null)
        {
            foreach ($collection as $key=>$values)
            {
                $segments_info=array(
                    'label' => $values->getTitle(),
                    'value' => $values->getCode(),
                    'stores' => $values->getDisplayInStore(),
                    'categories' => $values->getDisplayInCategory()
                );

				if (Mage::helper('advancednewsletter')->search($segments_info['categories'], $cur_category))
                    $segments[]=array('label'=>$values->getTitle() ,'value'=>$values->getCode());
            }
       }
        else
        {
			foreach ($collection as $key=>$values)
            {
                if ($values->getTitle())
                $segments[]=array(
                    'label'=>$values->getTitle(),
                    'code' =>$values->getCode(),
                    'value'=>$values->getSegmentId()
                );
            }
        }
        return $segments;
    }

    public function getStoreDefaultSegments($store)
    {
        $segments=array();
        $collection = $this->getCollection();
        $collection->getSelect()
                   ->where('default_store = ? or default_store = 0', $store)
                   ->order('display_order');
        $collection->load();

        foreach ($collection as $key=>$values)
        {
           $segments[]=array(
                'label' => $values->getTitle(),
                'value' => $values->getCode(),
           );
        }

        return $segments;
    }

    public function getCategoryDefaultSegments($category)
    {
        $__categoriesArray = Mage::getModel('catalog/category')
                ->getCollection()
                ->addAttributeToSelect('name')
                ->addAttributeToSort('path', 'asc')
                ->addAttributeToFilter('entity_id', $category)
                ->load()
                ->toArray();

        $segments=array();
        $collection = $this->getCollection();
        if ($category==0)
        {
        $collection->getSelect()
                   ->where('default_category = 0')
                   ->order('display_order');    
        }
        else
        {
        $collection->getSelect()
                   ->where('default_category = '.$category. ' or default_category = '.$__categoriesArray[$category]['parent_id'].' or default_category = 0')
                   ->order('display_order');
        }
        $collection->load();

        foreach ($collection as $key=>$values)
        {
           $segments[]=array(
                'label' => $values->getTitle(),
                'value' => $values->getCode(),
           );
        }

        return $segments;
    }

    public function getSegmentsCodesbyIds($segmentsids)
    {
        $collection = $this->getCollection();
        $collection->getSelect()
                   ->where('segment_id in (?)', $segmentsids);
        $collection->load();

        $segmentscodes=array();
        foreach ($collection as $key=>$values)
            $segmentscodes[]=$values->getCode();

        return $segmentscodes;
    }

    public function checkSegmentforSelection($segment_code, $category_id)
    {
        $__categoriesArray = Mage::getModel('catalog/category')
                ->getCollection()
                ->addAttributeToSelect('name')
                ->addAttributeToSort('path', 'asc')
                ->addAttributeToFilter('entity_id', $category_id)
                ->load()
                ->toArray();

        $collection = $this->getCollection();
        $collection->getSelect()
                   ->where('code = ?', $segment_code);
        $row = $collection->getFirstItem();

        if ($category_id)
            if ($row->getDefaultCategory()==$category_id||$row->getDefaultCategory()==0||$row->getDefaultCategory()==$__categoriesArray[$category_id]['parent_id']) return true;
        else
            if ($row->getDefaultCategory()==$category_id||$row->getDefaultCategory()==0) return true;
        return false;
    }

    public function remakeSegmentsByIds($segmentsids_line)
    {
        $segmentsids = explode(',', $segmentsids_line);
        $segmentstitles = array();
        foreach ($segmentsids as $segmentid)
        {
            $collection = $this->getCollection();
            $collection->getSelect()
                       ->where('segment_id = ?', $segmentid);
            $row = $collection->getFirstItem();
            $segmentstitles[] = $row->getTitle();
        }
        return implode(',', $segmentstitles);
    }

    public function remakeStoresByIds($storesids_line)
    {
        if (!$storesids_line) return 'Any';
        $storesids = explode(',', $storesids_line);
        $storesnames = array();
        foreach ($storesids as $storeid)
            if (Mage::app()->getSafeStore($storeid)) $storesnames[] = Mage::app()->getSafeStore($storeid)->name;
        return implode(',', $storesnames);
    }

    public function remakeCategoriesByIds($categoriesids_line)
    {
        if (!$categoriesids_line) return 'Any';
        $categoriesids = explode(',', $categoriesids_line);
        $categoriestitles = array();
        foreach ($categoriesids as $categoryid)
            $categoriestitles[] = Mage::getModel('catalog/category')->load($categoryid)->getName();
        return implode(',', $categoriestitles);
    }

	public function getSegmentList($without_all = false)
    {
        return $this->getResource()->getSegmentList($without_all);
    }
}