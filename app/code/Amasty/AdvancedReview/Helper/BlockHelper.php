<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2020 Amasty (https://www.amasty.com)
 * @package Amasty_AdvancedReview
 */


namespace Amasty\AdvancedReview\Helper;

use Amasty\AdvancedReview\Block\Comment\Form as CommentForm;
use Amasty\AdvancedReview\Block\Images;
use Amasty\AdvancedReview\Block\Review\Toolbar;
use Amasty\AdvancedReview\Block\Helpful;
use Amasty\AdvancedReview\Block\Summary;
use Amasty\AdvancedReview\Model\Sources\Recommend;
use Amasty\AdvancedReview\Model\Sources\UseDefaultConfig;
use Magento\Catalog\Model\Product;
use Magento\Review\Model\ResourceModel\Review\Collection as ReviewCollection;

class BlockHelper implements \Magento\Framework\Data\CollectionDataSourceInterface
{

    const ADMIN_ANSWER_ACCOUNT_ONLY = 'amasty_admin_answer_account_only';

    /**
     * @var \Magento\Framework\View\Element\BlockFactory
     */
    private $blockFactory;

    /**
     * @var Config
     */
    private $config;

    /**
     * @var \Magento\Framework\Stdlib\StringUtils
     */
    private $stringUtils;

    /**
     * @var \Magento\Customer\Model\SessionFactory
     */
    private $sessionFactory;

    /**
     * @var \Magento\Framework\Escaper
     */
    private $escaper;

    /**
     * @var \Magento\Framework\UrlInterface
     */
    private $urlBuilder;

    /**
     * @var \Magento\Framework\App\RequestInterface
     */
    private $request;

    /**
     * @var \Amasty\AdvancedReview\Model\Sources\Sort
     */
    private $sortModel;

    public function __construct(
        \Magento\Framework\View\Element\BlockFactory $blockFactory,
        \Amasty\AdvancedReview\Helper\Config $config,
        \Magento\Framework\Stdlib\StringUtils $stringUtils,
        \Magento\Customer\Model\SessionFactory $sessionFactory,
        \Magento\Framework\Escaper $escaper,
        \Magento\Framework\UrlInterface $urlBuilder,
        \Magento\Framework\App\RequestInterface $request,
        \Amasty\AdvancedReview\Model\Sources\Sort $sortModel
    ) {
        $this->blockFactory = $blockFactory;
        $this->config = $config;
        $this->stringUtils = $stringUtils;
        $this->sessionFactory = $sessionFactory;
        $this->escaper = $escaper;
        $this->urlBuilder = $urlBuilder;
        $this->request = $request;
        $this->sortModel = $sortModel;
    }

    /**
     * @return mixed
     */
    private function getCustomerSession()
    {
        return $this->sessionFactory->create();
    }

    /**
     * @param \Magento\Review\Model\Review $review
     * @return string
     */
    public function getReviewAnswerHtml($review)
    {
        if ($this->config->isAllowAnswer()
            && $review->getAnswer()
            && !$this->config->isAdminAnswerAvailableOnAccountOnly($review)
        ) {
            $html = $review->getAnswer();
        }

        return $html ?? '';
    }

    /**
     * @return bool
     */
    public function isProsConsEnabled()
    {
        return $this->config->isProsConsEnabled();
    }

    /**
     * @param $review
     * @return string
     */
    public function getVerifiedBuyerHtml($review)
    {
        $html = '';
        if ($review->getVerifiedBuyer()) {
            $html .= sprintf(
                '<div class="amreview-verified">%s</div>',
                __('Verified Buyer')
            );
        }

        return $html;
    }

    /**
     * @param $review
     * @return string
     */
    public function getRecommendedHtml($review)
    {
        $html = '';
        if ($this->config->isRecommendFieldEnabled() && $review->getData('is_recommended') == Recommend::RECOMMENDED) {
            $html .= sprintf(
                '<p class="amreview-recommended">%s</p>',
                __('I recommend this product')
            );
        }

        return $html;
    }

    /**
     * @param \Magento\Review\Model\Review $review
     * @param bool $short
     * @return string
     */
    public function getHelpfulHtml($review, $short = false)
    {
        $html = '';
        if ($this->config->isAllowHelpful()) {
            /** @var Helpful $block */
            $block = $this->blockFactory->createBlock(Helpful::class)->setReview($review);
            if ($block) {
                if ($short) {
                    $block->useShortTemplate();
                }
                $html .= $block->toHtml();
            }
        }

        return $html;
    }

    /**
     * @param Product $product
     * @param ReviewCollection $displayedCollection
     *
     * @return string
     */
    public function getReviewsSummaryHtml($product, $displayedCollection)
    {
        $html = '';
        $block = $this->blockFactory->createBlock(Summary::class)
            ->setProduct($product)
            ->setDisplayedCollection($displayedCollection);
        if ($block) {
            $html .= $block->toHtml();
        }

        return $html;
    }

    /**
     * @param int $reviewId
     * @param bool $addSizeStyles
     * @return string
     */
    public function getReviewImagesHtml($reviewId, $addSizeStyles = true)
    {
        $html = '';
        if ($this->config->isAllowImages()) {
            $block = $this->blockFactory
                ->createBlock(Images::class)
                ->setReviewId($reviewId)
                ->setAddSizeStyles($addSizeStyles)
                ->setSlidesToShow($this->getSlidesToShow());
            if ($block) {
                $html = $block->toHtml();
            }
        }

        return $html;
    }

    /**
     * @return bool
     */
    public function isAllowGuest()
    {
        return $this->config->isAllowGuest() || $this->getCustomerSession()->getId();
    }

    /**
     * @return int
     */
    public function getSlidesToShow()
    {
        return (int)$this->config->getSlidesToShow();
    }

    /**
     * Get URL for ajax call
     *
     * @param array $params
     *
     * @return string
     */
    public function getProductReviewUrl($params = [])
    {
        $params['_secure'] = $this->request->isSecure();

        return $this->urlBuilder->getUrl(
            'review/product/listAjax',
            $params
        );
    }

    /**
     * used when reviews filtered by rating
     *
     * @param $filterStars
     * @param $productId
     *
     * @return string
     */
    public function getAdditionalTitle($filterStars, $productId)
    {
        $additionalTitle = '';
        if ($filterStars) {
            $additionalTitle = ' ' . __('with %1 stars | ', $filterStars);
            $additionalTitle .= ' <a rel="nofollow" title="' . __('Show All') . '" href="'
                . $this->getProductReviewUrl(['id' => $productId])
                . '">' . __('Show All') . '</a>';
        }

        return $additionalTitle;
    }

    /**
     * @param ReviewCollection $collection
     * @return string
     */
    public function getReviewToolbarHtml($collection)
    {
        $html = '';
        $block = $this->blockFactory->createBlock(Toolbar::class)
            ->setCollection($collection);
        if ($block) {
            $html = $block->toHtml();
        }

        return $html;
    }

    /**
     * @return bool
     */
    public function isCommentsAllowed()
    {
        return $this->config->isCommentsEnabled() && $this->canUserComment();
    }

    /**
     * @return bool
     */
    public function canUserComment()
    {
        return $this->config->isGuestCanComment() || $this->getCustomerSession()->getId();
    }
}
