<?php
namespace Themevast\HotDeals\Block;

class View extends \Magento\Framework\View\Element\Template
{
    protected $_coreRegistry = null;

    protected $_catalogLayer;

    public function __construct(
        \Magento\Framework\View\Element\Template\Context $context,
        \Magento\Catalog\Model\Layer\Resolver $layerResolver,
        \Magento\Framework\Registry $registry,
        array $data = []
        ) {
        $this->_catalogLayer = $layerResolver->get();
        $this->_coreRegistry = $registry;
        parent::__construct($context, $data);
    }
	 protected function _prepareLayout()
    {
        $this->_addBreadcrumbs();
        $this->pageConfig->getTitle()->set($this->getConfigSys('title'));
     //   $this->pageConfig->setKeywords($this->getConfigSys('meta_keywords'));
      //  $this->pageConfig->setDescription($this->getConfigSys('meta_description'));

        return parent::_prepareLayout();
    }
	public function getConfigSys($value=''){

	   $config =  $this->_scopeConfig->getValue('hotdeals/hotdeals_config/'.$value, \Magento\Store\Model\ScopeInterface::SCOPE_STORE);
	   return $config; 
	 
	} 
	  protected function _addBreadcrumbs()
    {
        if ($this->_scopeConfig->getValue('web/default/show_cms_breadcrumbs', \Magento\Store\Model\ScopeInterface::SCOPE_STORE)
            && ($breadcrumbsBlock = $this->getLayout()->getBlock('breadcrumbs'))
        ) {
            $breadcrumbsBlock->addCrumb(
                'home',
                [
                    'label' => __('Home'),
                    'title' => __('Go to Home Page'),
                    'link' => $this->_storeManager->getStore()->getBaseUrl()
                ]
            );
            $breadcrumbsBlock->addCrumb(
                'hotdeal',
                [
                    'label' => __('Hot Deals'),
                    'title' => __(sprintf('Go to Product Deal'))
                ]
            );
        }
    }
    public function getProductListHtml()
    {
        return $this->getChildHtml('product_list');
    }

    protected function _beforeToHtml()
    {
        return parent::_beforeToHtml();  
    }
}