<?php
namespace Themevast\Blog\Block\Post\PostList;

use Magento\Store\Model\ScopeInterface;

abstract class AbstractList extends \Magento\Framework\View\Element\Template
{
    
    protected $_filterProvider;

    
    protected $_post;

    
    protected $_coreRegistry;

    
    protected $_postCollectionFactory;

    
    protected $_postCollection;

    protected $_localeResolver;
    public function __construct(
        \Magento\Framework\View\Element\Template\Context $context,
        \Magento\Framework\Registry $coreRegistry,
        \Magento\Cms\Model\Template\FilterProvider $filterProvider,
        \Themevast\Blog\Model\ResourceModel\Post\CollectionFactory $postCollectionFactory,
		\Magento\Framework\Locale\ResolverInterface $localeResolver,
        array $data = []
    ) {
        parent::__construct($context, $data);
        $this->_coreRegistry = $coreRegistry;
        $this->_filterProvider = $filterProvider;
        $this->_postCollectionFactory = $postCollectionFactory;
		 $this->_localeResolver = $localeResolver;
    }

    
    protected function _preparePostCollection()
    {
        $this->_postCollection = $this->_postCollectionFactory->create()
            ->addActiveFilter()
            ->addStoreFilter($this->_storeManager->getStore()->getId())
            ->setOrder('publish_time', 'DESC');

        if ($this->getPageSize()) {
            $this->_postCollection->setPageSize($this->getPageSize());
        }
    }

    public function getPostCollection()
    {
        if (is_null($this->_postCollection)) {
            $this->_preparePostCollection();
        }

        return $this->_postCollection;
    }

}
