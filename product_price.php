<?php
include('app/Mage.php');
Mage::app();

$adapter = Mage::getSingleton('core/resource')->getConnection('core_write');

$product_price_result = $adapter->query("SELECT * FROM product_price WHERE is_import=0 LIMIT 100");

$product_price = $product_price_result->fetchAll();

foreach($product_price as $item){
    $sku = $item['sku'];
    $weight = $item['weight'];
    $cost = $item['cost'];
    $shipping_cost = $item['shipping_cost'];
    $product_result = $adapter->query("SELECT * FROM catalog_product_entity WHERE sku='".$sku."'");
    $product = $product_result->fetch();

    //155采购价，156运费，80重量,75价格
    if($product){
        $product_id = $product['entity_id'];

        $bcshipping = Mage::helper('bcshipping')->getShippingCoseByCountry($weight,'US');
        $price = $cost+$shipping_cost+($weight*$bcshipping->getPrice())+$bcshipping->getAdditionalPrice();
        $price = number_format($price*2/6.5,'2');

        //$attributes = array(155=>$cost,156=>$shipping_cost,80=>$weight,75=>$price);
        $attributes = array(152=>$cost,153=>$shipping_cost,80=>$weight,75=>$price);
        foreach ($attributes as $attribute_id=>$value) {
            $p_attr_result = $adapter->query("SELECT * FROM catalog_product_entity_decimal WHERE entity_id=".$product_id." AND store_id=0 AND attribute_id=".$attribute_id);
            $p_attr = $p_attr_result->fetch();
            if($p_attr){
                $set = array('value'=>$value);
                $where = 'value_id='.$p_attr['value_id'];
                $adapter->update('catalog_product_entity_decimal',$set,$where);
            }else{
                $row = array('entity_type_id'=>4,'attribute_id'=>$attribute_id,'store_id'=>0,'entity_id'=>$product_id,'value'=>$value);
                $adapter->insert('catalog_product_entity_decimal',$row);
            }
        }
    }
    $set = array('is_import'=>1);
    $where = "sku='".$sku."'";
    $adapter->update('product_price',$set,$where);
}