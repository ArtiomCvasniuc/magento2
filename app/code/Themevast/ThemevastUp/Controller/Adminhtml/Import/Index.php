<?php
namespace Themevast\ThemevastUp\Controller\Adminhtml\Import;

class Index extends \Magento\Backend\App\Action
{
    
    protected $_coreRegistry = null;

    protected $resultPageFactory;

    public function __construct(
        \Magento\Backend\App\Action\Context $context,
        \Magento\Framework\View\Result\PageFactory $resultPageFactory,
        \Magento\Framework\Registry $registry
    ) {
        $this->resultPageFactory = $resultPageFactory;
        $this->_coreRegistry = $registry;
        parent::__construct($context);
    }
	
    public function execute()
    {
        $resultPage = $this->resultPageFactory->create();
        $resultPage->setActiveMenu('Themevast_ThemevastUp::themebackup_import')
            ->addBreadcrumb(__('Themevast Import'), __('Themevast Import'))
            ->addBreadcrumb(__('Import Data'), __('Import Data'));
        return $resultPage;
    }
}
