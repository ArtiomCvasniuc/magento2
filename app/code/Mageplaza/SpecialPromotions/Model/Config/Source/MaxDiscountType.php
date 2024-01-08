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

use Magento\Framework\Data\OptionSourceInterface;

/**
 * Class MaxDiscountType
 * @package Mageplaza\SpecialPromotions\Model\Config\Source
 */
class MaxDiscountType implements OptionSourceInterface
{
    const TYPE_NO      = 0;
    const TYPE_FIXED   = 1;
    const TYPE_PERCENT = 2;

    /**
     * {@inheritdoc}
     * @codeCoverageIgnore
     * @since 100.1.0
     */
    public function toOptionArray()
    {
        return [
            [
                'value' => self::TYPE_NO,
                'label' => __('No')
            ],
            [
                'value' => self::TYPE_FIXED,
                'label' => __('Fixed amount')
            ],
            [
                'value' => self::TYPE_PERCENT,
                'label' => __('Percent of cart subtotal')
            ]
        ];
    }
}
