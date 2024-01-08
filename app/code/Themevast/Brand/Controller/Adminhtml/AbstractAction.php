<?php

namespace Themevast\Brand\Controller\Adminhtml;


abstract class AbstractAction extends \Magento\Backend\App\Action {

	
	protected $_coreRegistry;
	
    public function __construct(\Magento\Backend\App\Action\Context $context, \Magento\Framework\Registry $coreRegistry)
    {
        $this->_coreRegistry = $coreRegistry;
        parent::__construct($context);
    }

	protected function _isAllowed() {
		return $this->_authorization->isAllowed('Themevast_Brand::brand');
	}
}
