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

namespace Mageplaza\QuickCart\Block\Cart;

use Magento\Framework\View\Design\Theme\ThemeProviderInterface;
use Magento\Framework\View\Element\Template;
use Magento\Framework\View\Element\Template\Context;
use Mageplaza\QuickCart\Helper\Data;

/**
 * Class Design
 * @package Mageplaza\QuickCart\Block\Cart
 */
class Design extends Template
{
    /**
     * @var Data
     */
    private $helper;

    /**
     * @var ThemeProviderInterface
     */
    private $themeProviderInterface;

    /**
     * Design constructor.
     *
     * @param Context $context
     * @param Data $helper
     * @param ThemeProviderInterface $themeProviderInterface
     * @param array $data
     */
    public function __construct(
        Context $context,
        Data $helper,
        ThemeProviderInterface $themeProviderInterface,
        array $data = []
    ) {
        $this->helper                 = $helper;
        $this->themeProviderInterface = $themeProviderInterface;

        parent::__construct($context, $data);
    }

    /**
     * @return Data
     */
    public function getHelper()
    {
        return $this->helper;
    }

    /**
     * @return string
     */
    public function getCurrentTheme()
    {
        return $this->themeProviderInterface->getThemeById($this->getHelper()->getCurrentThemeId())->getCode();
    }
}
