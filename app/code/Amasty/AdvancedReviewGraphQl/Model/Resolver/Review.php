<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2020 Amasty (https://www.amasty.com)
 * @package Amasty_AdvancedReviewGraphQl
 */


declare(strict_types=1);

namespace Amasty\AdvancedReviewGraphQl\Model\Resolver;

use Amasty\AdvancedReview\Api\CommentRepositoryInterface;
use Magento\Framework\GraphQl\Exception\GraphQlNoSuchEntityException;
use Magento\Framework\GraphQl\Schema\Type\ResolveInfo;
use Magento\Framework\GraphQl\Config\Element\Field;
use Magento\Framework\GraphQl\Query\ResolverInterface;
use Amasty\AdvancedReview\Block\Summary;
use Magento\Catalog\Model\ProductRepository;
use Magento\Review\Model\ResourceModel\Review\CollectionFactory;
use Amasty\AdvancedReview\Helper\Config;
use Amasty\AdvancedReview\Block\Review\Toolbar;
use Amasty\AdvancedReview\Block\Helpful;
use Magento\Review\Model\ResourceModel\Rating\Option\Vote\CollectionFactory as VoteCollectionFactory;
use Amasty\AdvancedReview\Block\Images;

class Review implements ResolverInterface
{
    /**
     * @var Summary
     */
    private $summary;

    /**
     * @var ProductRepository
     */
    private $productRepository;

    /**
     * @var CollectionFactory
     */
    private $collectionFactory;

    /**
     * @var Config
     */
    private $config;

    /**
     * @var Toolbar
     */
    private $toolbar;

    /**
     * @var Helpful
     */
    private $helpful;

    /**
     * @var VoteCollectionFactory
     */
    private $voteFactory;

    /**
     * @var Images
     */
    private $images;

    /**
     * @var CommentRepositoryInterface
     */
    private $commentRepository;

    public function __construct(
        Summary $summary,
        ProductRepository $productRepository,
        CollectionFactory $collectionFactory,
        Config $config,
        Helpful $helpful,
        VoteCollectionFactory $voteFactory,
        Images $images,
        Toolbar $toolbar,
        CommentRepositoryInterface $commentRepository
    ) {
        $this->summary = $summary;
        $this->productRepository = $productRepository;
        $this->collectionFactory = $collectionFactory;
        $this->config = $config;
        $this->toolbar = $toolbar;
        $this->helpful = $helpful;
        $this->voteFactory = $voteFactory;
        $this->images = $images;
        $this->commentRepository = $commentRepository;
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
            $product = $this->productRepository->getById($args['productId']);
            $reviewCollection = $this->getReviewCollection($product->getId(), $args['storeId']);
        } catch (\Exception $e) {
            throw new GraphQlNoSuchEntityException(__('Wrong parameter storeId.'));
        }

        $this->summary->setProduct($product);
        $this->prepareAdditionalData($reviewCollection, $args['storeId']);
        $data = $reviewCollection->toArray();
        $this->summary->setDisplayedCollection($reviewCollection);

        $extraData = [
            'isRecommendFieldEnabled' => $this->config->isRecommendFieldEnabled(),
            'isAllowGuest' => $this->config->isAllowGuest(),
            'isAllowAnswer' => $this->config->isAllowAnswer(),
            'isAllowCoupons' => $this->config->isAllowCoupons(),
            'isAllowHelpful' => $this->config->isAllowHelpful(),
            'isAllowImages' => $this->config->isAllowImages(),
            'isProsConsEnabled' => $this->config->isProsConsEnabled(),
            'ratingSummary' => $this->summary->getRatingSummary(),
            'ratingSummaryValue' =>  $this->summary->getRatingSummaryValue(),
            'detailedSummary' => $this->prepareDetailedSummary(),
            'recomendedPercent' => $this->summary->getRecomendedPercent(),
            'availableOrders' => $this->prepareAvailableToolbarOptions($this->toolbar->getAvailableOrders()),
            'availableFilters' => $this->prepareAvailableToolbarOptions($this->toolbar->getAvailableFilters()),
            'isFilteringEnabled' => $this->toolbar->isFilteringEnabled(),
            'isSortingEnabled' => $this->toolbar->isSortingEnabled(),
            'isToolbarDisplayed' => $this->toolbar->isToolbarDisplayed(),
            'maxImageHeight' => $this->images->getMaxHeight()
        ];

        return array_merge($data, $extraData);
    }

    /**
     * @param $productId
     * @param $storeId
     * @return \Magento\Review\Model\ResourceModel\Review\Collection
     */
    private function getReviewCollection($productId, $storeId)
    {
        return $this->collectionFactory->create()->addStoreFilter($storeId)
            ->addStatusFilter(\Magento\Review\Model\Review::STATUS_APPROVED)
            ->addEntityFilter('product', $productId)
            ->addFieldToSelect(
                [
                    new \Zend_Db_Expr('detail.guest_email'),
                    new \Zend_Db_Expr('main_table.*')
                ]
            )
            ->setDateOrder();
    }

    /**
     * @return array
     */
    private function prepareDetailedSummary()
    {
        $detailedSummary = $this->summary->getDetailedSummary();

        return [
          'one' => $detailedSummary[1],
          'two' => $detailedSummary[2],
          'three' => $detailedSummary[3],
          'four' => $detailedSummary[4],
          'five' => $detailedSummary[5],
        ];
    }

    /**
     * @param $data
     * @return string
     */
    private function prepareAvailableToolbarOptions($data)
    {
        $items = [];
        foreach ($data as $item) {
            $items[] = $item->getText();
        }

        return $items ? implode(';', $items) : '';
    }

    /**
     * @param $reviewCollection
     * @param $storeId
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    private function prepareAdditionalData($reviewCollection, $storeId)
    {
        foreach ($reviewCollection as $review) {
            $this->prepareVotes($review, $storeId);
            $this->prepareImages($review);
            $review->setComments($this->commentRepository->getListByReviewId($review->getId())->getItems());
        }
    }

    /**
     * @param $review
     * @param $storeId
     */
    private function prepareVotes($review, $storeId)
    {
        $this->helpful->setReview($review);
        $review->setPlusReview($this->helpful->getPlusReview());
        $review->setMinusReview($this->helpful->getMinusReview());
        $votes = $this->voteFactory->create()->setReviewFilter($review->getId())
            ->setStoreFilter($storeId)
            ->addRatingInfo($storeId);
        $review->setRatingVotes($votes->getItems());
    }

    /**
     * @param $review
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    private function prepareImages($review)
    {
        $images = [];
        $this->images->setReviewId($review->getId());
        foreach ($this->images->getCollection() as $image) {
            $images[] = [
                'full_path' => $this->images->getFullImagePath($image),
                'resized_path' => $this->images->getResizedImagePath($image),
            ];
        }

        $review->setImages($images);
    }
}
