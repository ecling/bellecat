<?php
include('app/Mage.php');
Mage::app();

$helper=Mage::helper('flytcloud');

$collection = Mage::getResourceModel('flytcloud/order_shipping_status_collection')
    ->addFieldToFilter('shipping_status',array('eq'=>'3'))
    ->setPageSize(5)
    ->load();

try{
    foreach($collection as $_item){
        $order = Mage::getModel('sales/order')->load($_item->getOrder());
        if($helper->isSameAddress($order)){
            $helper->submitOrderToFlytcloud($order);
        }else{
            $_item->setShippingStatus('4')
                ->save();
        }
    }
}catch(Extractor $e){
    
}