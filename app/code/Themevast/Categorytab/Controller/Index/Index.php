<?php
/* ducdevphp@gmail.com */
namespace Themevast\Categorytab\Controller\Index;

class Index extends \Magento\Framework\App\Action\Action
{
	protected $resultPageFactory;
	protected $_categoryFactory;
	protected $storeManager;
	protected $productCollectionFactory;
	protected $connection;
	protected $resource;
	protected $productVisibility;
	protected $_statusProduct;
	public function __construct(
		\Magento\Framework\App\Action\Context $context,
		\Magento\Framework\View\Result\PageFactory $resultPageFactory,
		\Magento\Catalog\Model\CategoryFactory $categoryFactory,
		\Magento\Catalog\Model\ResourceModel\Product\CollectionFactory $productCollectionFactory,
		\Magento\Catalog\Model\Product\Attribute\Source\Status $statusProduct,
		\Magento\Framework\App\ResourceConnection $resourceConnection,
		\Magento\Catalog\Model\Product\Visibility $productVisibility,
		\Magento\Store\Model\StoreManagerInterface $storeManager
		)
	{
		$this->_statusProduct = $statusProduct;
		$this->productCollectionFactory = $productCollectionFactory;
		$this->_categoryFactory = $categoryFactory;
		$this->resultPageFactory = $resultPageFactory;
		$this->storeManager = $storeManager;
		$this->connection = $resourceConnection->getConnection();
		$this->resource = $resourceConnection;
		$this->productVisibility = $productVisibility;
		parent::__construct($context);
	}

	public function execute()
	{
		$this->_view->loadLayout();
		$params = $this->getRequest()->getParams();
		$_cfg =  $params['cattabconfig'];
		$is_ajax_cattab =$params['is_ajax_cattab'];
		$config = (array)json_decode(base64_decode(strtr($_cfg, '-_', '+/')));
		if (!$this->getRequest()->isAjax() || !$is_ajax_cattab) {
			return;
		}
		$id = $params['cattabId'];
		$collection=$this->getProductCate($id,$config);
		$data = $config;
        $data['productCollection'] = $collection;
		$data['bestsellerCollection']='';
		if($config['show_best']){
			$bestsellerIds = $params['bestIds'];
			$bestarray  = explode(',',$bestsellerIds);
			if(in_array($id,$bestarray)){
				$data['bestsellerCollection']=$this->getBestsellerProductByCat($id,$config);
			}
		}
		$res = [];
		$res['cattab_ajax_data'] = $this->_view->getLayout()->createBlock('Themevast\Categorytab\Block\Ajax')->setData($data)->toHtml();
		$this->getResponse()->representJson(
			$this->_objectManager->get('Magento\Framework\Json\Helper\Data')->jsonEncode($res)
			);
	}
	public function getProductCate($id,$config) {
       
		$storeId    = $this->storeManager->getStore()->getId();
		$_category =  $this->_categoryFactory->create()->load($id);
        $json_products = array();
		 $_productCollection = $this->productCollectionFactory->create()
              ->addAttributeToSelect('*')
              ->addCategoryFilter($_category)
			  ->setStoreId($storeId);
			 $qty = $config['qty'];	
			 if($qty<1) $qty = 8;
			 $_productCollection ->setPageSize($qty); 		
		return $_productCollection;
		
    }
/*ducdevphp*/	
	  public function getBestsellerProductByCat($id,$config)
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
		 $qty =  $config['qty_best'];
		 if($qty<1) $qty = 1;
        $products->setPageSize($qty)->setCurPage(1);
		return $products;
    }
}
/* ducdepzai :)*/