<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2020 Amasty (https://www.amasty.com)
 * @package Amasty_AdvancedReview
 */


namespace Amasty\AdvancedReview\Model\OptionSource\Reminder;

use Magento\Framework\Option\ArrayInterface;

class Status implements ArrayInterface
{
    const WAITING = 0;

    const SENT = 1;

    const SENT_WITH_COUPON = 99;

    const FAILED = 2;

    const CANCELED = 3;

    const UNSUBSCRIBED = 4;

    const DISABLED_FOR_GROUP = 5;

    /**
     * @return array
     */
    public function toOptionArray()
    {
        return [
            ['value' => self::WAITING, 'label'=> __('Waiting for Sending')],
            ['value' => self::SENT, 'label'=> __('Sent Successfully')],
            ['value' => self::FAILED, 'label'=> __('Sending Failed')],
            ['value' => self::CANCELED, 'label'=> __('Canceled')],
            ['value' => self::UNSUBSCRIBED, 'label'=> __('Email was not sent. Customer was unsubscribed.')],
            [
                'value' => self::DISABLED_FOR_GROUP,
                'label'=> __(
                    'Email was not sent. Customer belongs to Customer Group disabled in Review Reminder settings.'
                )
            ]
        ];
    }
}
