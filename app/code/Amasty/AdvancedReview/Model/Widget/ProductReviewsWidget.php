<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2020 Amasty (https://www.amasty.com)
 * @package Amasty_AdvancedReview
 */


namespace Amasty\AdvancedReview\Model\Widget;

use Magento\Store\Model\StoreManagerInterface;
use Amasty\AdvancedReview\Model\ResourceModel\Review\Collection as ReviewCollection;
use Amasty\AdvancedReview\Model\ResourceModel\Review\CollectionFactory as ReviewCollectionFactory;
use Magento\Catalog\Api\ProductRepositoryInterface;

class ProductReviewsWidget
{
    /**
     * @var ProductRepositoryInterface
     */
    private $productRepository;

    /**
     * @var ReviewCollectionFactory
     */
    private $reviewCollectionFactory;

    /**
     * @var StoreManagerInterface
     */
    private $storeManager;

    /**
     * @var \Magento\Catalog\Model\Product
     */
    private $product = null;

    public function __construct(
        ProductRepositoryInterface $productRepository,
        ReviewCollectionFactory $reviewCollectionFactory,
        StoreManagerInterface $storeManager,
        array $data = []
    ) {
        $this->productRepository = $productRepository;
        $this->reviewCollectionFactory = $reviewCollectionFactory;
        $this->storeManager = $storeManager;
    }

    /**
     * @param int $productId
     * @param int $limit
     * @return ReviewCollection|\Magento\Review\Model\ResourceModel\Review\Collection
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function getReviewsCollection($productId, $limit = 0)
    {
        /** @var ReviewCollection $reviewsCollection */
        $reviewsCollection = $this->reviewCollectionFactory->create()
            ->addStoreFilter($this->storeManager->getStore()->getId())
            ->addEntityFilter('product', $productId)
            ->addStatusFilter(\Magento\Review\Model\Review::STATUS_APPROVED)
            ->setDateOrder();

        if ($limit) {
            $countCollection = clone $reviewsCollection;
            $ids = array_keys($countCollection->getItems());
            $ids = array_slice($ids, 0, $limit);
            $reviewsCollection->addFieldToFilter('main_table.review_id', ['in' => $ids]);
        }

        return $reviewsCollection;
    }

    /**
     * @param int $productId
     * @return \Magento\Catalog\Api\Data\ProductInterface
     */
    public function getProduct($productId)
    {
        try {
            if (!$this->product) {
                $this->product = $this->productRepository->getById($productId);
            }
        } catch (\Magento\Framework\Exception\NoSuchEntityException $e) {
            $this->product = false;
        }

        return $this->product;
    }
}
