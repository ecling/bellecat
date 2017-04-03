<?php
include('app/Mage.php');
Mage::app();

$adapter = Mage::getSingleton('core/resource')->getConnection('core_write');
$helper=Mage::helper('flytcloud');

$collection = Mage::getResourceModel('flytcloud/order_shipping_status_collection')
    ->addFieldToFilter('shipping_status',array('eq'=>'3'))
    ->setPageSize(10)
    ->load();

$order = Mage::getModel('sales/order')->load(18928);
$submit = $helper->submitOrderToFlytcloud($order);

/*
try{
    foreach($collection as $_item){
        $fly_order_result = $adapter->query("select * from flytcloud_order_shipping_status where `order`=".$_item->getOrder());
        $fly_order = $fly_order_result->fetch();
        
        if($fly_order['flytcloud_order_id']||$fly_order['shipping_status']==5){
            continue;
        }
        
        $where = 'id='.$_item->getId();
        $adapter->update('flytcloud_order_shipping_status',array('shipping_status'=>5),$where);
        $order = Mage::getModel('sales/order')->load($_item->getOrder());
        if($helper->isSameAddress($order)){
            Mage::log($order->getIncrementId(),null,'fly_submmit.log');
            $submit = $helper->submitOrderToFlytcloud($order);
            if(!$submit){
                $adapter->update('flytcloud_order_shipping_status',array('shipping_status'=>3),$where);
            }
        }else{
            $_item->setShippingStatus('4')
                ->save();
        }
    }
}catch(Extractor $e){

}
*/