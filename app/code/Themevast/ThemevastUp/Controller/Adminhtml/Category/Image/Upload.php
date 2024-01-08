<?php
namespace Themevast\ThemevastUp\Controller\Adminhtml\Category\Image;

use Magento\Framework\Controller\ResultFactory;

class Upload extends \Magento\Catalog\Controller\Adminhtml\Category\Image\Upload
{

    public function execute()
    {
        try {
            $result = $this->imageUploader->saveFileToTmpDir('image');

            $result['cookie'] = [
                'name' => $this->_getSession()->getName(),
                'value' => $this->_getSession()->getSessionId(),
                'lifetime' => $this->_getSession()->getCookieLifetime(),
                'path' => $this->_getSession()->getCookiePath(),
                'domain' => $this->_getSession()->getCookieDomain(),
            ];
        } catch (\Exception $e) {
            $result = ['error' => $e->getMessage(), 'errorcode' => $e->getCode()];
        }
		
		if($result['error'] != '')
		{
			try {
				$result = $this->imageUploader->saveFileToTmpDir('tv_menu_icon_img');

				$result['cookie'] = [
					'name' => $this->_getSession()->getName(),
					'value' => $this->_getSession()->getSessionId(),
					'lifetime' => $this->_getSession()->getCookieLifetime(),
					'path' => $this->_getSession()->getCookiePath(),
					'domain' => $this->_getSession()->getCookieDomain(),
				];
			} catch (\Exception $e) {
				$result = ['error' => $e->getMessage(), 'errorcode' => $e->getCode()];
			}
		}
		
		if($result['error'] != '')
		{
			try {
				$result = $this->imageUploader->saveFileToTmpDir('vc_menu_icon_img');

				$result['cookie'] = [
					'name' => $this->_getSession()->getName(),
					'value' => $this->_getSession()->getSessionId(),
					'lifetime' => $this->_getSession()->getCookieLifetime(),
					'path' => $this->_getSession()->getCookiePath(),
					'domain' => $this->_getSession()->getCookieDomain(),
				];
			} catch (\Exception $e) {
				$result = ['error' => $e->getMessage(), 'errorcode' => $e->getCode()];
			}
		}
		
        return $this->resultFactory->create(ResultFactory::TYPE_JSON)->setData($result);
    }
}
