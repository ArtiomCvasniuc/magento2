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

namespace Mageplaza\StoreSwitcher\Helper;

use Magento\Framework\App\ActionInterface;
use Magento\Framework\App\Helper\Context;
use Magento\Framework\Exception\InputException;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\HTTP\Header;
use Magento\Framework\ObjectManagerInterface;
use Magento\Framework\Session\SessionManagerInterface;
use Magento\Framework\Stdlib\Cookie\CookieMetadataFactory;
use Magento\Framework\Stdlib\Cookie\CookieSizeLimitReachedException;
use Magento\Framework\Stdlib\Cookie\FailureToSendException;
use Magento\Framework\Stdlib\CookieManagerInterface;
use Magento\Framework\UrlInterface;
use Magento\Store\Api\Data\StoreInterface;
use Magento\Store\Model\StoreManagerInterface;
use Mageplaza\Core\Helper\AbstractData;
use Mageplaza\GeoIP\Helper\Address as GeoIpAddress;
use Mageplaza\StoreSwitcher\Model\Config\Source\PageType;
use Mageplaza\StoreSwitcher\Model\Rule;
use Mageplaza\StoreSwitcher\Model\RuleFactory;

/**
 * Class Data
 * @package Mageplaza\StoreSwitcher\Helper
 */
class Data extends AbstractData
{
    const CONFIG_MODULE_PATH = 'mpstoreswitcher';

    /**
     * @var RuleFactory
     */
    protected $_ruleFactory;

    /**
     * @var GeoIpAddress
     */
    protected $_geoIp;

    /**
     * @var CookieManagerInterface
     */
    protected $_cookieManager;

    /**
     * @var CookieMetadataFactory
     */
    protected $_cookieMetadataFactory;

    /**
     * @var SessionManagerInterface
     */
    protected $_sessionManager;

    /**
     * @var UrlInterface
     */
    protected $urlBuilder;

    /**
     * @var Header
     */
    protected $httpHeader;

    /**
     * Data constructor.
     *
     * @param Context $context
     * @param ObjectManagerInterface $objectManager
     * @param StoreManagerInterface $storeManager
     * @param GeoIpAddress $geoIp
     * @param CookieManagerInterface $cookieManager
     * @param CookieMetadataFactory $cookieMetadataFactory
     * @param SessionManagerInterface $sessionManager
     * @param RuleFactory $ruleFactory
     * @param UrlInterface $urlBuilder
     * @param Header $httpHeader
     */
    public function __construct(
        Context $context,
        ObjectManagerInterface $objectManager,
        StoreManagerInterface $storeManager,
        GeoIpAddress $geoIp,
        CookieManagerInterface $cookieManager,
        CookieMetadataFactory $cookieMetadataFactory,
        SessionManagerInterface $sessionManager,
        RuleFactory $ruleFactory,
        UrlInterface $urlBuilder,
        Header $httpHeader
    ) {
        $this->_geoIp                 = $geoIp;
        $this->_cookieManager         = $cookieManager;
        $this->_cookieMetadataFactory = $cookieMetadataFactory;
        $this->_sessionManager        = $sessionManager;
        $this->_ruleFactory           = $ruleFactory;
        $this->urlBuilder             = $urlBuilder;
        $this->httpHeader             = $httpHeader;

        parent::__construct($context, $objectManager, $storeManager);
    }

    /**
     * @param StoreInterface $store
     *
     * @return string
     */
    public function getTargetStoreRedirectUrl($store)
    {
        return $this->urlBuilder->getUrl(
            'stores/store/redirect',
            [
                '___store'                              => $store->getCode(),
                '___from_store'                         => $this->getStore()->getCode(),
                ActionInterface::PARAM_NAME_URL_ENCODED => $this->encodeUrl(
                    $store->getCurrentUrl(false)
                ),
            ]
        );
    }

    /**
     * @param null $storeId
     *
     * @return bool|StoreInterface
     */
    public function getStore($storeId = null)
    {
        try {
            return $this->storeManager->getStore($storeId);
        } catch (NoSuchEntityException $e) {
            $this->_logger->critical($e->getMessage());
        }

        return false;
    }

    /**
     * @param $url
     *
     * @return string
     */
    public function encodeUrl($url)
    {
        return strtr(base64_encode($url), '+/=', '-_,');
    }

    /**
     * @param Rule $rule
     *
     * @return bool
     */
    public function checkCountry($rule)
    {
        $geoIpData = $this->_geoIp->getGeoIpData();
        if ($geoIpData) {
            $currentCountry = $geoIpData['country_id'];
            $countries      = explode(',', $rule->getCountries());

            return in_array($currentCountry, $countries, true);
        }

        return false;
    }

    /**
     * @param Rule $rule
     *
     * @return bool
     */
    public function checkPageType($rule)
    {
        $type       = $rule->getPageType();
        $actionName = $this->_request->getFullActionName();

        if ($type === PageType::HOME_PAGE) {
            return $actionName === 'cms_index_index';
        }

        if ($type === PageType::SPECIFIC_PAGES) {
            $includePaths = $rule->getIncludePath();
            $excludePaths = $rule->getExcludePath();

            if ($this->checkPaths($excludePaths)) {
                return false;
            }

            if (!$this->checkPaths($includePaths)) {
                return false;
            }

            if ($this->checkPaths($includePaths) && !$this->checkPaths($excludePaths)) {
                return true;
            }
        }

        return true;
    }

    /**
     * @param $paths
     *
     * @return bool
     */
    public function checkPaths($paths)
    {
        if ($paths) {
            $currentPath = $this->_request->getRequestUri();

            $arrayPaths = explode("\n", $paths);
            $pathsUrl   = array_map('trim', $arrayPaths);
            foreach ($pathsUrl as $path) {
                if ($path && strpos($currentPath, $path) !== false) {
                    return true;
                }
            }
        }

        return false;
    }

    /**
     * @param Rule $rule
     *
     * @return bool
     */
    public function checkExcludeIPs($rule)
    {
        $excludeIps = explode("\n", $rule->getExcludeIps());
        $currentIp  = $this->_geoIp->getIpAddress();

        foreach ($excludeIps as $excludeIp) {
            if ($this->checkIp($currentIp, $excludeIp)) {
                return false;
            }
        }

        return true;
    }

    /**
     * @param $ipAddr
     * @param $range
     *
     * @return bool
     */
    public function checkIp($ipAddr, $range)
    {
        if (strpos($range, '*') !== false) {
            $high = $range;
            $low  = $high;
            if (strpos($range, '-') !== false) {
                [$low, $high] = explode('-', $range, 2);
            }
            $low   = str_replace('*', '0', $low);
            $high  = str_replace('*', '255', $high);
            $range = $low . '-' . $high;
        }
        if (strpos($range, '-') !== false) {
            [$low, $high] = explode('-', $range, 2);

            return $this->ipCompare($ipAddr, $low, 1) && $this->ipcompare($ipAddr, $high, -1);
        }

        return $this->ipCompare($ipAddr, $range);
    }

    /**
     * @param $ip1
     * @param $ip2
     * @param int $op
     *
     * @return bool
     */
    private function ipCompare($ip1, $ip2, $op = 0)
    {
        $ip1Arr = explode('.', $ip1);
        $ip2Arr = explode('.', $ip2);

        for ($i = 0; $i < 4; $i++) {
            if ($ip1Arr[$i] < $ip2Arr[$i]) {
                return ($op === -1);
            }
            if ($ip1Arr[$i] > $ip2Arr[$i]) {
                return ($op === 1);
            }
        }

        return ($op === 0);
    }

    /**
     * Get Matching Rule
     *
     * @return mixed
     */
    public function getMatchingRule()
    {
        $rules = $this->_ruleFactory->create()->getCollection()->setOrder('priority', 'ASC');

        /** @var Rule $rule */
        foreach ($rules as $rule) {
            if ($rule->getStatus() &&
                $this->checkCountry($rule) &&
                $this->checkPageType($rule) && $this->checkExcludeIPs($rule) &&
                $this->checkUserAgents($rule)
            ) {
                return $rule;
            }
        }

        return [];
    }

    /**
     * @param Rule $rule
     *
     * @return bool
     */
    public function checkUserAgents($rule)
    {
        $userAgent = $this->httpHeader->getHttpUserAgent();
        $botList   = explode(',', $rule->getIgnoreUserAgents());

        if (!empty($userAgent) && count($botList)) {
            foreach ($botList as $bot) {
                if (stripos($userAgent, $bot) !== false) {
                    return false;
                }
            }
        }

        return true;
    }

    /**
     * @param $name
     *
     * @return null|string
     */
    public function getCookieByName($name)
    {
        return $this->_cookieManager->getCookie($name);
    }

    /**
     * @param $name
     * @param $value
     * @param int $duration
     *
     * @throws InputException
     * @throws CookieSizeLimitReachedException
     * @throws FailureToSendException
     */
    public function setCookie($name, $value, $duration = 86400)
    {
        $metadata = $this->_cookieMetadataFactory
            ->createPublicCookieMetadata()
            ->setDuration($duration)
            ->setPath($this->_sessionManager->getCookiePath())
            ->setDomain($this->_sessionManager->getCookieDomain());

        $this->_cookieManager->setPublicCookie(
            $name,
            $value,
            $metadata
        );
    }

    /**
     * @param $name
     *
     * @throws InputException
     * @throws FailureToSendException
     */
    public function deleteCookieByName($name)
    {
        $this->_cookieManager->deleteCookie(
            $name,
            $this->_cookieMetadataFactory
                ->createCookieMetadata()
                ->setPath($this->_sessionManager->getCookiePath())
                ->setDomain($this->_sessionManager->getCookieDomain())
        );
    }

    /**
     * Get Allow visitors save switched store view config
     *
     * @return mixed
     */
    public function getSaveSwitchedStoreConfig()
    {
        return $this->getConfigGeneral('save_switched_store');
    }

    /**
     * Get Http host
     *
     * @return mixed
     */
    public function getHttpHost()
    {
        return str_replace('.', '_', $this->_getRequest()->getHttpHost());
    }
}
