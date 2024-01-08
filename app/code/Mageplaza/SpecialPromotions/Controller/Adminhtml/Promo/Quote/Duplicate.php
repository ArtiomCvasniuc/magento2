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
use Magento\Backend\App\Action\Context;
use Magento\Framework\App\Response\Http\FileFactory;
use Magento\Framework\Registry;
use Magento\Framework\Stdlib\DateTime\Filter\Date;
use Magento\SalesRule\Model\ResourceModel\Rule\CollectionFactory;
use Magento\SalesRule\Model\Rule;
use Magento\SalesRule\Model\RuleFactory;
use Mageplaza\SpecialPromotions\Controller\Adminhtml\Promo\Quote;
use Psr\Log\LoggerInterface;

/**
 * Class Duplicate
 * @package Mageplaza\SpecialPromotions\Controller\Adminhtml\Promo\Quote
 */
class Duplicate extends Quote
{
    /**
     * @var RuleFactory
     */
    protected $ruleFactory;

    /**
     * @var LoggerInterface
     */
    protected $logger;

    /**
     * Duplicate constructor.
     *
     * @param Context $context
     * @param Registry $coreRegistry
     * @param FileFactory $fileFactory
     * @param Date $dateFilter
     * @param CollectionFactory $ruleCollectionfactory
     * @param RuleFactory $ruleFactory
     * @param LoggerInterface $logger
     */
    public function __construct(
        Context $context,
        Registry $coreRegistry,
        FileFactory $fileFactory,
        Date $dateFilter,
        CollectionFactory $ruleCollectionfactory,
        RuleFactory $ruleFactory,
        LoggerInterface $logger
    ) {
        $this->ruleFactory = $ruleFactory;
        $this->logger = $logger;

        parent::__construct($context, $coreRegistry, $fileFactory, $dateFilter, $ruleCollectionfactory);
    }

    /**
     * Duplicate promo quote action
     *
     * @return void
     */
    public function execute()
    {
        $id = $this->getRequest()->getParam('id');
        if (!$id) {
            $this->messageManager->addErrorMessage(__('We can\'t find a rule to duplicate.'));
            $this->_redirect('sales_rule/*/');

            return;
        }

        try {
            $model = $this->ruleFactory->create()
                ->load($id)
                ->setId(null)
                ->save();

            $this->messageManager->addSuccessMessage(__('You duplicated the rule.'));

            if ($model->getCouponType() == Rule::COUPON_TYPE_SPECIFIC && !$model->getUseAutoGeneration()) {
                $this->messageManager->addNoticeMessage(__('Coupon code is empty. Please fill it and save the rule to apply.'));
            }
        } catch (Exception $e) {
            $this->messageManager->addErrorMessage(
                __('We can\'t duplicate the rule right now. Please review the log and try again.')
            );
            $this->logger->critical($e);
            $this->_redirect('sales_rule/*/edit', ['id' => $this->getRequest()->getParam('id')]);

            return;
        }

        $this->_redirect('sales_rule/*/edit', ['id' => $model->getId()]);
    }
}
