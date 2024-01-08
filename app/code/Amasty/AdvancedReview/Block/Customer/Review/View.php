<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2020 Amasty (https://www.amasty.com)
 * @package Amasty_AdvancedReview
 */


namespace Amasty\AdvancedReview\Block\Customer\Review;

class View extends \Magento\Review\Block\Customer\View
{
    /**
     * Customer view template name
     *
     * @var string
     */
    protected $_template = 'Amasty_AdvancedReview::customer/view.phtml';

    /**
     * @return string
     */
    public function getReviewAnswerHtml()
    {
        $review = $this->getReviewData();
        if ($this->getData('config')->isAllowAnswer() && $review->getAnswer()) {
            $html = $review->getAnswer();
        }

        return $html ?? '';
    }
}
