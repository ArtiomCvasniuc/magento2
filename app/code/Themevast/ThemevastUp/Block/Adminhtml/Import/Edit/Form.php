<?php
namespace Themevast\ThemevastUp\Block\Adminhtml\Import\Edit;
use Magento\Config\Model\Config\Source\Yesno;

class Form extends \Magento\Backend\Block\Widget\Form\Generic
{
    protected $_yesno;

    protected $_systemStore;

    protected $_exportPaths;

    protected $_yesNo;

    protected $_importFiles;

    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \Magento\Framework\Registry $registry,
        \Magento\Framework\Data\FormFactory $formFactory,
        \Themevast\ThemevastUp\Model\Export\Source\Tv\ExportPaths $ExportPaths,
        \Magento\Store\Model\System\Store $systemStore,
        \Themevast\ThemevastUp\Model\Import\Source\Tv\Files  $importFiles,
        \Magento\Config\Model\Config\Source\Yesno $yesno,
        array $data = []
    ) {
        parent::__construct($context, $registry, $formFactory, $data);
        $this->_yesno = $yesno;
        $this->_exportPaths = $ExportPaths;
        $this->_systemStore = $systemStore;
        $this->_importFiles = $importFiles;
    }

    protected function _prepareForm()
    {

        if($this->_isAllowedAction('Themevast_ThemevastUp::themebackup_import')){
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

        $fieldset = $form->addFieldset('base_fieldset', ['legend' => __('Themevast Import')]);

        $folders = $this->_exportPaths->toArray();
        $fds = [];
        foreach ($folders as $k => $v) {
            $k = strtolower(str_replace("/", "-", $k));
            $fds[$k] = $v;
        }

        $fieldset->addField(
            'folder',
            'select',
                [
                    'label' => __('Backup Path'),
                    'title' => __('Backup Path'),
                    'name' => 'folder',
                    'options' => $fds,
                    'disabled' => $isElementDisabled,
                    'class' => 'tv-import'
                ]
        );
        foreach ($this->_exportPaths->toArray() as $tv => $val) {
            $orginKey = $tv;
            $tv = strtolower(str_replace("/", "-", $tv));
            $fieldPreset = $fieldset->addField($tv,
                'select', [
                    'name'      => $tv,
                    'label'     => __('Select File Import'),
                    'title'     => __('Select File Import'),
                    'values'    => $this->_importFiles->toOptionArray($orginKey),
                    'note' => '
					
                    File Path (file name + date format Ymd_His) : <strong>app/design/frontend/'.$orginKey.'/backup</strong>
                    <script type="text/javascript">
                    require(["jquery"], function(){
                        jQuery("#'.$tv.'").parents(".admin__field").hide();
                        jQuery(".tv-import").change(function(){
                            var folder_name = jQuery(this).val();
                            if(folder_name!="'.$tv.'"){
                                jQuery("#'.$tv.'").parents(".admin__field").hide();    
                            }else{
                                jQuery("#'.$tv.'").parents(".admin__field").show();    
                            }
                        }).change();
                        jQuery("#'.$tv.'").change(function(){
                            var data_import_file = jQuery(this).val();
                            if(data_import_file == "data_import_file"){
                                jQuery("#data_import_file").parents(".admin__field").show();
                            }else{
                                jQuery("#data_import_file").parents(".admin__field").hide();
                            }
                        }).change();
                    });</script>'
                ]);
        }

        $fieldset->addField(
            'data_import_file',
            'file',
            [
                'name' => 'data_import_file',
                'label' => __('Upload Custom File'),
                'title' => __('Upload Custom File')
            ]
        );

        $fieldset->addField(
            'overwrite_blocks',
            'select',
            [
                'name' => 'overwrite_blocks',
                'label' => __('Overwrite Data'),
                'title' => __('Overwrite Data'),
                'values' => $this->_yesno->toArray(),
                'note' => __('Overwrite current data(cms page , static block , modules themevast ) If they exist in file import(export backup)')
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
                    'note' => __('Store apply')
                ]
            );

        $form->setUseContainer(true);
        $this->setForm($form);
        return parent::_prepareForm();
    }

    protected function _isAllowedAction($resourceId)
    {
        return $this->_authorization->isAllowed($resourceId);
    }
}