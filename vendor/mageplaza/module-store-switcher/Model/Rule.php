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

namespace Mageplaza\StoreSwitcher\Model;

use Magento\Framework\Model\AbstractModel;
use Mageplaza\StoreSwitcher\Api\Data\StoreRuleInterface;

/**
 * Class Rule
 * @package Mageplaza\StoreSwitcher\Model
 */
class Rule extends AbstractModel implements StoreRuleInterface
{
    /**
     * Cache tag
     *
     * @var string
     */
    const CACHE_TAG = 'mageplaza_storeswitcher_rules';

    /**
     * Cache tag
     *
     * @var string
     */
    protected $_cacheTag = 'mageplaza_storeswitcher_rules';

    /**
     * Event prefix
     *
     * @var string
     */
    protected $_eventPrefix = 'mageplaza_storeswitcher_rules';

    /**
     * Initialize resource model
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init(ResourceModel\Rule::class);
    }

    /**
     * Get identities
     *
     * @return array
     */
    public function getIdentities()
    {
        return [self::CACHE_TAG . '_' . $this->getId()];
    }

    /**
     * Get Store Rule Name
     *
     * @return string
     */
    public function getName()
    {
        return $this->_getData(self::NAME);
    }

    /**
     * @param string $name
     *
     * @return $this
     */
    public function setName($name)
    {
        return $this->setData(self::NAME, $name);
    }

    /**
     * Status Store Rule
     *
     * @return int
     */
    public function getStatus()
    {
        return $this->_getData(self::STATUS);
    }

    /**
     * @param int $status
     *
     * @return $this
     */
    public function setStatus($status)
    {
        return $this->setData(self::STATUS, $status);
    }

    /**
     * Priority Store Rule
     *
     * @return int
     */
    public function getPriority()
    {
        return $this->_getData(self::PRIORITY);
    }

    /**
     * @param int $priority
     *
     * @return $this
     */
    public function setPriority($priority)
    {
        return $this->setData(self::PRIORITY, $priority);
    }

    /**
     * Get countries to apply rule
     *
     * @return string
     */
    public function getCountries()
    {
        return $this->_getData(self::COUNTRIES);
    }

    /**
     * @param string $countries
     *
     * @return $this
     */
    public function setCountries($countries)
    {
        return $this->setData(self::COUNTRIES, $countries);
    }

    /**
     * Get Page Type to apply rule
     *
     * @return string
     */
    public function getPageType()
    {
        return $this->_getData(self::PAGE_TYPE);
    }

    /**
     * @param string $type
     *
     * @return $this
     */
    public function setPageType($type)
    {
        return $this->setData(self::PAGE_TYPE, $type);
    }

    /**
     * Get path included to apply rule
     *
     * @return string
     */
    public function getIncludePath()
    {
        return $this->_getData(self::INCLUDE_PATH);
    }

    /**
     * @param string $path
     *
     * @return $this
     */
    public function setIncludePath($path)
    {
        return $this->setData(self::INCLUDE_PATH, $path);
    }

    /**
     * Get path excluded to apply rule
     *
     * @return string
     */
    public function getExcludePath()
    {
        return $this->_getData(self::EXCLUDE_PATH);
    }

    /**
     * @param string $path
     *
     * @return $this
     */
    public function setExcludePath($path)
    {
        return $this->setData(self::EXCLUDE_PATH, $path);
    }

    /**
     * Get list ips excluded to apply rule
     *
     * @return string
     */
    public function getExcludeIps()
    {
        return $this->_getData(self::EXCLUDE_IPS);
    }

    /**
     * @param string $ips
     *
     * @return $this
     */
    public function setExcludeIps($ips)
    {
        return $this->setData(self::EXCLUDE_IPS, $ips);
    }

    /**
     * Get redirect type
     *
     * @return string
     */
    public function getRedirectType()
    {
        return $this->_getData(self::REDIRECT_TYPE);
    }

    /**
     * @param string $type
     *
     * @return $this
     */
    public function setRedirectType($type)
    {
        return $this->setData(self::REDIRECT_TYPE, $type);
    }

    /**
     * Get change type
     *
     * @return string
     */
    public function getChangeType()
    {
        return $this->_getData(self::CHANGE_TYPE);
    }

    /**
     * @param string $type
     *
     * @return $this
     */
    public function setChangeType($type)
    {
        return $this->setData(self::CHANGE_TYPE, $type);
    }

    /**
     * Get Store Id redirected
     *
     * @return int
     */
    public function getStoreRedirected()
    {
        return $this->_getData(self::STORE_REDIRECTED);
    }

    /**
     * @param int $toreId
     *
     * @return $this
     */
    public function setStoreRedirected($toreId)
    {
        return $this->setData(self::STORE_REDIRECTED, $toreId);
    }

    /**
     * Get Currency is changed
     *
     * @return string
     */
    public function getCurrency()
    {
        return $this->_getData(self::CURRENCY);
    }

    /**
     * @param string $currency
     *
     * @return $this
     */
    public function setCurrency($currency)
    {
        return $this->setData(self::CURRENCY, $currency);
    }

    /**
     * Get Redirect Url
     *
     * @return string
     */
    public function getRedirectUrl()
    {
        return $this->_getData(self::REDIRECT_URL);
    }

    /**
     * @param string $url
     *
     * @return $this
     */
    public function setRedirectUrl($url)
    {
        return $this->setData(self::REDIRECT_URL, $url);
    }

    /**
     * Get store rule create at date
     *
     * @return string
     */
    public function getCreatedAt()
    {
        return $this->_getData(self::CREATED_AT);
    }

    /**
     * @param string $date
     *
     * @return $this
     */
    public function setCreatedAt($date)
    {
        return $this->setData(self::CREATED_AT, $date);
    }

    /**
     * Get update at date
     *
     * @return string
     */
    public function getUpdatedAt()
    {
        return $this->_getData(self::UPDATED_AT);
    }

    /**
     * @param string $date
     *
     * @return $this
     */
    public function setUpdatedAt($date)
    {
        return $this->setData(self::UPDATED_AT, $date);
    }
}
