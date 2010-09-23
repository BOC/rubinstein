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
class AW_Advancednewsletter_Model_Mysql4_Segmentsmanagment extends Mage_Core_Model_Mysql4_Abstract
{
    public function _construct()
    {
        $this->_init('advancednewsletter/segmentsmanagment', 'segment_id');
    }

	public function getSegmentList($without_all = false)
    {
        $db = $this->_getReadAdapter();

        $select = $db->select()
            ->from($this->getMainTable(), array('code', 'title'));

        $data = $db->fetchAll($select);

        $res = array();

		if ($without_all==false)
		$res[] = array('value' => AW_Advancednewsletter_Helper_Data::AN_SEGMENTS_ALL, 'label' => Mage::helper('advancednewsletter')->__('All segments'));

        foreach($data as $row)
            $res[] = array('value' => $row['code'], 'label' => $row['title']);

        return $res;
    }

}