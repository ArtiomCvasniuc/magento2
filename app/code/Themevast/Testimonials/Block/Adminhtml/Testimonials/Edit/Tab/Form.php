<?php
namespace Themevast\Testimonials\Block\Adminhtml\Testimonials\Edit\Tab;

use Themevast\Testimonials\Model\Config\Source\Status;

class Form extends \Magento\Backend\Block\Widget\Form\Generic
 implements \Magento\Backend\Block\Widget\Tab\TabInterface {

	protected $_systemStore;
	protected $_wysiwygConfig;
    protected $_helper;
	protected $_objectManager;
	public function __construct(
		\Magento\Backend\Block\Template\Context $context,
		\Magento\Framework\Registry $registry,
		\Magento\Framework\Data\FormFactory $formFactory,
		\Magento\Store\Model\System\Store $systemStore,
		\Magento\Cms\Model\Wysiwyg\Config $wysiwygConfig,
		\Themevast\Testimonials\Helper\Data $helper,
		\Magento\Framework\ObjectManagerInterface $objectManagerInterface,
		array $data = []
	) {
		$this->_wysiwygConfig = $wysiwygConfig;
        $this->_helper = $helper;
		$this->_localeDate = $context->getLocaleDate();
		$this->_systemStore = $systemStore;
		$this->_objectManager = $objectManagerInterface;
		parent::__construct($context, $registry, $formFactory, $data);
	}


	protected function _prepareForm() {
		$model = $this->_coreRegistry->registry('testimonials_data');

		$form = $this->_formFactory->create();
		
		$fieldset = $form->addFieldset('base_fieldset', ['legend' => __('Testimonial Information')]);

		if ($model->getId()) {
			$fieldset->addField('testimonial_id', 'hidden', ['name' => 'testimonial_id']);
		}
      
        $fieldset->addField(
            'name',
            'text',
            ['name' => 'name', 'label' => __('Name'), 'title' => __('Name'), 'required' => true]
        );

      
        $fieldset->addField(
            'email',
            'text',
            ['name' => 'email', 'label' => __('Email'), 'title' => __('Email'), 'required' => true, 'class' => 'validate-email']
        );
		
		$fieldset->addField(
            'job',
            'text',
            ['name' => 'job', 'label' => __('Job'), 'title' => __('Job')]
        );
		
        $fieldset->addField(
            'avatar',
            'image',
            [
                'name' => 'avatar',
                'label' => __('Avatar'),
                'title' => __('Avatar'),
                'note' => 'Allow image type: jpg, jpeg, gif, png',
            ]
        );
				
		$fieldset->addField(
            'rating', 'select',
            [
                'name' => 'rating',
                'label' => __('Rating'),
                'values' => $this->_objectManager->create('Themevast\Testimonials\Model\Config\Source\Rat')->toOptionArray()
            ]
        );
		if (!$model->getId()) {
            $model->setData('rating',\Themevast\Testimonials\Model\Config\Source\Rat::STAR5);
        }
		$elements['store_id'] = $fieldset->addField(
			'store_id',
			'multiselect',
			[
				'name' => 'stores[]',
				'label' => __('Store View'),
				'title' => __('Store View'),
				'required' => true,
				'values' => $this->_systemStore->getStoreValuesForForm(false, true),
			]
		);
        $fieldset->addField(
            'website',
            'text',
            ['name' => 'website', 'label' => __('Website'), 'title' => __('Website')]
        );

		
        $fieldset->addField(
            'company',
            'text',
            ['name' => 'company', 'label' => __('Company'), 'title' => __('Company')]
        );

      
        $fieldset->addField(
            'address',
            'text',
            ['name' => 'address', 'label' => __('Address'), 'title' => __('Address')]
        );

	   $fieldset->addField(
            'position',
            'text',
            ['name' => 'position', 'label' => __('Position'), 'title' => __('Position')]
        );
		
        $fieldset->addField(
            'testimonial',
            'editor',
            [
                'name' => 'testimonial',
                'label' => __('Testimonial'),
                'title' => __('Testimonial'),
                'style'     => 'width:580px; height:265px;',
                'wysiwyg'   => true,
                'config'    => $this->_wysiwygConfig->getConfig(),
               /*  'class' => $this->getAmountWord() */
            ]
        );
         
       
        $fieldset->addField(
            'created_time',
            'date',
            [
                'name' => 'created_time',
                'label' => __('Created Time'),
                'date_format' => 'yyyy-MM-dd',
                'time_format' => 'hh:mm:ss',
                'style' => 'display:inline-block; width: 100%; padding: 5px 10px;',

            ]
        );

       
        $fieldset->addField(
            'is_active',
            'select',
            [
                'label' => __('Status'),
                'title' => __('Status'),
                'name' => 'is_active',
                'required' => true,
               'options' => Status::getAvailableStatuses(),
            ]
        );
		if (!$model->getId()) {
             $model->setData('is_active',Status::APPROVE);
         }
		$form->setValues($model->getData());
		$this->setForm($form);

		return parent::_prepareForm();
	}

	public function getBrand() {
		return $this->_coreRegistry->registry('brand');
	}

	public function getTabLabel()
    {
        return __('News Info');
    }
 
   
    public function getTabTitle()
    {
        return __('News Info');
    }
 
    public function canShowTab()
    {
        return true;
    }
 
    public function isHidden()
    {
        return false;
    }
	protected function _isAllowedAction($resourceId) {
		return $this->_authorization->isAllowed($resourceId);
	}
}
