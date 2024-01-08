<?php
namespace Themevast\Blog\Block\Post\PostList;
use Magento\Store\Model\ScopeInterface;

class Item extends \Themevast\Blog\Block\Post\AbstractPost
{
	public function getMediaFolder() {
		$media_folder = $this->_storeManager->getStore()->getBaseUrl(\Magento\Framework\UrlInterface::URL_TYPE_MEDIA);
		return $media_folder;
	}
	public function getCommentsType()
    {
        return $this->_scopeConfig->getValue(
            'tvblog/post_view/comments/type', ScopeInterface::SCOPE_STORE
        );
    }
}
