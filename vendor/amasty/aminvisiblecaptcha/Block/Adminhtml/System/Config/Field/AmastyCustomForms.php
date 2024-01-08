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

class AmastyCustomForms extends Field
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
        $url = "https://amasty.com/custom-form-for-magento-2.html"
            . "?utm_source=extension&utm_medium=link&utm_campaign=captcham2-cform2";

        if ($this->moduleHelper->isOriginMarketplace()) {
            $url = "https://marketplace.magento.com/amasty-module-magento-custom-form.html";
        }

        //Because it's necessary for codesniffer
        //phpcs:ignore Magento2.SQL.RawQuery.FoundRawSql
        $html = "Create customizable forms to collect additional information about your customers and"
            . " view the received data from the admin panel. "
            . "Organize questions into seo optimized, responsive and easy to navigate knowledge base. <a href='"
            . $url . "' target='_blank'>Learn more</a>.";

        $element->setComment($html);

        return parent::render($element);
    }
}
