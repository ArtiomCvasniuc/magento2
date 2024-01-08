<?php
namespace Themevast\Blog\Controller\Adminhtml\Import;

class Index extends \Magento\Backend\App\Action
{
	
    public function execute()
    {
        $this->_view->loadLayout();
        $this->_setActiveMenu('Themevast_Blog::import');
        $title = __('Blog Import');
        $this->_view->getPage()->getConfig()->getTitle()->prepend($title);
        $this->_addBreadcrumb($title, $title);
        $this->_view->renderLayout();
    }
}
