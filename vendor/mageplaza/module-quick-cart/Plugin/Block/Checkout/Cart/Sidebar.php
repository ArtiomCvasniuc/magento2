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
 * @package     Mageplaza_QuickCart
 * @copyright   Copyright (c) Mageplaza (https://www.mageplaza.com/)
 * @license     https://www.mageplaza.com/LICENSE.txt
 */

namespace Mageplaza\QuickCart\Plugin\Block\Checkout\Cart;

use Mageplaza\QuickCart\Helper\Data;
use Mageplaza\QuickCart\Model\ConfigProvider;

/**
 * Class Sidebar
 * @package Mageplaza\QuickCart\Plugin\Block\Checkout\Cart
 */
class Sidebar
{
    /**
     * @var Data
     */
    private $helper;

    /**
     * @var ConfigProvider
     */
    private $configProvider;

    /**
     * Sidebar constructor.
     *
     * @param Data $helper
     * @param ConfigProvider $configProvider
     */
    public function __construct(
        Data $helper,
        ConfigProvider $configProvider
    ) {
        $this->helper         = $helper;
        $this->configProvider = $configProvider;
    }

    /**
     * @param \Magento\Checkout\Block\Cart\Sidebar $subject
     * @param array $result
     *
     * @return array
     */
    public function afterGetConfig(\Magento\Checkout\Block\Cart\Sidebar $subject, $result)
    {
        if (!$this->helper->isEnabled()) {
            return $result;
        }

        return array_merge_recursive($result, $this->configProvider->getConfig());
    }

    /**
     * @param \Magento\Checkout\Block\Cart\Sidebar $subject
     * @param string $result
     *
     * @return string
     */
    public function afterGetJsLayout(\Magento\Checkout\Block\Cart\Sidebar $subject, $result)
    {
        if (!$this->helper->isEnabled()) {
            return $result;
        }

        $jsLayout = Data::jsonDecode($result);

        if (isset($jsLayout['components']['minicart_content'])) {
            $isCheckoutScope = count($subject->getItems()) && $this->helper->isCheckoutScope();

            $minicart = &$jsLayout['components']['minicart_content'];

            $minicart['component']          = 'Mageplaza_QuickCart/js/view/minicart';
            $minicart['config']['template'] = 'Mageplaza_QuickCart/minicart/content';

            $children = &$minicart['children'];

            if ($this->helper->isShowCoupon()) {
                $children['mpquickcart_coupon'] = [
                    'component'   => 'Mageplaza_QuickCart/js/view/coupon',
                    'displayArea' => 'extraInfo',
                    'config'      => [
                        'template' => 'Mageplaza_QuickCart/minicart/coupon'
                    ],
                ];

                if ($isCheckoutScope) {
                    $children['mpquickcart_coupon']['component'] = 'Mageplaza_QuickCart/js/view/coupon-quote';
                }
            }

            $children['mpquickcart_totals'] = [
                'component'   => 'Mageplaza_QuickCart/js/view/totals',
                'displayArea' => 'extraInfo',
                'config'      => [
                    'template' => 'Mageplaza_QuickCart/minicart/totals'
                ],
            ];

            if ($isCheckoutScope) {
                $children['mpquickcart_totals']['component'] = 'Mageplaza_QuickCart/js/view/totals-quote';

                if ($this->helper->isModuleOutputEnabled('Mageplaza_RewardPoints')) {
                    $children['mpquickcart_totals']['component'] = 'Mageplaza_QuickCart/js/view/totals-reward-points';
                }
            }

            if (isset($children['item.renderer'])) {
                $children['item.renderer']['config']['template'] = 'Mageplaza_QuickCart/minicart/item/default';
            }
        }

        return Data::jsonEncode($jsLayout);
    }
}
