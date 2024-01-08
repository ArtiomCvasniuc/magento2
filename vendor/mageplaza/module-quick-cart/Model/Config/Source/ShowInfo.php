<?php
/**
 * Mageplaza
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the mageplaza.com license that is
 * available through the world-wide-web at this URL:
 * https://www.mageplaza.com/LICENSE.txt
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade this extension to newer
 * version in the future.
 *
 * @category    Mageplaza
 * @package     Mageplaza_QuickCart
 * @copyright   Copyright (c) Mageplaza (https://www.mageplaza.com/)
 * @license     https://www.mageplaza.com/LICENSE.txt
 */

namespace Mageplaza\QuickCart\Model\Config\Source;

/**
 * Class ShowInfo
 * @package Mageplaza\QuickCart\Model\Config\Source
 */
class ShowInfo extends AbstractSource
{
    const FULL     = 'full';
    const SUBTOTAL = 'subtotal';

    /**
     * @return array
     */
    public static function getOptionArray()
    {
        return [
            self::FULL     => __('Full Total Information'),
            self::SUBTOTAL => __('Only Subtotal'),
        ];
    }
}
