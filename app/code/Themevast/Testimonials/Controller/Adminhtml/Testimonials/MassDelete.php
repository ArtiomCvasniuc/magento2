<?php
namespace Themevast\Testimonials\Controller\Adminhtml\Testimonials;

use Magento\Framework\Controller\ResultFactory;
use Magento\Backend\App\Action\Context;
use Magento\Ui\Component\MassAction\Filter;
use Themevast\Testimonials\Model\ResourceModel\Testimonials\CollectionFactory;

class MassDelete extends \Magento\Backend\App\Action
{
  
    protected $filter;

    
    protected $collectionFactory;

    public function __construct(
    Context $context, 
    Filter $filter, 
    CollectionFactory 
    $collectionFactory)
    {
        $this->filter = $filter;
        $this->collectionFactory = $collectionFactory;
        parent::__construct($context);
    }

	
  /*   protected function _isAllowed()
    {
        return $this->_authorization->isAllowed('Mageducdq_Producttab::tab_delete');
    } */

    public function execute()
    {
        $collection = $this->filter->getCollection($this->collectionFactory->create());
        $collectionSize = $collection->getSize();

        foreach ($collection as $tab) {
            $tab->delete();
        }

        $this->messageManager->addSuccess(__('A total of %1 record(s) have been deleted.', $collectionSize));

        $resultRedirect = $this->resultFactory->create(ResultFactory::TYPE_REDIRECT);
        return $resultRedirect->setPath('*/*/');
    }
}
