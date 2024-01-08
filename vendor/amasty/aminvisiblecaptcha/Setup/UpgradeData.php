<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2020 Amasty (https://www.amasty.com)
 * @package Amasty_InvisibleCaptcha
 */


namespace Amasty\InvisibleCaptcha\Setup;

use Amasty\InvisibleCaptcha\Model\Config\Source\DefaultForms;
use Amasty\InvisibleCaptcha\Model\ConfigProvider;
use Magento\Framework\App\Config\ReinitableConfigInterface;
use Magento\Framework\App\Config\Storage\WriterInterface;
use Magento\Framework\Setup\UpgradeDataInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\ModuleDataSetupInterface;

class UpgradeData implements UpgradeDataInterface
{
    /**
     * @var \Magento\Framework\App\Config\ScopeConfigInterface
     */
    private $scopeConfig;

    /**
     * @var WriterInterface
     */
    private $configWriter;

    /**
     * @var DefaultForms
     */
    private $formsSource;

    /**
     * @var ConfigProvider
     */
    private $configProvider;

    public function __construct(
        ReinitableConfigInterface $scopeConfig,
        WriterInterface $configWriter,
        DefaultForms $formsSource,
        ConfigProvider $configProvider
    ) {
        $this->scopeConfig = $scopeConfig;
        $this->configWriter = $configWriter;
        $this->formsSource = $formsSource;
        $this->configProvider = $configProvider;
    }

    /**
     * Upgrades data for the InvisibleCaptcha module
     *
     * @param ModuleDataSetupInterface $setup
     * @param ModuleContextInterface   $context
     */
    public function upgrade(ModuleDataSetupInterface $setup, ModuleContextInterface $context)
    {
        $setup->startSetup();
        if ($context->getVersion() && version_compare($context->getVersion(), '2.0.0', '<')) {
            $this->moveSetting(
                'aminvisiblecaptcha/general/captchaUrls',
                'aminvisiblecaptcha/advanced/captchaUrls'
            );
            $this->moveSetting(
                'aminvisiblecaptcha/general/captchaSelectors',
                'aminvisiblecaptcha/advanced/captchaSelectors'
            );
        }

        if ($context->getVersion() && version_compare($context->getVersion(), '2.2.2', '<')) {
            $this->reorganizeSettings();
        }

        $setup->endSetup();
    }

    protected function reorganizeSettings()
    {
        $settingMap = [
            'general/captchaKey'      => 'setup/captchaKey',
            'general/captchaSecret'   => 'setup/captchaSecret',
            'general/captchaVersion'  => 'setup/captchaVersion',
            'general/errorMessage'    => 'setup/errorMessage',
            'general/captchaLanguage' => 'setup/captchaLanguage',
            'general/captchaKeyV3'    => 'setup/captchaKeyV3',
            'general/captchaSecretV3' => 'setup/captchaSecretV3',
            'general/captchaScore'    => 'setup/captchaScore',
            'general/badgeTheme'      => 'setup/badgeTheme',
            'general/badgePosition'   => 'setup/badgePosition',

            'advanced/enabledCaptchaForGuestsOnly' => 'general/enabledCaptchaForGuestsOnly',
            'advanced/ipWhiteList'                 => 'general/ipWhiteList',

            'advanced/captchaUrls'      => 'forms/urls',
            'advanced/captchaSelectors' => 'forms/selectors',
        ];

        foreach ($settingMap as $oldName => $newName) {
            $this->moveSetting(
                'aminvisiblecaptcha/' . $oldName,
                'aminvisiblecaptcha/' . $newName
            );
        }

        $this->scopeConfig->reinit();

        $allDefaultUrls = array_column($this->formsSource->toOptionArray(), 'value');

        if ($currentCustomUrls = $this->configProvider->getCustomUrls()) {
            // Select only forms specified in "Urls to Enable" setting
            $defaultUrlsToSelect = array_intersect($allDefaultUrls, $currentCustomUrls);
            $this->configWriter->save('aminvisiblecaptcha/forms/defaultForms', implode(',', $defaultUrlsToSelect));

            // Exclude default urls from custom urls field
            $newCustomUrls = array_diff($currentCustomUrls, $allDefaultUrls);
            $this->configWriter->save('aminvisiblecaptcha/forms/urls', implode(PHP_EOL, $newCustomUrls));
        }

        if ($currentCustomSelectors = $this->configProvider->getCustomSelectors()) {
            // Exclude default selectors from custom selectors field
            $newCustomSelectors = array_filter(
                $currentCustomSelectors,
                function ($selector) use ($allDefaultUrls) {
                    foreach ($allDefaultUrls as $url) {
                        if (false !== strpos($selector, $url)) {
                            return false;
                        }
                    }

                    return true;
                }
            );
            $this->configWriter->save(
                'aminvisiblecaptcha/forms/selectors',
                implode(PHP_EOL, $newCustomSelectors)
            );
        }

        $this->scopeConfig->reinit();
    }

    protected function moveSetting(string $oldName, string $newName)
    {
        if ($value = $this->scopeConfig->getValue($oldName)) {
            $this->configWriter->save($newName, $value);
            $this->configWriter->delete($oldName);
        }
    }
}
