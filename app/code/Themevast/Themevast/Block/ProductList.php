<?php 
/*
//ducdq
//ducdevphp@gmail.com
*/
namespace Themevast\Themevast\Block;
class ProductList extends \Magento\Catalog\Block\Product\AbstractProduct
{
	 protected $_productloader;  
	 public function __construct(
        \Magento\Catalog\Block\Product\Context $context,
        \Magento\Framework\Url\Helper\Data $urlHelper,
		\Magento\Catalog\Model\ProductFactory $productloader,
        array $data = []
        ) {
		$this->_productloader = $productloader;
    	$this->urlHelper = $urlHelper;
        parent::__construct($context, $data);
    }
	protected function _beforeToHtml()
    {
	//	$this->_prepareData();
		/* $priceRender = $this->getLayout()->getBlock('product.price.render.default'); */
		$this->setTemplate('Themevast_Themevast::ajaxcart/success1.phtml');
        return parent::_beforeToHtml();
    }
	public function getAddToCartPostParams(\Magento\Catalog\Model\Product $product)
    {
        $url = $this->getAddToCartUrl($product);
        return [
            'action' => $url,
            'data' => [
                'product' => $product->getEntityId(),
                \Magento\Framework\App\ActionInterface::PARAM_NAME_URL_ENCODED =>
                    $this->urlHelper->getEncodedUrl($url),
            ]
        ];
    }
	public function getProductHtml(){
		$productId = (int)$this->getRequest()->getParam('product');
		$product   = $this->_productloader->create()->load($productId);
		$html = $this->getLayout()->createBlock('Themevast\Themevast\Block\Related1')->setData('productC',$product)->toHtml();
        return $html;
	}
	public function getProducts()
    {
		if($this->getRequest()->getParam('product')){
			$productId = (int)$this->getRequest()->getParam('product');
			return $this->_productloader->create()->load($productId);
		}
		return ;
		
    } 
	public function getQtyP()
    {
		if($this->getRequest()->getParam('qty')){
			$qty = (int)$this->getRequest()->getParam('qty');
			return $qty;
		}
		return ;
		
    } 
}