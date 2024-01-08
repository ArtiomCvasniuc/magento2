<?php
namespace Themevast\ThemevastUp\Block\Adminhtml\Export;

class Edit extends \Magento\Backend\Block\Widget\Form\Container
{
	
    protected $_coreRegistry = null;

    
    public function __construct(
        \Magento\Backend\Block\Widget\Context $context,
        \Magento\Framework\Registry $registry,
        array $data = []
    ) {
        $this->_coreRegistry = $registry;
        parent::__construct($context, $data);
    }

    protected function _construct()
    {
        parent::_construct();
        $this->_blockGroup = 'Themevast_ThemevastUp';
        $this->_controller = 'adminhtml_export';
        $this->updateButton('save', 'label', __('Export Data'));
    }

    public function getHeaderText()
    {
        return __('Themevast Export');
    }

    protected function _isAllowedAction($resourceId)
    {
        return $this->_authorization->isAllowed($resourceId);
    }

    protected function _prepareLayout()
    {
        return parent::_prepareLayout();
    }
}