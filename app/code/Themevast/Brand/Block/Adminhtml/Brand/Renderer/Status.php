<?php
namespace Themevast\Brand\Block\Adminhtml\Brand\Renderer;

class Status extends \Magento\Backend\Block\Widget\Grid\Column\Renderer\AbstractRenderer {
	
	public function __construct(
		\Magento\Backend\Block\Context $context,
		array $data = []
	) {
		parent::__construct($context, $data);
	}

	public function render(\Magento\Framework\DataObject $row) {
	  if ( \Themevast\Brand\Model\Config\Source\Status::STATUS_ENABLED == $row->getStatus()) {
            $cell = '<span class="grid-severity-notice"><span>' . __('Enable'). '</span></span>';
        } else {
            $cell = '<span class="grid-severity-critical"><span>' .__('Disable'). '</span></span>';
        }
        return $cell;
	}
	
}
