<?php
class Martin_SalesReports_Block_Adminhtml_Product extends Mage_Adminhtml_Block_Widget_Grid_Container{
    protected $_addButtonLabel;
    protected $_backButtonLabel;
    protected $_blockGroup = 'salesreports';
    protected $_controller = 'adminhtml_product';
    
    public function __construct()
    {
        $this->_headerText = Mage::helper('customer')->__('SKU 销量报表');
        parent::__construct();
    }    
}