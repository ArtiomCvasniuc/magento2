<?php
//ducdevphp@gmail.com
namespace Themevast\Brand\Block\Adminhtml\Grid\Column;

class Multistore extends \Magento\Backend\Block\Widget\Grid\Column
{
	protected $_objectManager;
	public function __construct(
        \Magento\Backend\Block\Template\Context $context,
		\Magento\Framework\ObjectManagerInterface $objectManager,
        array $data = []
    ) {
		$this->_objectManager =$objectManager;
        parent::__construct($context, $data);
    }
	
    public function getFrameCallback()
    {
        return [$this, 'decorateStore'];
    }
	
	

    public function decorateStore($value, $row, $column, $isExport)
    {
		$brand_id = $row->getData('brand_id');
		$brand = $this->_objectManager->create('Themevast\Brand\Model\Brand')->load($brand_id);
        $store_ids = $brand->getData('store_id');
		$manager =$this->_objectManager->create('Magento\Store\Model\StoreManagerInterface');
		$html='';
		if(in_array(0,$store_ids)){
			return __('All Store Views');
		}else{
			$i=1;
			foreach($store_ids as $store_id){
				
				$store = $manager->getStore($store_id);
				$html.=  $store->getName();
				if(count($store_ids)!=$i){
					$html.=' | ';
				}
				$i++;
			}
		return $html;
		}
    }
}
