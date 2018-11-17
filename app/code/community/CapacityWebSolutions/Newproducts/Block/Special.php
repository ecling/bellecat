<?php
/***************************************************************************
Extension Name	: New Products
Extension URL	: http://www.magebees.com/magento-new-products-extension.html
Copyright		: Copyright (c) 2015 MageBees, http://www.magebees.com
Support Email	: support@magebees.com
 ***************************************************************************/
class CapacityWebSolutions_Newproducts_Block_Special extends Mage_Catalog_Block_Product_New //Mage_Catalog_Block_Product_Abstract
{
    /**
     * Name of request parameter for page number value
     */
    const PAGE_VAR_NAME                     = 'p';
    protected $_productCollection;

    public function __construct() {
        parent::_construct();
        $this->addColumnCountLayoutDepend('empty', 6)
            ->addColumnCountLayoutDepend('one_column', 5)
            ->addColumnCountLayoutDepend('two_columns_left', 4)
            ->addColumnCountLayoutDepend('two_columns_right', 4)
            ->addColumnCountLayoutDepend('three_columns', 3);

        $this->setStoreId(Mage::app()->getStore()->getId());

        //General Settings
        $this->setEnabled((bool)Mage::getStoreConfig("newproducts/general/enabled"));
        $this->setDisplayHeading(false);
        $this->setHeading(Mage::getStoreConfig("newproducts/general/heading"));
        $this->setChooseProducts(Mage::getStoreConfig("newproducts/general/choose_products"));
        $this->setChooseType((bool)Mage::getStoreConfig("newproducts/general/choose_type"));
        $this->setDays((int)Mage::getStoreConfig("newproducts/general/new_days"));
        $this->setDisplayBy(Mage::getStoreConfig("newproducts/general/display_by"));
        $this->setCategories(Mage::getStoreConfig("newproducts/general/categories"));
        $this->setSortBy(Mage::getStoreConfig("newproducts/general/sort_by"));
        $this->setSortOrder(Mage::getStoreConfig("newproducts/general/sort_order"));

        $this->setProductsPrice((bool)Mage::getStoreConfig("newproducts/general/products_price"));
        $this->setReview((bool)Mage::getStoreConfig("newproducts/general/review"));
        $this->setShortDesc((bool)Mage::getStoreConfig("newproducts/general/short_desc"));
        $this->setDescLimit((int)Mage::getStoreConfig("newproducts/general/desc_limit"));
        $this->setAddToCart((bool)Mage::getStoreConfig("newproducts/general/add_to_cart"));
        $this->setAddToWishlist((bool)Mage::getStoreConfig("newproducts/general/add_to_wishlist"));
        $this->setAddToCompare((bool)Mage::getStoreConfig("newproducts/general/add_to_compare"));
        $this->setOutOfStock((bool)Mage::getStoreConfig("newproducts/general/out_of_stock"));
        $this->setIsResponsive((bool)Mage::getStoreConfig('newproducts/general/isresponsive'));

        //Template Settings
        $this->setCustomTemplate(Mage::getStoreConfig("newproducts/template/select_template"));
        $this->setProductsCount((int)Mage::getStoreConfig("newproducts/template/number_of_items"));
        $this->setShowPager((bool)Mage::getStoreConfig("newproducts/template/show_pager"));
        $this->setProductsPerPage((int)Mage::getStoreConfig("newproducts/template/products_per_page"));
        $this->setHeight((int)Mage::getStoreConfig("newproducts/template/thumbnail_height"));
        $this->setWidth((int)Mage::getStoreConfig("newproducts/template/thumbnail_width"));
    }

    public function setWidgetOptions(){
        //General Settings
        $this->setDisplayHeading((bool)$this->getWdDisplayHeading());
        $this->setHeading($this->getWdHeading());
        $this->setChooseProducts($this->getWdChooseProducts());
        $this->setChooseType($this->getWdChooseType());
        $this->setDays((int)$this->getWdNewDays());
        $this->setDisplayBy((int)$this->getWdDisplayBy());
        $this->setCategories($this->getWdCategories());
        $this->setSortBy($this->getWdSortBy());
        $this->setSortOrder($this->getWdSortOrder());
        $this->setProductsPrice((bool)$this->getWdProductsPrice());
        $this->setReview((bool)$this->getWdReview());
        $this->setShortDesc((bool)$this->getWdShortDesc());
        $this->setDescLimit((int)$this->getWdDescLimit());
        $this->setAddToCart((bool)$this->getWdAddToCart());
        $this->setAddToWishlist((bool)$this->getWdAddToWishlist());
        $this->setAddToCompare((bool)$this->getWdAddToCompare());
        $this->setOutOfStock((bool)$this->getWdOutOfStock());

        //Template Settings
        $this->setProductsCount((int)$this->getWdNumberOfItems());
        $this->setShowPager((bool)$this->getWdShowPager());
        $this->setProductsPerPage((int)$this->getWdProductsPerPage());
        $this->setHeight((int)$this->getWdThumbnailHeight());
        $this->setWidth((int)$this->getWdThumbnailWidth());
    }

    protected function _getProductCollection()  {
        $todayStartOfDayDate  = Mage::app()->getLocale()->date()
            ->setTime('00:00:00')
            ->toString(Varien_Date::DATETIME_INTERNAL_FORMAT);

        $todayEndOfDayDate  = Mage::app()->getLocale()->date()
            ->setTime('23:59:59')
            ->toString(Varien_Date::DATETIME_INTERNAL_FORMAT);

        /** @var $collection Mage_Catalog_Model_Resource_Product_Collection */
        $collection = Mage::getResourceModel('catalog/product_collection');
        $collection->setVisibility(Mage::getSingleton('catalog/product_visibility')->getVisibleInCatalogIds());


        $collection = $this->_addProductAttributesAndPrices($collection)
            ->addStoreFilter()
            ->addAttributeToFilter('special_from_date', array('or'=> array(
                0 => array('date' => true, 'to' => $todayEndOfDayDate),
                )
            ), 'left')
            ->addAttributeToFilter('special_to_date', array('or'=> array(
                0 => array('date' => true, 'from' => $todayStartOfDayDate),
                1 => array('is' => new Zend_Db_Expr('null')))
            ), 'left')
            ->addAttributeToFilter(
                array(
                    array('attribute' => 'special_from_date', 'is'=>new Zend_Db_Expr('not null')),
                    array('attribute' => 'special_to_date', 'is'=>new Zend_Db_Expr('not null'))
                )
                )
            ->addAttributeToFilter('special_price',array('lt'=>new Zend_Db_Expr('e.price')))
            ->addAttributeToSort('created_at', 'desc')
        ;

        return $collection;
    }

    /**
     * Prepare collection with new products
     *
     * @return Mage_Core_Block_Abstract
     */
    protected function _beforeToHtml(){
        if($this->getType()=="newproducts/widget")
        {
            $this->setWidgetOptions();
        }
        $this->getPagerHtml();
        $this->getProductCollection()->load();
        return parent::_beforeToHtml();
        //$this->setProductCollection($this->_getProductCollection());
    }

    public function getProductCollection(){
        if(is_null($this->_productCollection)){
            $this->_productCollection = $this->_getProductCollection();
        }
        return $this->_productCollection;
    }


    public function _toHtml(){
        $this->setTemplate('newproducts/newproducts-grid.phtml');
        return parent::_toHtml();
    }

    /**
     * Render pagination HTML
     *
     * @return string
     */
    public function getPagerHtml()
    {
        if ($this->getShowPager()) {
            if (!$this->_pager) {
                $this->_pager = $this->getLayout()
                    ->createBlock('newproducts/widget_html_pager', 'widget.new.product.list.pager');

                $this->_pager->setUseContainer(true)
                    ->setShowAmounts(true)
                    ->setShowPerPage(false)
                    ->setPageVarName(self::PAGE_VAR_NAME)
                    ->setLimit(24)
                    //->setLimit($this->getProductsPerPage())
                    //->setTotalLimit(100)
                    ->setCollection($this->getProductCollection());
            }
            if ($this->_pager instanceof Mage_Core_Block_Abstract) {
                //return $this->_pager->toHtml();
                $this->setChild('pager', $this->_pager);
            }
        }
        return $this;
    }

}
