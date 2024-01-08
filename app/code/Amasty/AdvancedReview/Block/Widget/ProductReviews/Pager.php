<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2020 Amasty (https://www.amasty.com)
 * @package Amasty_AdvancedReview
 */


namespace Amasty\AdvancedReview\Block\Widget\ProductReviews;

use Magento\Framework\Serialize\Serializer\Json;
use Magento\Framework\View\Element\Template;

class Pager extends \Magento\Theme\Block\Html\Pager
{
    /**
     * @var string
     */
    protected $_template = 'Amasty_AdvancedReview::widget/product_reviews/components/pager.phtml';

    /**
     * @var Json
     */
    private $json;

    public function __construct(
        Template\Context $context,
        Json $json,
        array $data = []
    ) {
        parent::__construct($context, $data);
        $this->json = $json;
    }

    /**
     * @param string $page
     * @return string
     */
    public function getPageUrl($page)
    {
        return $this->_urlBuilder ->getUrl('amasty_advancedreview/widget/paging', ['p' => $page]);
    }

    /**
     * @return string
     */
    public function getJsonData()
    {
        return $this->json->serialize($this->getData());
    }
}
