<?php
	/*ducdevphp@gmail.com*/
?>
<?php 
namespace Themevast\ProductTab\Block;

use Magento\Catalog\Api\CategoryRepositoryInterface;
use Magento\Catalog\Model\Category;
use Magento\Catalog\Model\Product;
use Magento\Eav\Model\Entity\Collection\AbstractCollection;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\DataObject\IdentityInterface;

class ProductTabWidget extends \Magento\Catalog\Block\Product\AbstractProduct  implements \Magento\Widget\Block\BlockInterface
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
	protected $_protype;
	protected $_connection;
	protected $_resource;
	protected $_reportCollection;
	protected $_objectManager;
	public function __construct(
        \Magento\Catalog\Block\Product\Context $context,
        \Magento\Framework\Data\Helper\PostHelper $postDataHelper,
        \Magento\Catalog\Model\Layer\Resolver $layerResolver,
        CategoryRepositoryInterface $categoryRepository,
        \Magento\Framework\Url\Helper\Data $urlHelper,
		\Magento\Catalog\Model\ResourceModel\Product\CollectionFactory $productCollectionFactory,
		\Themevast\ProductTab\Model\Config\Source\Type $proType,
		\Magento\Framework\ObjectManagerInterface $objectManager,
		\Magento\Catalog\Model\Product\Visibility $productVisibility,
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
		$this->_protype = $proType;
        parent::__construct(
            $context,
            $data
        );
    }
   
	public function getConfigSys($value=''){

	   $config =  $this->_scopeConfig->getValue('producttab/new_status/'.$value, \Magento\Store\Model\ScopeInterface::SCOPE_STORE);
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
	public function getProducttabIds()
    {
        return $this->getData('tab_id');
    }
	public function getProductCollection($id)
    {
		$collection=$this->_objectManager->create('Themevast\ProductTab\Model\Product')->getProductByTab($id,$this->getData());
		return $collection;
	}
	public function getProductHtml($data){
		 //mageducdq
		 $template = 'Themevast_ProductTab::producttab/items/items_slider.phtml';
		 $html = $this->getLayout()->createBlock('Themevast\ProductTab\Block\ProductList')->setData($data)->setTemplate($template)->toHtml();
        return $html;
	}
	public function isTabActionF($id){
		$activeId =$this->getTabActive($id);
		if($id==$activeId){
			return true;
		}
		return false;
	}
	public function getTabActive($id){
		$tabs=$this->_protype->toOptionArray();
		$tabIds=[];
		foreach($tabs as $tab){
			$tabIds[] = $tab['value'];
		}
		$active='';
		$cfPreload = $this->getConfig('tab_preload');
		if(in_array($cfPreload,$tabIds) && ($cfPreload==$id)){
			$active=$cfPreload;
		}
		if(($id==$tabIds[0])&&(!in_array($cfPreload,$tabIds))){
			$active=$tabIds[0];
		}
		return $active;
	}
	protected function getCustomerGroupId()
	{
		$customerGroupId =   (int) $this->getRequest()->getParam('cid');
		if ($customerGroupId == null) {
			$customerGroupId = $this->httpContext->getValue(Context::CONTEXT_GROUP);
		}
		return $customerGroupId;
	}
    public function getTitle()
    {
        return $this->getData('title');
    }
	public function getIdentify()
    {
        return $this->getData('identify');
    }
	public function getNameTab($product_type){
		 $name ="";
		 switch ($product_type) {
                case \Themevast\ProductTab\Model\Config\Source\Type::LATEST:
                    $name ="Latest";
                    break;
                case \Themevast\ProductTab\Model\Config\Source\Type::NEWARRIVAL:
                    $name ="New Arrival";
                    break;
                case \Themevast\ProductTab\Model\Config\Source\Type::SPECIAL:
                    $name ="Special";
                    break;
                case \Themevast\ProductTab\Model\Config\Source\Type::MOSTPOPULAR:
                    $name ="Most Popular";
                    break;
				case \Themevast\ProductTab\Model\Config\Source\Type::BESTSELLER:
                    $name ="Best Seller";
                    break;
				case \Themevast\ProductTab\Model\Config\Source\Type::TOPRATED:
                    $name ="Top Rated";
                    break;
				
				case \Themevast\ProductTab\Model\Config\Source\Type::RANDOM:
                    $name ="Random";
                    break; 	
				case \Themevast\ProductTab\Model\Config\Source\Type::FEATURED:
                    $name ="Featured";
                    break;
				case \Themevast\ProductTab\Model\Config\Source\Type::DEALS:
                    $name ="Deals";
					break;
            }
		return $name;
	}
}