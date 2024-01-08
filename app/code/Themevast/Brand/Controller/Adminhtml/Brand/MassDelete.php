<?php
namespace Themevast\Brand\Controller\Adminhtml\Brand;

class MassDelete extends \Themevast\Brand\Controller\Adminhtml\AbstractAction
{
    public function execute()
    {
        $brandIds = $this->getRequest()->getParam('id');
        if (!is_array($brandIds) || empty($brandIds)) {
            $this->messageManager->addError(__('Please select brand(s).'));
        } else {
            try {
                foreach ($brandIds as $brandId) {
                    $brand =$this->_objectManager->create('Themevast\Brand\Model\Brand')->load($brandId);
                    $brand->delete();
                }
                $this->messageManager->addSuccess(
                    __('A total of %1 record(s) have been deleted.', count($brandIds))
                );
            } catch (\Exception $e) {
                $this->messageManager->addError($e->getMessage());
            }
        }
        $this->_redirect('*/*/');
    }
}
