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

namespace Mageplaza\SpecialPromotions\Api\Data;

/**
 * Interface DiscountDetailsItemInterface
 * @package Mageplaza\SpecialPromotions\Api\Data
 */
interface DiscountDetailsItemInterface
{
    /**
     * Get discount amount value
     *
     * @return float|string
     */
    public function getAmount();

    /**
     * @param string|float $amount
     *
     * @return $this
     */
    public function setAmount($amount);

    /**
     * Item id
     *
     * @return int|string
     */
    public function getItemId();

    /**
     * @param string|int $itemId
     *
     * @return $this
     */
    public function setItemId($itemId);

    /**
     * Item qty applied
     *
     * @return int
     */
    public function getQty();

    /**
     * @param string|int $qty
     *
     * @return $this
     */
    public function setQty($qty);
}
