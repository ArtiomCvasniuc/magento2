<?php
namespace Themevast\Themeoption\Block\Adminhtml\System\Config\Form\Field;

use Magento\Framework\Registry;

class Color extends \Magento\Config\Block\System\Config\Form\Field
{
    protected $_coreRegistry;
    
    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        Registry $coreRegistry,
        array $data = []
    ) {
        $this->_coreRegistry = $coreRegistry;
        parent::__construct($context, $data);
    }
    
    protected function _getElementHtml(\Magento\Framework\Data\Form\Element\AbstractElement $element)
    {
        $base = $this->getBaseUrl();
        $html = $element->getElementHtml();
		$moduleName = $this->getModuleName();
        $tvPath = $this->getViewFileUrl($moduleName.'/js');
        if(!$this->_coreRegistry->registry('colorpicker_loaded')) {
            $html .= '<script type="text/javascript" src="'. $tvPath .'/jscolor.js"></script><style type="text/css">input.jscolor { background-image: url('.$tvPath.'/color.png) !important; background-position: calc(100% - 8px) center; background-repeat: no-repeat; padding-right: 40px !important; } input.jscolor.disabled,input.jscolor[disabled] { pointer-events: none; }</style>';
            $this->_coreRegistry->registry('colorpicker_loaded', 1);
        }
        $html .= '<script type="text/javascript">
                var el = document.getElementById("'. $element->getHtmlId() .'");
                el.className = el.className + " jscolor";
            </script>';
        return $html;
    }
}