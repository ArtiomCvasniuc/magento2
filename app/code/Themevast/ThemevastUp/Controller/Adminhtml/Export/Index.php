<?php
namespace Themevast\ThemevastUp\Controller\Adminhtml\Export;

class Index extends \Magento\Backend\App\Action
{

  
    protected $resultPageFactory;

    public function __construct(
        \Magento\Backend\App\Action\Context $context,
        \Magento\Framework\View\Result\PageFactory $resultPageFactory
    ) {
        $this->resultPageFactory = $resultPageFactory;
        parent::__construct($context);
    }

  
    public function execute()
    {
        $resultPage = $this->resultPageFactory->create();
        $resultPage->setActiveMenu('Themevast_ThemevastUp::themebackup_export')
            ->addBreadcrumb(__('Themevast Export'), __('Themevast Export'))
            ->addBreadcrumb(__('Export Data'), __('Export Data'));
        return $resultPage;
    }
}
