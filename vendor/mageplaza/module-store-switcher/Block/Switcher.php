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

namespace Mageplaza\StoreSwitcher\Block;

use Magento\Framework\Phrase;
use Magento\Framework\View\Element\Template;
use Magento\Framework\View\Element\Template\Context;
use Magento\Store\Api\Data\StoreInterface;
use Mageplaza\StoreSwitcher\Helper\Data as HelperData;
use Mageplaza\StoreSwitcher\Model\Config\Source\ActionType;
use Mageplaza\StoreSwitcher\Model\Config\Source\ChangeType;

/**
 * Class Switcher
 * @package Mageplaza\StoreSwitcher\Block
 */
class Switcher extends Template
{
    /**
     * @var HelperData
     */
    protected $_helperData;

    /**
     * Switcher constructor.
     *
     * @param Context $context
     * @param HelperData $helperData
     * @param array $data
     */
    public function __construct(
        Context $context,
        HelperData $helperData,
        array $data = []
    ) {
        $this->_helperData = $helperData;

        parent::__construct($context, $data);
    }

    /**
     * Get Country name of store switched
     *
     * @return string
     */
    public function getSwitcherCountryLabel()
    {
        return $this->getStoreSwitcher()->getName();
    }

    /**
     * @return StoreInterface
     */
    public function getStoreSwitcher()
    {
        $rule = $this->_helperData->getMatchingRule();

        return $this->getStore($rule->getStoreRedirected());
    }

    /**
     * @return string
     */
    public function getStoreSwitcherUrl()
    {
        $storeSwitch = $this->getStoreSwitcher();

        return $this->_helperData->getTargetStoreRedirectUrl($storeSwitch);
    }

    /**
     * Get label Redirect popup
     *
     * @return Phrase
     */
    public function getPopupSwitcherLabel()
    {
        $currentCountry = $this->getStore()->getName();

        return __(
            'You are in %1, would you like to switch to the %2?',
            $currentCountry,
            $this->getSwitcherCountryLabel()
        );
    }

    /**
     * Get Allow visitors save switched store view config
     *
     * @return mixed
     */
    public function getSaveSwitchedStoreConfig()
    {
        return $this->_helperData->getSaveSwitchedStoreConfig();
    }

    /**
     * Check to show redirect popup
     *
     * @return bool
     */
    public function checkRedirectPopup()
    {
        $rule = $this->_helperData->getMatchingRule();

        return ($rule &&
            $rule->getRedirectType() == ActionType::REDIRECT_STORE_CURRENCY &&
            $rule->getChangeType() == ChangeType::MANUALLY &&
            $rule->getStoreRedirected() &&
            $rule->getStoreRedirected() != $this->getStore()->getId()
        );
    }

    /**
     * @return string
     */
    public function getStoreSwitcherCode()
    {
        $rule = $this->_helperData->getMatchingRule();

        return $this->getStore($rule->getStoreRedirected())->getCode();
    }

    /**
     * Get data save popup
     *
     * @return string
     */
    public function getDataSave()
    {
        $data = [
            'lastStore' => $this->getStore()->getCode()
        ];

        return HelperData::jsonEncode($data);
    }

    /**
     * @param null $storeId
     *
     * @return bool|StoreInterface
     */
    public function getStore($storeId = null)
    {
        return $this->_helperData->getStore($storeId);
    }
}
