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

namespace Mageplaza\SpecialPromotions\Observer;

use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;
use Magento\Framework\Pricing\PriceCurrencyInterface;
use Magento\Quote\Model\Quote\Item\AbstractItem;
use Magento\SalesRule\Model\Rule;
use Mageplaza\SpecialPromotions\Helper\Data;
use Mageplaza\SpecialPromotions\Model\Validator;

/**
 * Class SaleruleValidatorProcess
 * @package Mageplaza\SpecialPromotions\Observer
 */
class SaleruleValidatorProcess implements ObserverInterface
{
    /**
     * @var Data
     */
    protected $helper;

    /**
     * @var Validator
     */
    protected $validator;

    /**
     * @var PriceCurrencyInterface
     */
    protected $priceCurrency;

    /**
     * SaleruleValidatorProcess constructor.
     *
     * @param Data $helper
     * @param Validator $validator
     * @param PriceCurrencyInterface $priceCurrency
     */
    public function __construct(
        Data $helper,
        Validator $validator,
        PriceCurrencyInterface $priceCurrency
    ) {
        $this->helper = $helper;
        $this->validator = $validator;
        $this->priceCurrency = $priceCurrency;
    }

    /**
     * @param Observer $observer
     *
     * @return $this|void
     */
    public function execute(Observer $observer)
    {
        if ($this->helper->isEnabled()) {
            /** @var Rule $rule */
            $rule = $observer->getEvent()->getRule();

            /** @var AbstractItem $item */
            $item = $observer->getEvent()->getItem();
            $item->setSalesruleApplied($rule);
        }

        return $this;
    }
}
