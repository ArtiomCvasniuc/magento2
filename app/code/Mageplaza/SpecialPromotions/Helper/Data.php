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
 * @copyright   Copyright (c) Mageplaza (https://www.mageplaza.com/)
 * @license     https://www.mageplaza.com/LICENSE.txt
 */

namespace Mageplaza\SpecialPromotions\Helper;

use Mageplaza\Core\Helper\AbstractData;

/**
 * Class Data
 * @package Mageplaza\SpecialPromotions\Helper
 */
class Data extends AbstractData
{
    const CONFIG_MODULE_PATH = 'specialpromotions';

    /**
     * @param null $storeId
     *
     * @return bool
     */
    public function isDebugEnabled($storeId = null)
    {
        if (!$this->isEnabled($storeId) || !$this->getModuleConfig('develop/enabled')) {
            return false;
        }

        $whiteList = $this->getModuleConfig('develop/whitelist_ip');
        if (!$whiteList) {
            return true;
        }

        $clientIps = array_filter(array_map('trim', explode(',', $this->_request->getClientIp())));
        $clientIp = end($clientIps);
        $whiteList = explode(',', $whiteList);
        foreach ($whiteList as $ip) {
            if ($this->checkIp($clientIp, $ip)) {
                return true;
            }
        }

        return false;
    }

    /**
     * Check Ip
     *
     * @param $ip
     * @param $range
     *
     * @return bool
     */
    public function checkIp($ip, $range)
    {
        if (strpos($range, '*') !== false) {
            $low = $high = $range;
            if (strpos($range, '-') !== false) {
                list($low, $high) = explode('-', $range, 2);
            }
            $low = str_replace('*', '0', $low);
            $high = str_replace('*', '255', $high);
            $range = $low . '-' . $high;
        }
        if (strpos($range, '-') !== false) {
            list($low, $high) = explode('-', $range, 2);

            return $this->ipCompare($ip, $low, 1) && $this->ipcompare($ip, $high, -1);
        }

        return $this->ipCompare($ip, $range);
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
}
