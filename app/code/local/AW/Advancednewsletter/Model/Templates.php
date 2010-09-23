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
class AW_Advancednewsletter_Model_Templates extends Mage_Core_Model_Abstract
{
    public function _construct()
    {
    parent::_construct();
        $this->_init('advancednewsletter/templates');
    }

    public function getSmtpInfo($template_id)
    {
        return $this->getTemplate($template_id)->getId();
    }

    public function getSegmentsByTemplateid($template_id)
    {
        return explode(',', $this->getTemplate($template_id)->getSegmentsIds());
    }

    public function getSmtpById($template_id)
    {
        return $this->getTemplate($template_id)->getSmtpId();
    }

	public function getTemplate($template_id)
	{
		$collection = $this->getCollection();
        $collection->getSelect()
                   ->where('template_id = ?', $template_id);
		$row = $collection->getFirstItem();
		if (!$row) return $this;
		return $row;
	}

	public function deleteSegmentFromTemplates($segment_id)
	{
		$collection = $this->getCollection();
		$collection->getSelect()
                   ->where('find_in_set(?, segments_ids)', $segment_id);
		$rows = $collection->getItems();

		foreach ($rows as $row)
		{
			$segments_ids = explode(',',$row->getSegmentsIds());
			$segments_ids = array_diff($segments_ids, array($segment_id));
			$row
				->setSegmentsIds(implode(',', $segments_ids))
				->save();
		}
	}
}