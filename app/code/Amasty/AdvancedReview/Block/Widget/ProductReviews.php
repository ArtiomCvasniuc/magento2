<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2020 Amasty (https://www.amasty.com)
 * @package Amasty_AdvancedReview
 */


namespace Amasty\AdvancedReview\Block\Widget;

use Amasty\AdvancedReview\Block\Widget\ProductReviews\ReviewsList;
use Amasty\AdvancedReview\Model\ResourceModel\Review\Collection as ReviewCollection;
use Amasty\AdvancedReview\Model\Widget\ProductReviewsWidgetFactory;
use Magento\Framework\View\Element\Template;
use Amasty\AdvancedReview\Block\Widget\ProductReviews\ProductInfo;
use Amasty\AdvancedReview\Block\Widget\ProductReviews\Form;

class ProductReviews extends \Magento\Framework\View\Element\Template
{
    /**
     * @var string
     */
    protected $_template = 'Amasty_AdvancedReview::widget/product_reviews/container.phtml';

    /**
     * @var \Magento\Catalog\Model\Product
     */
    private $product;

    /**
     * @var ProductReviewsWidgetFactory
     */
    private $productReviewsWidget;

    public function __construct(
        Template\Context $context,
        ProductReviewsWidgetFactory $productReviewsWidgetFactory,
        array $data = []
    ) {
        parent::__construct($context, $data);
        $this->productReviewsWidget = $productReviewsWidgetFactory->create();
    }

    private function initProduct()
    {
        $rewriteData = explode('/', $this->getData('id_path'));
        $this->product = $this->productReviewsWidget->getProduct($rewriteData[1] ?? 0);
    }

    /**
     * @return string
     */
    public function toHtml()
    {
        $html = '';
        $this->initProduct();
        if ($this->product) {
            $html = parent::toHtml();
        }

        return $html;
    }

    /**
     * @return mixed
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function getReviewsList()
    {
        return $this->getLayout()->createBlock(ReviewsList::class)
            ->setData($this->getData())
            ->setProductId($this->product->getId())
            ->toHtml();
    }

    /**
     * @return ReviewCollection|\Magento\Review\Model\ResourceModel\Review\Collection
     */
    public function getReviewCollection()
    {
        return $this->productReviewsWidget->getReviewsCollection($this->product->getId());
    }

    /**
     * @return string
     */
    public function getAddReviewForm()
    {
        $component = ['components' => ['review-form' => ['component' => 'Magento_Review/js/view/review']]];

        return $this->getLayout()->createBlock(Form::class)
            ->setTemplate('Magento_Review::form.phtml')
            ->setData('jsLayout', $component)
            ->setProduct($this->product)
            ->toHtml();
    }

    /**
     * @return string
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function getProductView()
    {
        return $this->getLayout()->createBlock(ProductInfo::class)
                ->setProduct($this->product)
                ->setReviewsCollection($this->getReviewCollection())
                ->toHtml();
    }

    /**
     * @return int
     */
    public function getProductId()
    {
        return $this->product->getId();
    }
}
