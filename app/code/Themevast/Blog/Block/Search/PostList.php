<?php
namespace Themevast\Blog\Block\Search;

use Magento\Store\Model\ScopeInterface;

class PostList extends \Themevast\Blog\Block\Post\PostList
{
	
    public function getQuery()
    {
        return urldecode($this->getRequest()->getParam('q'));
    }

    protected function _preparePostCollection()
    {
        parent::_preparePostCollection();

        $q = $this->getQuery();
        $this->_postCollection->addFieldToFilter(
            array('title', 'content_heading', 'content'),
            array(
                array('like' => '%'.$q.'%'),
                array('like' => '%'.$q.'%'),
                array('like' => '% '.$q.' %')
            )
        );
    }

    protected function _prepareLayout()
    {
        $title = $this->_getTitle();
        $this->_addBreadcrumbs($title);
        $this->pageConfig->getTitle()->set($title);

        return parent::_prepareLayout();
    }

    protected function _addBreadcrumbs($title)
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
            $breadcrumbsBlock->addCrumb('blog_search', ['label' => $title, 'title' => $title]);
        }
    }

    protected function _getTitle()
    {
        return __('Search "%1"', $this->getQuery());
    }

}
