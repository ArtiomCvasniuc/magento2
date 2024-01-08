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

/**
 * Class ItemAction
 * @package Mageplaza\SpecialPromotions\Model\Config\Source
 */
class ItemAction implements ArrayInterface
{
    const NONE_ACTION        = 'none';
    const CHEAPEST_ACTION    = 'cheapest';
    const EXPENSIVE_ACTION   = 'expensive';
    const BUY_X_GET_Y_ACTION = 'buy_x_get_y';
    const EACH_N_ITEM_ACTION = 'each_n';
    const GROUP_ITEM_ACTION  = 'group';
    const PRODUCT_SET_ACTION = 'product_set';

    /**
     * Return websites array
     *
     * @return array
     */
    public function toOptionArray()
    {
        return [
            ['label' => __('-- Please Select --'), 'value' => self::NONE_ACTION],
            ['label' => __('Cheapest item'), 'value' => self::CHEAPEST_ACTION],
            ['label' => __('Expensive item'), 'value' => self::EXPENSIVE_ACTION],
//            ['label' => __('Buy product X get product Y with discount'), 'value' => self::BUY_X_GET_Y_ACTION],
//            ['label' => __('Each N-th item after M items added'), 'value' => self::EACH_N_ITEM_ACTION],
//            ['label' => __('Each group of M-th items'), 'value' => self::GROUP_ITEM_ACTION],
//            ['label' => __('Product set'), 'value' => self::PRODUCT_SET_ACTION]
        ];
    }
}
