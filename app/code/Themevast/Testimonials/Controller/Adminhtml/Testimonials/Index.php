<?php
namespace Themevast\Testimonials\Controller\Adminhtml\Testimonials;

class Index extends \Themevast\Testimonials\Controller\Adminhtml\AbstractAction
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

    public function execute()
    {
		 if ($this->getRequest()->getParam('ajax')) {
            $this->_forward('grid');
            return;
        }
        $resultPage = $this->resultPageFactory->create();
        return $resultPage;
    }
}
