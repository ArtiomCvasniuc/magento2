<?php
namespace Themevast\Testimonials\Block\Adminhtml\Testimonials\Edit;

use Magento\Backend\Block\Widget\Tabs as WigetTabs;

class Tabs extends WigetTabs {
	public function _construct()
	{
      parent::_construct();
      $this->setId('testimonial_manager');
      $this->setDestElementId('edit_form');
      $this->setTitle(__('Testimonial Information'));
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
                                'Themevast\Testimonials\Block\Adminhtml\Testimonials\Edit\Tab\Form'
                            )->toHtml()
                       ,
						 'active' => true
                    ]
                );  
      return parent::_prepareLayout();
	}
}
