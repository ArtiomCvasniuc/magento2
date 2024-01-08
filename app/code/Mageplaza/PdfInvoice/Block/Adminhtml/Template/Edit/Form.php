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

namespace Mageplaza\PdfInvoice\Block\Adminhtml\Template\Edit;

use Magento\Backend\Block\Widget\Form\Generic;

/**
 * Class Form
 * @package Mageplaza\PdfInvoice\Block\Adminhtml\Template\Edit
 */
class Form extends Generic
{
    /**
     * @var string
     */
    protected $_template = 'Mageplaza_PdfInvoice::widget/form.phtml';

    /**
     * @inheritdoc
     */
    protected function _prepareForm()
    {
        /** @var \Magento\Framework\Data\Form $form */
        $form = $this->_formFactory->create([
                'data' => [
                    'id'      => 'edit_form',
                    'action'  => $this->getUrl('*/*/save'),
                    'method'  => 'post',
                    'enctype' => 'multipart/form-data'
                ]
            ]);

        $form->setUseContainer(true);
        $this->setForm($form);

        return parent::_prepareForm();
    }

    /**
     * Get load template url
     * @return string
     */
    public function getLoadTemplateUrl()
    {
        return $this->getUrl('pdfinvoice/template/load');
    }

    /**
     * Get preview template  url
     * @return string
     */
    public function getPreviewTemplateUrl()
    {
        return $this->getUrl('pdfinvoice/template/preview');
    }

    /**
     * Get Template type
     * @return mixed
     */
    public function getTemplateType()
    {
        return $this->getRequest()->getParam('type', 'invoice');
    }

    /**
     * Get template html
     * @return string
     */
    public function getTemplateHtml()
    {
        $model = $this->_coreRegistry->registry('current_template');
        if ($model->getId()) {
            return true;
        }

        return false;
    }
}
