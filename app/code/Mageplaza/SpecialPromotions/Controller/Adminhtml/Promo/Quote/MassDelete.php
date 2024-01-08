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
 * @package     Mageplaza_SpecialPromotions
 * @copyright   Copyright (c) Mageplaza (http://www.mageplaza.com/)
 * @license     https://www.mageplaza.com/LICENSE.txt
 */

namespace Mageplaza\SpecialPromotions\Controller\Adminhtml\Promo\Quote;

use Exception;
use Magento\Backend\Model\View\Result\Redirect;
use Magento\Framework\App\ResponseInterface;
use Magento\Framework\Controller\ResultFactory;
use Magento\Framework\Controller\ResultInterface;
use Magento\SalesRule\Model\ResourceModel\Rule\Collection;
use Mageplaza\SpecialPromotions\Controller\Adminhtml\Promo\Quote;

/**
 * Class MassDelete
 * @package Mageplaza\SpecialPromotions\Controller\Adminhtml\Promo\Quote
 */
class MassDelete extends Quote
{
    /**
     * @return Redirect|ResponseInterface|ResultInterface
     */
    public function execute()
    {
        $ruleIds = $this->getRequest()->getPost('rule_ids');
        if (!$ruleIds || !is_array($ruleIds)) {
            $this->messageManager->addErrorMessage(__('Rules does not exist.'));
        } else {
            $deletedRules = 0;
            $error = [];

            /** @var Collection $rules */
            $rules = $this->ruleCollectionfactory->create()
                ->addFieldToFilter('rule_id', ['in' => $ruleIds]);
            foreach ($rules as $rule) {
                try {
                    $rule->delete();
                    $deletedRules++;
                } catch (Exception $e) {
                    $error[] = $rule->getId();
                }
            }

            if ($deletedRules) {
                $this->getMessageManager()->addSuccessMessage(__('Deleted %1 rule(s).', $deletedRules));
            }

            if (count($error)) {
                $this->messageManager->addErrorMessage(__(
                    'An error occured while deleting this rule(s): %1',
                    implode(', ', $error)
                ));
            }
        }

        /** @var Redirect $resultRedirect */
        $resultRedirect = $this->resultFactory->create(ResultFactory::TYPE_REDIRECT);

        return $resultRedirect->setPath('*/*');
    }
}
