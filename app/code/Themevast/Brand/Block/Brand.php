<?php
namespace Themevast\Brand\Block;

class Brand extends \Magento\Framework\View\Element\Template implements \Magento\Widget\Block\BlockInterface
{
    protected $_filterProvider;
    protected $_brandCollection;
    protected $storeManager;
	protected $_scopeConfig;
    public function __construct(
        \Magento\Framework\View\Element\Template\Context $context,
		\Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig,
		\Magento\Cms\Model\Template\FilterProvider $filterProvider,
        \Themevast\Brand\Model\ResourceModel\Brand\CollectionFactory $brandCollection,
        array $data = []
    ) {
        parent::__construct($context, $data);
        $this->_brandCollection = $brandCollection;
		$this->_scopeConfig = $scopeConfig;
		$this->storeManager = $context->getStoreManager();
		$this->_filterProvider = $filterProvider;
    }
    
	public function getConfig($value=''){

	   $config =  $this->_scopeConfig->getValue('brand_setting/general/'.$value, \Magento\Store\Model\ScopeInterface::SCOPE_STORE);
	   return $config; 
	 
	}
    public function getBrand()
    {
        $brand = $this->_brandCollection->create()
            ->addActiveFilter()
            ->addStoreFilter($this->_storeManager->getStore()->getId())
            ->setOrder('position', 'DESC');
		$brand->setPageSize($this->getConfig('qty'))->setCurPage(1);
		return $brand;
    }
	
	public function getTitle()
    {
        return $this->getData('title');
    }
	public function getIdentify()
    {
        return $this->getData('identify');
    }
	public function getMediaFolder() {
		$media_folder = $this->_storeManager->getStore()->getBaseUrl(\Magento\Framework\UrlInterface::URL_TYPE_MEDIA);
		return $media_folder;
	}
	public function getContentText($html)
	{
		$html = $this->_filterProvider->getPageFilter()->filter($html);
        return $html;
	}
}
