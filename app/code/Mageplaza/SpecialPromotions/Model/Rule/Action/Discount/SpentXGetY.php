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

namespace Mageplaza\SpecialPromotions\Model\Rule\Action\Discount;

use Magento\Framework\Pricing\PriceCurrencyInterface;
use Magento\Quote\Model\Quote\Item\AbstractItem;
use Magento\SalesRule\Model\Rule;
use Magento\SalesRule\Model\Rule\Action\Discount\AbstractDiscount;
use Magento\SalesRule\Model\Rule\Action\Discount\DataFactory;
use Magento\SalesRule\Model\Validator;
use Mageplaza\SpecialPromotions\Helper\Data;

/**
 * Class SpentXGetY
 * @package Mageplaza\SpecialPromotions\Model\Rule\Action\Discount
 */
class SpentXGetY extends AbstractDiscount
{
    /**
     * @var Data
     */
    protected $helper;

    /**
     * SpentXGetY constructor.
     *
     * @param Validator $validator
     * @param DataFactory $discountDataFactory
     * @param PriceCurrencyInterface $priceCurrency
     * @param Data $helper
     */
    public function __construct(
        Validator $validator,
        DataFactory $discountDataFactory,
        PriceCurrencyInterface $priceCurrency,
        Data $helper
    ) {
        $this->helper = $helper;

        parent::__construct($validator, $discountDataFactory, $priceCurrency);
    }

    /**
     * @param Rule $rule
     * @param AbstractItem $item
     * @param float $qty
     *
     * @return \Magento\SalesRule\Model\Rule\Action\Discount\Data
     */
    public function calculate($rule, $item, $qty)
    {
        /** @var \Magento\SalesRule\Model\Rule\Action\Discount\Data $discountData */
        $discountData = $this->discountFactory->create();

        if ($this->helper->isEnabled($item->getStoreId())) {
            $x = $rule->getDiscountStep();
            $y = $rule->getDiscountAmount();
            if (!$x || $y > $x) {
                return $discountData;
            }

            $totalItemPrice = $this->validator->getItemBasePrice($item) * $qty;

            $buyAndDiscount = $x + $y;
            $fullRulePeriod = floor($totalItemPrice / $buyAndDiscount);

            $baseDiscount = $fullRulePeriod * $y;

            $freeAmount = $totalItemPrice - $fullRulePeriod * $buyAndDiscount;
            if ($freeAmount > $x) {
                $baseDiscount += $freeAmount - $x;
            }
        } else {
            $baseDiscount = 0;
        }

        $discount = $this->priceCurrency->convert($baseDiscount, $item->getQuote()->getStore());

        $discountData->setAmount($discount);
        $discountData->setBaseAmount($baseDiscount);

        return $discountData;
    }
}
