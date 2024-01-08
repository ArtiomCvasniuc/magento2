<?php
namespace Themevast\QuickView\Controller\Product;

use Themevast\QuickView\Controller\Product as ProductController;
use Magento\Framework\Controller\ResultFactory;

class Quickview extends ProductController
{
    public function execute()
    {
        $pr = $this->initProduct();
        $resultLayout = $this->resultFactory->create(ResultFactory::TYPE_LAYOUT);
		$resultLayout->addHandle('catalog_product_view_type_'. $pr->getTypeId());
        return $resultLayout;
    }
}
