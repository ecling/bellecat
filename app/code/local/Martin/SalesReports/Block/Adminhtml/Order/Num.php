<?php
class Martin_SalesReports_Block_Adminhtml_Order_Num extends Mage_Adminhtml_Block_Template{ 
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
        
        $collection = Mage::getResourceModel('sales/order_collection');
        
        $collection->getSelect()
            ->reset(Zend_Db_Select::COLUMNS)
            ->columns("DATE_FORMAT(created_at,'%y%m%d') AS date")
            ->columns("SUM(base_subtotal) AS subtotal")
            ->where('created_at>=?',array('from'=>$from))
            ->where('created_at<?',array('to'=>$to))
            ->where("status='complete' OR status='processing'")
            ->group('date');

        return $collection;
    }
}