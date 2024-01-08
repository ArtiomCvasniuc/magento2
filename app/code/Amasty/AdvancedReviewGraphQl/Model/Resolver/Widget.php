<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2020 Amasty (https://www.amasty.com)
 * @package Amasty_AdvancedReviewGraphQl
 */


declare(strict_types=1);

namespace Amasty\AdvancedReviewGraphQl\Model\Resolver;

use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\GraphQl\Schema\Type\ResolveInfo;
use Magento\Framework\GraphQl\Config\Element\Field;
use Magento\Framework\GraphQl\Query\ResolverInterface;

class Widget implements ResolverInterface
{
    /**
     * @var \Amasty\AdvancedReview\Block\Widget\Reviews
     */
    private $reviewsWidget;

    /**
     * @var \Amasty\Base\Model\Serializer
     */
    private $serializer;

    /**
     * @var \Magento\Widget\Model\ResourceModel\Widget\Instance\Collection
     */
    private $widgetCollection;

    /**
     * @var \Magento\Catalog\Helper\Product\Compare
     */
    private $compareHelper;

    public function __construct(
        \Amasty\AdvancedReview\Block\Widget\Reviews $reviewsWidget,
        \Amasty\Base\Model\Serializer $serializer,
        \Magento\Catalog\Helper\Product\Compare $compareHelper,
        \Magento\Widget\Model\ResourceModel\Widget\Instance\Collection $widgetCollection
    ) {
        $this->reviewsWidget = $reviewsWidget;
        $this->widgetCollection = $widgetCollection;
        $this->serializer = $serializer;
        $this->compareHelper = $compareHelper;
    }

    /**
     * @param Field $field
     * @param \Magento\Framework\GraphQl\Query\Resolver\ContextInterface $context
     * @param ResolveInfo $info
     * @param array|null $value
     * @param array|null $args
     * @return array|\Magento\Framework\GraphQl\Query\Resolver\Value|mixed
     */
    public function resolve(
        Field $field,
        $context,
        ResolveInfo $info,
        array $value = null,
        array $args = null
    ) {
        try {
            $this->setWidgetData($args['widgetId'], $args['storeId']);
        } catch (\Exception $e) {
            $data['title'] = $e->getMessage();
            return $data;
        }

        $data = $this->reviewsWidget->getData();
        $products = $this->reviewsWidget->getReviewsCollection()->getItems();
        foreach ($products as $product) {
            $data['items'][] = $this->prepareData($product);
        }

        return $data;
    }

    /**
     * @param int $id
     * @param int $storeId
     * @throws \Exception
     */
    private function setWidgetData(int $id, int $storeId)
    {
        $widget = $this->widgetCollection->getItemById($id);
        if (!$this->validateStore($widget, $storeId)) {
            throw new LocalizedException(__('Wrong parameter storeId.'));
        }
        $widgetParams = $widget->getWidgetParameters();

        $this->reviewsWidget->setNameInLayout('advanced_review_widget');
        $this->reviewsWidget->setData('store_id', $storeId);
        $this->reviewsWidget->setData($widgetParams);
        $this->reviewsWidget->setData(
            'conditions',
            $this->serializer->serialize($this->reviewsWidget->getConditions())
        );
    }

    /**
     * @param $widget
     * @param int $storeId
     * @return bool
     */
    private function validateStore($widget, int $storeId)
    {
        return in_array(\Magento\Store\Model\Store::DEFAULT_STORE_ID, $widget->getStoreIds())
            || in_array($storeId, $widget->getStoreIds());
    }

    /**
     * @param $product
     * @return array
     */
    private function prepareData($review)
    {
        $product = $this->reviewsWidget->getProduct($review);
        $ratingsVotes = $review->getRatingVotes();
        $format = $this->reviewsWidget->getDateFormat() ? : \IntlDateFormatter::MEDIUM;
        $vote = $ratingsVotes->getFirstItem();

        $data['model'] = $product;
        $data['productUrl'] = $product->getProductUrl();
        $data['name'] = $product->getName();
        $data['voteRatingCode'] = $vote->getRatingCode();
        $data['voteRatingValue'] = $vote->getValue();
        $data['recommendedHtml'] = $this->reviewsWidget->getAdvancedHelper()->getRecommendedHtml($review);
        $data['reviewBy'] = $review->getNickname();
        $data['reviewMessage'] = $this->reviewsWidget->getReviewMessage($review->getDetail());
        $data['date'] = $this->reviewsWidget->formatDate($review->getCreatedAt(), $format);

        return $data;
    }
}
