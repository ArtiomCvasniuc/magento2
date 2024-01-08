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
 * @package     Mageplaza_AbandonedCart
 * @copyright   Copyright (c) Mageplaza (https://www.mageplaza.com/)
 * @license     https://www.mageplaza.com/LICENSE.txt
 */

namespace Mageplaza\AbandonedCart\Controller\Checkout;

use Magento\Checkout\Model\Session as CheckoutSession;
use Magento\Customer\Model\Session;
use Magento\Framework\App\Action\Action;
use Magento\Framework\App\Action\Context;
use Magento\Framework\App\ResponseInterface;
use Magento\Quote\Model\Quote;
use Magento\Quote\Model\QuoteFactory;
use Mageplaza\AbandonedCart\Helper\Data;
use Mageplaza\AbandonedCart\Model\Logs;
use Mageplaza\AbandonedCart\Model\LogsFactory;
use Mageplaza\AbandonedCart\Model\Token;

/**
 * Class Cart
 * @package Mageplaza\AbandonedCart\Controller\Checkout
 */
class Cart extends Action
{
    /**
     * @var Token
     */
    protected $tokenModel;

    /**
     * @var QuoteFactory
     */
    protected $quoteFactory;

    /**
     * @var Session
     */
    protected $customerSession;

    /**
     * @var CheckoutSession
     */
    protected $checkoutSession;

    /**
     * @var LogsFactory
     */
    protected $logsFactory;

    /**
     * @var Data
     */
    protected $helperData;

    /**
     * Cart constructor.
     *
     * @param Context $context
     * @param QuoteFactory $quoteFactory
     * @param Token $tokenModel
     * @param LogsFactory $logsFactory
     * @param Session $customerSession
     * @param Data $helperData
     * @param CheckoutSession $checkoutSession
     */
    public function __construct(
        Context $context,
        QuoteFactory $quoteFactory,
        Token $tokenModel,
        LogsFactory $logsFactory,
        Session $customerSession,
        Data $helperData,
        CheckoutSession $checkoutSession
    ) {
        parent::__construct($context);

        $this->tokenModel = $tokenModel;
        $this->quoteFactory = $quoteFactory;
        $this->customerSession = $customerSession;
        $this->logsFactory = $logsFactory;
        $this->helperData = $helperData;
        $this->checkoutSession = $checkoutSession;
    }

    /**
     * Recovery cart by cart link
     *
     * @return ResponseInterface
     */
    public function execute()
    {
        $token = $this->getRequest()->getParam('token');
        if (($token !== 'test_email') && !$this->helperData->isEnabled()) {
            return $this->goBack();
        }

        $quoteId = (int)$this->getRequest()->getParam('id');
        if (($token !== 'test_email') && !$this->tokenModel->validateCartLink($quoteId, $token)) {
            $this->messageManager->addErrorMessage(__('You can\'t used this link.'));

            return $this->goBack();
        }

        /** @var Quote $quote */
        $quote = $this->quoteFactory->create()->load($quoteId);

        /** @var Logs $logs */
        $logs = $this->logsFactory->create();

        if (!$quote->getId() || !$quote->getIsActive()) {
            $this->messageManager->addErrorMessage(__('An error occurred while recovering your cart.'));

            return $this->goBack();
        }

        $customerId = (int)$quote->getCustomerId();
        if (!$customerId) {
            $this->checkoutSession->setQuoteId($quoteId);
            $logs->updateRecovery($quoteId);
            $this->messageManager->addSuccessMessage(__('The recovery succeeded.'));

            return $this->goBack();
        }

        if (!$this->customerSession->isLoggedIn()) {
            if (!$this->customerSession->loginById($customerId)) {
                $this->messageManager->addErrorMessage(__('An error occurred while logging in your account. Please try to log in again.'));

                return $this->goBack();
            }

            $this->customerSession->regenerateId();
            $logs->updateRecovery($quoteId);
            $this->messageManager->addSuccessMessage(__('The recovery succeeded.'));
        } elseif ((int)$this->customerSession->getId() !== $customerId) {
            $this->messageManager->addNoticeMessage(__('Please login with %1', $quote->getCustomerEmail()));

            return $this->goBack();
        }

        $logs->updateRecovery($quoteId);
        $this->messageManager->addSuccessMessage(__('The recovery succeeded.'));

        return $this->goBack();
    }

    /**
     * @return ResponseInterface
     */
    protected function goBack()
    {
        return $this->_redirect('checkout/cart');
    }
}
