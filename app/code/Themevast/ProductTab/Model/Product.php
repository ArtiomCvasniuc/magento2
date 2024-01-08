<?php
//dev by ducdq 
//ducdevphp@gmail
namespace Themevast\ProductTab\Model;
 
class Product extends \Magento\Framework\DataObject
{
	protected $_productCollectionFactory;

    protected $_reportCollection;

    protected $_catalogProductVisibility;

    protected $_localeDate;

    protected $_storeManager;

    protected $_resource; 
	
	protected $_resourceConnection;
	
	protected $_categoryFactory;
	 
	protected $_statusProduct;
	
	protected $_objectManager;
	
    protected $_productCollection; 
	
	protected $_coreRegistry;
	
	protected $_connection;
	public function __construct(
        \Magento\Catalog\Model\ResourceModel\Product\CollectionFactory $productCollectionFactory,
        \Magento\Reports\Model\ResourceModel\Product\CollectionFactory $reportCollection,
        \Magento\Catalog\Model\Product\Visibility $catalogProductVisibility,
        \Magento\Framework\Stdlib\DateTime\TimezoneInterface $localeDate,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Magento\Framework\Stdlib\DateTime\DateTime $date,
        \Magento\Framework\App\ResourceConnection $resourceConnection,
		/* \Mageducdq\Producttab\Model\TabFactory $tabFactory, */
		\Magento\Catalog\Model\CategoryFactory $categoryFactory,
		\Magento\Catalog\Model\Product\Attribute\Source\Status $statusProduct,
		\Magento\Framework\ObjectManagerInterface $objectManagerInterface,
		 \Magento\Framework\Registry $coreRegistry,
        array $data = []
        ) {
        $this->_localeDate = $localeDate;
        $this->_productCollectionFactory = $productCollectionFactory;
        $this->_reportCollection = $reportCollection;
        $this->_catalogProductVisibility = $catalogProductVisibility;
        $this->_storeManager = $storeManager;
        $this->date = $date;
        $this->_resourceConnection = $resourceConnection;
		$this->_categoryFactory = $categoryFactory;
		$this->_statusProduct = $statusProduct;
		$this->_objectManager = $objectManagerInterface;
		$this->_coreRegistry = $coreRegistry;
		$this->_resource = $resourceConnection;
		$this->_connection = $resourceConnection->getConnection();
		parent::__construct($data);
    }
	public function getProductByTab($tab_id,$cf){
		 $collection="";
		 switch ($tab_id) {
                case \Themevast\ProductTab\Model\Config\Source\Type::LATEST:
                    $collection =$this->getLastProduct($cf);
                    break;
                case \Themevast\ProductTab\Model\Config\Source\Type::NEWARRIVAL:
                    $collection =$this->getNewProduct($cf);
                    break;
                case \Themevast\ProductTab\Model\Config\Source\Type::SPECIAL:
                    $collection =$this->getSaleProduct($cf);
                    break;
                case \Themevast\ProductTab\Model\Config\Source\Type::MOSTPOPULAR:
                    $collection =$this->getMostPopualProduct($cf);
                    break;
				case \Themevast\ProductTab\Model\Config\Source\Type::BESTSELLER:
                    $collection =$this->getBestsellerProducts($cf);
                    break;
				case \Themevast\ProductTab\Model\Config\Source\Type::TOPRATED:
                    $collection =$this->getTopRatedProduct($cf);
                    break;
				
				case \Themevast\ProductTab\Model\Config\Source\Type::RANDOM:
                    $collection =$this->getRandomProduct($cf);
                    break; 	
				case \Themevast\ProductTab\Model\Config\Source\Type::FEATURED:
                    $collection =$this->getFeaturedProduct($cf);
                    break;
				case \Themevast\ProductTab\Model\Config\Source\Type::DEALS:
                    $collection =$this->getDealsProduct($cf);
					break;
            }
		return $collection;
	}
	public function getFeaturedProduct($cf) {
		$storeId = $this->_storeManager->getStore(true)->getId();
		$products = $this->_productCollectionFactory->create()->setStoreId($storeId);
		$attributes = $this->_objectManager->get('Magento\Catalog\Model\Config')->getProductAttributes();
		$products
			->addAttributeToSelect($attributes)
			->addAttributeToFilter('featured', 1)
            ->addMinimalPrice()
            ->addFinalPrice()
            ->addTaxPercents()
            ->addUrlRewrite()
            ->setVisibility($this->_catalogProductVisibility->getVisibleInCatalogIds());
        $products->setPageSize($cf['qty'])->setCurPage(1);
		return $products;
	}
				
	public function getSaleProduct($cf) {
			
		$storeId = $this->_storeManager->getStore(true)->getId();
		$products = $this->_productCollectionFactory->create()->setStoreId($storeId);
		$attributes = $this->_objectManager->get('Magento\Catalog\Model\Config')->getProductAttributes();
		$todayDate= date('Y-m-d', time());
		$products
			->addAttributeToSelect($attributes)
            ->addMinimalPrice()
            ->addFinalPrice()
            ->addTaxPercents()
            ->addUrlRewrite()
            ->setVisibility($this->_catalogProductVisibility->getVisibleInCatalogIds())
			->addAttributeToFilter('special_to_date', array('date'=>true, 'from'=> $todayDate));
        $products->setPageSize($cf['qty'])->setCurPage(1);
		return $products;
	}

	public function getNewProduct($cf) {
		$storeId = $this->_storeManager->getStore(true)->getId();
		$products = $this->_productCollectionFactory->create()->setStoreId($storeId);
		$attributes = $this->_objectManager->get('Magento\Catalog\Model\Config')->getProductAttributes();
		$todayDate= date('Y-m-d', time());
		$products
			->addAttributeToSelect($attributes)
            ->addMinimalPrice()
            ->addFinalPrice()
            ->addTaxPercents()
            ->addUrlRewrite()
            ->setVisibility($this->_catalogProductVisibility->getVisibleInCatalogIds())
			->addAttributeToFilter('news_from_date', array('date'=>true, 'to'=> $todayDate))
			->addAttributeToFilter('news_to_date', array('date'=>true, 'from'=> $todayDate))
			->addAttributeToSort('news_from_date','desc');	
        $products->setPageSize($cf['qty'])->setCurPage(1);
		return $products;
	}
	// public function getRandomProduct($cf) {
		// $storeId = $this->_storeManager->getStore(true)->getId();
	// //	$category = $this->categoryRepository->get($this->_storeManager->getStore()->getRootCategoryId());
	// //	$products = $category->getProductCollection();
		// $products = $this->_productCollectionFactory->create()->setStoreId($storeId);
		// $attributes = $this->_objectManager->get('Magento\Catalog\Model\Config')->getProductAttributes();
		// /* $products->joinField(
            // 'position',
            // 'catalog_category_product',
            // 'position',
            // 'product_id=entity_id',
            // 'category_id=' . (int)$category->getId()
        // ); */
		// $products
            // ->addAttributeToSelect($attributes)
            // ->addMinimalPrice()
            // ->addFinalPrice()
            // ->addTaxPercents()
          // /*   ->addUrlRewrite($category->getId()) */
            // ->setVisibility($this->_catalogProductVisibility->getVisibleInCatalogIds());
		// if(!$qty = $this->getConfig('qty'))
			// $qty = 8;
		// if($products->getSize() > $qty)
		// {
			// $prids = $products->getAllIds();
			// $keys = array_rand($prids, $qty);
			// $ids = array();
			// foreach($keys as $key)
				// $ids[] = $prids[$key];
			// $products->addAttributeToFilter('entity_id', array('in'=>$ids));	
		// }
        // $products->setPageSize($cf['qty'])->setCurPage(1);
		// return $products;
	// }
     public function getRandomProduct($cf){
        /* $catIds = explode(',', $Ids); */
	//	$catIds=$Ids;
   //     $catProIds = $this->getProductIdsByCategories($Ids);
		$collection = $this->_productCollectionFactory->create();
		$attributes = $this->_objectManager->get('Magento\Catalog\Model\Config')->getProductAttributes();
		$collection->addAttributeToSelect($attributes)
					 ->addStoreFilter();
		//if (count($catProIds)) $collection->addIdFilter($catProIds);
	//	$collection->addAttributeToFilter('status', ['in' => $this->_statusProduct->getVisibleStatusIds()]);
		$collection->setVisibility($this->_catalogProductVisibility->getVisibleInCatalogIds());
        $collection->getSelect()->order('RAND()');
        $collection->setPageSize($cf['qty'])->setCurPage(1);
		//var_dump($collection->getData());die();
        return $collection;
    }
     public function getDealsProduct($cf)
    {
        $todayStartOfDayDate = $this->_localeDate->date()->setTime(0, 0, 0)->format('Y-m-d H:i:s');
        $todayEndOfDayDate = $this->_localeDate->date()->setTime(23, 59, 59)->format('Y-m-d H:i:s');
		$storeId = $this->_storeManager->getStore(true)->getId();
		$attributes = $this->_objectManager->get('Magento\Catalog\Model\Config')->getProductAttributes();
        $collection = $this->_productCollectionFactory->create()->setStoreId($storeId);
        $collection->setVisibility($this->_catalogProductVisibility->getVisibleInCatalogIds())
        ->addAttributeToSelect($attributes)
        ->addStoreFilter()->addAttributeToFilter(
            'special_from_date',
            [
            'or' => [
            0 => ['date' => true, 'to' => $todayEndOfDayDate],
            1 => ['is' => new \Zend_Db_Expr('null')],
            ]
            ],
            'left'
            )->addAttributeToFilter(
            'special_to_date',
            [
            'or' => [
            0 => ['date' => true, 'from' => $todayStartOfDayDate],
            1 => ['is' => new \Zend_Db_Expr('not null')],
            ]
            ],
            'left'
            )->addAttributeToFilter(
            [
            ['attribute' => 'special_from_date', 'is' => new \Zend_Db_Expr('not null')],
            ['attribute' => 'special_to_date', 'is' => new \Zend_Db_Expr('not null')],
            ]
            )->addAttributeToSort(
            'special_from_date',
            'desc'
            );
            $collection->setPageSize($cf['qty'])->setCurPage(1);
            return $collection;
    }
	 public function getLastProduct($cf)
    {
        $storeId = $this->_storeManager->getStore(true)->getId();
        $collection = $this->_productCollectionFactory->create()->setStoreId($storeId);
		$attributes = $this->_objectManager->get('Magento\Catalog\Model\Config')->getProductAttributes();
        $collection->setVisibility($this->_catalogProductVisibility->getVisibleInCatalogIds())
        ->addAttributeToSelect($attributes)
        ->addStoreFilter()
        ->getSelect()->group("e.entity_id");
		$collection->setPageSize($cf['qty'])->setCurPage(1);
        return $collection;
    }
	 public function getBestsellerProducts($cf)
    {
		$storeId = $this->_storeManager->getStore(true)->getId();
	//	$_category =  $this->_categoryFactory->create()->load($id);
		$attributes = $this->_objectManager->get('Magento\Catalog\Model\Config')->getProductAttributes();
		$collection = $this->_productCollectionFactory->create()
						// ->addAttributeToSelect($attributes)
						// ->addCategoryFilter($_category)
						 ->setStoreId($storeId);
		$select = $this->_connection->select()
								->from($this->_resource->getTableName('sales_order_item'), 'product_id')
								->order('sum(`qty_ordered`) Desc')
								->group('product_id')
								->limit(100);
		$producIds = array(); 
		foreach ($this->_connection->query($select)->fetchAll() as $row) {
		   $producIds[] = $row['product_id'];
		}
		$collection
			->addAttributeToSelect($attributes)
			->addAttributeToFilter('entity_id', array('in'=>$producIds))
            ->addMinimalPrice()
            ->addFinalPrice()
            ->addTaxPercents()
            ->addUrlRewrite()
            ->setVisibility($this->_catalogProductVisibility->getVisibleInCatalogIds());
		$collection->setPageSize($cf['qty'])->setCurPage(1);
		return $collection;
		// $storeId = $this->_storeManager->getStore(true)->getId();
		// $attributes = $this->_objectManager->get('Magento\Catalog\Model\Config')->getProductAttributes();
		// $collection = $this->_productCollectionFactory->create()->setStoreId($storeId);
		// $collection->addAttributeToSelect('*')->addStoreFilter();
        // $collection->setVisibility($this->_catalogProductVisibility->getVisibleInCatalogIds())
        // ->addAttributeToSelect('*')
        // ->addStoreFilter()
        // ->joinField(
            // 'qty_ordered',
            // $this->_resource->getTableName('sales_bestsellers_aggregated_monthly'),
            // 'qty_ordered',
            // 'product_id=entity_id',
            // 'at_qty_ordered.store_id=' . (int)$storeId,
            // 'at_qty_ordered.qty_ordered > 0',
            // 'left'
            // );
		// //$collection->addAttributeToFilter('status', ['in' => $this->_statusProduct->getVisibleStatusIds()]);
        // $collection->setPageSize($cf['qty'])->setCurPage(1);
      // //  $collection->load();
        // return $collection;
    }
	 public function getTopRatedProduct($cf)
    {
        $storeId = $this->_storeManager->getStore(true)->getId();
		$attributes = $this->_objectManager->get('Magento\Catalog\Model\Config')->getProductAttributes();
        $collection = $this->_productCollectionFactory->create()->setStoreId($storeId);
        $collection->setVisibility($this->_catalogProductVisibility->getVisibleInCatalogIds())
        ->addAttributeToSelect($attributes)
        ->addStoreFilter();
		$rating_summary = $this->_resource->getTableName('review_entity_summary');
        $collection
			->getSelect()
            ->join(array('rat' => $rating_summary), "e.entity_id = rat.entity_pk_value AND rat.store_id=" .$storeId, array())
         //   ->where('rat.status = ?', 'complete')
            ->group('e.entity_id')
            ->order('rating_summary DESC');
		$collection->setPageSize($cf['qty'])->setCurPage(1);
        return $collection;
    }
	 public function getMostPopualProduct($cf)
    {
        $collection = $this->_reportCollection->create()->addAttributeToSelect('*')->addViewsCount();
		$attributes = $this->_objectManager->get('Magento\Catalog\Model\Config')->getProductAttributes();
        $collection->setVisibility($this->_catalogProductVisibility->getVisibleInCatalogIds())
        ->addAttributeToSelect($attributes)
        ->addStoreFilter()
        ->getSelect()->group("e.entity_id");
		$collection->setPageSize($cf['qty'])->setCurPage(1);
        return $collection;
    }
}
 //ducdevphp@gmail.com
?>