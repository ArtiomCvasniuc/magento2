<?php
 
namespace Themevast\Testimonials\Block\Adminhtml\Testimonials;

use Magento\Backend\Block\Widget\Form\Container;
use Magento\Backend\Block\Widget\Context;
use Magento\Framework\Registry;
 
class Edit extends Container
{
    protected $_coreRegistry = null;
 
    public function __construct(
        Context $context,
        Registry $registry,
        array $data = []
    ) {
        $this->_coreRegistry = $registry;
        parent::__construct($context, $data);
    }
 
    protected function _construct()
    {
        $this->_objectId = 'testimonial_id';
        $this->_controller = 'adminhtml_testimonials';
        $this->_blockGroup = 'Themevast_Testimonials';
 
        parent::_construct();
 
        $this->buttonList->update('save', 'label', __('Save'));
        $this->buttonList->add(
            'saveandcontinue',
            [
                'label' => __('Save and Continue Edit'),
                'class' => 'save',
                'data_attribute' => [
                    'mage-init' => [
                        'button' => [
                            'event' => 'saveAndContinueEdit',
                            'target' => '#edit_form'
                        ]
                    ]
                ]
            ],
            -100
        );
      /*   $this->buttonList->update('delete', 'label', __('Delete')); */
    }

    public function getHeaderText()
    {
        $newsRegistry = $this->_coreRegistry->registry('testimonials_data');
        if ($newsRegistry->getId()) {
            $newsTitle = $this->escapeHtml($newsRegistry->getName());
            return __("Edit Testimonial '%1'", $newsTitle);
        } else {
            return __('Add Testimonial');
        }
    }
 
   protected function _isAllowedAction($resourceId)
    {
        return $this->_authorization->isAllowed($resourceId);
    }

    protected function _getSaveAndContinueUrl()
    {
        return $this->getUrl('*/*/save', ['_current' => true, 'back' => 'edit', 'active_tab' => '{{testimonial_id}}']);
    }

    protected function _prepareLayout()
    {
        $this->_formScripts[] = "
            function toggleEditor() {
                if (tinyMCE.getInstanceById('category_content') == null) {
                    tinyMCE.execCommand('mceAddControl', false, 'category_content');
                } else {
                    tinyMCE.execCommand('mceRemoveControl', false, 'category_content');
                }
            };
        ";
        return parent::_prepareLayout();
    }
}