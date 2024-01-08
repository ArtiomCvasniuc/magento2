<?php
 
namespace Themevast\SlideBanner\Controller\Adminhtml\Slider;
 
 
class Slidegrid extends \Magento\Backend\App\Action
{
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
	
	public function __construct(
        \Magento\Backend\App\Action\Context $context,
        \Magento\Framework\Registry $coreRegistry,
        \Magento\Framework\View\Result\PageFactory $resultPageFactory
    ) {
        $this->_coreRegistry = $coreRegistry;
        $this->_resultPageFactory = $resultPageFactory;
       parent::__construct($context);
    }
   public function execute()
   {
      return $this->_resultPageFactory->create();
   }
}
 