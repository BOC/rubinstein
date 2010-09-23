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
class AW_Advancednewsletter_Helper_Logger extends Mage_Core_Helper_Abstract{
	
	const LOG_TO_FILE = true;                           // Log to file
	const FOLDER_NAME = 'advancednewsletter';			// Folder to log to
	const PREFIX = "AW_AN";
	
	
	protected $_logger;
	
	public static $active;
	
	public function log($msg){
		// Logs message to output and file
		
        if(!self::$active) return;
		
		$cur_time = date("Y-M-d h:i:s", Mage::getModel('core/date')->timestamp(time()));
        echo "[".self::PREFIX."] [".$cur_time."] ".$msg."\r\n";
		
		if(!$this->_logger && self::LOG_TO_FILE){
			$folder1 = Mage::getConfig()->getVarDir()."/log";
			
			if(!file_exists($folder1)){
				if(!@mkdir($folder1)){
					echo "[".self::PREFIX."] Failed to create folder $folder1\r\n";
					return;
				}
			}	
			
			$folder2 = Mage::getConfig()->getVarDir()."/log/".self::FOLDER_NAME;
			if(!file_exists($folder2)){
				if(!@mkdir($folder2)){
					echo "[".self::PREFIX."] Failed to create folder $folder2\r\n";
					return;
				}
			}
			
			if(!($this->_logger = @fopen(($folder2.DS.date("Ymd").".log"), 'a'))){
				echo "[".self::PREFIX."] Folder $folder2 is not writable\r\n";
				return;
			}
		}
		if(self::LOG_TO_FILE){
			fwrite($this->_logger, ("[".self::PREFIX."] [".$cur_time."] ".$msg."\r\n"));
		}
	}
	
	public function setActive($status){
		self::$active = $status;
	}
	
}
