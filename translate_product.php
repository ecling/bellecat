<?php
include 'app/Mage.php';
Mage::app();
Mage::app()->setCurrentStore(Mage_Core_Model_App::ADMIN_STORE_ID);

$store_id = 7;

$adapter = Mage::getSingleton('core/resource')->getConnection('core_write');

$crontab_result = $adapter->query("select * from crontab where crontab_id=2");
$crontab = $crontab_result->fetch();

$collection_result = $adapter->query("SELECT entity_id FROM `catalog_product_entity` WHERE entity_id>".$crontab['node']." ORDER BY entity_id ASC LIMIT 10");
$collection = $collection_result->fetchAll();

foreach ($collection as $_item) {
    $product_id = $_item['entity_id'];
    $last_id = $product_id;
    $product = Mage::getModel('catalog/product')->setStoreId(0)->load($product_id);
    $product->setData('_edit_mode', true);

    $title = $product->getTitle();
    $desc1 = trim($product->getDesc1());
    $desc2 = trim($product->getDesc2());
    $short_description = trim($product->getShortDescription());

    $name_tran = google_trans($title, 'pt');
    $desc1_tran = google_trans($desc1, 'pt');
    $desc2_tran = google_trans($desc2, 'pt');
    $short_description_tran = google_trans($short_description, 'pt');

    if ($desc1_tran || $desc2_tran) {
        $description_tran = $desc1_tran . $desc2_tran;
    } else {
        $description_tran = false;
    }

    if ($name_tran && $description_tran && $short_description_tran) {
        $product->setStoreId($store_id)
            ->setName($name_tran)
            ->setDescription($description_tran)
            ->setShortDescription($short_description_tran)
            ->save();
    } else {
        Mage::log($product_id . '---translate failed', null, 'translate_product.log');
    }
}

if(isset($last_id)) {
    $set = array('node' => $product_id);
    $where = 'crontab_id=2';
    $adapter->update('crontab', $set, $where);
}

function google_trans($text, $target) {
    $apiKey = 'AIzaSyAYNByJiGLfhiF3HujNKcHkm_KVazOCFkw';
    $url = 'https://www.googleapis.com/language/translate/v2?key=' . $apiKey . '&q=' . rawurlencode($text) . '&source=en&target='.$target;

    $handle = curl_init();
    curl_setopt($handle, CURLOPT_URL, $url);
    curl_setopt($handle, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($handle, CURLOPT_SSL_VERIFYPEER, false);
    $response = curl_exec($handle);

    $responseDecoded = json_decode($response, true);
    curl_close($handle);

    return $responseDecoded['data']['translations'][0]['translatedText'];
}