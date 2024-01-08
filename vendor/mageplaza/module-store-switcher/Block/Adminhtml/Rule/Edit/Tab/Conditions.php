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
 * @package     Mageplaza_StoreSwitcher
 * @copyright   Copyright (c) Mageplaza (https://www.mageplaza.com/)
 * @license     https://www.mageplaza.com/LICENSE.txt
 */

namespace Mageplaza\StoreSwitcher\Block\Adminhtml\Rule\Edit\Tab;

use Magento\Backend\Block\Template\Context;
use Magento\Backend\Block\Widget\Form\Generic;
use Magento\Backend\Block\Widget\Tab\TabInterface;
use Magento\Directory\Model\Config\Source\Country as Countries;
use Magento\Framework\Data\Form;
use Magento\Framework\Data\FormFactory;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Registry;
use Mageplaza\StoreSwitcher\Block\Adminhtml\Rule\Edit\Tab\Renderer\SelectAll;
use Mageplaza\StoreSwitcher\Model\Config\Source\PageType;
use Mageplaza\StoreSwitcher\Model\Rule;

/**
 * Class Conditions
 * @package Mageplaza\StoreSwitcher\Block\Adminhtml\Rule\Edit\Tab
 */
class Conditions extends Generic implements TabInterface
{
    /**
     * Path to template file.
     *
     * @var string
     */
    protected $_template = 'Mageplaza_StoreSwitcher::rule/conditions.phtml';

    /**
     * @var Countries
     */
    protected $_countries;

    /**
     * @var PageType
     */
    protected $_pageType;

    /**
     * Conditions constructor.
     *
     * @param Context $context
     * @param Registry $registry
     * @param FormFactory $formFactory
     * @param Countries $countries
     * @param PageType $pageType
     * @param array $data
     */
    public function __construct(
        Context $context,
        Registry $registry,
        FormFactory $formFactory,
        Countries $countries,
        PageType $pageType,
        array $data = []
    ) {
        $this->_countries = $countries;
        $this->_pageType  = $pageType;

        parent::__construct($context, $registry, $formFactory, $data);
    }

    /**
     * @return Generic
     * @throws LocalizedException
     */
    protected function _prepareForm()
    {
        /** @var Rule $rule */
        $rule = $this->_coreRegistry->registry('mageplaza_storeswitcher_rule');

        /** @var Form $form */
        $form = $this->_formFactory->create();

        $form->setHtmlIdPrefix('rule_');
        $form->setFieldNameSuffix('rule');
        $fieldset = $form->addFieldset('base_fieldset', [
            'legend' => __('Conditions'),
            'class'  => 'fieldset-wide'
        ]);

        $fieldset->addField('countries', 'multiselect', [
            'name'   => 'countries',
            'label'  => __('Countries'),
            'title'  => __('Countries'),
            'values' => $this->_countries->toOptionArray(),
            'note'   => __('Apply to the selected country(ies)')
        ])->setAfterElementHtml($this->getLayout()
            ->createBlock(SelectAll::class)->toHtml());

        $fieldset->addField('page_type', 'select', [
            'name'   => 'page_type',
            'label'  => __('Page Type'),
            'title'  => __('Page Type'),
            'values' => $this->_pageType->toOptionArray()
        ]);

        $fieldset->addField('include_path', 'textarea', [
            'name'  => 'include_path',
            'label' => __('Apply by path of Url'),
            'title' => __('Apply by path of Url')
        ]);

        $note = '<p>' . __('E.g: Enter /blog will be excluded all pages path contain /blog') . '</p>' .
            '<p>' . __('Samples:') . '</p>' .
            '<p>/blog/</p>' .
            '<p>/checkout</p>' .
            '<p>/onestepcheckout</p>' .
            '<p>/paypal</p>';
        $fieldset->addField('exclude_path', 'textarea', [
            'name'  => 'exclude_path',
            'label' => __('Exclude by path of URL'),
            'title' => __('Exclude by path of URL'),
            'note'  => $note
        ]);

        $fieldset->addField('exclude_ips', 'textarea', [
            'name'  => 'exclude_ips',
            'label' => __('Exclude IPs'),
            'title' => __('Exclude IPs'),
            'note'  => __('IP ranges are accepted. E.g., 172.168.*.*; 172.0.0.0-172.1.0.0')
        ]);

        $fieldset->addField('ignore_user_agents', 'textarea', [
            'name'  => 'ignore_user_agents',
            'label' => __('Search engines to ignore'),
            'title' => __('Search engines to ignore'),
            'note'  => __('List search engines and user agents to ignore.') . '<br>' . __('Separated by comma(,)')
        ]);

        $form->addValues($rule->getData());
        $this->setForm($form);

        return parent::_prepareForm();
    }

    /**
     * Prepare label for tab
     *
     * @return string
     */
    public function getTabLabel()
    {
        return __('Conditions');
    }

    /**
     * Prepare title for tab
     *
     * @return string
     */
    public function getTabTitle()
    {
        return $this->getTabLabel();
    }

    /**
     * Can show tab in tabs
     *
     * @return boolean
     */
    public function canShowTab()
    {
        return true;
    }

    /**
     * Tab is hidden
     *
     * @return boolean
     */
    public function isHidden()
    {
        return false;
    }
}
