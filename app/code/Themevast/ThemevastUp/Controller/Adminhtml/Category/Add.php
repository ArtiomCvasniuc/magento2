<?php
namespace Themevast\ThemevastUp\Controller\Adminhtml\Category;

class Add extends \Magento\Catalog\Controller\Adminhtml\Category\Add
{
    /**
     * Add new category form
     *
     * @return \Magento\Backend\Model\View\Result\Forward
     */
    public function execute()
    {
        $parentId = (int)$this->getRequest()->getParam('parent');

        $category = $this->_initCategory(true);
        if (!$category || !$parentId || $category->getId()) {
            /** @var \Magento\Backend\Model\View\Result\Redirect $resultRedirect */
            $resultRedirect = $this->resultRedirectFactory->create();
            return $resultRedirect->setPath('catalog/*/', ['_current' => true, 'id' => null]);
        }

        /**
         * Check if there are data in session (if there was an exception on saving category)
         */
        $categoryData = $this->_getSession()->getCategoryData(true);
        if (is_array($categoryData)) {
            unset($categoryData['image']);
            unset($categoryData['tv_menu_icon_img']);
            unset($categoryData['vc_menu_icon_img']);
            $category->addData($categoryData);
        }

        $resultPageFactory = $this->_objectManager->get('Magento\Framework\View\Result\PageFactory');
        /** @var \Magento\Backend\Model\View\Result\Page $resultPage */
        $resultPage = $resultPageFactory->create();

        if ($this->getRequest()->getQuery('isAjax')) {
            return $this->ajaxRequestResponse($category, $resultPage);
        }

        $resultPage->setActiveMenu('Magento_Catalog::catalog_categories');
        $resultPage->getConfig()->getTitle()->prepend(__('New Category'));
        $resultPage->addBreadcrumb(__('Manage Catalog Categories'), __('Manage Categories'));

        $block = $resultPage->getLayout()->getBlock('catalog.wysiwyg.js');
        if ($block) {
            $block->setStoreId(0);
        }

        return $resultPage;
    }
}
