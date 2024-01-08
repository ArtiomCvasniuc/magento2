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
 * @copyright   Copyright (c) Mageplaza (http://www.mageplaza.com/)
 * @license     https://www.mageplaza.com/LICENSE.txt
 */

namespace Mageplaza\PdfInvoice\Block\Adminhtml;

use Magento\Backend\Block\Widget\Container;
use Mageplaza\PdfInvoice\Model\Source\Type;

/**
 * Class Template
 * @package Mageplaza\PdfInvoice\Block\Adminhtml
 */
class Template extends Container
{
    /**
     * @inheritdoc
     */
    protected function _prepareLayout()
    {
        $addButtonProps = [
            'id'           => 'add_new_template',
            'label'        => __('Add New Template'),
            'class'        => 'add',
            'button_class' => '',
            'class_name'   => 'Magento\Backend\Block\Widget\Button\SplitButton',
            'options'      => $this->_getAddTemplateButtonOptions(),
        ];
        $this->buttonList->add('add_new', $addButtonProps);

        return parent::_prepareLayout();
    }

    /**
     * Retrieve options for 'Add Product' split button
     *
     * @return array
     */
    protected function _getAddTemplateButtonOptions()
    {
        $splitButtonOptions = [];

        foreach (Type::getOptionArray() as $type => $label) {
            $splitButtonOptions[$type] = [
                'label'   => __($label),
                'onclick' => "setLocation('" . $this->_getProductCreateUrl($type) . "')",
                'default' => $type == 'invoice',
            ];
        }

        return $splitButtonOptions;
    }

    /**
     * Retrieve product create url by specified product type
     *
     * @param string $type
     *
     * @return string
     */
    protected function _getProductCreateUrl($type)
    {
        return $this->getUrl('pdfinvoice/*/new', ['type' => $type]);
    }
}
