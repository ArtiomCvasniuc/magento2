<?php
/**
 * Mageplaza
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Mageplaza.com license that is
 * available through the world-wide-web at this URL:
 * https://www.mageplaza.com/LICENSE.txt
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade this extension to newer
 * version in the future.
 *
 * @category   Mageplaza
 * @package    Mageplaza_PdfInvoice
 * @copyright   Copyright (c) Mageplaza (http://www.mageplaza.com/)
 * @license     https://www.mageplaza.com/LICENSE.txt
 */

namespace Mageplaza\PdfInvoice\Controller\Adminhtml\Template;

use Exception;
use Magento\Framework\Controller\Result\Redirect;
use Mageplaza\PdfInvoice\Controller\Adminhtml\Template;

/**
 * Class Delete
 * @package Mageplaza\PdfInvoice\Controller\Adminhtml\Template
 */
class Delete extends Template
{
    /**
     * @return $this|Redirect
     */
    public function execute()
    {
        $resultRedirect = $this->resultRedirectFactory->create();
        $id = $this->getRequest()->getParam('id');
        if ($id) {
            if ($this->checkTemplate($id)) {
                $this->messageManager->addError(__('Cannot delete! The Template is using in config'));

                return $resultRedirect->setPath('*/template/edit', ['id' => $id]);
            }

            $template = $this->_templateFactory->create();
            $template->load($id);

            try {
                $template->delete();
                $this->messageManager->addSuccess(__('The Template has been deleted.'));
                $resultRedirect->setPath('*/*/');

                return $resultRedirect;
            } catch (Exception $e) {
                $this->messageManager->addError($e->getMessage());
                $resultRedirect->setPath('*/template/edit', ['id' => $template->getId()]);

                return $resultRedirect;
            }
        }

        $this->messageManager->addError(__('Template to delete was not found.'));
        $resultRedirect->setPath('*/*/');

        return $resultRedirect;
    }
}
