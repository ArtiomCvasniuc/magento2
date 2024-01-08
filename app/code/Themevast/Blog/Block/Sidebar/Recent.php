<?php

namespace Themevast\Blog\Block\Sidebar;


class Recent extends \Themevast\Blog\Block\Post\PostList\AbstractList
{
    use Widget;

    
    protected $_widgetKey = 'recent_posts';

    
    public function _construct()
    {
        $this->setPageSize(
            (int) $this->_scopeConfig->getValue(
                'tvblog/sidebar/'.$this->_widgetKey.'/posts_per_page',
                \Magento\Store\Model\ScopeInterface::SCOPE_STORE
            )
        );
        return parent::_construct();
    }

    
	public function getIdentities()
    {
        return [\Magento\Cms\Model\Block::CACHE_TAG . '_blog_recent_posts_widget'  ];
    }
	
	public function getMediaFolder() {
		$media_folder = $this->_storeManager->getStore()->getBaseUrl(\Magento\Framework\UrlInterface::URL_TYPE_MEDIA);
		return $media_folder;
	}
	
	public function getConfig($config)
	{
		return $this->_scopeConfig->getValue('tvblog/general/'.$config, \Magento\Store\Model\ScopeInterface::SCOPE_STORE);
	}
	
	 public function getFacebookAppId()
    {
        return $this->_scopeConfig->getValue(
            'tvblog/post_view/comments/fb_app_id', \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        );
    }
	public function getCommentsType()
    {
        return $this->_scopeConfig->getValue(
            'tvblog/post_view/comments/type', \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        );
    }
	 public function getLocaleCode()
    {
        return $this->_localeResolver->getLocale();
    }
    
    public function getNumberOfComments()
    {
        return (int)$this->_scopeConfig->getValue(
            'tvblog/post_view/comments/number_of_comments', \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        );
    }
	 public function getDisqusShortname()
    {
        return $this->_scopeConfig->getValue(
            'tvblog/post_view/comments/disqus_forum_shortname', ScopeInterface::SCOPE_STORE
        );
    }
}
