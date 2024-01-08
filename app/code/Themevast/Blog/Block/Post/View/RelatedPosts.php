<?php
namespace Themevast\Blog\Block\Post\View;

use Magento\Catalog\Model\ResourceModel\Product\Collection;
use Magento\Framework\View\Element\AbstractBlock;

class RelatedPosts extends \Themevast\Blog\Block\Post\PostList\AbstractList
{
    public function _construct()
    {
        $this->setPageSize(5);
        return parent::_construct();
    }

    protected function _preparePostCollection()
    {
        parent::_preparePostCollection();
        $this->_postCollection
            ->addFieldToFilter('post_id', array('in' => $this->getPost()->getRelatedPostIds() ?: array(0)))
            ->addFieldToFilter('post_id', array('neq' => $this->getPost()->getId()))
            ->setPageSize(
                (int) $this->_scopeConfig->getValue(
                    'tvblog/post_view/related_posts/number_of_posts',
                    \Magento\Store\Model\ScopeInterface::SCOPE_STORE
                )
            );
    }

    public function displayPosts()
    {
        return (bool) $this->_scopeConfig->getValue(
            'tvblog/post_view/related_posts/enabled',
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        );
    }

    public function getPost()
    {
        if (!$this->hasData('post')) {
            $this->setData('post',
                $this->_coreRegistry->registry('current_blog_post')
            );
        }
        return $this->getData('post');
    }

    public function getIdentities()
    {
        return [\Magento\Cms\Model\Page::CACHE_TAG . '_relatedposts_'.$this->getPost()->getId()  ];
    }
}
