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

namespace Mageplaza\QuickCart\Model;

use Magento\Checkout\Model\ConfigProviderInterface;
use Mageplaza\QuickCart\Helper\Data;

/**
 * Class ConfigProvider
 * @package Mageplaza\QuickCart\Model
 */
class ConfigProvider implements ConfigProviderInterface
{
    /**
     * @var Data
     */
    private $helper;

    /**
     * ConfigProvider constructor.
     *
     * @param Data $helper
     */
    public function __construct(Data $helper)
    {
        $this->helper = $helper;
    }

    /**
     * {@inheritdoc}
     */
    public function getConfig()
    {
        return [
            'mpquickcart' => [
                'isHover'  => $this->helper->isHover(),
                'showFull' => $this->helper->isShowFull(),
            ]
        ];
    }
}
