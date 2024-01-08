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

namespace Mageplaza\PdfInvoice\Block\Adminhtml\Template\Edit\Tab;

use Magento\Backend\Block\Template\Context;
use Magento\Backend\Block\Widget\Form\Generic;
use Magento\Backend\Block\Widget\Tab\TabInterface;
use Magento\Framework\Data\Form;
use Magento\Framework\Data\FormFactory;
use Magento\Framework\Registry;
use Magento\Framework\View\Asset\Repository;
use Magento\Store\Model\System\Store;
use Magento\Variable\Model\VariableFactory;
use Mageplaza\PdfInvoice\Helper\Data;
use Mageplaza\PdfInvoice\Model\Source\Type;
use Mageplaza\PdfInvoice\Model\Source\Variables;

/**
 * Class Information
 * @package Mageplaza\PdfInvoice\Block\Adminhtml\Template\Edit\Tab
 */
class Information extends Generic implements TabInterface
{
    /**
     * @type Store
     */
    protected $_systemStore;

    /**
     * @type Type
     */
    protected $_type;

    /**
     * @var Data
     */
    protected $helperData;

    /**
     * @var Variables
     */
    protected $_variables;

    /**
     * @var VariableFactory
     */
    protected $_variableFactory;

    /**
     * @var Repository
     */
    protected $_assetRepo;

    /**
     * Information constructor.
     *
     * @param Context $context
     * @param Registry $registry
     * @param FormFactory $formFactory
     * @param Store $systemStore
     * @param Type $type
     * @param Data $helperData
     * @param VariableFactory $variableFactory
     * @param Variables $variables
     * @param array $data
     */
    public function __construct(
        Context $context,
        Registry $registry,
        FormFactory $formFactory,
        Store $systemStore,
        Type $type,
        Data $helperData,
        VariableFactory $variableFactory,
        Variables $variables,
        array $data = []
    ) {
        $this->_type = $type;
        $this->_systemStore = $systemStore;
        $this->helperData = $helperData;
        $this->_variableFactory = $variableFactory;
        $this->_variables = $variables;
        $this->_assetRepo = $context->getAssetRepository();

        parent::__construct($context, $registry, $formFactory, $data);
    }

    /**
     * @inheritdoc
     */
    protected function _prepareForm()
    {
        /** @var Form $form */
        $form = $this->_formFactory->create();

        $typeTemplate = $this->_request->getParam('type', Type::INVOICE);

        $model = $this->_coreRegistry->registry('current_template');
        if (!$model->getId()) {
            $fieldset = $form->addFieldset('base_fieldset', ['legend' => __('Load default template')]);

            $fieldset->addField('type', 'hidden', ['name' => 'type', 'value' => $typeTemplate]);

            $defaultImage = array_values($this->getImageUrls($typeTemplate))[0];
            $fieldset->addField('default_template', 'select', [
                    'required' => true,
                    'name'     => 'default_template',
                    'label'    => __('Template'),
                    'title'    => __('Template'),
                    'values'   => $this->helperData->toOptionArray(),
                    'note'     => '<img src="' . $defaultImage . '" alt="demo"  class="article_image" id="mp-image">'
                ]);

            $fieldset->addField('images-urls', 'hidden', [
                    'name'  => 'image-urls',
                    'value' => Data::jsonEncode($this->getImageUrls($typeTemplate))
                ]);

            $insertVariableButton = $this->getLayout()->createBlock(
                'Magento\Backend\Block\Widget\Button',
                '',
                [
                    'data' => [
                        'type'  => 'button',
                        'label' => __('Load Template'),
                    ]
                ]
            );
            $fieldset->addField('load_template', 'note', [
                'text'  => $insertVariableButton->toHtml(),
                'label' => ''
            ]);
        }

        $fieldset = $form->addFieldset('base_fieldset_information', ['legend' => __('Template Information')]);

        if ($model->getId()) {
            $fieldset->addField('template_id', 'hidden', ['name' => 'id']);
        }

        $typeOptionArray = Type::getOptionArray();
        $fieldset->addField('apply_template_at', 'label', [
                'label'              => __('Apply Template At'),
                'container_id'       => 'apply_template_at',
                'after_element_html' => $this->getApplyTemplateAt($typeOptionArray[$typeTemplate])
            ]);

        $fieldset->addField('name', 'text', [
                'name'     => 'name',
                'label'    => __('Template Name'),
                'title'    => __('Template Name'),
                'required' => true
            ]);

        $fieldset->addField('variables', 'hidden', [
                'name'  => 'variables',
                'value' => Data::jsonEncode($this->getVariables())
            ]);
        if ($model->getId()) {
            $model->setVariables(Data::jsonEncode($this->getVariables()));
        }

        $insertVariableButton = $this->getLayout()->createBlock(
            'Magento\Backend\Block\Widget\Button',
            '',
            [
                'data' => [
                    'type'  => 'button',
                    'label' => __('Insert Variable...')
                ]
            ]
        );

        $fieldset->addField('insert_variable', 'note', ['text' => $insertVariableButton->toHtml(), 'label' => '']);

        $fieldset->addType('tab_template', '\Mageplaza\PdfInvoice\Block\Adminhtml\Template\Edit\Tab\Render\Textarea');
        $fieldset->addField('template_html', 'tab_template', []);

        $fieldset->addField('template_styles', 'textarea', [
                'label' => __('Template style'),
                'title' => __('Template style'),
                'name'  => 'template_styles',
                'cols'  => 20,
                'rows'  => 5,
                'value' => '',
                'wrap'  => 'soft',
            ]);

        if ($model->getId()) {
            $form->setValues($model->getData());
        }

        $this->setForm($form);

        return parent::_prepareForm();
    }

    /**
     * Retrieve variables to insert into email
     *
     * @return array
     */
    public function getVariables()
    {
        $variables = [];
        $variables[] = $this->_variables->toOptionArray(true);

        $customVariables = $this->_variableFactory->create()->getVariablesOptionArray(true);
        if ($customVariables) {
            $variables[] = $customVariables;
        }

        return $variables;
    }

    /**
     * Get image url
     *
     * @param $type
     *
     * @return array
     */
    public function getImageUrls($type)
    {
        $urls = [];
        foreach ($this->helperData->toOptionArray() as $template) {
            $urls[$template['value']] = $this->_assetRepo->getUrl('Mageplaza_PdfInvoice::images/' . $type . '/' . $template['value'] . '.jpg');
        }

        return $urls;
    }

    /**
     * Get current used for
     *
     * @param $type
     *
     * @return string
     */
    public function getApplyTemplateAt($type)
    {
        $url = $this->getUrl('adminhtml/system_config/edit/section/pdfinvoice');

        return '<span>' . __('Stores -> Configuration -> Mageplaza -> ') . '<a href="' . $url . '" target="_blank">' . __('PDF Invoice') . '</a> -> ' . __($type) . '</span>';
    }

    /**
     * Prepare label for tab
     *
     * @return string
     */
    public function getTabLabel()
    {
        return __('Template Information');
    }

    /**
     * Prepare title for tab
     *
     * @return string
     */
    public function getTabTitle()
    {
        return __('Template Information');
    }

    /**
     * {@inheritdoc}
     */
    public function canShowTab()
    {
        return true;
    }

    /**
     * {@inheritdoc}
     */
    public function isHidden()
    {
        return false;
    }
}
