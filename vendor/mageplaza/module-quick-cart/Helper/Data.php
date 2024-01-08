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

namespace Mageplaza\QuickCart\Helper;

use Magento\Framework\App\Helper\Context;
use Magento\Framework\App\Request\Http;
use Magento\Framework\ObjectManagerInterface;
use Magento\Framework\Pricing\PriceCurrencyInterface;
use Magento\Framework\View\DesignInterface;
use Magento\Store\Model\StoreManagerInterface;
use Mageplaza\Core\Helper\AbstractData;
use Mageplaza\QuickCart\Model\Config\Source\PopupEffect;
use Mageplaza\QuickCart\Model\Config\Source\ShowInfo;

/**
 * Class Data
 * @package Mageplaza\QuickCart\Helper
 */
class Data extends AbstractData
{
    const CONFIG_MODULE_PATH = 'mpquickcart';

    /**
     * @var PriceCurrencyInterface
     */
    private $priceCurrency;

    /**
     * Data constructor.
     *
     * @param Context $context
     * @param ObjectManagerInterface $objectManager
     * @param StoreManagerInterface $storeManager
     * @param PriceCurrencyInterface $priceCurrency
     */
    public function __construct(
        Context $context,
        ObjectManagerInterface $objectManager,
        StoreManagerInterface $storeManager,
        PriceCurrencyInterface $priceCurrency
    ) {
        $this->priceCurrency = $priceCurrency;

        parent::__construct($context, $objectManager, $storeManager);
    }

    /**
     * @param null $storeId
     *
     * @return string
     */
    public function getPopupEffect($storeId = null)
    {
        return $this->getConfigGeneral('popup_effect', $storeId);
    }

    /**
     * @param null $storeId
     *
     * @return bool
     */
    public function isHover($storeId = null)
    {
        return $this->getPopupEffect($storeId) === PopupEffect::HOVER;
    }

    /**
     * @param null $storeId
     *
     * @return bool
     */
    public function isAutoOpen($storeId = null)
    {
        return $this->getConfigGeneral('auto_open', $storeId) && !$this->isHover($storeId);
    }

    /**
     * @param null $storeId
     *
     * @return bool
     */
    public function isShowCoupon($storeId = null)
    {
        return (bool) $this->getConfigGeneral('show_coupon', $storeId);
    }

    /**
     * @param null $storeId
     *
     * @return bool
     */
    public function isShowFull($storeId = null)
    {
        return $this->getConfigGeneral('show_info', $storeId) === ShowInfo::FULL;
    }

    /**
     * @param null $storeId
     *
     * @return bool
     */
    public function isFixedIcon($storeId = null)
    {
        return (bool) $this->getConfigGeneral('fixed_icon', $storeId);
    }

    /**
     * @param string $code
     * @param null $storeId
     *
     * @return array|mixed
     */
    public function getConfigDesign($code = '', $storeId = null)
    {
        $code = ($code !== '') ? '/' . $code : '';

        return $this->getModuleConfig('design' . $code, $storeId);
    }

    /**
     * @param null $storeId
     *
     * @return string
     */
    public function getHeaderBackground($storeId = null)
    {
        return $this->getConfigDesign('header_background', $storeId) ?: '#1979c3';
    }

    /**
     * @param null $storeId
     *
     * @return string
     */
    public function getHeaderText($storeId = null)
    {
        return $this->getConfigDesign('header_text', $storeId) ?: '#ffffff';
    }

    /**
     * @param null $storeId
     *
     * @return string
     */
    public function getSubtotalBackground($storeId = null)
    {
        return $this->getConfigDesign('subtotal_background', $storeId) ?: '#ffffff';
    }

    /**
     * @param null $storeId
     *
     * @return string
     */
    public function getSubtotalText($storeId = null)
    {
        return $this->getConfigDesign('subtotal_text', $storeId) ?: '#333333';
    }

    /**
     * @param null $storeId
     *
     * @return string
     */
    public function getCustomCss($storeId = null)
    {
        return $this->getConfigDesign('custom_css', $storeId);
    }

    /**
     * @param float $amount
     * @param bool $includeContainer
     * @param null $scope
     * @param null $currency
     * @param int $precision
     *
     * @return float
     */
    public function formatPrice(
        $amount,
        $includeContainer = true,
        $scope = null,
        $currency = null,
        $precision = PriceCurrencyInterface::DEFAULT_PRECISION
    ) {
        return $this->priceCurrency->format($amount, $includeContainer, $precision, $scope, $currency);
    }

    /**
     * Get current theme id
     * @return mixed
     */
    public function getCurrentThemeId()
    {
        return $this->getConfigValue(DesignInterface::XML_PATH_THEME_ID);
    }

    /**
     * @return bool
     */
    public function isCheckoutScope()
    {
        /** @var Http $request */
        $request = $this->_request;
        $scopes  = ['checkout_cart_index', 'checkout_index_index', 'onestepcheckout_index_index'];

        return in_array($request->getFullActionName(), $scopes, true);
    }
}
