<?php

class Martin_Flytcloud_TrackController extends Mage_Core_Controller_Front_Action
{
    public function IndexAction(){
        $num = $this->getRequest()->getParam('num',null);
        $num = trim($num);

        if(substr($num,0,2)=='YT'){
            $type = 'yt';
        }elseif(substr($num,0,1)=='F'){
            $type = 'ft';
        }else{
            $type = '4px';
        }

        if($type=='yt'){
            $api_url =   'http://api.yunexpress.com/LMS.API/api/WayBill/GetTrackingNumber?trackingNumber='.$num;
            $user_num = 'C34260';
            $api_secret = 'FH71ONhnfTw=';
            $token = $user_num.'&'.$api_secret;
            $headr = array();
            $headr[] = 'Authorization: Basic '.base64_encode($token);
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, $api_url);
            curl_setopt($ch, CURLOPT_HTTPHEADER, $headr);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
            $result = curl_exec($ch);
            curl_close($ch);
            $result = Mage::helper('core')->jsonDecode($result);

            if($result['ResultCode']=='0000') {
                $track = array('type' => $type, 'info' => $result);
                Mage::register('track', $track);
            }
        }elseif($type=='4px') {
            $test = Mage::getModel('flytcloud/disifang_orderonlinetools');
            $result = $test->cargoTrackingService(array($num));

            $track = array('type'=>$type,'info'=>$result);

            Mage::register('track', $track);
        }else {
            $url = 'http://tracking.sellercube.com/Tracking/TrackOrder';
            $post_data = array('key' => '7C1FB2D775CEBB44ABEF86F3975D9AC8', 'tracking_number' => $num);

            try {
                $ch = curl_init();
                curl_setopt($ch, CURLOPT_URL, $url);
                curl_setopt($ch, CURLOPT_POST, 1);
                curl_setopt($ch, CURLOPT_POSTFIELDS, $post_data);
                curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
                $result = curl_exec($ch);
                curl_close();
                $result = Mage::helper('core')->jsonDecode($result);
                $track = $result['dat']['0']['track'];
                $track = array('type'=>$type,'info'=>$track);
                Mage::register('track', $track);
            } catch (Mage_Core_Exception $e) {
                Mage::logException($e);
            }
        }
        $this->loadLayout();
        $this->renderLayout();   
    }
}