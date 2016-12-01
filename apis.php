<?php
$client = new SoapClient('http://www.bellecat.com/api/soap/?wsdl');
// If somestuff requires api authentification,
// then get a session token
$session = $client->login('api_user', 'qwer1234');
//$ss="orderIncrementId";
//$result = $client->call($session,'sales_order.info','100000021');
//print_r($result);
$filters = array(
    'created_at' => array(
        'from' => '2016-03-11 02:13:00',
        'to' => '2016-03-15 06:14:26'
    )
);
$results=$client->call($session,'sales_order.list',array(array('created_at'=>array('from'=>'2016-05-20 06:14:26','to'=>'2016-05-23 20:22:14'))));
//$results=$client->call($session,'sales_order.list');

//print_r($results);
// If you don't need the session anymore
//$client->endSession($session);
//$orderinfo = $client->call($session, 'sales_order.info','');
//print_r($orderinfo);
foreach($results as $oreders){
	$orderinfo = $client->call($session, 'sales_order.info',$oreders['increment_id']);
//echo $orderinfo['items'][0]['product_options'].'---'.$oreders['weight'].'---'.$oreders['grand_total'].'---'.$oreders['status'].'<br>';
    $opt=$orderinfo['items'][0];
	$option=unserialize($orderinfo['items'][0]['product_options']);
	print_r($option);
	$op=$option['options'];
	//print_r($op);
	echo "++++++++++++++++++++++++++++++++++++++++++++++++++++++<br/><br/><br/><br/>";
	$shu=count($op);
	if($shu==2){
	echo $oreders['increment_id'].'---'.$opt['order_id'].$opt["name"]."------".$op[0]['label'].":".$op[0]['value'].'--'.$op[1]['label'].":".$op[1]['value'].'---'.$oreders['weight'].'---'.$oreders['grand_total'].'---'.$oreders['status']."<br>";
	}else{
		echo $oreders['increment_id'].'---'.$opt["name"]."------".$op[0]['label'].":".$op[0]['value'].'---'.$oreders['weight'].'---'.$oreders['grand_total'].'---'.$oreders['status']."<br>";
	
		
	}
	
	
}

?>