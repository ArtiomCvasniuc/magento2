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
 * @package     Mageplaza_StoreSwitcher
 * @copyright   Copyright (c) Mageplaza (https://www.mageplaza.com/)
 * @license     https://www.mageplaza.com/LICENSE.txt
 */

namespace Mageplaza\StoreSwitcher\Model\Config\Source;

use Magento\Framework\Option\ArrayInterface;

/**
 * Class PageType
 * @package Mageplaza\StoreSwitcher\Model\Config\Source
 */
class PageType implements ArrayInterface
{
    const HOME_PAGE = 'home_page';

    const ALL_PAGES = 'all_pages';

    const SPECIFIC_PAGES = 'specific_pages';

    /**
     * Options getter
     *
     * @return array
     */
    public function toOptionArray()
    {
        $options = [];

        foreach ($this->toArray() as $value => $label) {
            $options[] = [
                'value' => $value,
                'label' => $label
            ];
        }

        return $options;
    }

    /**
     * @return array
     */
    protected function toArray()
    {
        return [
            self::HOME_PAGE      => __('Home Page'),
            self::ALL_PAGES      => __('All Pages'),
            self::SPECIFIC_PAGES => __('Specific Pages')
        ];
    }
}
