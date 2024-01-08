<?php
namespace Themevast\Brand\Controller\Adminhtml\Brand;

use Magento\Framework\App\Filesystem\DirectoryList;

class Save extends \Themevast\Brand\Controller\Adminhtml\AbstractAction {
	
	public function execute() {
		if ($data = $this->getRequest()->getPostValue()) {
			$model = $this->_objectManager->create('Themevast\Brand\Model\Brand');
			$storeViewId = $this->getRequest()->getParam('store');
			
			if ($id = $this->getRequest()->getParam('brand_id')) {
				$model->load($id);
			}
			try {
				$uploader = $this->_objectManager->create('Magento\MediaStorage\Model\File\Uploader',['fileId' => 'image']);
				$uploader->setAllowedExtensions(['jpg', 'jpeg', 'gif', 'png']);
				$imageAdapter = $this->_objectManager->get('Magento\Framework\Image\AdapterFactory')->create();
				$uploader->addValidateCallback('brand_image', $imageAdapter, 'validateUploadFile');
				$uploader->setAllowRenameFiles(true);
				$uploader->setFilesDispersion(true);
				$mediaDirectory = $this->_objectManager->get('Magento\Framework\Filesystem')
				                       ->getDirectoryRead(DirectoryList::MEDIA);
				$result = $uploader->save($mediaDirectory->getAbsolutePath(\Themevast\Brand\Model\Brand::BASE_MEDIA_PATH));
				$data['image'] = \Themevast\Brand\Model\Brand::BASE_MEDIA_PATH . $result['file'];
			} catch (\Exception $e) {
				if ($e->getCode() == 0) {
					$this->messageManager->addError($e->getMessage());
				}
				if (isset($data['image']) && isset($data['image']['value'])) {
					if (isset($data['image']['delete'])) {
						$data['image'] = null;
						$data['delete_image'] = true;
					} else if (isset($data['image']['value'])) {
						$data['image'] = $data['image']['value'];
					} else {
						$data['image'] = null;
					}
				}
			}

			$model->setData($data)
			      ->setStoreViewId($storeViewId);

			try {
				$model->save();

				$this->messageManager->addSuccess(__('The brand has been saved.'));
				$this->_getSession()->setFormData(false);
				 if ($this->getRequest()->getParam('back')) {
                $this->_redirect('*/*/edit', ['id' => $model->getId()]);
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
				$this->messageManager->addException($e, __('Something went wrong while saving the brand.'));
			}

			$this->_getSession()->setFormData($data);
			$this->_redirect('*/*/edit', array('id' => $model->getId()));
			return;
		}
		$this->_redirect('*/*/');
	}
}
