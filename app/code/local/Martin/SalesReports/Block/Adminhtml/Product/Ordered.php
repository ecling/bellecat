<?php
class Martin_SalesReports_Block_Adminhtml_Product_Ordered extends Mage_Adminhtml_Block_Template{
    public function getCollection(){
        if($from = $this->helper('salesreports')->getParam('from')){
            $from = DateTime::createFromFormat('m/d/Y',$from);
            $from = $from->format('Y-m-d');
        }else{
            $from = date('Y-m-d',time()-3600*24*7);
        }

        if($to = $this->helper('salesreports')->getParam('to')){
            $to = strtotime($to)+3600*24;
            $to = date('Y-m-d',$to);
            //$to = DateTime::createFromFormat('m/d/Y',$to);
            //$to = $to->format('Y-m-d');
        }else{
            $to  = date('Y-m-d',time());
        }

        //$product_id = $this->getRequest()->getParam('id',null);

        $collection = Mage::getResourceModel('sales/order_item_collection');

        $collection->getSelect()
            ->reset(Zend_Db_Select::COLUMNS)
            ->joinLeft('sales_flat_order AS order','main_table.order_id=order.entity_id','')
            ->columns("DATE_FORMAT(order.created_at,'%y/%m/%d') AS date")
            ->columns("SUM(main_table.qty_ordered) AS subtotal")
            ->where('order.created_at>=?',array('from'=>$from))
            ->where('order.created_at<?',array('to'=>$to))
            ->where("order.status='complete' OR order.status='processing'")
            ->group('date');

        return $collection;
    }
}