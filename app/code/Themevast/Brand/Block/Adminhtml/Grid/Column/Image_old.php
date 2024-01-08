<?php
namespace Themevast\Brand\Block\Adminhtml\Grid\Column;

class Image extends \Magento\Backend\Block\Widget\Grid\Column
{

    public function getFrameCallback()
    {
        return [$this, 'decorateStatus'];
    }

    public function decorateStatus($value, $row, $column, $isExport)
    {

		$objectManager = \Magento\Framework\App\ObjectManager::getInstance();
		$brand_id = $row->getData('brand_id');
		$brand = $objectManager->create('Themevast\Brand\Model\Brand')->load($brand_id);
		$storeManager = $objectManager->create('Magento\Store\Model\StoreManagerInterface');
        $image_url = $brand->getData('image');
		$url = $storeManager->getStore()->getBaseUrl(\Magento\Framework\UrlInterface::URL_TYPE_MEDIA) . $image_url;
		return '<img width="150" height="75" src ="' . $url . '" alt="' . $brand->getImage() . '" >';
    }
}
