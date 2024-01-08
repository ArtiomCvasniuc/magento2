<?php
namespace Themevast\Categorytab\Block;
class CateWidget extends \Magento\Catalog\Block\Product\AbstractProduct implements \Magento\Widget\Block\BlockInterface
{
    
    const DEFAULT_PRODUCTS_COUNT = 10;

   
    const PAGE_VAR_NAME = 'np';

    
    const DEFAULT_PRODUCTS_PER_PAGE = 5;

    
    const DEFAULT_SHOW_PAGER = false;

    
    protected $pager;

    
    protected $httpContext;

    
    protected $_catalogProductVisibility;

    
    protected $productCollectionFactory;

    
    protected $sqlBuilder;

    
    protected $rule;

    protected $productVisibility;
    protected $conditionsHelper;
	protected $storeManager;
	protected $_categoryFactory;
	protected $productFactory;
	protected $_scopeConfig;
    protected $connection;
	protected $resource;
	protected $_blockModelStatic;
	protected $_statusProduct;
	protected $_urlDecoder;
	protected $_filter;
    public function __construct(
        \Magento\Catalog\Block\Product\Context $context,
        \Magento\Catalog\Model\ResourceModel\Product\CollectionFactory $productCollectionFactory,
		\Magento\Catalog\Model\Product\Attribute\Source\Status $statusProduct,
        \Magento\Catalog\Model\Product\Visibility $catalogProductVisibility,
        \Magento\Framework\App\Http\Context $httpContext,
        \Magento\Rule\Model\Condition\Sql\Builder $sqlBuilder,
        \Magento\CatalogWidget\Model\Rule $rule,
        \Magento\Widget\Helper\Conditions $conditionsHelper,
		\Magento\Catalog\Model\CategoryFactory $categoryFactory,
		\Magento\Cms\Model\Block $blockModel,
		\Magento\Framework\App\ResourceConnection $resourceConnection,
		\Magento\Catalog\Model\Product\Visibility $productVisibility,
		\Magento\Framework\Url\DecoderInterface $urlDecoder,
		\Magento\Framework\Filter\Email $email,
        array $data = []
    ) {
        $this->productCollectionFactory = $productCollectionFactory;
        $this->_catalogProductVisibility = $catalogProductVisibility;
        $this->httpContext = $httpContext;
        $this->sqlBuilder = $sqlBuilder;
        $this->rule = $rule;
        $this->conditionsHelper = $conditionsHelper;
		$this->_categoryFactory = $categoryFactory;
		$this->_scopeConfig = $context->getScopeConfig();
		$this->storeManager = $context->getStoreManager();
		$this->resource = $resourceConnection;
		$this->connection = $resourceConnection->getConnection();
		$this->productVisibility = $productVisibility;
		$this->_blockModelStatic = $blockModel;
		$this->_statusProduct = $statusProduct;
		$this->_urlDecoder = $urlDecoder;
		$this->_filter = $email;
        parent::__construct(
            $context,
            $data
        );
        $this->_isScopePrivate = true;
    }

    
    protected function _construct()
    {  
        parent::_construct();
        $this->addColumnCountLayoutDepend('empty', 6)
            ->addColumnCountLayoutDepend('1column', 5)
            ->addColumnCountLayoutDepend('2columns-left', 4)
            ->addColumnCountLayoutDepend('2columns-right', 4)
            ->addColumnCountLayoutDepend('3columns', 3);

    }


    public function getCmsBlockModel(){
        return $this->_blockModelStatic;
    }
	
    public function getProductPriceHtml(
        \Magento\Catalog\Model\Product $product,
        $priceType = null,
        $renderZone = \Magento\Framework\Pricing\Render::ZONE_ITEM_LIST,
        array $arguments = []
    ) {
        if (!isset($arguments['zone'])) {
            $arguments['zone'] = $renderZone;
        }
        $arguments['price_id'] = isset($arguments['price_id'])
            ? $arguments['price_id']
            : 'old-price-' . $product->getId() . '-' . $priceType;
        $arguments['include_container'] = isset($arguments['include_container'])
            ? $arguments['include_container']
            : true;
        $arguments['display_minimal_price'] = isset($arguments['display_minimal_price'])
            ? $arguments['display_minimal_price']
            : true;

        $priceRender = $this->getLayout()->getBlock('product.price.render.default');

        $price = '';
        if ($priceRender) {
            $price = $priceRender->render(
                \Magento\Catalog\Pricing\Price\FinalPrice::PRICE_CODE,
                $product,
                $arguments
            );
        }
        return $price;
    }
	
	protected function _getDefaultStoreId(){
        return \Magento\Store\Model\Store::DEFAULT_STORE_ID;
    }

	public function _beforeToHtml123() {
		
		  
		$categoryId = 8;
		$cate_product = $this->getProductCate($categoryId); 
	
	}
	
	public function getCategory($id) {
		return 	$_category =  $this->_categoryFactory->create()->load($id);
	}
	
	public function getConfigSys($value=''){

	   $config =  $this->_scopeConfig->getValue('categorytab/new_status/'.$value, \Magento\Store\Model\ScopeInterface::SCOPE_STORE);
	   return $config; 
	 
	}
	public function getConfigs(){
		$config = $this->_scopeConfig->getValue('categorytab/new_status',\Magento\Store\Model\ScopeInterface::SCOPE_STORES,$this->storeManager->getStore(true)->getId());
	    return $config; 
	}
	public function getConfig($key, $default = '')
	{
		if($this->hasData($key))
		{
			return $this->getData($key);
		}
		return $default;
	}
	public function getProductCate($id = NULL) {
       
		$storeId = $this->getRequest()->getParam('store', $this->_getDefaultStoreId());
		$_category =  $this->_categoryFactory->create()->load($id);

        $json_products = array();
		 $_productCollection = $this->productCollectionFactory->create()
              ->addAttributeToSelect('*')
               ->addCategoryFilter($_category);
			 $qty = $this->getConfig('qty');	
			 if($qty<1) $qty = 8;
			 $_productCollection ->setPageSize($qty); 		
		return $_productCollection;
		
    }
    public function getBestsellerProductByCat($id = NULL)
    {
    	$storeId    = $this->storeManager->getStore()->getId();
		$_category =  $this->_categoryFactory->create()->load($id);
		$products = $this->productCollectionFactory->create()
						 ->addAttributeToSelect('*')
						 ->addCategoryFilter($_category)
						 ->setStoreId($storeId);
		$select = $this->connection->select()
								->from($this->resource->getTableName('sales_order_item'), 'product_id')
								->order('sum(`qty_ordered`) Desc')
								->group('product_id')
								->limit(100);
		$producIds = array(); 
		foreach ($this->connection->query($select)->fetchAll() as $row) {
		   $producIds[] = $row['product_id'];
		}
		$products
			->addAttributeToSelect('*')
			->addAttributeToFilter('entity_id', array('in'=>$producIds))
            ->addMinimalPrice()
            ->addFinalPrice()
            ->addTaxPercents()
            ->addUrlRewrite()
            ->setVisibility($this->productVisibility->getVisibleInCatalogIds());
		 $qty = $this->getConfig('qty_best');
		 if($qty<1) $qty = 1;
        $products->setPageSize($qty)->setCurPage(1);
		return $products;
    }

   

    /**
     * Get value of widgets' title parameter
     *
     * @return mixed|string
     */
    public function getTitle()
    {
        return $this->getData('title');
    }
	public function getIdentify()
    {
        return $this->getData('identify');
    }
	 public function getCategoryIds()
    {
        return $this->getData('category_id');
    }
	public function generateRandomString($length = 10) {
        $characters = 'abcdefghijklmnopqrstuvwxyz';
        $randomString = '';
        for ($i = 0; $i < $length; $i++) {
            $randomString .= $characters[rand(0, strlen($characters) - 1)];
        }
        return $randomString;
    }
	public function isTabActionF($id){
		$activeId =$this->getTabActive($id);
		if($id==$activeId){
			return true;
		}
		return false;
	}
	public function getTabActive($id){
		$tabs=$this->getTabByConfigAndStore();
		$tabIds=[];
		foreach($tabs as $tab){
			$tabIds[] = $tab['tab_id'];
		}
		$active='';
		$cfPreload = $this->getConfig('tab_preload');
		if(in_array($cfPreload,$tabIds) && ($cfPreload==$id)){
			$active=$cfPreload;
		}
		if(($this->getConfig('show_tab_all'))&&($cfPreload==='all')&&($cfPreload==$id)){
			$active='all';
		}
		if(($cfPreload!='all')&&($id==$tabIds[0])&&(!in_array($cfPreload,$tabIds))){
			$active=$tabIds[0];
		}
		return $active;
	}
	public function getProductHtml($data){
		  
		 $template = 'Themevast_Categorytab::categorytab/product/items.phtml';
		if($this->getConfig('templatetype')){
			$template ='Themevast_Categorytab::categorytab/product/'.$this->getConfig('templatetype').'_items.phtml';
		}
		 $html = $this->getLayout()->createBlock('Themevast\Categorytab\Block\ProductList')->setData($data)->setTemplate($template)->toHtml();
        return $html;
	}
	public function filterImage($item){
		
		$params = explode('/',$item);
		$key = array_search('___directive', $params);
		if ($key)
		{
			$directive = $params[$key+1];
			$directive = $this->_urlDecoder->decode($directive);
			$url = $this->_filter->filter($directive);
			if($url)
			{
				return $item;
			}
		}
		else
		{
			return $this->_getCatDirMedia().$item;
		}
	}
	protected function _getCatDirMedia()
	{
		$dir = $this->storeManager->getStore()->getBaseUrl(\Magento\Framework\UrlInterface::URL_TYPE_MEDIA);
		return $dir;
	}
	public function hasImage($item){
		return ($item)?true:false;
	}
	 protected function _beforeToHtml()
    {
        $templatetype = $this->getConfig('templatetype');
        if($templatetype){
			$template ='Themevast_Categorytab::categorytab/'.$templatetype.'.phtml';
            $this->setTemplate($template);
        }else{
			$template ='Themevast_Categorytab::categorytab/tab.phtml';
			$this->setTemplate($template);
        }
        return parent::_beforeToHtml();
    }
}
