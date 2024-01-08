<?php
/**
 * Mageplaza
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Mageplaza.com license that is
 * available through the world-wide-web at this URL:
 * https://www.mageplaza.com/LICENSE.txt
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade this extension to newer
 * version in the future.
 *
 * @category    Mageplaza
 * @package     Mageplaza_PdfInvoice
 * @copyright   Copyright (c) Mageplaza (https://www.mageplaza.com/)
 * @license     https://www.mageplaza.com/LICENSE.txt
 */

namespace Mageplaza\PdfInvoice\Block\Adminhtml\Template\Edit\Tab\Render;

use Magento\Framework\Data\Form\Element\AbstractElement;

/**
 * Class Textarea
 * @package Mageplaza\PdfInvoice\Block\Adminhtml\Template\Edit\Tab\Render
 */
class Textarea extends AbstractElement
{
    /**
     * Get element html
     * @return string
     */
    public function getElementHtml()
    {
        $html = '<nav class="mp-tab">';
        $html .= '<button id="template_html_bt" type="button" class="action-default active"><span><span class="mp-caret"></span>' . __('Edit html') . '</span></button>';
        $html .= '<div class="mp-bt-r"><a href="https://mpdf.github.io/html-support/html-tags.html" target="_blank">' . __('HTML,') . '</a><a href="https://mpdf.github.io/css-stylesheets/supported-css.html" target="_blank">' . __('CSS Supported') . '</a></div>';
        $html .= '<button id="preview_change" type="button" class="action-default scalable" ><span>' . __('Preview') . '</span></button>';
        $html .= '</nav>';
        $html .= '<textarea id="template_html" name="template_html" title="Template html" cols="20" rows="5" class=" textarea admin__control-textarea">' . $this->getEscapedValue() . '</textarea>';
        $html .= '<iframe id="iframe" name="mp-iframe" ></iframe>';

        return $html;
    }
}
