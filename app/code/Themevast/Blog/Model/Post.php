<?php
namespace Themevast\Blog\Model;
class Post extends \Magento\Framework\Model\AbstractModel
{
    
    const STATUS_ENABLED = 1;
    const STATUS_DISABLED = 0;

    protected $_eventPrefix = 'themevast_blog_post';

    
    protected $_eventObject = 'blog_post';

    protected $_url;

   
    protected $_categoryCollectionFactory;

    
    protected $_productCollectionFactory;

   
    protected $_parentCategories;
	
	 protected $_authorFactory;

    public function __construct(
        \Magento\Framework\Model\Context $context,
        \Magento\Framework\Registry $registry,
        \Magento\Framework\UrlInterface $url,
		\Themevast\Blog\Model\AuthorFactory $authorFactory,
        \Themevast\Blog\Model\ResourceModel\Category\CollectionFactory $categoryCollectionFactory,
        \Magento\Catalog\Model\ResourceModel\Product\CollectionFactory $productCollectionFactory,
        \Magento\Framework\Model\ResourceModel\AbstractResource $resource = null,
        \Magento\Framework\Data\Collection\AbstractDb $resourceCollection = null,
        array $data = []
    ) {
        $this->_url = $url;
		$this->_authorFactory = $authorFactory;
        $this->_categoryCollectionFactory = $categoryCollectionFactory;
        $this->_productCollectionFactory = $productCollectionFactory;

        parent::__construct($context, $registry, $resource, $resourceCollection, $data);
    }

    protected function _construct()
    {
        $this->_init('Themevast\Blog\Model\ResourceModel\Post');
    }

    public function getOwnTitle($plural = false)
    {
        return $plural ? 'Post' : 'Posts';
    }

    
    public function isActive()
    {
        return ($this->getStatus() == self::STATUS_ENABLED);
    }

    
    public function getAvailableStatuses()
    {
        return [self::STATUS_DISABLED => __('Disabled'), self::STATUS_ENABLED => __('Enabled')];
    }

    
    public function checkIdentifier($identifier, $storeId)
    {
        return $this->_getResource()->checkIdentifier($identifier, $storeId);
    }

    
    public function getUrl()
    {
        return 'blog/post/'.$this->getIdentifier();
    }

    
    public function getPostUrl()
    {
        return $this->_url->getUrl($this->getUrl());
    }

    
    public function getParentCategories()
    {
        if (is_null($this->_parentCategories)) {
            $this->_parentCategories = $this->_categoryCollectionFactory->create()
                ->addFieldToFilter('category_id', array('in' => $this->getCategories()))
                ->addStoreFilter($this->getStoreId())
                ->addActiveFilter();
        }

        return $this->_parentCategories;
    }

   
    public function getCategoriesCount()
    {
        return count($this->getParentCategories());
    }

    
    public function getRelatedPosts()
    {
        return $this->getCollection()
            ->addFieldToFilter('post_id', array('in' => $this->getRelatedPostIds() ?: array(0)))
            ->addStoreFilter($this->getStoreId());
    }

    public function getRelatedProducts()
    {
        $collection = $this->_productCollectionFactory->create()
            ->addFieldToFilter('entity_id', array('in' => $this->getRelatedProductIds() ?: array(0)));

        if ($storeIds = $this->getStoreId()) {
            $collection->addStoreFilter($storeIds[0]);
        }

        return $collection;
    }
	
	public function getAuthor()
    {
        if (!$this->hasData('author')) {
            $author = false;
            if ($authorId = $this->getData('author_id')) {
                $_author = $this->_authorFactory->create();
                $_author->load($authorId);
                if ($_author->getId()) {
                    $author = $_author;
                }
            }
            $this->setData('author', $author);
        }
        return $this->getData('author');
    }
}
