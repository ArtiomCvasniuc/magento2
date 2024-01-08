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

namespace Mageplaza\StoreSwitcher\Api\Data;

/**
 * Interface StoreRuleInterface
 * @package Mageplaza\StoreSwitcher\Api\Data
 */
interface StoreRuleInterface
{
    /**
     * Constants for keys of data array. Identical to the name of the getter in snake case
     */
    const NAME             = 'name';
    const STATUS           = 'status';
    const PRIORITY         = 'priority';
    const COUNTRIES        = 'countries';
    const PAGE_TYPE        = 'page_type';
    const INCLUDE_PATH     = 'include_path';
    const EXCLUDE_PATH     = 'exclude_path';
    const EXCLUDE_IPS      = 'exclude_ips';
    const REDIRECT_TYPE    = 'redirect_type';
    const CHANGE_TYPE      = 'change_type';
    const STORE_REDIRECTED = 'store_redirected';
    const CURRENCY         = 'currency';
    const REDIRECT_URL     = 'redirect_url';
    const CREATED_AT       = 'created_at';
    const UPDATED_AT       = 'updated_at';

    /**
     * Get Rule Id
     *
     * @return int
     */
    public function getId();

    /**
     * @param int $ruleId
     *
     * @return $this
     */
    public function setId($ruleId);

    /**
     * Get Store Rule Name
     *
     * @return string
     */
    public function getName();

    /**
     * @param string $name
     *
     * @return $this
     */
    public function setName($name);

    /**
     * Status Store Rule
     *
     * @return int
     */
    public function getStatus();

    /**
     * @param int $status
     *
     * @return $this
     */
    public function setStatus($status);

    /**
     * Priority Store Rule
     *
     * @return int
     */
    public function getPriority();

    /**
     * @param int $priority
     *
     * @return $this
     */
    public function setPriority($priority);

    /**
     * Get countries to apply rule
     *
     * @return string
     */
    public function getCountries();

    /**
     * @param string $countries
     *
     * @return $this
     */
    public function setCountries($countries);

    /**
     * Get Page Type to apply rule
     *
     * @return string
     */
    public function getPageType();

    /**
     * @param string $type
     *
     * @return $this
     */
    public function setPageType($type);

    /**
     * Get path included to apply rule
     *
     * @return string
     */
    public function getIncludePath();

    /**
     * @param string $path
     *
     * @return $this
     */
    public function setIncludePath($path);

    /**
     * Get path excluded to apply rule
     *
     * @return string
     */
    public function getExcludePath();

    /**
     * @param string $path
     *
     * @return $this
     */
    public function setExcludePath($path);

    /**
     * Get list ips excluded to apply rule
     *
     * @return string
     */
    public function getExcludeIps();

    /**
     * @param string $ips
     *
     * @return $this
     */
    public function setExcludeIps($ips);

    /**
     * Get redirect type
     *
     * @return string
     */
    public function getRedirectType();

    /**
     * @param string $type
     *
     * @return $this
     */
    public function setRedirectType($type);

    /**
     * Get change type
     *
     * @return string
     */
    public function getChangeType();

    /**
     * @param string $type
     *
     * @return $this
     */
    public function setChangeType($type);

    /**
     * Get Store Id redirected
     *
     * @return int
     */
    public function getStoreRedirected();

    /**
     * @param int $toreId
     *
     * @return $this
     */
    public function setStoreRedirected($toreId);

    /**
     * Get Currency is changed
     *
     * @return string
     */
    public function getCurrency();

    /**
     * @param string $currency
     *
     * @return $this
     */
    public function setCurrency($currency);

    /**
     * Get Redirect Url
     *
     * @return string
     */
    public function getRedirectUrl();

    /**
     * @param string $url
     *
     * @return $this
     */
    public function setRedirectUrl($url);

    /**
     * Get store rule create at date
     *
     * @return string
     */
    public function getCreatedAt();

    /**
     * @param string $date
     *
     * @return $this
     */
    public function setCreatedAt($date);

    /**
     * Get update at date
     *
     * @return string
     */
    public function getUpdatedAt();

    /**
     * @param string $date
     *
     * @return $this
     */
    public function setUpdatedAt($date);
}
