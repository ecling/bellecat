<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/12/5 0005
 * Time: 10:28
 */

include 'app/Mage.php';
Mage::app();
Mage::app()->setCurrentStore(Mage_Core_Model_App::ADMIN_STORE_ID);

$adapter = Mage::getSingleton('core/resource')->getConnection('core_write');

$time = time()-24*3600;
$time = date('Y-m-d H:i:s',$time);

$crontab_result = $adapter->query("SELECT node FROM `crontab` WHERE crontab_id=3");
$crontab = $crontab_result->fetch();
$last_order_id = $crontab['node'];

//获取需要处理的订单
$order_result = $adapter->query("SELECT * FROM `sales_flat_order` 
  WHERE entity_id>$last_order_id AND (`status`='pending' OR `status`='pending_payment') AND `created_at`<='$time'
  ORDER BY entity_id ASC
  LIMIT 1;");

if($order = $order_result->fetch()){
    //查找相近订单，去除重复订单
    $same_time = strtotime($order['created_at'])-3600;
    $same_time = date('Y-m-d H;i:s',$same_time);
    $customer_email = $order['customer_email'];
    $last_order_id = $order['entity_id'];

    $adapter->update('crontab',array('node'=>$last_order_id),'crontab_id=3');
    $same_customer_result = $adapter->query("SELECT * FROM `sales_flat_order` WHERE created_at>'$same_time' AND customer_email='$customer_email' AND entity_id!=$last_order_id");

    if($same_customer = $same_customer_result->fetch()){

    }else{
        $adapter->insert('sales_order_place_again',array('order_id'=>$last_order_id,'first_send'=>1,'second_send'=>0));

        $order = Mage::getModel('sales/order')->load($last_order_id);
        $payment_code = $order->getPayment()->getMethodInstance()->getCode();
        $storeId = $order->getStoreId();

        if($payment_code=='checkoutapihosted'){
            $place_url = Mage::app()->getStore($storeId)->getUrl('chargepayment/order/place',array('id'=>$last_order_id));
        }else{
            $place_url = Mage::app()->getStore($storeId)->getUrl('adyen/order/place',array('id'=>$last_order_id));
        }


        $customerName = $order->getCustomerFirstname().' '.$order->getCustomerLastname();
        $templateId = 'sales_email_order_place_again';
        $mailer = Mage::getModel('core/email_template_mailer');
        $emailInfo = Mage::getModel('core/email_info');
        $emailInfo->addTo($order->getCustomerEmail(), $customerName);
        $mailer->addEmailInfo($emailInfo);

        $mailer->setSender(Mage::getStoreConfig('sales_email/shipment/identity', $storeId));
        $mailer->setStoreId($storeId);
        $mailer->setTemplateId($templateId);
        $mailer->setTemplateParams(array(
                'place_url'        => $place_url,
            )
        );
        $mailer->send();
    }
}


/*
$order = Mage::getModel('sales/order')->load(50758);

$methodInstance = $order->getPayment()->getMethodInstance();

$stateObject = new Varien_Object();

$methodInstance->initialize($methodInstance->getConfigData('payment_action'), $stateObject);

*/


/*
$storeId  = 1;
$templateId = 'sales_email_order_place_again';
$place_url = Mage::app()->getStore($storeId)->getUrl('adyen/order/place',array('id'=>5563));

$mailer = Mage::getModel('core/email_template_mailer');

$emailInfo = Mage::getModel('core/email_info');
//$emailInfo->addTo($order->getCustomerEmail(), $customerName);
$emailInfo->addTo('864899535@qq.com', 'ling adam');
$mailer->addEmailInfo($emailInfo);

$mailer->setSender(Mage::getStoreConfig('sales_email/shipment/identity', $storeId));
$mailer->setStoreId($storeId);
$mailer->setTemplateId($templateId);
$mailer->setTemplateParams(array(
        'place_url'        => $place_url,
    )
);
$mailer->send();
*/



