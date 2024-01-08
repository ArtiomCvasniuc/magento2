<?php
/**
//dev by ducdq 
//ducdevphp@gmail
*/
namespace Themevast\HotDeals\Model;
 
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
	
	 public function getAllDealsProduct()
    {
        $todayStartOfDayDate = $this->_localeDate->date()->setTime(0, 0, 0)->format('Y-m-d H:i:s');
        $todayEndOfDayDate = $this->_localeDate->date()->setTime(23, 59, 59)->format('Y-m-d H:i:s');
		$storeId = $this->_storeManager->getStore(true)->getId();
		$attributes = $this->_objectManager->get('Magento\Catalog\Model\Config')->getProductAttributes();
        $collection = $this->_productCollectionFactory->create()->setStoreId($storeId);
        $collection->setVisibility($this->_catalogProductVisibility->getVisibleInCatalogIds())
        ->addAttributeToSelect('*')
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
            )->addTaxPercents();
         //   $collection->setPageSize($cf['qty'])->setCurPage(1);
            return $collection;
    }
}
/**
* ducdevphp@gmail.com
*/
?>