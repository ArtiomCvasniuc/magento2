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

namespace Mageplaza\PdfInvoice\Block\Adminhtml\Template;

use Magento\Backend\Block\Widget\Context;
use Magento\Backend\Block\Widget\Form\Container;
use Magento\Framework\Registry;
use Mageplaza\PdfInvoice\Model\Template;

/**
 * Class Edit
 * @package Mageplaza\PdfInvoice\Block\Adminhtml\Template
 */
class Edit extends Container
{
    /**
     * Core registry
     *
     * @var Registry
     */
    public $_coreRegistry;

    /**
     * @type Template
     */
    public $_currentTemplate;

    /**
     * Edit constructor.
     *
     * @param Context $context
     * @param Registry $coreRegistry
     * @param array $data
     */
    public function __construct(
        Context $context,
        Registry $coreRegistry,
        array $data = []
    ) {
        $this->_coreRegistry = $coreRegistry;

        parent::__construct($context, $data);
    }

    /**
     * Construct
     */
    protected function _construct()
    {
        $this->_objectId = 'id';
        $this->_blockGroup = 'Mageplaza_PdfInvoice';
        $this->_controller = 'adminhtml_template';

        parent::_construct();

        $this->buttonList->add(
            'saveandcontinue',
            [
                'label'          => __('Save and Continue Edit'),
                'class'          => 'save',
                'data_attribute' => [
                    'mage-init' => ['button' => ['event' => 'saveAndContinueEdit', 'target' => '#edit_form']]
                ]
            ],
            -100
        );
        $this->buttonList->update('save', 'label', __('Save Template'));
        $this->buttonList->remove('reset');
    }

    /**
     * Get edit form container header text
     *
     * @return string
     */
    public function getHeaderText()
    {
        $currentTemplate = $this->getCurrentTemplate();
        if ($currentTemplate && $currentTemplate->getId()) {
            return __("Edit Template '%1'", $this->escapeHtml($currentTemplate->getName()));
        }

        return __('New Template');
    }

    /**
     * Get current template
     *
     * @return Template|mixed
     */
    public function getCurrentTemplate()
    {
        if (!$this->_currentTemplate) {
            $this->_currentTemplate = $this->_coreRegistry->registry('current_template');
        }

        return $this->_currentTemplate;
    }
}
