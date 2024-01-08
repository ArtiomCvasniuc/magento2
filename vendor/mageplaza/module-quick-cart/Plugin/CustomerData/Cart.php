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
 * @package     Mageplaza_QuickCart
 * @copyright   Copyright (c) Mageplaza (https://www.mageplaza.com/)
 * @license     https://www.mageplaza.com/LICENSE.txt
 */

namespace Mageplaza\QuickCart\Plugin\CustomerData;

use Magento\Checkout\Model\Session;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\UrlInterface;
use Magento\Quote\Api\CartTotalRepositoryInterface;
use Magento\Quote\Model\Cart\TotalSegment;
use Magento\Quote\Model\QuoteIdMask;
use Magento\Quote\Model\QuoteIdMaskFactory;
use Mageplaza\QuickCart\Helper\Data;
use Mageplaza\RewardPoints\Helper\Point;
use Mageplaza\RewardPoints\Model\Source\DisplayPointLabel;
use Psr\Log\LoggerInterface;

/**
 * Class Cart
 * @package Mageplaza\RewardPoints\Plugin\CustomerData
 */
class Cart
{
    /**
     * @var Data
     */
    private $helper;

    /**
     * @var Session
     */
    private $session;

    /**
     * @var LoggerInterface
     */
    private $logger;

    /**
     * @var \Magento\Customer\Model\Session
     */
    private $customerSession;

    /**
     * @var CartTotalRepositoryInterface
     */
    private $cartTotalRepository;

    /**
     * @var QuoteIdMaskFactory
     */
    private $quoteIdMaskFactory;

    /**
     * @var UrlInterface
     */
    private $urlBuilder;

    /**
     * Cart constructor.
     *
     * @param Data $helper
     * @param Session $session
     * @param LoggerInterface $logger
     * @param \Magento\Customer\Model\Session $customerSession
     * @param CartTotalRepositoryInterface $cartTotalRepository
     * @param QuoteIdMaskFactory $quoteIdMaskFactory
     * @param UrlInterface $urlBuilder
     */
    public function __construct(
        Data $helper,
        Session $session,
        LoggerInterface $logger,
        \Magento\Customer\Model\Session $customerSession,
        CartTotalRepositoryInterface $cartTotalRepository,
        QuoteIdMaskFactory $quoteIdMaskFactory,
        UrlInterface $urlBuilder
    ) {
        $this->helper              = $helper;
        $this->session             = $session;
        $this->logger              = $logger;
        $this->customerSession     = $customerSession;
        $this->cartTotalRepository = $cartTotalRepository;
        $this->quoteIdMaskFactory  = $quoteIdMaskFactory;
        $this->urlBuilder          = $urlBuilder;
    }

    /**
     * @param \Magento\Checkout\CustomerData\Cart $subject
     * @param array $result
     *
     * @return array
     */
    public function afterGetSectionData(\Magento\Checkout\CustomerData\Cart $subject, $result)
    {
        try {
            $this->appendSegment($result);
        } catch (NoSuchEntityException $e) {
            $this->logger->critical($e->getMessage());
        } catch (LocalizedException $e) {
            $this->logger->critical($e->getMessage());
        }

        return $result;
    }

    /**
     * @param array $result
     *
     * @throws LocalizedException
     * @throws NoSuchEntityException
     */
    protected function appendSegment(&$result)
    {
        $quote   = $this->session->getQuote();
        $storeId = $quote->getStoreId();

        if (!$this->helper->isEnabled($storeId)) {
            return;
        }

        $quoteId = $quote->getId();
        if (!$isLoggedIn = $this->customerSession->isLoggedIn()) {
            /** @var $quoteIdMask QuoteIdMask */
            $quoteIdMask = $this->quoteIdMaskFactory->create()->load($quoteId, 'quote_id');
            $quoteId     = $quoteIdMask->getMaskedId();
        }

        $storeCode = $quote->getStore()->getCode();

        $result['mpquickcart'] = [
            'couponCode' => $quote->getCouponCode(),
            'isLoggedIn' => $isLoggedIn,
            'quoteId'    => $quoteId,
            'totals'     => [],
            'apiUrl'     => $this->urlBuilder->getUrl(sprintf('rest/%s/V1', $storeCode), ['_secure' => true]),
        ];

        if (!$this->helper->isShowFull($storeId)) {
            return;
        }

        $totals = $this->cartTotalRepository->get($quote->getId());

        /** @var TotalSegment $segment */
        foreach ($totals->getTotalSegments() as $segment) {
            $data  = $segment->toArray();
            $value = $segment->getValue();
            switch ($segment->getCode()) {
                case 'mp_reward_spent':
                case 'mp_reward_earn':
                    /** @var Point $pointHelper */
                    $pointHelper   = $this->helper->getObject(Point::class);
                    $isLabelBefore = $pointHelper->getPointLabelPosition($storeId) === DisplayPointLabel::BEFORE_AMOUNT;
                    $pointLabel    = $value > 1
                        ? $pointHelper->getPluralPointLabel($storeId)
                        : $pointHelper->getPointLabel($storeId);

                    $pattern = $isLabelBefore ? $pointLabel . '{point}' : '{point}' . $pointLabel;

                    $data['value'] = sprintf('<b>%s</b>', str_replace('{point}', $value, $pattern));

                    break;
                default:
                    $data['value'] = $this->helper->formatPrice($value, false, $storeId);
                    break;
            }

            $result['mpquickcart']['totals'][] = $data;
        }
    }
}
