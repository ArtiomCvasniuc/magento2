<?php
namespace Themevast\Brand\Block\Adminhtml\Grid\Column;

class Image extends \Magento\Backend\Block\Widget\Grid\Column
{
	protected $_storeManager;
	protected $_brandFactory;
	public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
		\Themevast\Brand\Model\BrandFactory $brandFactory,
        array $data = []
    ) {
        $this->_storeManager = $storeManager;
		$this->_brandFactory = $brandFactory;
        parent::__construct($context, $data);
    }
    public function getFrameCallback()
    {
        return [$this, 'decorateImage'];
    }

    public function decorateImage($value, $row, $column, $isExport)
    {
		$html = '';
		$mediaUrl = $this->_storeManager->getStore()->getBaseUrl(\Magento\Framework\UrlInterface::URL_TYPE_MEDIA);
		if($brand_id = $row->getData('brand_id')){
		$brand = $this->_brandFactory->create()->load($brand_id);
			$image_url = $mediaUrl.$brand->getData('image');
			$html = '<img width="150" height="75" src ="' .$image_url. '" alt="' . $brand->getTitle() . '" >';
		}
		return $html;
    }
}
