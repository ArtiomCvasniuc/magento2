<?php
namespace Themevast\Blog\Controller\Category;

class View extends \Magento\Framework\App\Action\Action
{
   
    public function execute()
    {
        $id = $this->getRequest()->getParam('id');
        $category = $this->_objectManager->create('Themevast\Blog\Model\Category')->load($id);
        if (!$category->getId()) {
            $this->_forward('index', 'noroute', 'cms');
            return;
        }

        $this->_objectManager->get('\Magento\Framework\Registry')->register('current_blog_category', $category);

        $this->_view->loadLayout();
        $this->_view->renderLayout();
    }
}
