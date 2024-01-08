<?php
namespace Themevast\Blog\Block\Post;

use Magento\Store\Model\ScopeInterface;

class View extends AbstractPost
{
    protected function _prepareLayout()
    {
        $post = $this->getPost();
        $this->_addBreadcrumbs($post);
        $this->pageConfig->addBodyClass('blog-post-' . $post->getIdentifier());
        $this->pageConfig->getTitle()->set($post->getTitle());
        $this->pageConfig->setKeywords($post->getMetaKeywords());
        $this->pageConfig->setDescription($post->getMetaDescription());

        return parent::_prepareLayout();
    }

    
    protected function _addBreadcrumbs(\Themevast\Blog\Model\Post $post)
    {
        if ($this->_scopeConfig->getValue('web/default/show_cms_breadcrumbs', ScopeInterface::SCOPE_STORE)
            && ($breadcrumbsBlock = $this->getLayout()->getBlock('breadcrumbs'))
        ) {
            $breadcrumbsBlock->addCrumb(
                'home',
                [
                    'label' => __('Home'),
                    'title' => __('Go to Home Page'),
                    'link' => $this->_storeManager->getStore()->getBaseUrl()
                ]
            );
            $breadcrumbsBlock->addCrumb(
                'blog',
                [
                    'label' => __('Blog'),
                    'title' => __('Go to Blog Home Page'),
                    'link' => $this->_storeManager->getStore()->getUrl('blog')
                ]
            );
            $breadcrumbsBlock->addCrumb('blog_post', [
                'label' => $post->getTitle(),
                'title' => $post->getTitle()
            ]);
        }
    }
	
	public function getMediaFolder() {
		$media_folder = $this->_storeManager->getStore()->getBaseUrl(\Magento\Framework\UrlInterface::URL_TYPE_MEDIA);
		return $media_folder;
	}
}
