<?php
include('app/Mage.php');
Mage::app();

$adapter = Mage::getSingleton('core/resource')->getConnection('core_write');

$crontab_result = $adapter->query("SELECT * FROM crontab");

$crontab = $crontab_result->fetch();

if($crontab){
    $first_id = $crontab['node'];
    $colleciton_result = $adapter->query("SELECT * FROM catalog_product_entity WHERE entity_id>".$first_id." LIMIT 10");
    $collection = $colleciton_result->fetchAll();
    foreach ($collection as $item) {
        $product_id = $item['entity_id'];
        $product = Mage::getModel('catalog/product')->load($product_id);
        if($product->getId()){
            $weight = $product->getWeight();
            $cost = $product->getPurchasePrice();
            $shipping_cost = $product->getShippingCost();
            if($weight&&$cost&&$shipping_cost){
                $bcshipping = Mage::helper('bcshipping')->getShippingCoseByCountry($weight,'NL');
                $price = $cost+$shipping_cost+($weight*$bcshipping->getPrice())+$bcshipping->getAdditionalPrice();
                $price = number_format($price*2/6.5,'2');

                if($price<6){
                    $price = $price-1;
                }

                if($price>=6&&$price<=20){
                    $price = $price-3;
                }

                if($price>20){
                    $price = $price-5;
                }

                if($price>0){
                    $product->setPrice($price)->save();
                }
            }
        }
        $last_id = $product_id;
    }

    if(isset($last_id)) {
        $set = array('node' => $last_id);
        $where = 'crontab_id=1';
        $adapter->update('crontab', $set, $where);
    }
}


