<?php
namespace Themevast\Brand\Block\Adminhtml\Brand\Edit\Tab;

use Themevast\Brand\Model\Config\Source\Status;

class Form extends \Magento\Backend\Block\Widget\Form\Generic
 implements \Magento\Backend\Block\Widget\Tab\TabInterface {

	protected $_systemStore;

	public function __construct(
		\Magento\Backend\Block\Template\Context $context,
		\Magento\Framework\Registry $registry,
		\Magento\Framework\Data\FormFactory $formFactory,
		\Magento\Store\Model\System\Store $systemStore,
		array $data = []
	) {
		$this->_localeDate = $context->getLocaleDate();
		$this->_systemStore = $systemStore;
		parent::__construct($context, $registry, $formFactory, $data);
	}


	protected function _prepareForm() {
		$model = $this->_coreRegistry->registry('brand_data');

		$form = $this->_formFactory->create();
		
		$fieldset = $form->addFieldset('base_fieldset', ['legend' => __('Brand Information')]);

		if ($model->getId()) {
			$fieldset->addField('brand_id', 'hidden', ['name' => 'brand_id']);
		}

		$elements = [];
		$elements['title'] = $fieldset->addField(
			'title',
			'text',
			[
				'name' => 'title',
				'label' => __('Title'),
				'title' => __('Title'),
			]
		);
		$elements['status'] = $fieldset->addField(
			'status',
			'select',
			[
				'label' => __('Status'),
				'title' => __('Brand Status'),
				'name' => 'status',
				'options' => Status::getAvailableStatuses(),
			]
		);
		 if (!$model->getId()) {
            $model->setData('status',Status::STATUS_ENABLED);
        }
		$elements['link'] = $fieldset->addField(
			'link',
			'text',
			[
				'title' => __('Link Brand'),
				'label' => __('Link Brand'),
				'name' => 'link',
			]
		);

		$elements['image'] = $fieldset->addField(
			'image',
			'image',
			[
				'title' => __('Brand Image'),
				'label' => __('Brand Image'),
				'name' => 'image',
			]

		);
		
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
		
		$elements['description'] = $fieldset->addField(
			'description',
			'textarea',
			[
				'title' => __('Description'),
				'label' => __('Description'),
				'name' => 'description',
			]
		);
		
		$elements['order'] = $fieldset->addField(
			'position',
			'text',
			[
				'name' => 'position',
				'label' => __('Position'),
				'title' => __('Position'),
			]
		);
			
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
