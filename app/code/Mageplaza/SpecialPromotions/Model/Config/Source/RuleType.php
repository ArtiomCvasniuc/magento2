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

namespace Mageplaza\SpecialPromotions\Model\Config\Source;

use Magento\Framework\Option\ArrayInterface;
use Magento\SalesRule\Model\Rule;

/**
 * Class RuleType
 * @package Mageplaza\SpecialPromotions\Model\Config\Source
 */
class RuleType implements ArrayInterface
{
    const SPENT_X_GET_Y_ACTION = 'spent_x_get_y';
    const CART_SPENT_X_GET_Y_ACTION = 'cart_spent_x_get_y';

    /**
     * Return websites array
     *
     * @return array
     */
    public function toOptionArray()
    {
        return [
            ['label' => __('Percent of product price discount'), 'value' => Rule::BY_PERCENT_ACTION],
            ['label' => __('Fixed amount discount'), 'value' => Rule::BY_FIXED_ACTION],
            ['label' => __('Fixed amount discount for whole cart'), 'value' => Rule::CART_FIXED_ACTION],
            ['label' => __('To-fixed amount discount'), 'value' => Rule::TO_FIXED_ACTION],
            ['label' => __('Buy X get Y free (discount amount is Y)'), 'value' => Rule::BUY_X_GET_Y_ACTION],
            ['label' => __('For each $X spent, get $Y discount'), 'value' => self::SPENT_X_GET_Y_ACTION],
            [
                'label' => __('For each $X spent, get $Y discount for the whole cart'),
                'value' => self::CART_SPENT_X_GET_Y_ACTION
            ]
        ];
    }
}
