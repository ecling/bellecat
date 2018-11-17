<?php
exit();
$ch = curl_init();
$opts = array(
    CURLOPT_CONNECTTIMEOUT => 10,
    CURLOPT_RETURNTRANSFER => true,
    CURLOPT_TIMEOUT        => 60,
    CURLOPT_USERAGENT      => 'facebook-php-3.0',
    CURLOPT_PROXY => 'http://127.0.0.1:1080',
    CURLOPT_SSL_VERIFYPEER => 0,
    CURLOPT_SSL_VERIFYHOST => 0,
    CURLOPT_POSTFIELDS => 'client_id=238699510057866&client_secret=16da7211310eec9fff815214e57cea20&code=AQAGzEX5-3lvtgKl4icgmgTIhQE7CR8Cs2v_brnPwkAn7tKDTuuzEoLBmQ5_ux7TPXxR07J63iZ3kcOtiiUWibuZExJIYCC2EDrwqxGcSyn9KuSfwpGVFHFeaeuiEriZLFo6nF0bHYzx4dnEunNL-fYsOj_go83ffkS-TWiCgpUgE_nf6M95ofppa3Wk-KQvALlyM9vlQ9GUGgAS2GRIvWg2vvV0oBuXB5uIz5zUVQhd0Mt9By7hjm3ixE-jTRNBp-vFzZE3Auc_-DTtx0BjS1QFpZlDeQGmZFtyWVAg8EiC-nBqpt_8AeDYl0cULPCNNmLiEDm_r7kSRufQAIJh65Y3&redirect_uri=https%3A%2F%2Ftest.bellecat.com%2Fen%2Ffblogin%2Findex%2Findex%2Fauth%2F1%2F%3Fenforce_https%3D1',
    CURLOPT_URL => 'https://graph.facebook.com/v3.1/oauth/access_token'
);
curl_setopt_array($ch, $opts);
$result = curl_exec($ch);
var_dump(json_decode($result));
curl_close($ch);
exit();
include 'app/Mage.php';
Mage::app();

$orderAmount = (int)Mage::helper('adyen')->formatAmount(0.01, 'EUR');
var_dump($orderAmount);

/*
echo md5('linglovehuang');
exit();
*/

include 'app/Mage.php';
Mage::app();
//Mage::log('test',null,'test.log');

//echo date('Y-m-d H:i:s',time());

if (isset($_SERVER['HTTP_CLIENT_IP'])) {
    $ipaddress = $_SERVER['HTTP_CLIENT_IP'];
} elseif(isset($_SERVER['HTTP_X_FORWARDED_FOR'])) {
    $ipaddress = $_SERVER['HTTP_X_FORWARDED_FOR'];
} elseif(isset($_SERVER['HTTP_X_FORWARDED'])) {
    $ipaddress = $_SERVER['HTTP_X_FORWARDED'];
}elseif(isset($_SERVER['HTTP_FORWARDED_FOR'])) {
    $ipaddress = $_SERVER['HTTP_FORWARDED_FOR'];
} elseif(isset($_SERVER['HTTP_FORWARDED'])) {
    $ipaddress = $_SERVER['HTTP_FORWARDED'];
} elseif(isset($_SERVER['REMOTE_ADDR'])) {
    $ipaddress = $_SERVER['REMOTE_ADDR'];
} else {
    $ipaddress = '';
}
var_dump($ipaddress);

/*
//echo Mage::getSingleton('core/date')->gmtTimestamp('2017-11-08 02:06:42');

//echo 'php time: '.date('Y-m-d H:m:s').'<br/>';

$startTime = '2017/11/27 10:15:00';
if($startTime<='2017/11/25 17:15:00'){
    $endTime = '2017/11/25 17:15:00';
}elseif($startTime<='2017/11/26 17:15:00'){
    $endTime = '2017/11/26 17:15:00';
}elseif($startTime<='2017/11/27 17:15:00'){
    $endTime = '2017/11/27 17:15:00';
}
var_dump($endTime);

exit();

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
*/
