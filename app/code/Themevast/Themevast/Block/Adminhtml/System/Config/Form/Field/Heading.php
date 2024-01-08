<?php
/**
 * ducdevphp@gmail.com
 */
namespace Themevast\Themevast\Block\Adminhtml\System\Config\Form\Field;
use Magento\Config\Block\System\Config\Form\Field;

class Heading extends Field
{

    public function render(\Magento\Framework\Data\Form\Element\AbstractElement $tv)
    {
        $fieldConfig = $tv->getFieldConfig();
        $htmlId = $tv->getHtmlId();
        $html = '<tr id="row_' . $htmlId . '">'
        . '<td class="label" colspan="3">';

        $html .= '<div style="border-bottom: 1px solid #c3c3c3;
        font-size: 17px;
        border-left: #ba4000 solid 5px;
        padding: 5px 15px;
        text-align: left !important;
        margin: 15px;">';
        $html .= $tv->getLabel();
        $html .= '</div>';
        $html .= '<p class="note"><span>' . $tv->getComment() . '</span></p>';
        $html .= '</td></tr>';
        return $html;
    }
}