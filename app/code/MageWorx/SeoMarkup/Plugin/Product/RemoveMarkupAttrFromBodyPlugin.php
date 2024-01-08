<?php
/**
 * Copyright © MageWorx. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace MageWorx\SeoMarkup\Plugin\Product;

use Magento\Framework\App\RequestInterface;
use Magento\Framework\View\Page\Config as PageConfig;
use MageWorx\SeoMarkup\Helper\Product as HelperProduct;

class RemoveMarkupAttrFromBodyPlugin
{
    /**
     * @var RequestInterface
     */
    protected $request;

    /**
     * @var HelperProduct
     */
    protected $helperProduct;

    /**
     * RemoveMarkupAttrFromBodyPlugin constructor.
     *
     * @param RequestInterface $request
     * @param HelperProduct $helperProduct
     */
    public function __construct(RequestInterface $request, HelperProduct $helperProduct)
    {
        $this->request       = $request;
        $this->helperProduct = $helperProduct;
    }

    /**
     * @param PageConfig $subject
     * @param callable $proceed
     * @param string $elementType
     * @return string[]
     */
    public function aroundGetElementAttributes(PageConfig $subject, callable $proceed, $elementType)
    {
        $fullActionName = $this->request->getFullActionName();
        $result         = $proceed($elementType);

        if ($fullActionName == 'catalog_product_view'
            && $elementType == PageConfig::ELEMENT_TYPE_BODY
            && $this->helperProduct->isRsEnabled()
        ) {
            if (isset($result['itemtype'])) {
                unset($result['itemtype']);
            }

            if (isset($result['itemscope'])) {
                unset($result['itemscope']);
            }
        }

        return $result;
    }
}
