<?php

class Lengow_Export_IndexController extends Mage_Core_Controller_Front_Action
{
	public function IndexAction()
	{
		if(@!isset($_GET['storeID']))
		$store= 0;
		else
		$store=$_GET['storeID'];
		$this->GetHeader($this->getContent($_GET['format'],$store),$_GET['format']);
	}

	protected function GetHeader($content, $format = 'csv', $contentType = 'text/plain', $contentLength = null)
	{
		if( ($format =='csv') || ($format =='txt'))
		$contentType='text/plain';
		elseif($format =='xml')
		$contentType='text/xml';
		else
		exit();
		$this->getResponse()
		->setHttpResponseCode(200)
		->setHeader('Pragma', 'public', true)
		->setHeader('Cache-Control', 'must-revalidate, post-check=0, pre-check=0', true)
		->setHeader('Content-type', $contentType, true)
		->setHeader('Content-Length', strlen($content))
		->setBody($content);
	}

	public function getContent($contentType = 'csv', $storeID = 0)
	{
		set_time_limit(0);
		ini_set('display_errors', 1);
		error_reporting(E_ALL ^ E_NOTICE);

		require_once 'app/Mage.php';
		Mage::app('default');
		try
		{
			$products = Mage::getModel('catalog/product')->getCollection();
			// $products
			if($storeID != 0)
			{
				$products->addStoreFilter($storeID);
				$products->joinAttribute('description', 'catalog_product/description', 'entity_id', null, 'inner', $storeID);
			}
			$products->addAttributeToFilter('status', 1);//enabled
			$products->addAttributeToFilter('visibility', 4);//catalog, search
			$products->addAttributeToSelect('*');
			$prodIds=$products->getAllIds();

			//$product = Mage::getModel('catalog/product');
			$heading = array('ID','WEIGHT','MANUFACTURER','NAME','DESCRIPTION','PRICE','PRICE_PROMO','PROMO_FROM','PROMO_TO','STOCK','QUANTITY','URL','IMAGE','FDP','CATEGORY');

			//setEntityTypeFilter(4) => Product Entity
			$attributesInfo = Mage::getResourceModel('eav/entity_attribute_collection')
			->setEntityTypeFilter(4)
			->addSetInfo()
			->getData();
			foreach($attributesInfo as $attribute)
			{
				$code = $attribute['attribute_code'];
				$is_user_defined = $attribute['is_user_defined'];
				if($is_user_defined==1)
				{
					if(!in_array('ATT_'.strtoupper($code), $heading))
					{
						array_push($heading, 'ATT_'.strtoupper($code));
					}
				}
			}
			if( ($contentType =='csv') || ($contentType =='txt'))
			{
				$feed_line=implode("|", $heading)."\r\n";
			}
			elseif($contentType =='xml')
			{
				$feed_line='<?xml version="1.0" encoding="utf-8"?>'.PHP_EOL.'<catalog>'.PHP_EOL;
			}
			else
			{
				exit();
			}
			$buffer = $feed_line;

			foreach($prodIds as $productId)
			{
				$product = Mage::getModel('catalog/product');
				$product->load($productId);

				$product_data = array();
				$product_data['sku']=$product->getSku();
				$product_data['weight']=$product->getWeight();
				$product_data['brand']=$product->getResource()->getAttribute('manufacturer')->getFrontend()->getValue($product);
				$product_data['title']=$product->getName();
				$product_data['description']=$product->getDescription();
				$product_data['price']=$product->getPrice();
				$product_data['specialprice']=$product->getSpecialPrice();
				$product_data['promo_from']=$product->getSpecialFromDate();
				$product_data['promo_to']=$product->getSpecialToDate();
				$product_data['availability']=$product->getStockItem()->getIsInStock();
				$product_data['stock_descrip']=$product->getStockItem()->getQty();
				$product_data['link']=$product->getProductUrl();
				$product_data['image_link']=Mage::getBaseUrl(Mage_Core_Model_Store::URL_TYPE_MEDIA).'catalog/product'.$product->getImage();
				$product_data['shipping_rate']='voir site';
				//GESTION DE LA CATEGORIE
				$product_type = '';
				foreach($product->getCategoryIds() as $_categoryId)
				{
					$category = Mage::getModel('catalog/category')->load($_categoryId);
					$product_type .= $category->getName().' > ';
				}
				$product_data['category']=rtrim($product_type,' > ');

				foreach($attributesInfo as $attribute)
				{
					$code = $attribute['attribute_code'];
					$is_user_defined = $attribute['is_user_defined'];
					$value = $product->getResource()->getAttribute($code)->getFrontend()->getValue($product);
					if($is_user_defined==1)
					{
					//	echo "code:".($code)." - value:".$value."\n";
						$product_data[$code]=$value;
					}
				}

				foreach($product_data as $k=>$val)
				{
					$bad=array('"',"\r\n","\n","\r","\t");
					$good=array(""," "," "," ","");
					$product_data[$k] = '"'.str_replace($bad,$good,$val).'"';
				}

				if( ($contentType =='csv') || ($contentType =='txt'))
				{
					$feed_line = implode("|", $product_data)."\r\n";
				}
				elseif($contentType =='xml')
				{
					$feed_line = '<product> ';
					foreach($product_data as $k=>$val)
					{
						$feed_line .= '<'.$k.'><![CDATA['.$val.']]></'.$k.'>'.PHP_EOL;
					}
					$feed_line .= '</product>'.PHP_EOL;
				}
				else
				exit();
				$buffer .= $feed_line;
				unset($product_data);
				unset($product);
			}
			if($contentType == 'xml')
			$buffer .='</catalog>';
			return $buffer;
		}
		catch(Exception $e)
		{
			die($e->getMessage());
		}
	}
}