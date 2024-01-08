<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2020 Amasty (https://www.amasty.com)
 * @package Amasty_AdvancedReviewGraphQl
 */


declare(strict_types=1);

namespace Amasty\AdvancedReviewGraphQl\Model\Resolver;

use Magento\Framework\GraphQl\Schema\Type\ResolveInfo;
use Magento\Framework\GraphQl\Config\Element\Field;
use Magento\Framework\GraphQl\Query\ResolverInterface;

class Setting implements ResolverInterface
{
    /**
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    private $storeManager;

    /**
     * @var \Amasty\AdvancedReview\Helper\Config
     */
    private $settings;

    public function __construct(
        \Amasty\AdvancedReview\Helper\Config $settings,
        \Magento\Store\Model\StoreManagerInterface $storeManager
    ) {
        $this->storeManager = $storeManager;
        $this->settings = $settings;
    }

    /**
     * @param Field $field
     * @param \Magento\Framework\GraphQl\Query\Resolver\ContextInterface $context
     * @param ResolveInfo $info
     * @param array|null $value
     * @param array|null $args
     * @return array|\Magento\Framework\GraphQl\Query\Resolver\Value|mixed
     * @throws \Exception
     */
    public function resolve(
        Field $field,
        $context,
        ResolveInfo $info,
        array $value = null,
        array $args = null
    ) {
        try {
            if (isset($args['storeId'])) {
                $this->storeManager->setCurrentStore($args['storeId']);
            }

            return $this->getData();
        } catch (\Exception $e) {
            return ['error' => __('Wrong post id.')];
        }
    }

    /**
     * @return array
     */
    private function getData()
    {
        return [
            'isGDPREnabled' => $this->settings->isGDPREnabled(),
            'getGDPRText' => $this->settings->getGDPRText(),
            'getReviewImageWidth' => $this->settings->getReviewImageWidth(),
            'isAllowReminder' => $this->settings->isReminderEnabled(),
            'isCommentsEnabled' => $this->settings->isCommentsEnabled(),
            'isGuestCanComment' => $this->settings->isGuestCanComment(),
            'isReminderEnabled' => $this->settings->isReminderEnabled(),
        ];
    }
}
