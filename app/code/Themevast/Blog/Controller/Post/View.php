<?php
namespace Themevast\Blog\Controller\Post;

class View extends \Magento\Framework\App\Action\Action
{
    public function execute()
    {
        $id = $this->getRequest()->getParam('id');
        $post = $this->_objectManager->create('Themevast\Blog\Model\Post')->load($id);
        if (!$post->getId()) {
            $this->_forward('index', 'noroute', 'cms');
            return;
        }

        $this->_objectManager->get('\Magento\Framework\Registry')->register('current_blog_post', $post);

        $this->_view->loadLayout();
        $this->_view->renderLayout();
    }

}
