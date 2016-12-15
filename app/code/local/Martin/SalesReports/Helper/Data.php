<?php
class Martin_SalesReports_Helper_Data extends Mage_Core_Helper_Abstract{
    public function getParam($key){
        $session = Mage::getSingleton('adminhtml/session');
        $sessionParamName = 'salesreports-'.$key;
        $value = null;
        if($value = Mage::app()->getRequest()->getParam($key)){
            $session->setData($sessionParamName, $value);
        }else{
            $value = $session->getData($sessionParamName);
        }
        return $value;
    }   
}