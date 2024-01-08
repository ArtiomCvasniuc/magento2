<?php
namespace Themevast\Testimonials\Controller\Index;

use Themevast\Testimonials\Helper\Data;
use Themevast\Testimonials\Model\TestimonialsFactory;
use Magento\Framework\App\Action\Action;
use Magento\Framework\App\Action\Context;
use Magento\Framework\App\ResponseInterface;
use Magento\Framework\View\Result\PageFactory;
use Magento\Framework\App\RequestInterface;

class NewAction extends Action
{
    protected $_pageFactory;

    protected $_dataHelper;
	
    protected $_testimonialsFactory;

    public function __construct(
        Context $context,
        PageFactory $pageFactory,
        Data $dataHelper,
        TestimonialsFactory $testimonialsFactory
    )
    {
        parent::__construct($context);
        $this->_pageFactory = $pageFactory;
        $this->_dataHelper = $dataHelper;
        $this->_testimonialsFactory = $testimonialsFactory;
    }

    public function execute()
    {
        $resultPage = $this->_pageFactory->create();
        $resultPage->getConfig()->getTitle()->set('Submit Your Testimonial');
        return $resultPage;
    }

}