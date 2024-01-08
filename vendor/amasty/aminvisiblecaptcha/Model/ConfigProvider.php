<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2020 Amasty (https://www.amasty.com)
 * @package Amasty_InvisibleCaptcha
 */


namespace Amasty\InvisibleCaptcha\Model;

use Amasty\Base\Model\ConfigProviderAbstract;
use Amasty\InvisibleCaptcha\Model\Config\Source\CaptchaVersion;
use Amasty\InvisibleCaptcha\Model\Config\Source\Extension as ExtensionSource;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\DataObject;
use Magento\Framework\Module\Manager as ModuleManager;

/**
 * Class ConfigProvider
 *
 * Config Provider for settings Amasty Invisible Captcha extension
 */
class ConfigProvider extends ConfigProviderAbstract
{
    /**#@+
     * Constants defined for xpath of system configuration
     */
    const CONFIG_PATH_GENERAL_ENABLE_MODULE = 'general/enabledCaptcha';
    const CONFIG_PATH_GENERAL_ENABLE_FOR_GUESTS_ONLY = 'general/enabledCaptchaForGuestsOnly';
    const CONFIG_PATH_GENERAL_WHITELIST_IP = 'general/ipWhiteList';

    const CONFIG_PATH_SETUP_CAPTCHA_VERSION = 'setup/captchaVersion';
    const CONFIG_PATH_SETUP_CAPTCHA_SCORE = 'setup/captchaScore';
    const CONFIG_PATH_SETUP_CAPTCHA_ERROR_MESSAGE = 'setup/errorMessage';
    const CONFIG_PATH_SETUP_SITE_KEY = 'setup/captchaKey';
    const CONFIG_PATH_SETUP_SECRET_KEY = 'setup/captchaSecret';
    const CONFIG_PATH_SETUP_SITE_KEY_V3 = 'setup/captchaKeyV3';
    const CONFIG_PATH_SETUP_SECRET_KEY_V3 = 'setup/captchaSecretV3';
    const CONFIG_PATH_SETUP_BADGE_POSITION = 'setup/badgePosition';
    const CONFIG_PATH_SETUP_BADGE_THEME = 'setup/badgeTheme';
    const CONFIG_PATH_SETUP_LANGUAGE = 'setup/captchaLanguage';

    const CONFIG_PATH_FORMS_DEFAULT_FORMS = 'forms/defaultForms';
    const CONFIG_PATH_FORMS_URLS = 'forms/urls';
    const CONFIG_PATH_FORMS_SELECTORS = 'forms/selectors';

    const CONFIG_PATH_INTEGRATIONS = 'amasty';
    /**#@-*/

    const FORM_SELECTOR_PATTERN = 'form[action*="%s"]';

    protected $pathPrefix = 'aminvisiblecaptcha/';

    /**
     * Amasty extension URLs to validate
     *
     * @var array
     */
    private $additionalURLs = [];

    /**
     * Amasty extension form selectors
     *
     * @var array
     */
    private $additionalSelectors = [];

    public function __construct(
        ScopeConfigInterface $scopeConfig,
        ModuleManager $moduleManager,
        DataObject $extensionsData
    ) {
        parent::__construct($scopeConfig);

        foreach ($extensionsData->getData() as $configId => $data) {
            if ($this->isIntegrationEnabled($configId)
                && $moduleManager->isEnabled($data['name'])
            ) {
                $this->additionalURLs[] = $data['url'];
                $this->additionalSelectors[] = $data['selector'];
            }
        }
    }

    /**
     * @return bool
     */
    public function isEnabled()
    {
        return $this->isSetFlag(self::CONFIG_PATH_GENERAL_ENABLE_MODULE);
    }

    public function isConfigured($storeId = null)
    {
        return !empty($this->getSiteKey($storeId)) && !empty($this->getSecretKey($storeId));
    }

    /**
     * @param int|null $storeId
     *
     * @return int
     */
    public function getCaptchaVersion($storeId = null)
    {
        return (int)$this->getValue(self::CONFIG_PATH_SETUP_CAPTCHA_VERSION, $storeId);
    }

    /**
     * @param int|null $storeId
     *
     * @return float
     */
    public function getCaptchaScore($storeId = null)
    {
        return $this->getValue(self::CONFIG_PATH_SETUP_CAPTCHA_SCORE, $storeId);
    }

    /**
     * @param int|null $storeId
     *
     * @return string
     */
    public function getConfigErrorMessage($storeId = null)
    {
        return $this->getValue(self::CONFIG_PATH_SETUP_CAPTCHA_ERROR_MESSAGE, $storeId);
    }

    /**
     * @param int|null $storeId
     *
     * @return bool
     */
    public function isEnabledForGuestsOnly($storeId = null)
    {
        return (bool)$this->getValue(self::CONFIG_PATH_GENERAL_ENABLE_FOR_GUESTS_ONLY, $storeId);
    }

    /**
     * @param int|null $storeId
     *
     * @return string
     */
    public function getSiteKey($storeId = null)
    {
        $configPath = self::CONFIG_PATH_SETUP_SITE_KEY;
        if ($this->getCaptchaVersion() == CaptchaVersion::VERSION_3) {
            $configPath = self::CONFIG_PATH_SETUP_SITE_KEY_V3;
        }

        return $this->getValue($configPath, $storeId);
    }

    /**
     * @param int|null $storeId
     *
     * @return string
     */
    public function getSecretKey($storeId = null)
    {
        $configPath = self::CONFIG_PATH_SETUP_SECRET_KEY;
        if ($this->getCaptchaVersion() == CaptchaVersion::VERSION_3) {
            $configPath = self::CONFIG_PATH_SETUP_SECRET_KEY_V3;
        }

        return $this->getValue($configPath, $storeId);
    }

    /**
     * @param int|null $storeId
     *
     * @return string
     */
    public function getBadgePosition($storeId = null)
    {
        return $this->getValue(self::CONFIG_PATH_SETUP_BADGE_POSITION, $storeId);
    }

    /**
     * @param int|null $storeId
     *
     * @return string
     */
    public function getBadgeTheme($storeId = null)
    {
        return $this->getValue(self::CONFIG_PATH_SETUP_BADGE_THEME, $storeId);
    }

    /**
     * @param int|null $storeId
     *
     * @return string
     */
    public function getLanguage($storeId = null)
    {
        $language = $this->getValue(self::CONFIG_PATH_SETUP_LANGUAGE, $storeId);
        if ($language && 7 > mb_strlen($language)) {
            $language = '&hl=' . $language;
        } else {
            $language = '';
        }

        return $language;
    }

    /**
     * @param int|null $storeId
     *
     * @return array
     */
    public function getWhiteIps($storeId = null): array
    {
        return $this->explode($this->getValue(self::CONFIG_PATH_GENERAL_WHITELIST_IP, $storeId));
    }

    /**
     * @param int|null $storeId
     *
     * @return array
     */
    public function getCustomSelectors($storeId = null): array
    {
        return $this->explode($this->getValue(self::CONFIG_PATH_FORMS_SELECTORS, $storeId));
    }

    /**
     * @param int|null $storeId
     *
     * @return array
     */
    public function getCustomUrls($storeId = null): array
    {
        return $this->explode($this->getValue(self::CONFIG_PATH_FORMS_URLS, $storeId));
    }

    public function getEnabledDefaultForms($storeId = null): array
    {
        return $this->explode($this->getValue(self::CONFIG_PATH_FORMS_DEFAULT_FORMS, $storeId));
    }

    public function isIntegrationEnabled(string $moduleConfigId, $storeId = null): bool
    {
        $integrationStatus = (int)$this->getValue(self::CONFIG_PATH_INTEGRATIONS . '/' . $moduleConfigId, $storeId);

        return $integrationStatus === ExtensionSource::INTEGRATION_ENABLED;
    }

    public function getAllFormSelectors($storeId = null): array
    {
        $defaultFormsSelectors = array_map(
            function ($url) {
                return sprintf(self::FORM_SELECTOR_PATTERN, $url);
            },
            $this->getEnabledDefaultForms($storeId)
        );

        return array_merge(
            $this->getCustomSelectors($storeId),
            $defaultFormsSelectors,
            $this->additionalSelectors
        );
    }

    public function getAllUrls($storeId = null): array
    {
        return array_merge(
            $this->getCustomUrls($storeId),
            $this->getEnabledDefaultForms($storeId),
            $this->additionalURLs
        );
    }

    /**
     * @param string|null $string
     * @return array
     */
    protected function explode($string): array
    {
        $string = trim($string);

        return !empty($string)
            ? preg_split('|\s*[\r\n,]+\s*|', $string, -1, PREG_SPLIT_NO_EMPTY)
            : [];
    }
}
