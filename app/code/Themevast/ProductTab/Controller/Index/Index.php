<?php
/* ducdevphp@gmail.com */
namespace Themevast\ProductTab\Controller\Index;

class Index extends \Magento\Framework\App\Action\Action
{
	protected $resultPageFactory;
	protected $_categoryFactory;
	protected $storeManager;
	protected $productCollectionFactory;
	protected $connection;
	protected $resource;
	protected $productVisibility;
	public function __construct(
		\Magento\Framework\App\Action\Context $context,
		\Magento\Framework\View\Result\PageFactory $resultPageFactory,
		\Magento\Catalog\Model\CategoryFactory $categoryFactory,
		\Magento\Catalog\Model\ResourceModel\Product\CollectionFactory $productCollectionFactory,
		\Magento\Framework\App\ResourceConnection $resourceConnection,
		\Magento\Catalog\Model\Product\Visibility $productVisibility,
		\Magento\Store\Model\StoreManagerInterface $storeManager
		)
	{
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
		$_cfg =  $params['protabconfig'];
		$is_ajax_protab =$params['is_ajax_protab'];
		$config = (array)json_decode(base64_decode(strtr($_cfg, '-_', '+/')));
		if (!$this->getRequest()->isAjax() || !$is_ajax_protab) {
			return;
		}
		$id = $params['protabId'];
		$collection=$this->_objectManager->create('Themevast\ProductTab\Model\Product')->getProductByTab($id,$config);
		$data = $config;
        $data['productCollection'] = $collection;
		$res = [];
		$res['protab_ajax_data'] = $this->_view->getLayout()->createBlock('Themevast\ProductTab\Block\Ajax')->setData($data)->toHtml();
		$this->getResponse()->representJson(
			$this->_objectManager->get('Magento\Framework\Json\Helper\Data')->jsonEncode($res)
			);
	}
}
/* ducdepzai :)*/