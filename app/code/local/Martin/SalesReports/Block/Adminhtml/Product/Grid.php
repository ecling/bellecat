<?php
class Martin_SalesReports_Block_Adminhtml_Product_Grid extends Mage_Adminhtml_Block_Widget_Grid{
    public function __construct()
    {
        parent::__construct();
        $this->setId('customerGrid');
        $this->setUseAjax(true);
        $this->setDefaultSort('entity_id');
        $this->setSaveParametersInSession(true);
    }
    
    protected function _prepareCollection()
    {
        $collection = Mage::getResourceModel('salesreports/order_item_collection')
            ->addAttributeToSelect('sku')
            ->addAttributeToSelect('name')
            ->addAttributeToSelect('qty_ordered')
            ->addAttributeToSelect('created_at'); 
            
        $collection->getSelect()
            ->columns("sum(qty_ordered) as num")
            ->order('num desc')
            ->group('product_id');
        
        $this->setCollection($collection);

        return parent::_prepareCollection();
    }
    
    protected function _prepareColumns()
    {
        $this->addColumn('sku', array(
            'header'    => Mage::helper('customer')->__('sku'),
            'index'     => 'sku'
        ));
        
        $this->addColumn('name', array(
            'header'    => Mage::helper('customer')->__('Name'),
            'index'     => 'name'
        ));
        
        $this->addColumn('qty_ordered', array(
            'header'    => Mage::helper('customer')->__('Ordered Qty'),
            'index'     => 'num',
            'type'      => 'number'
        ));

        $this->addColumn('action',
            array(
                'header'    =>  Mage::helper('customer')->__('Action'),
                'width'     => '100',
                'type'      => 'action',
                'getter'    => 'getId',
                'actions'   => array(
                    array(
                        'caption'   => Mage::helper('customer')->__('Edit'),
                        'url'       => array('base'=> '*/*/edit'),
                        'field'     => 'id'
                    )
                ),
                'filter'    => false,
                'sortable'  => false,
                'index'     => 'stores',
                'is_system' => true,
        ));


        return parent::_prepareColumns();
    }
}