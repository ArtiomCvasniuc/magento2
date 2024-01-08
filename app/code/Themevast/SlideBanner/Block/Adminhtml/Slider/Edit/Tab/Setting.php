<?php
 
namespace Themevast\SlideBanner\Block\Adminhtml\Slider\Edit\Tab;

use Magento\Backend\Block\Widget\Form\Generic;
use Magento\Backend\Block\Widget\Tab\TabInterface;
use Magento\Backend\Block\Template\Context;
use Magento\Framework\Registry;
use Magento\Framework\Data\FormFactory;
use Magento\Cms\Model\Wysiwyg\Config;
 
class Setting extends Generic implements TabInterface
{
    /**
     * @var \Magento\Cms\Model\Wysiwyg\Config
     */
    protected $_wysiwygConfig;
 
    protected $_newsStatus;
    protected $_objectManager;
 
   /**
     * @param Context $context
     * @param Registry $registry
     * @param FormFactory $formFactory
     * @param Config $wysiwygConfig
     * @param array $data
     */
    public function __construct(
        Context $context,
        Registry $registry,
        FormFactory $formFactory,
        Config $wysiwygConfig,
		\Magento\Framework\ObjectManagerInterface $objectManager,
        array $data = []
    ) {
        $this->_wysiwygConfig = $wysiwygConfig;
        $this->_objectManager = $objectManager;
        parent::__construct($context, $registry, $formFactory, $data);
    }
 
    /**
     * Prepare form fields
     *
     * @return \Magento\Backend\Block\Widget\Form
     */
    protected function _prepareForm()
    {
       /** @var $model \Themevast\SlideBanner\Model\Slide */
        $model = $this->_coreRegistry->registry('slider_form_data');
		$defaultSetting = array('items'=>1, 'itemsDesktop'=>'1', 'itemsDesktopSmall' => '1', 'itemsTablet' => '1', 'itemsMobile' => '1', 'slideSpeed' => 500, 'paginationSpeed' => 500, 'rewindSpeed'=>500);
		$setting = $model->getSliderSetting();
		$data = array_merge($defaultSetting, $setting);
        /** @var \Magento\Framework\Data\Form $form */
        $form = $this->_formFactory->create();
        $form->setHtmlIdPrefix('slider_');
        $form->setFieldNameSuffix('slider');
 
        $fieldset = $form->addFieldset(
            'base_fieldset',
            ['legend' => __('Setting')]
        );
		$fieldset->addField(
            'autoPlay',
            'select',
            [
                'name'        => 'slider_setting[autoPlay]',
                'label'    => __('Autoplay'),
                'required'     => false,
				'values'=> [['value'=>false, 'label'=> __('False')], ['value'=>true, 'label'=> __('True')]]
            ]
        );
		$fieldset->addField(
            'navigation',
            'select',
            [
                'name'        => 'slider_setting[navigation]',
                'label'    => __('Navigation'),
                'required'     => false,
				'values'=> [['value'=>false, 'label'=> __('False')], ['value'=>true, 'label'=> __('True')]]
            ]
        );
		$fieldset->addField(
            'stopOnHover',
            'select',
            [
                'name'        => 'slider_setting[stopOnHover]',
                'label'    => __('Stop On Hover'),
                'required'     => false,
				'values'=> [['value'=>false, 'label'=> __('False')], ['value'=>true, 'label'=> __('True')]]
            ]
        );
		$fieldset->addField(
            'pagination',
            'select',
            [
                'name'        => 'slider_setting[pagination]',
                'label'    => __('Pagination'),
                'required'     => false,
				'values'=> [['value'=>false, 'label'=> __('False')], ['value'=>true, 'label'=> __('True')]]
            ]
        );
		$fieldset->addField(
            'scrollPerPage',
            'select',
            [
                'name'        => 'slider_setting[scrollPerPage]',
                'label'    => __('Scroll Per Page'),
                'required'     => false,
				'values'=> [['value'=>false, 'label'=> __('False')], ['value'=>true, 'label'=> __('True')]]
            ]
        );
		$fieldset->addField(
            'items',
            'text',
            [
                'name'        => 'slider_setting[items]',
                'label'    => __('Items'),
                'required'     => true,
				'class' => 'validate-number', 
				'default'=> 1
            ]
        );
		$fieldset->addField(
            'rewindSpeed',
            'text',
            [
                'name'        => 'slider_setting[rewindSpeed]',
                'label'    => __('Rewind Speed'),
                'required'     => true,
				'class' => 'validate-number', 
				'default'=> 1
            ]
        );
		$fieldset->addField(
            'paginationSpeed',
            'text',
            [
                'name'        => 'slider_setting[paginationSpeed]',
                'label'    => __('Pagination Speed'),
                'required'     => true,
				'class' => 'validate-number', 
				'default'=> 1
            ]
        );
		$fieldset->addField(
            'slideSpeed',
            'text',
            [
                'name'        => 'slider_setting[slideSpeed]',
                'label'    => __('Slide Speed'),
                'required'     => true,
				'class' => 'validate-number', 
				'default'=> 1
            ]
        );
		$fieldset->addField(
            'itemsDesktop',
            'text',
            [
                'name'        => 'slider_setting[itemsDesktop]',
                'label'    => __('Items Desktop'),
                'required'     => true
            ]
        );
		$fieldset->addField(
            'itemsDesktopSmall',
            'text',
            [
                'name'        => 'slider_setting[itemsDesktopSmall]',
                'label'    => __('Items Desktop Small'),
                'required'     => true
            ]
        );
		$fieldset->addField(
            'itemsTablet',
            'text',
            [
                'name'        => 'slider_setting[itemsTablet]',
                'label'    => __('Items Tablet'),
                'required'     => true
            ]
        );
        $form->setValues($data);
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
        return __('Slider Info');
    }
 
    /**
     * Prepare title for tab
     *
     * @return string
     */
    public function getTabTitle()
    {
        return __('Slider Info');
    }
 
    
    public function canShowTab()
    {
        return true;
    }
 
    public function isHidden()
    {
        return false;
    }
}