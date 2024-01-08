<?php

namespace Themevast\HotDeals\Block\Product;

use Magento\Catalog\Api\CategoryRepositoryInterface;
use Magento\Catalog\Model\Category;
use Magento\Catalog\Model\Product;
use Magento\Eav\Model\Entity\Collection\AbstractCollection;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\DataObject\IdentityInterface;

class ProductList extends \Magento\Catalog\Block\Product\ListProduct {

    protected $_collection;

    protected $_productCollection;

    protected $_imageHelper;
    
    protected $_catalogLayer;

    protected $_postDataHelper;

    protected $urlHelper;

    protected $categoryRepository;
	
	protected $_productDealModel;
    public function __construct(
		\Magento\Catalog\Block\Product\Context $context, 
		\Magento\Framework\Data\Helper\PostHelper $postDataHelper, 
		\Magento\Catalog\Model\Layer\Resolver $layerResolver, 
		CategoryRepositoryInterface $categoryRepository, 
		\Magento\Framework\Url\Helper\Data $urlHelper, 
		\Magento\Catalog\Model\ResourceModel\Product\Collection $collection,
		\Themevast\HotDeals\Model\Product $productDealModel,
		array $data = []
    ) {
        $this->imageBuilder = $context->getImageBuilder();
        $this->_catalogLayer = $layerResolver->get();
        $this->_postDataHelper = $postDataHelper;
        $this->categoryRepository = $categoryRepository;
        $this->urlHelper = $urlHelper;
        $this->_collection = $collection;
        $this->_imageHelper = $context->getImageHelper();
		$this->_productDealModel = $productDealModel;
        parent::__construct($context, $postDataHelper, $layerResolver, $categoryRepository, $urlHelper, $data);
    }
	
    protected function _getProductCollection() {
       // $config = $this->getData();
		//$config['qty'] = $this->getConfigSys('qty');
		$collection = $this->_productDealModel->getAllDealsProduct();
	//	$product_list_order = $this->getRequest()->getParam('product_list_order') ? $this->getRequest()->getParam('product_list_order') : $this->getToolbarBlock()->getDefaultOrder();
	//	$product_list_dir   = $this->getRequest()->getParam('product_list_dir') ? $this->getRequest()->getParam('product_list_dir') : $this->getToolbarBlock()->getDefaultDirection();
	//	$collection->setOrder($this->getRequest()->getParam('product_list_order'),$this->getRequest()->getParam('product_list_dir')); 
		$limit = (int)$this->getRequest()->getParam('product_list_limit') ? (int)$this->getRequest()->getParam('product_list_limit') : (int)$this->getToolbarBlock()->getDefaultPerPageValue();
		$collection->setPageSize($limit)->setCurPage($this->getRequest()->getParam('p'));
        $this->_productCollection = $collection;
        return $this->_productCollection;
    }

     /* public function getToolbarHtml() {
        return $this->getChildHtml('pager');
    } */

    /* public function getMode() {
        return 'grid';
    }

    public function getImageHelper() {
        return $this->_imageHelper;
    }

    public function getPageTitle() {
       
    } */

}
