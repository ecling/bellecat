<?php
/**
 * @author Ashley Schroder (aschroder.com)
 * @copyright  Copyright (c) 2014 Ashley Schroder
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

class Aschroder_SMTPPro_Model_Transports_Mailgun extends Aschroder_SMTPPro_Model_Transports_Basesmtp {
    /*
    public function getName($storeId) {
        return "Mailgun SMTP";
    }
    public function getEmail($storeId) {
        return 'postmaster@noreply.bellecat.com';
    }
    public function getPassword($storeId) {
        return '9050ba3b340298feb3237322285379d1-0470a1f7-af605aa2';
    }
    public function getHost($storeId) {
        return 'smtp.mailgun.org';
    }
    public function getPort($storeId) {
        return '587';
    }
    public function getAuth($storeId) {
        return 'login';
    }
    public function getSsl($storeId) {
        return 'tls';
    }
    */
    public function getName($storeId) {
        return "Google Apps/Gmail";
    }
    public function getEmail($storeId) {
        return 'no-reply@bellecat.com';
    }
    public function getPassword($storeId) {
        return 'belle123';
    }
    public function getHost($storeId) {
        return "smtp.gmail.com";
    }
    public function getPort($storeId) {
        return 587;
    }
    public function getAuth($storeId) {
        return 'login';
    }
    public function getSsl($storeId) {
        return 'tls';
    }
}