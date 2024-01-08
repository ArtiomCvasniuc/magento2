<?php
namespace Themevast\Blog\Model;

class Category extends \Magento\Framework\Model\AbstractModel
{
    
    const STATUS_ENABLED = 1;
    const STATUS_DISABLED = 0;

    protected $_eventPrefix = 'themevast_blog_category';

    protected $_eventObject = 'blog_category';

    
    protected $_url;

    
    public function __construct(
        \Magento\Framework\Model\Context $context,
        \Magento\Framework\Registry $registry,
        \Magento\Framework\UrlInterface $url,
        \Magento\Framework\Model\ResourceModel\AbstractResource $resource = null,
        \Magento\Framework\Data\Collection\AbstractDb $resourceCollection = null,
        array $data = []
    ) {
        $this->_url = $url;
        parent::__construct($context, $registry, $resource, $resourceCollection, $data);
    }

    
    protected function _construct()
    {
        $this->_init('Themevast\Blog\Model\ResourceModel\Category');
    }

    
    public function getOwnTitle($plural = false)
    {
        return $plural ? 'Category' : 'Categories';
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

    
    public function getParentIds()
    {
        $k = 'parent_ids';
        if (!$this->hasData($k)) {
            $this->setData($k,
                $this->getPath() ? explode('/', $this->getPath()) : array()
            );
        }

        return $this->getData($k);
    }

    
    public function getParentId()
    {
        if ($pIds = $this->getParentIds()) {
            return $pIds[count($pIds) - 1];
        }
        return 0;
    }

  
    public function isParent($category)
    {
        if (is_object($category)) {
            $category = $category->getId();
        }

        return in_array($category, $this->getParentIds());
    }

    public function isChild($category)
    {
        return $category->isParent($this);
    }

    public function getLevel()
    {
        return count($this->getParentIds());
    }

    public function getUrl()
    {
        return 'blog/category/'.$this->getIdentifier();
    }

    public function getCategoryUrl()
    {
        return $this->_url->getUrl($this->getUrl());
    }
}
