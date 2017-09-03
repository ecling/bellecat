<?php
class Martin_Bcshipping_IndexController extends Mage_Core_Controller_Front_Action
{
    public function indexAction(){
        $test = Mage::getModel('bcshipping/price')->getCollection();
        foreach ($test as $item) {
            var_dump($item->getData());
        }

    }
}