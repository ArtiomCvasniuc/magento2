<?php
namespace Themevast\Blog\Block\Category;

use Magento\Store\Model\ScopeInterface;

class View extends \Themevast\Blog\Block\Post\PostList
{
   
    protected function _preparePostCollection()
    {
        parent::_preparePostCollection();
        if ($category = $this->getCategory()) {
            $this->_postCollection->addCategoryFilter($category);
        }
    }

   
    public function getCategory()
    {
        return $this->_coreRegistry->registry('current_blog_category');
    }

    protected function _prepareLayout()
    {
        $category = $this->getCategory();
        $this->_addBreadcrumbs($category);
        $this->pageConfig->addBodyClass('blog-category-' . $category->getIdentifier());
        $this->pageConfig->getTitle()->set($category->getTitle());
        $this->pageConfig->setKeywords($category->getMetaKeywords());
        $this->pageConfig->setDescription($category->getMetaDescription());

        return parent::_prepareLayout();
    }

    protected function _addBreadcrumbs($category)
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
            $breadcrumbsBlock->addCrumb('blog_category',[
                'label' => $category->getTitle(),
                'title' => $category->getTitle()
            ]);
        }
    }
}
