<?php
namespace Themevast\Categorytab\Block;

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
		$this->setTemplate('Themevast_Categorytab::categorytab/product/ajax.phtml');
		return parent::_toHtml();
	}
	
	public function getProductHtml($data){
		  
		 $template = 'Themevast_Categorytab::categorytab/product/items.phtml';
		 if($this->getConfig('templatetype')){
			$template ='Themevast_Categorytab::categorytab/product/'.$this->getConfig('templatetype').'_items.phtml';
		 }
		 $html = $this->getLayout()->createBlock('Themevast\Categorytab\Block\ProductList')->setData($data)->setTemplate($template)->toHtml();
        return $html;
	}
}