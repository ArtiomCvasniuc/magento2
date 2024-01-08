<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2020 Amasty (https://www.amasty.com)
 * @package Amasty_InvisibleCaptcha
 */


namespace Amasty\InvisibleCaptcha\Model;

use Amasty\InvisibleCaptcha\Model\Config\Source\CaptchaVersion;
use Magento\Customer\Model\Group;

class Captcha
{
    /**
     * Google URL for checking captcha response
     */
    const GOOGLE_VERIFY_URL = 'https://www.google.com/recaptcha/api/siteverify';

    /**
     * @var \Magento\Framework\HTTP\Adapter\Curl
     */
    protected $curl;

    /**
     * @var \Magento\Customer\Model\Session
     */
    private $session;

    /**
     * @var \Amasty\Base\Model\GetCustomerIp
     */
    private $getCustomerIp;

    /**
     * @var ConfigProvider
     */
    private $configProvider;

    public function __construct(
        \Magento\Framework\HTTP\Adapter\Curl $curl,
        \Magento\Customer\Model\Session $session,
        \Amasty\Base\Model\GetCustomerIp $getCustomerIp,
        \Amasty\InvisibleCaptcha\Model\ConfigProvider $configProvider
    ) {
        $this->curl = $curl;
        $this->session = $session;
        $this->getCustomerIp = $getCustomerIp;
        $this->configProvider = $configProvider;
    }

    /**
     * Check is need to show captcha
     *
     * @return bool
     */
    public function isNeedToShowCaptcha()
    {
        if ($this->configProvider->isEnabled() && $this->configProvider->isConfigured()) {
            if ($this->session->getCustomerGroupId() == Group::NOT_LOGGED_IN_ID
                || !$this->configProvider->isEnabledForGuestsOnly()
            ) {
                if (!in_array($this->getCustomerIp->getCurrentIp(), $this->configProvider->getWhiteIps())) {
                    return true;
                }
            }
        }

        return false;
    }

    /**
     * Verification of token by Google
     *
     * @param string $token
     * @return array
     */
    public function verify($token)
    {
        $verification = [
            'success' => false,
            'error' => __('No reCaptcha token.')
        ];
        if ($token) {
            $curlParams = [
                'secret' => $this->configProvider->getSecretKey(),
                'response' => $token
            ];

            try {
                $this->curl->write(
                    \Zend_Http_Client::POST,
                    self::GOOGLE_VERIFY_URL,
                    '1.1',
                    [],
                    $curlParams
                );
                $googleResponse = $this->curl->read();
                $responseBody = \Zend_Http_Response::extractBody($googleResponse);
                $googleAnswer = \Zend_Json::decode($responseBody);
                if (array_key_exists('success', $googleAnswer)) {
                    if (isset($googleAnswer['score'])
                        && $this->configProvider->getCaptchaVersion() === CaptchaVersion::VERSION_3
                        && $googleAnswer['score'] < $this->configProvider->getCaptchaScore()
                    ) {
                        $verification['error'] = $this->configProvider->getConfigErrorMessage();
                        $verification['success'] = false;
                    } elseif ($googleAnswer['success']) {
                        $verification['success'] = true;
                    } elseif (array_key_exists('error-codes', $googleAnswer)) {
                        $verification['error'] = $this->getErrorMessage($googleAnswer['error-codes'][0]);
                    }
                }
            } catch (\Exception $e) {
                $verification['error'] = __($e->getMessage());
            }
        }

        return $verification;
    }

    private function getErrorMessage($errorCode)
    {
        $errorCodesGoogle = [
            'missing-input-secret' => __('The secret parameter is missing.'),
            'invalid-input-secret' => __('The secret parameter is invalid or malformed.'),
            'missing-input-response' => __('The response parameter is missing.'),
            'invalid-input-response' => __('The response parameter is invalid or malformed.'),
            'bad-request' => __('The request is invalid or malformed.'),
            // phpcs:ignore: Generic.Files.LineLength.TooLong
            'timeout-or-duplicate' => __('The response is no longer valid: either is too old or has been used previously.')
        ];

        if (array_key_exists($errorCode, $errorCodesGoogle)) {
            return $errorCodesGoogle[$errorCode];
        }

        return __('Something is wrong.');
    }
}
