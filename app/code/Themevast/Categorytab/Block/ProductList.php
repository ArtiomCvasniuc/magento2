<?php 
/*
//ducdq
//ducdevphp@gmail.com
*/
namespace Themevast\Categorytab\Block;
class ProductList extends \Magento\Catalog\Block\Product\AbstractProduct implements \Magento\Widget\Block\BlockInterface
{
	 protected $urlHelper;
	 protected $_blockModelStatic;
	 public function __construct(
        \Magento\Catalog\Block\Product\Context $context,
        \Magento\Framework\Url\Helper\Data $urlHelper,
		\Magento\Cms\Model\Block $blockModel,
        array $data = []
        ) {
    	$this->urlHelper = $urlHelper;
		$this->_blockModelStatic = $blockModel;
        parent::__construct($context, $data);
    }
	
	public function getCmsBlockModel(){
        return $this->_blockModelStatic;
    }
	public function getConfig($key, $default = '')
	{
		if($this->hasData($key))
		{
			return $this->getData($key);
		}
		return $default;
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
}