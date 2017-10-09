<?php
class Martin_Bcshipping_Helper_Data extends Mage_Core_Helper_Abstract
{
    public function calculate($product,$country){
        $purchase_price = $product->getPurchasePrice();
        $shipping_cost = $product->getShippingCost();
        $weight = $product->getWeight();
        $bcshipping = $this->getShippingCoseByCountry($weight,$country);
        $product_price = $purchase_price+$shipping_cost+($weight*$bcshipping->getPrice())+$bcshipping->getAdditionalPrice();
        $product_price = number_format($product_price*2/6.5,'2');
        return $product_price;
    }

    public function getShippingCoseByCountry($weight,$country){
        $bcshipping = Mage::getModel('bcshipping/price')->loadByCountry($weight,$country);
        if(!$bcshipping->getId()){
            $bcshipping = Mage::getModel('bcshipping/price')->loadByCountry($weight,'NL');
        }
        return $bcshipping;
    }

    public function getBasePrice(){

    }
}