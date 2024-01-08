<?php
	/*ducdevphp@gmail.com*/
?>
<?php 
namespace Themevast\HotDeals\Block;

use Magento\Catalog\Api\CategoryRepositoryInterface;
use Magento\Catalog\Model\Category;
use Magento\Catalog\Model\Product;
use Magento\Eav\Model\Entity\Collection\AbstractCollection;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\DataObject\IdentityInterface;

class DealWidget extends \Magento\Catalog\Block\Product\AbstractProduct  implements \Magento\Widget\Block\BlockInterface
{
	
    protected $_defaultToolbarBlock = 'Magento\Catalog\Block\Product\ProductList\Toolbar';

   
    protected $_productCollection;

    
    protected $_catalogLayer;

    
    protected $_postDataHelper;

    
    protected $urlHelper;

    
    protected $categoryRepository;
    protected $productCollectionFactory;
    protected $storeManager;
    protected $catalogConfig;
    protected $productVisibility;
    protected $scopeConfig;
	protected $_connection;
	protected $_resource;
	protected $_reportCollection;
	protected $_objectManager;
	protected $_productDealModel;
	public function __construct(
        \Magento\Catalog\Block\Product\Context $context,
        \Magento\Framework\Data\Helper\PostHelper $postDataHelper,
        \Magento\Catalog\Model\Layer\Resolver $layerResolver,
         CategoryRepositoryInterface $categoryRepository,
        \Magento\Framework\Url\Helper\Data $urlHelper,
		\Magento\Catalog\Model\ResourceModel\Product\CollectionFactory $productCollectionFactory,
		\Magento\Framework\ObjectManagerInterface $objectManager,
		\Magento\Catalog\Model\Product\Visibility $productVisibility,
		\Themevast\HotDeals\Model\Product $productDealModel,
        array $data = []
    ) {
        $this->_catalogLayer = $layerResolver->get();
        $this->_postDataHelper = $postDataHelper;
        $this->categoryRepository = $categoryRepository;
        $this->urlHelper = $urlHelper;
        $this->productCollectionFactory = $productCollectionFactory;
        $this->storeManager = $context->getStoreManager();
        $this->catalogConfig = $context->getCatalogConfig();
        $this->productVisibility = $productVisibility;
		$this->_objectManager = $objectManager;
		$this->_productDealModel = $productDealModel;
        parent::__construct(
            $context,
            $data
        );
    }
    protected function _beforeToHtml()
    {
        $template = $this->getConfig('template');
        if($template){
            $this->setTemplate($template);
        }else{            
            $this->setTemplate('hotdeals/items.phtml');                
        }
        return parent::_beforeToHtml();
    }
	public function getConfigSys($value=''){

	   $config =  $this->_scopeConfig->getValue('hotdeals/hotdeals_config/'.$value, \Magento\Store\Model\ScopeInterface::SCOPE_STORE);
	   return $config; 
	 
	} 
	 public function getConfig($key, $default = '')
    {
        if($this->hasData($key) && $this->getData($key))
        {
            return $this->getData($key);
        }
        return $default;
    }
	public function getProductCollection()
    {
		$config = $this->getData();
		$config['qty'] = $this->getConfigSys('qty');
		$collection = $this->_productDealModel->getDealsProduct($config);
        return $collection;
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
	public function generateRandomString($length = 10) {
        $characters = 'abcdefghijklmnopqrstuvwxyz';
        $randomString = '';
        for ($i = 0; $i < $length; $i++) {
            $randomString .= $characters[rand(0, strlen($characters) - 1)];
        }
        return $randomString;
    }
}