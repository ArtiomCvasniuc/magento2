<?php

namespace Themevast\Brand\Controller\Adminhtml\Brand;

class NewAction extends \Themevast\Brand\Controller\Adminhtml\AbstractAction
{
    protected $resultForwardFactory;
	
    public function __construct(
        \Magento\Backend\App\Action\Context $context,
        \Magento\Framework\Registry $coreRegistry,
        \Magento\Backend\Model\View\Result\ForwardFactory $resultForwardFactory
    ) {
        $this->resultForwardFactory = $resultForwardFactory;
        parent::__construct($context, $coreRegistry);
    }

    public function execute()
    {
        $resultForward = $this->resultForwardFactory->create();
        return $resultForward->forward('edit');
    }
}
