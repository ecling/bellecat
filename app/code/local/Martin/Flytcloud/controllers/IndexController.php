<?php

class Martin_Flytcloud_IndexController extends Mage_Core_Controller_Front_Action
{
    public function testAction()
    {
        
        try{

        } catch (Exception $ex) {
           var_dump($ex);exit;
        }
                        
    }
    public function indexAction(){//
        try{
            $roter=Mage::app()->getFrontController()->getRouter('standard');
            $modules=$roter->getModuleByFrontName('export');
            var_dump($modules);exit;   
        } catch (Exception $ex) {
           var_dump($ex);exit;
        }

    }
}