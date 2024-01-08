<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2020 Amasty (https://www.amasty.com)
 * @package Amasty_AdvancedReview
 */


namespace Amasty\AdvancedReview\Block\Widget\ProductReviews;

use Amasty\AdvancedReview\Helper\BlockHelper;
use Amasty\AdvancedReview\Model\ResourceModel\Review\Collection as ReviewCollection;
use Amasty\AdvancedReview\Model\Widget\ProductReviewsWidgetFactory;
use Magento\Framework\View\Element\Template;

class ReviewsList extends \Magento\Framework\View\Element\Template
{
    const MAX_DESCRIPTION_SIZE = 130;

    const TEMPLATE = 'Amasty_AdvancedReview::widget/product_reviews/components/review_list.phtml';

    /**
     * @var BlockHelper
     */
    private $blockHelper;

    /**
     * @var \Magento\Review\Model\ResourceModel\Review\Collection
     */
    private $reviewCollection = null;

    /**
     * @var ProductReviewsWidgetFactory
     */
    private $productReviewsWidget;

    public function __construct(
        Template\Context $context,
        BlockHelper $blockHelper,
        ProductReviewsWidgetFactory $productReviewsWidgetFactory,
        array $data = []
    ) {
        parent::__construct($context, $data);
        $this->blockHelper = $blockHelper;
        $this->productReviewsWidget = $productReviewsWidgetFactory->create();
    }

    /**
     * @return \Magento\Catalog\Api\Data\ProductInterface
     */
    private function getProduct()
    {
        return $this->productReviewsWidget->getProduct($this->getProductId());
    }

    /**
     * @return string
     */
    public function toHtml()
    {
        $html = '';
        if ($this->getProduct()) {
            $this->setTemplate(self::TEMPLATE);
            $html = parent::toHtml();
        }

        return $html;
    }

    /**
     * @return ReviewCollection|\Magento\Review\Model\ResourceModel\Review\Collection
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function getReviewsCollection()
    {
        if ($this->reviewCollection === null) {
            $this->reviewCollection =
                $this->productReviewsWidget->getReviewsCollection($this->getProductId(), $this->getReviewsCount());
        }

        return $this->reviewCollection;
    }

    /**
     * @return ReviewCollection
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function getLimitedReviewsCollection()
    {
        return $this->getReviewsCollection()
            ->setPageSize($this->getProductsPerPage())
            ->setCurPage((int)$this->getData('p'))
            ->load()->addRateVotes();
    }

    /**
     * @return string
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function getPager()
    {
        return $this->getLayout()->createBlock(\Amasty\AdvancedReview\Block\Widget\ProductReviews\Pager::class)
            ->setLimit($this->getProductsPerPage())
            ->setData($this->getData())
            ->setProductId($this->getProductId())
            ->setCollection($this->getReviewsCollection())
            ->toHtml();
    }

    /**
     * @return BlockHelper
     */
    public function getAdvancedHelper()
    {
        return $this->blockHelper;
    }
}
