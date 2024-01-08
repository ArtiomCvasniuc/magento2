<?php
/* ducdevphp@gmail.com*/
namespace Themevast\Testimonials\Controller\Adminhtml\Testimonials;

class Delete extends \Magento\Backend\App\Action
{

    public function execute()
    {
        $id = $this->getRequest()->getParam('testimonial_id');
        $resultRedirect = $this->resultRedirectFactory->create();
        if ($id) {
            $title = "";
            try {
                $model = $this->_objectManager->create('Themevast\Testimonials\Model\Testimonials');
                $model->load($id);
                $title = $model->getName();
                $model->delete();
                $this->messageManager->addSuccess(__('The testimonial of author %1 has been deleted.',$title));
                return $resultRedirect->setPath('*/*/');
            } catch (\Exception $e) {
                $this->messageManager->addError($e->getMessage());
                return $resultRedirect->setPath('*/*/edit', ['testimonial_id' => $id]);
            }
        }
        $this->messageManager->addError(__('We can\'t find a testimonial to delete.'));
        return $resultRedirect->setPath('*/*/');
    }

}