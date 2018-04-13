<?php
include('app/Mage.php');
Mage::app();

Mage::getModel('adyen/cronjob')->updateNotificationQueue();
?>
