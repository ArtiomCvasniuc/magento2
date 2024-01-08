<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2020 Amasty (https://www.amasty.com)
 * @package Amasty_AdvancedReview
 */


namespace Amasty\AdvancedReview\Controller\Widget;

use Magento\Catalog\Api\ProductRepositoryInterface;
use Magento\Framework\App\Action\Context;
use Magento\Framework\Controller\Result\JsonFactory;
use Amasty\AdvancedReview\Block\Widget\ProductReviews\ReviewsList;

class Paging extends \Magento\Framework\App\Action\Action
{
    /**
     * @var JsonFactory
     */
    private $resultJsonFactory;

    /**
     * @var ReviewsList
     */
    private $reviewsList;

    /**
     * @var ProductRepositoryInterface
     */
    private $productRepository;

    public function __construct(
        Context $context,
        ReviewsList $reviewsList,
        JsonFactory $resultJsonFactory,
        ProductRepositoryInterface $productRepository
    ) {
        parent::__construct($context);
        $this->resultJsonFactory = $resultJsonFactory;
        $this->reviewsList = $reviewsList;
        $this->productRepository = $productRepository;
    }

    /**
     * @return \Magento\Framework\Controller\Result\Json|\Magento\Framework\Controller\ResultInterface
     */
    public function execute()
    {
        $request = $this->getRequest();
        $html = '';
        if ($request->isAjax()) {
            $product = $this->initProduct((int)$request->getParam('product_id'));
            $html = $this->reviewsList->setData($request->getParams())
                ->setProduct($product)
                ->toHtml();
        }

        $resultPage = $this->resultJsonFactory->create();
        $resultPage->setHttpResponseCode(200);
        $resultPage->setData(['reviewsBlock' => $html]);

        return $resultPage;
    }

    /**
     * @param int $productId
     *
     * @return bool|\Magento\Catalog\Api\Data\ProductInterface
     */
    private function initProduct(int $productId)
    {
        try {
            $product = $this->productRepository->getById($productId);
        } catch (\Magento\Framework\Exception\NoSuchEntityException $e) {
            $product = false;
        }

        return $product;
    }
}
