<?php
namespace Themevast\Brand\Block\Adminhtml\Brand\Edit;

use Magento\Backend\Block\Widget\Tabs as WigetTabs;

class Tabs extends WigetTabs {
	public function _construct()
	{
      parent::_construct();
      $this->setId('brand_manager');
      $this->setDestElementId('edit_form');
      $this->setTitle(__('Brand Information'));
	}

	protected function _prepareLayout()
	{
	  $this->addTab(
                    'form_section',
                    [
                        'label' => __('General'),
						'title' => __('General'),
                        'content' => 
                            $this->getLayout()->createBlock(
                                'Themevast\Brand\Block\Adminhtml\Brand\Edit\Tab\Form'
                            )->toHtml()
                       ,
						 'active' => true
                    ]
                );  
      return parent::_prepareLayout();
	}
}
