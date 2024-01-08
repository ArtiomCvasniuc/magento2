<?php
namespace Themevast\ProductTab\Block;

class Ajax extends \Magento\Framework\View\Element\Template
{
	
    protected $urlHelper;

    public function __construct(
        \Magento\Catalog\Block\Product\Context $context,
        array $data = []
        ) {
        parent::__construct($context, $data);
    }

	public function getConfig($key, $default = '')
	{
		if($this->hasData($key))
		{
			return $this->getData($key);
		}
		return $default;
	}
/*ducdevphp*/
	public function _toHtml(){
		$this->setTemplate('Themevast_ProductTab::producttab/items/ajax.phtml');
		return parent::_toHtml();
	}
	
	public function getProductHtml($data){
		  
		 $template = 'Themevast_ProductTab::producttab/items/items_slider.phtml';
			
		 $html = $this->getLayout()->createBlock('Themevast\ProductTab\Block\ProductList')->setData($data)->setTemplate($template)->toHtml();
        return $html;
	}
}