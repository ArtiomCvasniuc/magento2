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

namespace Mageplaza\SpecialPromotions\Plugin;

use Magento\Framework\Pricing\PriceCurrencyInterface;
use Magento\Quote\Model\Quote\Item\AbstractItem;
use Mageplaza\SpecialPromotions\Helper\Data;
use Mageplaza\SpecialPromotions\Model\Validator as ValidatorModel;

/**
 * Class Utility
 * @package Mageplaza\SpecialPromotions\Plugin
 */
class Utility
{
    /**
     * @var Data
     */
    protected $helper;

    /**
     * @var ValidatorModel
     */
    protected $validator;

    /**
     * @var PriceCurrencyInterface
     */
    protected $priceCurrency;

    /**
     * Utility constructor.
     *
     * @param Data $helper
     * @param ValidatorModel $validator
     * @param PriceCurrencyInterface $priceCurrency
     */
    public function __construct(
        Data $helper,
        ValidatorModel $validator,
        PriceCurrencyInterface $priceCurrency
    ) {
        $this->helper = $helper;
        $this->validator = $validator;
        $this->priceCurrency = $priceCurrency;
    }

    /**
     * @param \Magento\SalesRule\Model\Utility $subject
     * @param callable $proceed
     * @param \Magento\SalesRule\Model\Rule\Action\Discount\Data $discountData
     * @param AbstractItem $item
     * @param $qty
     *
     * @return $this
     */
    public function aroundMinFix(
        \Magento\SalesRule\Model\Utility $subject,
        callable $proceed,
        \Magento\SalesRule\Model\Rule\Action\Discount\Data $discountData,
        AbstractItem $item,
        $qty
    ) {
        $proceed($discountData, $item, $qty);

        $rule = $item->getSalesruleApplied();
        if ($rule && $this->helper->isEnabled()) {
            $maxDiscount = $this->validator->getRuleMaxDiscountInfo($rule->getId());
            if ($maxDiscount !== null) {
                $baseDiscountAmount = $discountData->getBaseAmount() - $item->getBaseDiscountAmount();
                if ($baseDiscountAmount >= $maxDiscount) {
                    $baseDiscountAmount = $maxDiscount;
                    $discountAmount = $this->priceCurrency->convert($baseDiscountAmount, $item->getQuote()->getStore());

                    $discountData->setAmount($discountAmount + $item->getDiscountAmount());
                    $discountData->setBaseAmount($baseDiscountAmount + $item->getBaseDiscountAmount());
                }

                $this->validator->decrementRuleMaxDiscount($rule->getId(), $baseDiscountAmount);
            }

            $item->setSalesruleApplied(null);
            $this->addDiscountDescription($rule, $discountData, $item, $qty);
        }

        return $this;
    }

    /**
     * Add rule discount description label to address object
     *
     * @param \Magento\SalesRule\Model\Rule $rule
     * @param \Magento\SalesRule\Model\Rule\Action\Discount\Data $discountData
     * @param AbstractItem $item
     * @param $qty
     *
     * @return $this
     */
    public function addDiscountDescription(
        $rule,
        \Magento\SalesRule\Model\Rule\Action\Discount\Data $discountData,
        AbstractItem $item,
        $qty
    ) {
        $address = $item->getAddress();

        $itemDiscount = $discountData->getAmount() - $item->getDiscountAmount();
        $itemBaseDiscount = $discountData->getBaseAmount() - $item->getBaseDiscountAmount();

        /** Cart discount description */
        $description = $address->getDiscountDetailsArray();
        $ruleDescription = isset($description[$rule->getId()]) ? $description[$rule->getId()] : ['items' => []];
        $ruleDescription = [
            'rule_id'       => $rule->getId(),
            'label'         => $rule->getStoreLabel($address->getQuote()->getStore()) ?: $rule->getName(),
            'discount'      => (isset($ruleDescription['discount']) ? $ruleDescription['discount'] : 0) + $itemDiscount,
            'base_discount' => (isset($ruleDescription['base_discount']) ? $ruleDescription['base_discount'] : 0) + $itemBaseDiscount,
            'items'         => array_merge($ruleDescription['items'], [
                [
                    'item_id'       => $item->getId(),
                    'discount'      => $itemDiscount,
                    'base_discount' => $itemBaseDiscount,
                    'qty'           => $qty
                ]
            ])
        ];
        $description[$rule->getId()] = $ruleDescription;
        $address->setDiscountDetailsArray($description);

        return $this;
    }
}
