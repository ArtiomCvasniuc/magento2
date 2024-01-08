<?php
namespace Themevast\Blog\Block\Adminhtml\Category\Edit\Tab;

class Meta extends \Magento\Backend\Block\Widget\Form\Generic implements
    \Magento\Backend\Block\Widget\Tab\TabInterface
{
    
    protected function _prepareForm()
    {
        
        $model = $this->_coreRegistry->registry('current_model');

        $isElementDisabled = !$this->_isAllowedAction('Themevast_Blog::category');

        $form = $this->_formFactory->create();

        $form->setHtmlIdPrefix('category_');

        $fieldset = $form->addFieldset(
            'meta_fieldset',
            ['legend' => __('Meta Data'), 'class' => 'fieldset-wide']
        );

        $fieldset->addField(
            'meta_keywords',
            'textarea',
            [
                'name' => 'meta_keywords',
                'label' => __('Keywords'),
                'title' => __('Meta Keywords'),
                'disabled' => $isElementDisabled
            ]
        );

        $fieldset->addField(
            'meta_description',
            'textarea',
            [
                'name' => 'meta_description',
                'label' => __('Description'),
                'title' => __('Meta Description'),
                'disabled' => $isElementDisabled
            ]
        );

        $this->_eventManager->dispatch('themevast_blog_category_edit_tab_meta_prepare_form', ['form' => $form]);

        $form->setValues($model->getData());

        $this->setForm($form);

        return parent::_prepareForm();
    }

    
    public function getTabLabel()
    {
        return __('Meta Data');
    }

 
    public function getTabTitle()
    {
        return __('Meta Data');
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
