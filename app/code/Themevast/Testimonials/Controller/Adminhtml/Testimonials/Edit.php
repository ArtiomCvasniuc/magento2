<?php
namespace Themevast\Testimonials\Controller\Adminhtml\Testimonials;
class Edit extends \Themevast\Testimonials\Controller\Adminhtml\AbstractAction
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
		$resultPage->setActiveMenu('Themevast_Testimonials::testimonials_manager')
			->addBreadcrumb(__('Manager Testimonials'), __('Manager Testimonials'))
			->addBreadcrumb(__('Manager Testimonials'), __('Manager Testimonials'));
		return $resultPage;
	}

	public function execute()
	{
		
		$id = $this->getRequest()->getParam('testimonial_id');
		$model = $this->_objectManager->create('Themevast\Testimonials\Model\Testimonials');
		if ($id) {
			$model->load($id);
			if (!$model->getId()) {
				$this->messageManager->addError(__('This testimonial no longer exists ! .'));
				$resultRedirect = $this->resultRedirectFactory->create();
				return $resultRedirect->setPath('*/*/');
			}
		}
			$data = $this->_objectManager->get('Magento\Backend\Model\Session')->getFormData(true);
			//$data = $this->_getSession()->getFormData(true);
			if (!empty($data)) {
				$model->setData($data);
			}
			$this->_coreRegistry->register('testimonials_data', $model);
			$resultPage = $this->_initAction();
			$resultPage->addBreadcrumb(
				$id ? __('Edit Testimonials') : __('New Testimonials'),
				$id ? __('Edit Testimonials') : __('New Testimonials')
			);
			$resultPage->addContent(
				$this->_view->getLayout()->createBlock('\Themevast\Testimonials\Block\Adminhtml\Testimonials\Edit')
			);
			$resultPage->addLeft(
				$this->_view->getLayout()->createBlock('\Themevast\Testimonials\Block\Adminhtml\Testimonials\Edit\Tabs')
			);

			$resultPage->getConfig()->getTitle()->prepend(__('Testimonial'));
			$resultPage->getConfig()->getTitle()
				->prepend($model->getId() ? $model->getName() : __('New Testimonial'));
			return $resultPage;
	}
}