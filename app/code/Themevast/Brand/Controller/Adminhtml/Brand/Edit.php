<?php
namespace Themevast\Brand\Controller\Adminhtml\Brand;
class Edit extends \Themevast\Brand\Controller\Adminhtml\AbstractAction
{
	 protected $resultPageFactory;
	 
	 public function __construct(
        \Magento\Backend\App\Action\Context $context,
        \Magento\Framework\Registry $coreRegistry,
        \Magento\Framework\View\Result\PageFactory $resultPageFactory
    ) {
        $this->resultPageFactory = $resultPageFactory;
        parent::__construct($context, $coreRegistry);
    }
	
	protected function _initAction()
	{
		$resultPage = $this->resultPageFactory->create();
		$resultPage->setActiveMenu('Themevast_Brand::brand_manager')
			->addBreadcrumb(__('Manager Brand'), __('Manager Brand'))
			->addBreadcrumb(__('Manager Brand'), __('Manager Brand'));
		return $resultPage;
	}

	public function execute()
	{
		
		$id = $this->getRequest()->getParam('id');
		$model = $this->_objectManager->create('Themevast\Brand\Model\Brand');
		if ($id) {
			$model->load($id);
			if (!$model->getId()) {
				$this->messageManager->addError(__('This brand no longer exists ! .'));
				$resultRedirect = $this->resultRedirectFactory->create();
				return $resultRedirect->setPath('*/*/');
			}
		}
			$data = $this->_objectManager->get('Magento\Backend\Model\Session')->getFormData(true);
			//$data = $this->_getSession()->getFormData(true);
			if (!empty($data)) {
				$model->setData($data);
			}
			$this->_coreRegistry->register('brand_data', $model);
			$resultPage = $this->_initAction();
			$resultPage->addBreadcrumb(
				$id ? __('Edit Brand') : __('New Brand'),
				$id ? __('Edit Brand') : __('New Brand')
			);
			$resultPage->addContent(
				$this->_view->getLayout()->createBlock('\Themevast\Brand\Block\Adminhtml\Brand\Edit')
			);
			$resultPage->addLeft(
				$this->_view->getLayout()->createBlock('\Themevast\Brand\Block\Adminhtml\Brand\Edit\Tabs')
			);

			$resultPage->getConfig()->getTitle()->prepend(__('Brand'));
			$resultPage->getConfig()->getTitle()
				->prepend($model->getId() ? $model->getTitle() : __('New Brand'));
			return $resultPage;
	}
}