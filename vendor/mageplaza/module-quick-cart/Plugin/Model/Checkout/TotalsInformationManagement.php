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

namespace Mageplaza\QuickCart\Plugin\Model\Checkout;

use Closure;
use Magento\Checkout\Api\Data\TotalsInformationInterface;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Quote\Api\CartRepositoryInterface;
use Magento\Quote\Api\Data\ShippingAssignmentInterface;
use Magento\Quote\Api\Data\TotalsInterface;
use Magento\Quote\Model\Quote;

/**
 * Class TotalsInformationManagement
 * @package Mageplaza\QuickCart\Plugin\Model\Checkout
 */
class TotalsInformationManagement
{
    /**
     * @var CartRepositoryInterface
     */
    private $quoteRepository;

    /**
     * TotalsInformationManagement constructor.
     *
     * @param CartRepositoryInterface $quoteRepository
     */
    public function __construct(CartRepositoryInterface $quoteRepository)
    {
        $this->quoteRepository = $quoteRepository;
    }

    /**
     * @param \Magento\Checkout\Model\TotalsInformationManagement $subject
     * @param Closure $proceed
     * @param $cartId
     * @param TotalsInformationInterface $addressInformation
     *
     * @return TotalsInterface
     * @throws NoSuchEntityException
     */
    public function aroundCalculate(
        \Magento\Checkout\Model\TotalsInformationManagement $subject,
        Closure $proceed,
        $cartId,
        TotalsInformationInterface $addressInformation
    ) {
        $result = $proceed($cartId, $addressInformation);

        /* @var Quote $quote */
        $quote = $this->quoteRepository->get($cartId);

        $extensionAttributes = $quote->getExtensionAttributes();
        if ($extensionAttributes && $extensionAttributes->getShippingAssignments() && !$quote->isVirtual()) {
            /** @var ShippingAssignmentInterface[] $shippingAssignments */
            $shippingAssignments = $extensionAttributes->getShippingAssignments();

            if (count($shippingAssignments)) {
                $shippingAssignments[0]->getShipping()->setMethod($quote->getShippingAddress()->getShippingMethod());
            }

            $this->quoteRepository->save($quote);
        }

        return $result;
    }
}
