<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2020 Amasty (https://www.amasty.com)
 * @package Amasty_InvisibleCaptcha
 */


namespace Amasty\InvisibleCaptcha\Model\Config\Source;

use Magento\Framework\Data\OptionSourceInterface;

class Extension implements OptionSourceInterface
{
    const EXTENSION_NOT_INSTALLED = -1;
    const INTEGRATION_DISABLED = 0;
    const INTEGRATION_ENABLED = 1;

    /**
     * @var \Magento\Framework\Module\Manager
     */
    private $moduleManager;

    /**
     * @var string
     */
    private $extension = '';

    /**
     * Extension constructor.
     *
     * @param \Magento\Framework\Module\Manager $moduleManager
     * @param string                            $moduleName
     */
    public function __construct(
        \Magento\Framework\Module\Manager $moduleManager,
        $moduleName = ''
    ) {
        $this->moduleManager = $moduleManager;
        $this->extension = $moduleName;
    }

    /**
     * Options getter
     *
     * @return array
     */
    public function toOptionArray()
    {
        if ($this->moduleManager->isEnabled($this->extension)) {
            return [
                ['value' => self::INTEGRATION_DISABLED, 'label' => __('No')],
                ['value' => self::INTEGRATION_ENABLED, 'label' => __('Yes')],
            ];
        }

        return [['value' => self::EXTENSION_NOT_INSTALLED, 'label' => __('Not Installed')]];
    }
}
