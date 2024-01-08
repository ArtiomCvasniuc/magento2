<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2020 Amasty (https://www.amasty.com)
 * @package Amasty_InvisibleCaptcha
 */


namespace Amasty\InvisibleCaptcha\Model\Config\Source;

use Magento\Framework\Data\OptionSourceInterface;

class CaptchaVersion implements OptionSourceInterface
{
    const VERSION_2 = 2;
    const VERSION_3 = 3;

    /**
     * Get options
     *
     * @return array
     */
    public function toOptionArray()
    {
        return [
            ['value' => self::VERSION_2, 'label'=> __('Version 2')],
            ['value' => self::VERSION_3, 'label'=> __('Version 3')]
        ];
    }
}
