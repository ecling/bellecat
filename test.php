<?php
include 'app/Mage.php';
Mage::app('admin');

$test =  Mage::getModel('directory/currency')->getCurrencyRates('EUR','USD');
Mage::app()->getStore()->getBaseCurrency();
echo Mage::app()->getStore(5)->getCurrentCurrencyCode();
exit();

$product = Mage::getModel('catalog/product')->load(172);
$test = $product->getCategoryIds();
var_dump($test);
exit();

$store_id=2;

$adapter = Mage::getModel('core/resource')->getConnection('core_read');
$hot_result = $adapter->query("select product_id from sales_flat_order_item where store_id=".$store_id." group by product_id order by count(product_id) desc limit 50");
$rows = $hot_result->fetchAll();
foreach($rows as $key=>$row){
    if($key==0){
        $in_str = $row['product_id'];
    }else{
        $in_str = $in_str.','.$row['product_id'];
    }
    $rows[$key] = $row['product_id'];
}

$collection = Mage::getResourceModel('catalog/product_collection')
  ->setStore($store_id); 

$date = time()-10*24*3600;
$date = date('Y-m-d H:i:s',$date);
  
$select = $collection->getSelect()
        ->where("e.created_at>'".$date."' or e.entity_id in (".$in_str.")");  
  
print_r((string)$collection->getSelect());
exit();

$products = Mage::getResourceModel('catalog/product_collection')
        ->setStore($store_id)
        ->addAttributeToSelect('*')
        ->addStoreFilter($store_id)
        ->setPageSize($batch_max)
        ->setCurPage($count / $batch_max + 1)
        ->addUrlRewrite();

Mage::getSingleton('catalog/product_status')->addVisibleFilterToCollection($products);
Mage::getSingleton('catalog/product_visibility')->addVisibleInCatalogFilterToCollection($products);

print_r((string)$products->getSelect());