<?php
 
namespace Themevast\Brand\Block\Adminhtml\Brand\Edit;
 
use Magento\Backend\Block\Widget\Form\Generic;
 
class Form extends Generic
{
  
    protected function _prepareForm()
    {
        $form = $this->_formFactory->create(
            [
                'data' => [
                    'id'    => 'edit_form',
					'action' => $this->getUrl('*/*/save', ['store' => $this->getRequest()->getParam('store')]),
                    'method' => 'post',
					'enctype' => 'multipart/form-data',
                ]
            ]
        );
        $form->setUseContainer(true);
        $this->setForm($form);
 
        return parent::_prepareForm();
    }
}