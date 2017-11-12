<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017/11/8 0008
 * Time: 9:22
 */

if (Mage::helper('varnishcache')->isEnabled()) {
    // get domains for purging
    $domains = Mage::helper('varnishcache/cache')
        ->getStoreDomainList(0);

    // clean Varnish cache
    Mage::getModel('varnishcache/control')
        ->clean($domains, '.*', '.*');
    /*
    $this->_getSession()->addSuccess(
        Mage::helper('varnishcache')->__('The Varnish cache has been cleaned.')
    );
    */
}