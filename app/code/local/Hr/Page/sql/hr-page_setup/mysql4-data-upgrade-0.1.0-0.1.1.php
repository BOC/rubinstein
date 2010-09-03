<?php
Mage::log('YEP YEP');

$previousStore = Mage::app()->getStore();
Mage::app()->setCurrentStore(Mage_Core_Model_App::ADMIN_STORE_ID);

$layout_update_xml = <<<EOF
<reference name="head">
    <action method="addJs"><script>flash/swfobject.js</script></action>
    <action method="addItem"><type>skin_css</type><name>css/home.css</name><params/></action>
</reference>
<reference name="content">
    <block type="core/template" name="home.page" template="page/home.phtml" />
</reference>
EOF;

$cmsHomePage = Mage::getModel('cms/page')->load('home', 'url_key');
$cmsHomePage->setData('layout_update_xml', $layout_update_xml);
$cmsHomePage->save();

Mage::app()->setCurrentStore($previousStore->getId());
