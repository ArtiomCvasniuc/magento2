<?php

namespace Themevast\Blog\Block\Post\View;

use Magento\Store\Model\ScopeInterface;

class Comments extends \Magento\Framework\View\Element\Template
{
    
    protected $_localeResolver;

   
    protected $_coreRegistry;

   
    public function __construct(
        \Magento\Framework\View\Element\Template\Context $context,
        \Magento\Framework\Registry $coreRegistry,
        \Magento\Framework\Locale\ResolverInterface $localeResolver,
        array $data = []
    ) {
        parent::__construct($context, $data);
        $this->_coreRegistry = $coreRegistry;
        $this->_localeResolver = $localeResolver;
    }

    
    protected $_template = 'post/view/comments.phtml';

    
    public function getCommentsType()
    {
        return $this->_scopeConfig->getValue(
            'tvblog/post_view/comments/type', ScopeInterface::SCOPE_STORE
        );
    }

    
    public function getNumberOfComments()
    {
        return (int)$this->_scopeConfig->getValue(
            'tvblog/post_view/comments/number_of_comments', ScopeInterface::SCOPE_STORE
        );
    }

   
    public function getFacebookAppId()
    {
        return $this->_scopeConfig->getValue(
            'tvblog/post_view/comments/fb_app_id', ScopeInterface::SCOPE_STORE
        );
    }

    
    public function getDisqusShortname()
    {
        return $this->_scopeConfig->getValue(
            'tvblog/post_view/comments/disqus_forum_shortname', ScopeInterface::SCOPE_STORE
        );
    }

    public function getLocaleCode()
    {
        return $this->_localeResolver->getLocale();
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
}
