<?php
include('app/Mage.php');
Mage::app();

$adapter = Mage::getSingleton('core/resource')->getConnection('core_write');

$file = dirname(__FILE__).'/var/product.csv';

$handle = fopen($file,'r');

$rows = fgetcsv($handle);

while ($row = fgetcsv($handle)) {
    $sku = $row['0'];
    $product_result = $adapter->query("select * from catalog_product_entity where sku='".$sku."'");
    $product = $product_result->fetch();
    if($product) {
        $product_id = $product['entity_id'];
        $set = array('value'=>2);
        $where = 'attribute_id=96 AND entity_id='.$product_id;
        $adapter->update('catalog_product_entity_int', $set, $where);
    }
}

fclose($handle);


