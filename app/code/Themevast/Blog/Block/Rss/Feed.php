<?php
namespace Themevast\Blog\Block\Rss;

use Magento\Store\Model\ScopeInterface;


class Feed extends \Themevast\Blog\Block\Post\PostList\AbstractList
{
    
    public function getLink()
    {
        return $this->getUrl('blog/rss/feed');
    }

    
    public function getTitle()
    {
    	 return $this->_scopeConfig->getValue('tvblog/rss_feed/title', ScopeInterface::SCOPE_STORE);
    }

   
    public function getDescription()
    {
    	 return $this->_scopeConfig->getValue('tvblog/rss_feed/description', ScopeInterface::SCOPE_STORE);
    }

    public function getIdentities()
    {
        return [\Magento\Cms\Model\Page::CACHE_TAG . '_blog_rss_feed'  ];
    }

}
