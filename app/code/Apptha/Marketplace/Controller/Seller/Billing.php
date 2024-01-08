<?php
namespace Apptha\Marketplace\Controller\Seller;

use Magento\Backend\App\Action\Context;
use Magento\Framework\View\Result\PageFactory;

class Billing extends \Magento\Framework\App\Action\Action {

	protected $_resultPageFactory;

    public function __construct(Context $context, PageFactory $resultPageFactory) {
	$this->_resultPageFactory = $resultPageFactory;
	parent::__construct($context);	
    }
    
    public function execute() {
//        $this->_view->loadLayou ();
//        $this->_view->renderLayout();

	$resultPage = $this->_resultPageFactory->create();
        $resultPage->getConfig()->getTitle()->prepend(__(' heading '));

        $block = $resultPage->getLayout()
                ->createBlock('Apptha\Marketplace\Block\Seller\Billing')
                ->setTemplate('billing.phtml')
                ->toHtml();
        $this->getResponse()->setBody($block);

    }
}
