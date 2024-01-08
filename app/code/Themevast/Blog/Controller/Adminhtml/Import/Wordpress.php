<?php
namespace Themevast\Blog\Controller\Adminhtml\Import;

class Wordpress extends \Magento\Backend\App\Action
{
    public function execute()
    {
        $this->_view->loadLayout();
        $this->_setActiveMenu('Themevast_Blog::import');
        $title = __('Blog Import from WordPress (beta)');
        $this->_view->getPage()->getConfig()->getTitle()->prepend($title);
        $this->_addBreadcrumb($title, $title);

        $config = new \Magento\Framework\DataObject(
            (array)$this->_getSession()->getData('import_wordpress_form_data', true) ?: array()
        );

        $this->_objectManager->get('\Magento\Framework\Registry')->register('import_config', $config);

        $this->_view->renderLayout();
    }
}
