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
 * @package     Mageplaza_AutoRelated
 * @copyright   Copyright (c) Mageplaza (https://www.mageplaza.com/)
 * @license     https://www.mageplaza.com/LICENSE.txt
 */

namespace Mageplaza\AutoRelated\Model\Config\Source;

use Magento\Framework\Option\ArrayInterface;

/**
 * Class ProductNotDisplayed
 * @package Mageplaza\AutoRelated\Model\Config\Source
 */
class ProductNotDisplayed implements ArrayInterface
{
    const NONE        = 1;
    const IN_CART     = 2;
    const IN_WISHLIST = 3;

    /**
     * Return array of options as value-label pairs
     *
     * @return array Format: array(array('value' => '<value>', 'label' => '<label>'), ...)
     */
    public function toOptionArray()
    {
        return [
            ['value' => self::NONE, 'label' => __('-- Please Select --')],
            ['value' => self::IN_CART, 'label' => __('Cart')],
            ['value' => self::IN_WISHLIST, 'label' => __('Wishlist')],
        ];
    }
}
