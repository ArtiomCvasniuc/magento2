<?php
namespace Themevast\Brand\Block\Adminhtml\Grid\Column;

class Multistore extends \Magento\Backend\Block\Widget\Grid\Column
{

    public function getFrameCallback()
    {
        return [$this, 'decorateStatus'];
    }

    public function decorateStatus($value, $row, $column, $isExport)
    {
        /* if ($row->getIsActive() || $row->getStatus()) {
            $cell = '<span class="grid-severity-notice"><span>' . $value . '</span></span>';
        } else {
            $cell = '<span class="grid-severity-critical"><span>' . $value . '</span></span>';
        } */
		$objectManager = \Magento\Framework\App\ObjectManager::getInstance();
		$brand_id = $row->getData('brand_id');
		$brand = $objectManager->create('Themevast\Brand\Model\Brand')->load($brand_id);
        $store_ids = $brand->getData('store_id');
		$manager = $objectManager->create('Magento\Store\Model\StoreManagerInterface');
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
