<?php

namespace Themevast\Themeoption\Block;

class Timecountdown extends \Magento\Framework\View\Element\Template
{

    protected $_product = null;
    protected $_category = null;

    protected $_coreRegistry = null;

    protected $_date;
    public $_scopeConfig;

    public function __construct(
        \Magento\Framework\View\Element\Template\Context $context,
        \Magento\Framework\Registry $registry,
        \Magento\Framework\Stdlib\DateTime\DateTime $date,
        array $data = []
    )
    {
        
        $this->_coreRegistry = $registry;
        $this->_date = $date;
        $this->_scopeConfig = $context->getScopeConfig();
        parent::__construct($context, $data);
        
        $this->_product = $this->getProduct();
        $this->_category = $this->getCategory();
        
    }

    public function isPriceCountdown($product)
    { 
        $currentDate = strtotime($this->_date->gmtDate());
        $todate = $product->getSpecialToDate() ? (strtotime($product->getSpecialToDate())-$this->_date->getGmtOffset()) : 0;
        $fromdate = $product->getSpecialFromDate() ? (strtotime($product->getSpecialFromDate())-$this->_date->getGmtOffset()) : 0;
        if ($product->getSpecialPrice() && $product->getTimershow() && $todate) {
            if ($todate >= $currentDate && $fromdate <= $currentDate) {
                return true;
            }
        }
        return false;
    }

    public function getProduct()
    {
        return $this->_coreRegistry->registry('product');
    }
    
    public function getCategory()
    {
        return $this->_coreRegistry->registry('current_category');
    }
    
    public function getPageType() {
        if ($this->_product && $this->_product->getId()) {
            return 'product';
        } else if ( $this->_category && $this->_category->getId()) {
            return 'category';
        } else if ($this->getScopeConfig('timecountdown/general/display_in_one')) {
            return 'homepage-one';
        }
        return 'homepage';
    }
    
    public function getScopeConfig($path) {
        return $this->_scopeConfig->getValue($path, \Magento\Store\Model\ScopeInterface::SCOPE_STORE);
    }
}
