<?php
namespace Themevast\ThemevastUp\Helper;
use Magento\Framework\App\Filesystem\DirectoryList;
use Magento\Customer\Model\Session;

class Data extends \Magento\Framework\App\Helper\AbstractHelper
{
  
    protected $_filterProvider;

   
    protected $_storeManager;

    
    protected $_scopeConfig;

   
    protected $_coreRegistry;

   
    protected $_collectionThemeFactory;

    
    protected $_filesystem;

    protected $_request;

   
    protected $session;

   
    protected $_tvthemeDirectory;

    
    protected $_objectManager;

    public function __construct(
        \Magento\Framework\App\Helper\Context $context,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Magento\Framework\Registry $registry,
        \Magento\Cms\Model\Template\FilterProvider $filterProvider,
        \Magento\Theme\Model\ResourceModel\Theme\CollectionFactory $collectionThemeFactory,
        \Magento\Framework\Filesystem $filesystem,
        \Magento\Theme\Model\Theme $themeModel,
        \Magento\Framework\ObjectManagerInterface $objectManager,
        Session $customerSession
        ) {
        parent::__construct($context);
        $this->_storeManager = $storeManager;
        $this->_coreRegistry = $registry;
        $this->_filterProvider = $filterProvider;
        $this->_collectionThemeFactory = $collectionThemeFactory;
        $this->_filesystem = $filesystem;
        $this->_scopeConfig = $context->getScopeConfig();
        $this->_themeModel = $themeModel;
        $this->_request = $context->getRequest();
        $this->session = $customerSession;
        $this->_objectManager = $objectManager;
    }
	
    public function isHomePage()
    {
        $currentUrl = $this->getUrl('', ['_current' => true]);
        $urlRewrite = $this->getUrl('*/*/*', ['_current' => true, '_use_rewrite' => true]);
        return $currentUrl == $urlRewrite;
    }

    public function getMediaUrl(){
        $url = $this->_storeManager->getStore()->getBaseUrl(\Magento\Framework\UrlInterface::URL_TYPE_MEDIA);
        return $url;
    }

    public function getCoreRegistry(){
        return $this->_coreRegistry;
    }

    public function filter($str)
    {
        $html = $this->_filterProvider->getPageFilter()->filter($str);
        return $html;
    }

    public function getAllStores() {
        $allStores = $this->_storeManager->getStores();
        $stores = array();
        foreach ($allStores as $_eachStoreId => $val)
        {
            $stores[]  = $this->_storeManager->getStore($_eachStoreId)->getId();
        }
        return $stores;
    }
}