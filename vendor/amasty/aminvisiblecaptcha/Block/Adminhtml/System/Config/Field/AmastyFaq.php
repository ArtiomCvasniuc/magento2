<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2020 Amasty (https://www.amasty.com)
 * @package Amasty_InvisibleCaptcha
 */


namespace Amasty\InvisibleCaptcha\Block\Adminhtml\System\Config\Field;

use Amasty\Base\Helper\Module;
use Magento\Backend\Block\Template;
use Magento\Config\Block\System\Config\Form\Field;
use Magento\Framework\Data\Form\Element\AbstractElement;

class AmastyFaq extends Field
{
    /**
     * @var Module
     */
    private $moduleHelper;

    public function __construct(
        Template\Context $context,
        Module $moduleHelper,
        array $data = []
    ) {
        parent::__construct($context, $data);
        $this->moduleHelper = $moduleHelper;
    }

    public function render(AbstractElement $element)
    {
        $url = "https://amasty.com/faq-and-product-questions-for-magento-2.html"
            . "?utm_source=extension&utm_medium=link&utm_campaign=captcham2-faqm2";

        if ($this->moduleHelper->isOriginMarketplace()) {
            $url = "https://marketplace.magento.com/amasty-extension-faq-and-product-questions.html";
        }

        $html = "Let users ask questions on product pages and use this content to widen your store semantic core. "
            . "Organize questions into seo optimized, responsive and easy to navigate knowledge base. <a href='"
            . $url . "' target='_blank'>Learn more</a>.";

        $element->setComment($html);

        return parent::render($element);
    }
}
