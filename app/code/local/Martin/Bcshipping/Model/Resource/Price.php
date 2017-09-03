<?php
class Martin_Bcshipping_Model_Resource_Price extends Mage_Core_Model_Resource_Db_Abstract
{
    protected function _construct()
    {
        $this->_init('bcshipping/price','id');
    }
}