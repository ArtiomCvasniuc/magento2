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
 * @package     Mageplaza_Osc
 * @copyright   Copyright (c) Mageplaza (https://www.mageplaza.com/)
 * @license     https://www.mageplaza.com/LICENSE.txt
 */

namespace Mageplaza\Osc\Api;

use Magento\Checkout\Api\Data\ShippingInformationInterface;
use Magento\Framework\Exception\CouldNotSaveException;
use Magento\Framework\Exception\InputException;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Exception\NoSuchEntityException;
use Mageplaza\Osc\Api\Data\OscDetailsInterface;

/**
 * Interface for update item information
 * @api
 */
interface CheckoutManagementInterface
{
    /**
     * @param int $cartId
     * @param int $itemId
     * @param int $itemQty
     *
     * @return OscDetailsInterface
     * @throws NoSuchEntityException
     * @throws CouldNotSaveException
     */
    public function updateItemQty($cartId, $itemId, $itemQty);

    /**
     * @param int $cartId
     * @param int $itemId
     *
     * @return OscDetailsInterface
     * @throws NoSuchEntityException
     * @throws CouldNotSaveException
     */
    public function removeItemById($cartId, $itemId);

    /**
     * @param int $cartId
     *
     * @return OscDetailsInterface
     * @throws NoSuchEntityException
     * @throws LocalizedException
     */
    public function getPaymentTotalInformation($cartId);

    /**
     * @param int $cartId
     * @param bool $isUseGiftWrap
     *
     * @return OscDetailsInterface
     * @throws NoSuchEntityException
     * @throws CouldNotSaveException
     */
    public function updateGiftWrap($cartId, $isUseGiftWrap);

    /**
     * @param int $cartId
     * @param ShippingInformationInterface $addressInformation
     * @param string[] $customerAttributes
     * @param string[] $additionInformation
     *
     * @return bool
     * @throws InputException
     */
    public function saveCheckoutInformation(
        $cartId,
        ShippingInformationInterface $addressInformation,
        $customerAttributes = [],
        $additionInformation = []
    );
}
