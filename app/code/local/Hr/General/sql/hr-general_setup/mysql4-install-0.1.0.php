<?php
$installer = $this;
/* @var $installer Mage_Core_Model_Resource_Setup */

$installer->startSetup();
$connection = $installer->getConnection();
$connection->beginTransaction();

// Récupération de l'id de category root
$select = $connection->select();
$select->from($this->getTable('catalog_category_entity'), 'min(entity_id)');
$select->where($connection->quoteInto('parent_id <> ?', 0));
$rootCategId = $connection->fetchOne($select->__toString());


// Création d'un nouveau website
$connection->insert($this->getTable('core_website'), array(
 'code'	        => 'wbesite_hr_fr_fr',
 'name'          => 'Helena Rubinstein France',
 'sort_order'    => 10,
));
$websiteId = $connection->lastInsertId();

// Création d'un nouveau store
$connection->insert($this->getTable('core_store_group'), array(
 'website_id'        => $websiteId,
 'name'              => 'Boutique Helena Rubinstein France',
 'root_category_id'  => $rootCategId,
));
$groupId = $connection->lastInsertId();

// Création d'un nouveau storeview France FR
$connection->insert($this->getTable('core_store'), array(
 'code'	 	        => 'storeview_hr_fr_fr',
 'website_id'        => $websiteId,
 'group_id'	        => $groupId,
 'name'              => 'Helena Rubinstein France FR',
 'is_active'         => 1
));
$storeFrId = $connection->lastInsertId();

// Mise à jour des ids de store et storeview associés
$connection->update(
$this->getTable('core_store_group'),
array('default_store_id' => $storeFrId,),
$connection->quoteInto('group_id=?', $groupId)
);
$connection->update(
$this->getTable('core_website'),
array('default_group_id' => $groupId,),
$connection->quoteInto('website_id=?', $websiteId)
);

// Configuration
$connection->delete($this->getTable('core_config_data'),
$connection->quoteInto('scope=?', 'websites') . ' and ' .
$connection->quoteInto('scope_id=?', $websiteId));
$connection->delete($this->getTable('core_config_data'),
$connection->quoteInto('scope=?', 'stores') . ' and ' .
$connection->quoteInto('scope_id=?', $storeFrId));

$connection->insert($this->getTable('core_config_data'), array(
    'scope'	 	        => 'websites',
    'scope_id'          => $websiteId,
    'path'              => 'web/unsecure/base_url',
    'value'             => 'http://www.helenarubinstein.com/fr'
));
$connection->insert($this->getTable('core_config_data'), array(
    'scope'	 	        => 'websites',
    'scope_id'          => $websiteId,
    'path'              => 'general/country/allow',
    'value'             => 'FR'
));
$connection->insert($this->getTable('core_config_data'), array(
    'scope'	 	        => 'websites',
    'scope_id'          => $websiteId,
    'path'              => 'general/country/default',
    'value'             => 'FR'
));
$connection->insert($this->getTable('core_config_data'), array(
    'scope'	 	        => 'stores',
    'scope_id'          => $storeFrId,
    'path'              => 'general/locale/code',
    'value'             => 'fr_FR'
));
$connection->insert($this->getTable('core_config_data'), array(
    'scope'	 	        => 'stores',
    'scope_id'          => $storeFrId,
    'path'              => 'design/theme/skin',
    'value'             => 'fr'
));
// Configuration TVA de Magento v1.4
$connection->update($this->getTable('core_config_data'), array(
    'value'             => 'shipping'
), $connection->quoteInto('scope = ?', 'default')
. $connection->quoteInto(' AND scope_id = ?', 0)
. $connection->quoteInto(' AND path = ?', 'tax/calculation/based_on'));

$connection->update($this->getTable('core_config_data'), array(
    'value'             => 1
), $connection->quoteInto('scope = ?', 'default')
. $connection->quoteInto(' AND scope_id = ?', 0)
. $connection->quoteInto(' AND path = ?', 'tax/calculation/price_includes_tax'));

$connection->update($this->getTable('core_config_data'), array(
    'value'             => 1
), $connection->quoteInto('scope = ?', 'default')
. $connection->quoteInto(' AND scope_id = ?', 0)
. $connection->quoteInto(' AND path = ?', 'tax/calculation/shipping_includes_tax'));

$connection->update($this->getTable('core_config_data'), array(
    'value'             => 0
), $connection->quoteInto('scope = ?', 'default')
. $connection->quoteInto(' AND scope_id = ?', 0)
. $connection->quoteInto(' AND path = ?', 'tax/calculation/apply_after_discount'));

$connection->update($this->getTable('core_config_data'), array(
    'value'             => 1
), $connection->quoteInto('scope = ?', 'default')
. $connection->quoteInto(' AND scope_id = ?', 0)
. $connection->quoteInto(' AND path = ?', 'tax/calculation/discount_tax'));

$connection->update($this->getTable('core_config_data'), array(
    'value'             => 0
), $connection->quoteInto('scope = ?', 'default')
. $connection->quoteInto(' AND scope_id = ?', 0)
. $connection->quoteInto(' AND path = ?', 'tax/calculation/apply_tax_on'));

$connection->update($this->getTable('core_config_data'), array(
    'value'             => 'FR'
), $connection->quoteInto('scope = ?', 'default')
. $connection->quoteInto(' AND scope_id = ?', 0)
. $connection->quoteInto(' AND path = ?', 'tax/defaults/country'));

$connection->update($this->getTable('core_config_data'), array(
    'value'             => 0
), $connection->quoteInto('scope = ?', 'default')
. $connection->quoteInto(' AND scope_id = ?', 0)
. $connection->quoteInto(' AND path = ?', 'tax/defaults/region'));

$connection->update($this->getTable('core_config_data'), array(
    'value'             => '*'
), $connection->quoteInto('scope = ?', 'default')
. $connection->quoteInto(' AND scope_id = ?', 0)
. $connection->quoteInto(' AND path = ?', 'tax/defaults/postcode'));

$connection->update($this->getTable('core_config_data'), array(
    'value'             => 2
), $connection->quoteInto('scope = ?', 'default')
. $connection->quoteInto(' AND scope_id = ?', 0)
. $connection->quoteInto(' AND path = ?', 'tax/display/type'));

$connection->update($this->getTable('core_config_data'), array(
    'value'             => 2
), $connection->quoteInto('scope = ?', 'default')
. $connection->quoteInto(' AND scope_id = ?', 0)
. $connection->quoteInto(' AND path = ?', 'tax/display/shipping'));

$connection->update($this->getTable('core_config_data'), array(
    'value'             => 2
), $connection->quoteInto('scope = ?', 'default')
. $connection->quoteInto(' AND scope_id = ?', 0)
. $connection->quoteInto(' AND path = ?', 'tax/cart_display/price'));

$connection->update($this->getTable('core_config_data'), array(
    'value'             => 2
), $connection->quoteInto('scope = ?', 'default')
. $connection->quoteInto(' AND scope_id = ?', 0)
. $connection->quoteInto(' AND path = ?', 'tax/cart_display/subtotal'));

$connection->update($this->getTable('core_config_data'), array(
    'value'             => 2
), $connection->quoteInto('scope = ?', 'default')
. $connection->quoteInto(' AND scope_id = ?', 0)
. $connection->quoteInto(' AND path = ?', 'tax/cart_display/shipping'));

$connection->update($this->getTable('core_config_data'), array(
    'value'             => 0
), $connection->quoteInto('scope = ?', 'default')
. $connection->quoteInto(' AND scope_id = ?', 0)
. $connection->quoteInto(' AND path = ?', 'tax/cart_display/grandtotal'));

$connection->update($this->getTable('core_config_data'), array(
    'value'             => 0
), $connection->quoteInto('scope = ?', 'default')
. $connection->quoteInto(' AND scope_id = ?', 0)
. $connection->quoteInto(' AND path = ?', 'tax/cart_display/full_summary'));

$connection->update($this->getTable('core_config_data'), array(
    'value'             => 0
), $connection->quoteInto('scope = ?', 'default')
. $connection->quoteInto(' AND scope_id = ?', 0)
. $connection->quoteInto(' AND path = ?', 'tax/cart_display/zero_tax'));

$connection->update($this->getTable('core_config_data'), array(
    'value'             => 2
), $connection->quoteInto('scope = ?', 'default')
. $connection->quoteInto(' AND scope_id = ?', 0)
. $connection->quoteInto(' AND path = ?', 'tax/sales_display/price'));

$connection->update($this->getTable('core_config_data'), array(
    'value'             => 2
), $connection->quoteInto('scope = ?', 'default')
. $connection->quoteInto(' AND scope_id = ?', 0)
. $connection->quoteInto(' AND path = ?', 'tax/sales_display/subtotal'));

$connection->update($this->getTable('core_config_data'), array(
    'value'             => 2
), $connection->quoteInto('scope = ?', 'default')
. $connection->quoteInto(' AND scope_id = ?', 0)
. $connection->quoteInto(' AND path = ?', 'tax/sales_display/shipping'));

$connection->update($this->getTable('core_config_data'), array(
    'value'             => 0
), $connection->quoteInto('scope = ?', 'default')
. $connection->quoteInto(' AND scope_id = ?', 0)
. $connection->quoteInto(' AND path = ?', 'tax/sales_display/grandtotal'));

$connection->update($this->getTable('core_config_data'), array(
    'value'             => 0
), $connection->quoteInto('scope = ?', 'default')
. $connection->quoteInto(' AND scope_id = ?', 0)
. $connection->quoteInto(' AND path = ?', 'tax/sales_display/full_summary'));

$connection->update($this->getTable('core_config_data'), array(
    'value'             => 0
), $connection->quoteInto('scope = ?', 'default')
. $connection->quoteInto(' AND scope_id = ?', 0)
. $connection->quoteInto(' AND path = ?', 'tax/sales_display/zero_tax'));


// TVA France
$connection->insert($this->getTable('tax_calculation_rate'), array(
    'tax_country_id'	=> 'FR',
    'tax_region_id'     => 0,
    'tax_postcode'      => '*',
    'code'              => 'France',
    'rate'              => 19.6
));
$taxRateId = $connection->lastInsertId();
$connection->insert($this->getTable('tax_calculation_rate_title'), array(
    'tax_calculation_rate_id'	=> $taxRateId,
    'store_id'	                => $storeFrId,
    'value'	                    => 'TVA'
));
$connection->insert($this->getTable('tax_calculation_rule'), array(
    'code'	    => 'France 19.6%',
    'priority'	=> 0,
    'position'  => 0
));
$taxRuleId = $connection->lastInsertId();

// Récupération des id de class produit et client
$select->reset();
$select->from($this->getTable('tax_class'));
$select->where($connection->quoteInto('class_type = ?', 'PRODUCT'));
$taxClassProduct = $connection->fetchAll($select->__toString());
$select->reset(Zend_Db_Select::WHERE);
$select->where($connection->quoteInto('class_type = ?', 'CUSTOMER'));
$taxClassCustomer = $connection->fetchAll($select->__toString());

foreach ($taxClassProduct as $taxClassProductItem) {
    foreach ($taxClassCustomer as $taxClassCustomerItem) {
        $connection->insert($this->getTable('tax_calculation'), array(
            'tax_calculation_rate_id'	    => $taxRateId,
            'tax_calculation_rule_id'	    => $taxRuleId,
            'customer_tax_class_id'	        => $taxClassCustomerItem['class_id'],
            'product_tax_class_id'	        => $taxClassProductItem['class_id']
        ));
    }
}

$connection->commit();
$installer->endSetup();
