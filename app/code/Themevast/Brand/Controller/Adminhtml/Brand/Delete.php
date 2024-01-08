<?php
namespace Themevast\Brand\Controller\Adminhtml\Brand;

class Delete extends \Themevast\Brand\Controller\Adminhtml\AbstractAction
{
    public function execute()
    {
		$id = $this->getRequest()->getParam('id');
        if ($id) {
            $title = "";
            try {
                $model = $this->_objectManager->create('Themevast\Brand\Model\Brand');
                $model->load($id);
                $title = $model->getTitle();
                $model->delete();
                $this->messageManager->addSuccess(__('The brand %s has been deleted.',$title));
                return $resultRedirect->setPath('*/*/');
            } catch (\Exception $e) {
                $this->messageManager->addError($e->getMessage());
               // $this->_redirect('*/*/edit', ['brand_id' => $id]);
            }
        }
        $this->messageManager->addError(__('We can\'t find a brand to delete.'));
        $this->_redirect('*/*/');
    }
}
