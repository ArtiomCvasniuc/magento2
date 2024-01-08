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
 * @category    Mageplaza
 * @package     Mageplaza_StoreSwitcher
 * @copyright   Copyright (c) Mageplaza (https://www.mageplaza.com/)
 * @license     https://www.mageplaza.com/LICENSE.txt
 */

namespace Mageplaza\StoreSwitcher\Controller\Adminhtml\Rule;

use Exception;
use Magento\Framework\Controller\Result\Redirect;
use Mageplaza\StoreSwitcher\Controller\Adminhtml\Rule;

/**
 * Class Delete
 * @package Mageplaza\StoreSwitcher\Controller\Adminhtml\Rule
 */
class Delete extends Rule
{
    /**
     * @return Redirect
     */
    public function execute()
    {
        $resultRedirect = $this->resultRedirectFactory->create();
        if ($ruleId = $this->getRequest()->getParam('id')) {
            try {
                $this->ruleFactory->create()->load($ruleId)->delete();

                $this->messageManager->addSuccessMessage(__('The Rule has been deleted.'));
            } catch (Exception $e) {
                /** display error message */
                $this->messageManager->addErrorMessage($e->getMessage());
                /** go back to edit form */
                $resultRedirect->setPath('mpstoreswitcher/*/edit', ['id' => $ruleId]);

                return $resultRedirect;
            }
        } else {
            /** display error message */
            $this->messageManager->addErrorMessage(__('Rule to delete was not found.'));
        }

        /** goto grid */
        $resultRedirect->setPath('mpstoreswitcher/*/');

        return $resultRedirect;
    }
}
