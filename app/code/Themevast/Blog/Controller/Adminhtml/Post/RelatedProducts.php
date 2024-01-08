<?php
namespace Themevast\Blog\Controller\Adminhtml\Post;

class RelatedProducts extends \Themevast\Blog\Controller\Adminhtml\Post
{
	public function execute()
    {
        $model = $this->_getModel();
        $this->_getRegistry()->register('current_model', $model);

        $this->_view->loadLayout()
            ->getLayout()
            ->getBlock('blog.post.edit.tab.relatedproducts')
            ->setProductsRelated($this->getRequest()->getPost('products_related', null));
 
        $this->_view->renderLayout();
    }
}
