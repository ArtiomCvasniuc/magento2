<?php
namespace Themevast\Blog\Block\Post;

use Magento\Store\Model\ScopeInterface;

class Info extends \Magento\Framework\View\Element\Template
{
	
    protected $_template = 'post/info.phtml';

    public function getPostedOn($format = 'Y-m-d H:i:s')
    {
        return date($format, strtotime($this->getPost()->getData('publish_time')));
    }
	
	 public function isShowAuthor()
    {
        return (int)$this->_scopeConfig->getValue(
            'tvblog/author/enabled', \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        );
    }
	 public function isShowCountComments()
    {
        return (int)$this->_scopeConfig->getValue(
            'tvblog/post_view/comments/enabled_count_comments', \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        );
    }

}
