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

use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Pricing\PriceCurrencyInterface;
use Magento\Quote\Model\Quote\Item\AbstractItem;
use Magento\SalesRule\Model\DeltaPriceRound;
use Magento\SalesRule\Model\Rule;
use Magento\SalesRule\Model\Rule\Action\Discount\CartFixed;
use Magento\SalesRule\Model\Rule\Action\Discount\Data;
use Magento\SalesRule\Model\Rule\Action\Discount\DataFactory;
use Mageplaza\SpecialPromotions\Model\Validator;

/**
 * Class CartSpentXGetY
 * @package Mageplaza\SpecialPromotions\Model\Rule\Action\Discount
 */
class CartSpentXGetY extends CartFixed
{
    /**
     * @var \Mageplaza\SpecialPromotions\Helper\Data
     */
    protected $helper;

    /**
     * CartSpentXGetY constructor.
     *
     * @param Validator $validator
     * @param DataFactory $discountDataFactory
     * @param PriceCurrencyInterface $priceCurrency
     * @param DeltaPriceRound $deltaPriceRound
     * @param \Mageplaza\SpecialPromotions\Helper\Data $helper
     */
    public function __construct(
        Validator $validator,
        DataFactory $discountDataFactory,
        PriceCurrencyInterface $priceCurrency,
        DeltaPriceRound $deltaPriceRound,
        \Mageplaza\SpecialPromotions\Helper\Data $helper
    ) {
        $this->helper = $helper;

        parent::__construct($validator, $discountDataFactory, $priceCurrency, $deltaPriceRound);
    }

    /**
     * @param Rule $rule
     * @param AbstractItem $item
     * @param float $qty
     *
     * @return Data
     * @throws LocalizedException
     */
    public function calculate($rule, $item, $qty)
    {
        if (!$this->helper->isEnabled($item->getStoreId())) {
            /** @var Data $discountData */
            $discountData = $this->discountFactory->create();

            $discountData->setAmount(0);
            $discountData->setBaseAmount(0);

            return $discountData;
        }

        $ruleTotals = $this->validator->getRuleItemTotalsInfo($rule->getId());
        $rule->setDiscountAmount($ruleTotals['base_discount_amount']);

        return parent::calculate($rule, $item, $qty);
    }
}
