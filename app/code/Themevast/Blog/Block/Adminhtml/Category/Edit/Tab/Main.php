<?php
namespace Themevast\Blog\Block\Adminhtml\Category\Edit\Tab;

class Main extends \Magento\Backend\Block\Widget\Form\Generic implements
    \Magento\Backend\Block\Widget\Tab\TabInterface
{
    
    protected $_systemStore;

    
    protected $_categoryCollection;

    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \Magento\Framework\Registry $registry,
        \Magento\Framework\Data\FormFactory $formFactory,
        \Magento\Store\Model\System\Store $systemStore,
        \Themevast\Blog\Model\ResourceModel\Category\Collection $categoryCollection,
        array $data = []
    ) {
        $this->_systemStore = $systemStore;
        $this->_categoryCollection = $categoryCollection;
        parent::__construct($context, $registry, $formFactory, $data);
    }

    protected function _prepareForm()
    {
     
        $model = $this->_coreRegistry->registry('current_model');

        $isElementDisabled = !$this->_isAllowedAction('Themevast_Blog::category');

        $form = $this->_formFactory->create();

        $form->setHtmlIdPrefix('category_');

        $fieldset = $form->addFieldset('base_fieldset', ['legend' => __('Category Information')]);

        if ($model->getId()) {
            $fieldset->addField('category_id', 'hidden', ['name' => 'id']);
        }

        $fieldset->addField(
            'title',
            'text',
            [
                'name' => 'title',
                'label' => __('Category Title'),
                'title' => __('Category Title'),
                'required' => true,
                'disabled' => $isElementDisabled
            ]
        );

        $fieldset->addField(
            'is_active',
            'select',
            [
                'label' => __('Status'),
                'title' => __('Category Status'),
                'name' => 'is_active',
                'required' => true,
                'options' => $model->getAvailableStatuses(),
                'disabled' => $isElementDisabled
            ]
        );
        if (!$model->getId()) {
            $model->setData('is_active', $isElementDisabled ? '0' : '1');
        }

        $fieldset->addField(
            'identifier',
            'text',
            [
                'name' => 'identifier',
                'label' => __('URL Key'),
                'title' => __('URL Key'),
				'required' => true,
                'class' => 'validate-identifier',
                'note' => __('Relative to Web Site Base URL(Don\'t use "blog" in URL Key)'),
                'disabled' => $isElementDisabled
            ]
        );

        if (!$this->_storeManager->isSingleStoreMode()) {
            $field = $fieldset->addField(
                'store_id',
                'multiselect',
                [
                    'name' => 'stores[]',
                    'label' => __('Store View'),
                    'title' => __('Store View'),
                    'required' => true,
                    'values' => $this->_systemStore->getStoreValuesForForm(false, true),
                    'disabled' => $isElementDisabled
                ]
            );
            $renderer = $this->getLayout()->createBlock(
                'Magento\Backend\Block\Store\Switcher\Form\Renderer\Fieldset\Element'
            );
            $field->setRenderer($renderer);
        } else {
            $fieldset->addField(
                'store_id',
                'hidden',
                ['name' => 'stores[]', 'value' => $this->_storeManager->getStore(true)->getId()]
            );
            $model->setStoreId($this->_storeManager->getStore(true)->getId());
        }

        $categories[] = ['label' => __('Please select'), 'value' => 0];
        $collection = $this->_categoryCollection
            ->addFieldToFilter('category_id', array('neq' => $model->getId()))
            ->setOrder('position')
            ->getTreeOrderedArray();

        foreach($collection as $item) {
            if (!$model->isChild($item)) {
                $categories[] = array(
                    'label' => $this->_getSpaces($item->getLevel()).' '.$item->getTitle() . ($item->getIsActive() ? '' : ' ('.__('Disabled').')' ),
                    'value' => ($item->getPath() ? $item->getPath().'/' : '') . $item->getId() ,
                );
            }
        }

        if (count($categories)) {
            $field = $fieldset->addField(
                'path',
                'select',
                [
                    'name' => 'path',
                    'label' => __('Parent Category'),
                    'title' => __('Parent Category'),
                    'values' => $categories,
                    'disabled' => $isElementDisabled,
                    'style' => 'width:100%',
                ]
            );
        }

        $fieldset->addField(
            'position',
            'text',
            [
                'name' => 'position',
                'label' => __('Position'),
                'title' => __('Position'),
                'disabled' => $isElementDisabled
            ]
        );

        $this->_eventManager->dispatch('themevast_blog_category_edit_tab_main_prepare_form', ['form' => $form]);

        $form->setValues($model->getData());
        $this->setForm($form);

        return parent::_prepareForm();
    }

    protected function _getSpaces($n)
    {
        $s = '';
        for($i = 0; $i < $n; $i++) {
            $s .= '--- ';
        }

        return $s;
    }

    public function getTabLabel()
    {
        return __('Category Information');
    }

    public function getTabTitle()
    {
        return __('Category Information');
    }

  
    public function canShowTab()
    {
        return true;
    }

    
    public function isHidden()
    {
        return false;
    }

    protected function _isAllowedAction($resourceId)
    {
        return $this->_authorization->isAllowed($resourceId);
    }
}
