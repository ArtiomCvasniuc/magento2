<?php
namespace Themevast\Testimonials\Controller\Adminhtml\Testimonials;

use Magento\Framework\App\Filesystem\DirectoryList;

class Save extends \Themevast\Testimonials\Controller\Adminhtml\AbstractAction {
	
	public function execute() {
		if ($data = $this->getRequest()->getPostValue()) {
			$model = $this->_objectManager->create('Themevast\Testimonials\Model\Testimonials');
			$storeViewId = $this->getRequest()->getParam('store');
			
			if ($id = $this->getRequest()->getParam('testimonial_id')) {
				$model->load($id);
			}
			try {
				$uploader = $this->_objectManager->create('Magento\MediaStorage\Model\File\Uploader',['fileId' => 'avatar']);
				$uploader->setAllowedExtensions(['jpg', 'jpeg', 'gif', 'png']);
				$imageAdapter = $this->_objectManager->get('Magento\Framework\Image\AdapterFactory')->create();
				$uploader->addValidateCallback('base_avatar', $imageAdapter, 'validateUploadFile');
				$uploader->setAllowRenameFiles(true);
				$uploader->setFilesDispersion(true);
				$mediaDirectory = $this->_objectManager->get('Magento\Framework\Filesystem')
				                       ->getDirectoryRead(DirectoryList::MEDIA);
				$result = $uploader->save($mediaDirectory->getAbsolutePath(\Themevast\Testimonials\Model\Testimonials::BASE_MEDIA_PATH));
				$data['avatar'] = \Themevast\Testimonials\Model\Testimonials::BASE_MEDIA_PATH . $result['file'];
			} catch (\Exception $e) {
				if ($e->getCode() == 0) {
					$this->messageManager->addError($e->getMessage());
				}
				if (isset($data['avatar']) && isset($data['avatar']['value'])) {
					if (isset($data['avatar']['delete'])) {
						$data['avatar'] = null;
						$data['delete_image'] = true;
					} else if (isset($data['avatar']['value'])) {
						$data['avatar'] = $data['avatar']['value'];
					} else {
						$data['avatar'] = null;
					}
				}
			}
			/* var_dump($data['avatar']);die(); */
			$model->setData($data)
			      ->setStoreViewId($storeViewId);

			try {
				$model->save();

				$this->messageManager->addSuccess(__('The testimonials has been saved.'));
				$this->_getSession()->setFormData(false);
				 if ($this->getRequest()->getParam('back')) {
                $this->_redirect('*/*/edit', ['testimonial_id' => $model->getId()]);
				} else {
					$this->_redirect('*/*');
				}
				return;
			} catch (\Magento\Framework\Model\Exception $e) {
				$this->messageManager->addError($e->getMessage());
			} catch (\RuntimeException $e) {
				$this->messageManager->addError($e->getMessage());
			} catch (\Exception $e) {
				$this->messageManager->addError($e->getMessage());
				$this->messageManager->addException($e, __('Something went wrong while saving the testimonial.'));
			}

			$this->_getSession()->setFormData($data);
			$this->_redirect('*/*/edit', array('testimonial_id' => $this->getRequest()->getParam('testimonial_id')));
			return;
		}
		$this->_redirect('*/*/');
	}
}
