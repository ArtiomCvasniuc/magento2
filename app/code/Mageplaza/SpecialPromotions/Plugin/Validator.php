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

use Magento\Quote\Model\Quote\Address;
use Mageplaza\SpecialPromotions\Helper\Data;
use Mageplaza\SpecialPromotions\Model\Validator as ValidatorModel;

/**
 * Class Validator
 * @package Mageplaza\SpecialPromotions\Plugin
 */
class Validator
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
     * Validator constructor.
     *
     * @param Data $helper
     * @param ValidatorModel $validator
     */
    public function __construct(
        Data $helper,
        ValidatorModel $validator
    ) {
        $this->helper = $helper;
        $this->validator = $validator;
    }

    /**
     * @param \Magento\SalesRule\Model\Validator $subject
     * @param callable $proceed
     * @param mixed $items
     * @param Address $address
     *
     * @return mixed
     */
    public function aroundInitTotals(
        \Magento\SalesRule\Model\Validator $subject,
        callable $proceed,
        $items,
        Address $address
    ) {
        if ($this->helper->isEnabled()) {
            $this->validator->init($subject->getWebsiteId(), $subject->getCustomerGroupId(), $subject->getCouponCode());
            $this->validator->initActionTotals($items, $address);
        }

        return $proceed($items, $address);
    }

    /**
     * @param \Magento\SalesRule\Model\Validator $subject
     * @param callable $proceed
     * @param $address
     * @param $separator
     *
     * @return mixed
     */
    public function aroundPrepareDescription(
        \Magento\SalesRule\Model\Validator $subject,
        callable $proceed,
        $address,
        $separator = ', '
    ) {
        if ($this->helper->isEnabled()) {
            $descriptionArray = $address->getDiscountDetailsArray();
            if (!$descriptionArray && $address->getQuote()->getItemVirtualQty() > 0) {
                $descriptionArray = $address->getQuote()->getBillingAddress()->getDiscountDetailsArray();
            }

            $address->setDiscountDetails($this->helper->serialize($descriptionArray));
        }

        return $proceed($address, $separator);
    }
}
