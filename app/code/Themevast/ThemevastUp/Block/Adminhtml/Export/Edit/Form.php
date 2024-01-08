<?php

namespace Themevast\ThemevastUp\Block\Adminhtml\Export\Edit;

class Form extends \Magento\Backend\Block\Widget\Form\Generic
{
    
    protected $_yesno;

    protected $_systemStore;

    protected $_tvModules;

    protected $_exportPaths;

    protected $_cmsPage;

    protected $_staticBlock;
	
    protected $_widgets;

    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \Magento\Framework\Registry $registry,
        \Magento\Framework\Data\FormFactory $formFactory,
        \Magento\Config\Model\Config\Source\Yesno $yesno,
        \Themevast\ThemevastUp\Model\Export\Source\Tv\ExportPaths $ExportPaths,
        \Themevast\ThemevastUp\Model\Export\Source\Tv\TvModules $tvModules,
        \Themevast\ThemevastUp\Model\Export\Source\Tv\CmsPages $cmsPage,
        \Themevast\ThemevastUp\Model\Export\Source\Tv\StaticBlocks $staticBlock,
        \Themevast\ThemevastUp\Model\Export\Source\Tv\Widgets $widgets,
        \Magento\Store\Model\System\Store $systemStore,
        array $data = []
    ) {
        parent::__construct($context, $registry, $formFactory, $data);
        $this->_yesno = $yesno;
        $this->_exportPaths = $ExportPaths;
        $this->_tvModules = $tvModules;
        $this->_cmsPage = $cmsPage;
        $this->_staticBlock = $staticBlock;
        $this->_widgets = $widgets;
        $this->_systemStore = $systemStore;
    }

    protected function _prepareForm()
    {
        
        if($this->_isAllowedAction('Themevast_ThemevastUp::themebackup_export')){
            $isElementDisabled = false;
        }else {
            $isElementDisabled = true;
        }
		
        $form = $this->_formFactory->create(
                [
                    'data' => [
                    'id' => 'edit_form',
                    'action' => $this->getData('action'),
                    'method' => 'post',
                    'enctype' => 'multipart/form-data'
                    ]
                ]
            );

        $fieldset = $form->addFieldset('base_fieldset', ['legend' => __('Export Data')]);
		
		$fieldset->addField(
            'file_name',
            'text',
                [
                    'name' => 'file_name',
                    'label' => __('File Backup Name'),
                    'title' => __('File Backup Name'),
                    'required' => true,
                    'disabled' => $isElementDisabled
                ]
         );
        $fieldset->addField(
            'folder',
            'select',
                [
                    'label' => __('Data Backup Folder'),
                    'title' => __('Data Backup Folder'),
                    'name' => 'folder',
                    'options' => $this->_exportPaths->toArray(),
                    'disabled' => $isElementDisabled,
                    'note' => '<script type="text/javascript">
                        require(["jquery"], function(){
                            jQuery("#folder").change(function(){
                                var val ="app/design/" + jQuery(this).val();
                                jQuery("#folder-note").html("");
                                jQuery("#folder-note").append("Folder Backup: "+val);
                            }).change();
                        });
                    </script>'
                ]
        );

        $field = $fieldset->addField(
                'store_id',
                'select',
                [
                    'name' => 'store_id',
                    'label' => __('Configuration Scope'),
                    'title' => __('Configuration Scope'),
                    'values' => $this->_systemStore->getStoreValuesForForm(false, true),
                    'disabled' => $isElementDisabled,
                    'note' => __('Configuration of selected store will be saved in a file ( Apply for all system config of modules ) ')
                ]
            );

        $field = $fieldset->addField(
                'modules',
                'multiselect',
                [
                    'name' => 'modules[]',
                    'label' => __('Themevast Modules'),
                    'title' => __('Themevast Modules'),
                    'values' => $this->_tvModules->toOptionArray(),
                    'disabled' => $isElementDisabled
                ]
            );
            $renderer = $this->getLayout()->createBlock(
                'Themevast\ThemevastUp\Block\Adminhtml\Form\Renderer\Fieldset\Element'
            );
            $field->setRenderer($renderer);

        $field = $fieldset->addField(
                'cmspages',
                'multiselect',
                [
                    'name' => 'cmspages[]',
                    'label' => __('CMS Pages'),
                    'title' => __('CMS Pages'),
                    'values' => $this->_cmsPage->toOptionArray(),
                    'disabled' => $isElementDisabled
                ]
            );
            $renderer = $this->getLayout()->createBlock(
                'Themevast\ThemevastUp\Block\Adminhtml\Form\Renderer\Fieldset\Element'
            );
            $field->setRenderer($renderer);

        $field = $fieldset->addField(
                'cmsblocks',
                'multiselect',
                [
                    'name' => 'cmsblocks[]',
                    'label' => __('CMS Blocks'),
                    'title' => __('CMS Blocks'),
                    'values' => $this->_staticBlock->toOptionArray(),
                    'disabled' => $isElementDisabled
                ]
            );
            $renderer = $this->getLayout()->createBlock(
                'Themevast\ThemevastUp\Block\Adminhtml\Form\Renderer\Fieldset\Element'
            );
            $field->setRenderer($renderer);

            $field = $fieldset->addField(
                'widgets',
                'multiselect',
                [
                    'name' => 'widgets[]',
                    'label' => __('Widgets'),
                    'title' => __('Widgets'),
                    'values' => $this->_widgets->toOptionArray(),
                    'disabled' => $isElementDisabled
                ]
            );
            $renderer = $this->getLayout()->createBlock(
                'Themevast\ThemevastUp\Block\Adminhtml\Form\Renderer\Fieldset\Element'
            );
            $field->setRenderer($renderer);

        $form->setUseContainer(true);
        $this->setForm($form);
        return parent::_prepareForm();
    }

    protected function _isAllowedAction($resourceId)
    {
        return $this->_authorization->isAllowed($resourceId);
    }
}