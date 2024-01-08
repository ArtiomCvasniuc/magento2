<?php
 
namespace Themevast\SlideBanner\Controller\Adminhtml\Slide;

class Edit extends \Magento\Backend\App\Action
{
	protected $_objectId = 'slide_id';
    /**
     * Core registry
     *
     * @var \Magento\Framework\Registry
     */
    protected $_coreRegistry;
 
    /**
     * Result page factory
     *
     * @var \Magento\Framework\View\Result\PageFactory
     */
    protected $_resultPageFactory;
 
    /**
     * News model factory
     *
     * @var \Tutorial\SimpleNews\Model\NewsFactory
     */
    protected $_slideFactory;
 
    /**
     * @param Context $context
     * @param Registry $coreRegistry
     * @param PageFactory $resultPageFactory
     * @param NewsFactory $newsFactory
     */
    public function __construct(
        \Magento\Backend\App\Action\Context $context,
        \Magento\Framework\Registry $coreRegistry,
        \Magento\Framework\View\Result\PageFactory $resultPageFactory
    ) {
        $this->_coreRegistry = $coreRegistry;
        $this->_resultPageFactory = $resultPageFactory;
       parent::__construct($context);
    }
 
    /**
     * News access rights checking
     *
     * @return bool
     */
    protected function _isAllowed()
    {
        return $this->_authorization->isAllowed('Themevast_SlideBanner::manage_slide');
    }
	protected function _initAction()
    {
        
        $resultPage = $this->_resultPageFactory->create();
        // $resultPage->setActiveMenu('Webkul_Grid::grid')
            // ->addBreadcrumb(__('Grid'), __('Grid'))
            // ->addBreadcrumb(__('Manage Grid'), __('Manage Grid'));
        return $resultPage;
    }
    /**
     * @return void
     */
	public function execute()
    {
         
        // 1. Get ID and create model
        $id = $this->getRequest()->getParam('slide_id');
        $model = $this->_objectManager->create('Themevast\SlideBanner\Model\Slide');
 
        // 2. Initial checking
        if ($id) {
            $model->load($id);
            if (!$model->getId()) {
                $this->messageManager->addError(__('This grid record no longer exists.'));
                /** \Magento\Backend\Model\View\Result\Redirect $resultRedirect */
                $resultRedirect = $this->resultRedirectFactory->create();
 
                return $resultRedirect->setPath('*/*/');
            }
        }
 
        // 3. Set entered data if was error when we do save
        $data = $this->_objectManager->get('Magento\Backend\Model\Session')->getFormData(true);
        if (!empty($data)) {
            $model->setData($data);
        }
 
        // 4. Register model to use later in blocks
        $this->_coreRegistry->register('slide_form_data', $model);
 
        // 5. Build edit form
        /** @var \Magento\Backend\Model\View\Result\Page $resultPage */
        $resultPage = $this->_initAction();
        $resultPage->addBreadcrumb(
            $id ? __('Edit Post') : __('New Grid'),
            $id ? __('Edit Post') : __('New Grid')
        );
        $resultPage->getConfig()->getTitle()->prepend(__('Grids'));
        $resultPage->getConfig()->getTitle()
            ->prepend($model->getId() ? $model->getTitle() : __('New Grid'));
 
        return $resultPage;
    }
}