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
 * Interface DiscountDetailsInterface
 * @package Mageplaza\SpecialPromotions\Api\Data
 */
interface DiscountDetailsInterface
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
     * Tax rate title
     *
     * @return string
     */
    public function getTitle();

    /**
     * @param string $title
     *
     * @return $this
     */
    public function setTitle($title);

    /**
     * Rule id
     *
     * @return int|string
     */
    public function getRuleId();

    /**
     * @param string|int $ruleId
     *
     * @return $this
     */
    public function setRuleId($ruleId);

    /**
     * Items
     *
     * @return \Mageplaza\SpecialPromotions\Api\Data\DiscountDetailsItemInterface[]
     */
    public function getItems();

    /**
     * @param \Mageplaza\SpecialPromotions\Api\Data\DiscountDetailsItemInterface[] $items
     *
     * @return $this
     */
    public function setItems($items);
}
