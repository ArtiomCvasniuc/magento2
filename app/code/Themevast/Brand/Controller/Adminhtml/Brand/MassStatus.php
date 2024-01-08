<?php
namespace Themevast\Brand\Controller\Adminhtml\Brand;

class MassStatus extends \Themevast\Brand\Controller\Adminhtml\AbstractAction
{
    public function execute()
    {
        $brandIds = $this->getRequest()->getParam('id');
        $status = $this->getRequest()->getParam('status');
        $storeViewId = $this->getRequest()->getParam('store');
        if (!is_array($brandIds) || empty($brandIds)) {
            $this->messageManager->addError(__('Please select brand(s).'));
        } else {
            try {
                foreach ($brandIds as $brandId) {
                    $brand = $this->_objectManager->create('Themevast\Brand\Model\Brand')->load($brandId);
                    $brand->setStatus($status)
                           ->setIsMassupdate(true)
                           ->save();
                }
                $this->messageManager->addSuccess(
                    __('A total of %1 record(s) have been changed status.', count($brandIds))
                );
            } catch (\Exception $e) {
                $this->messageManager->addError($e->getMessage());
            }
        }
        $this->_redirect('*/*/', ['store' => $this->getRequest()->getParam('store')]);
    }
}
