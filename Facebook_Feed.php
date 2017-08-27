<?php
include('app/Mage.php');
Mage::app();

$ob = Mage::getModel('Facebook_AdsToolbox/observer');
$obins = new $ob;
$use_cache = false;
$obins->internalGenerateFacebookProductFeed(false, $use_cache);

echo 'success';