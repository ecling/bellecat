<?php
include('app/Mage.php');
Mage::app();

$adapter = Mage::getSingleton('core/resource')->getConnection('core_write');

$rows_reslut = $adapter->query("SELECT * FROM import_order WHERE `stauts`=0 LIMIT 10");

$rows = $rows_reslut->fetchAll();

foreach ($rows as $row) {
    $order_num = $row['order_num'];
    $order = Mage::getModel('sales/order')->loadByIncrementId($order_num);
    if($order->getId()){
        if($order->getStatus()!='processing' && $order->getStatus()!='complete'){
            $order->setState('processing')
                ->setStatus('processing')
                ->save();
            $set = array('stauts'=>1);
        }else{
            $set = array('stauts'=>2);
        }
    }else{
        $set = array('stauts'=>3);
    }
    $where = "order_num='".$order_num."'";
    $adapter->update('import_order',$set,$where);
}

