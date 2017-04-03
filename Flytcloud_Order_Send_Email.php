<?php
include('app/Mage.php');
Mage::app();

$adapter = Mage::getSingleton('core/resource')->getConnection('core_write');
$helper=Mage::helper('flytcloud');

$flyt_order_result = $adapter->query("select fo.* from flytcloud_order_shipping_status as fo
left join sales_flat_order as o on fo.order=o.entity_id
where o.status='processing' and fo.shipping_status=1 and update_time>='2017-03-29 00:00:00'
order by fo.order asc
limit 1");

$flyt_order = $flyt_order_result->fetch();
if($flyt_order){
    $orderId = $flyt_order['order'];
    $order = Mage::getModel('sales/order')->load($orderId);
    $tracks = array(array('carrier_code'=>'custom','number'=>$flyt_order['flytcloud_order_id'],'title'=>'FLYT'));

    try{
      $shipmentHelper=Mage::helper('flytcloud/shipment');
      
      $shippment=$shipmentHelper->getShipmentByOrderId($orderId);
      if($shippment)
        {
            $shippmentId=$shippment->getId();
        }
        $shipment=$shipmentHelper->initShippment($shippmentId, $orderId, $tracks);
        if(!$shipment->getId())$shipment->register();
         
        $shipmentHelper->saveShipment($shipment);
        
        $comment = '';
        foreach($tracks as $track)
        {
            $comment.=isset($track['number'])?$track['number']."\r\n":null;
        }
        $shipment->sendEmail(true, $comment);  
    }catch(Exception $e){
        Mage::log($e,null,'fly_order_sendemail.log');
    }
}