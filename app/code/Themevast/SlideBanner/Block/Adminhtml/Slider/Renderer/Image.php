<?php
namespace Themevast\SlideBanner\Block\Adminhtml\Slide\Renderer;
class Image extends \Magento\Backend\Block\Widget\Grid\Column\Renderer\Text
{
	protected $_storeManager;
    /**
     * Renders column
     *
     * @param  \Magento\Framework\DataObject $row
     * @return string
     */
	public function __construct(
        \Magento\Backend\Block\Context $context,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        array $data = []
    ) {
        $this->_storeManager = $storeManager;
        parent::__construct($context, $data);
    }
    public function render(\Magento\Framework\DataObject $row)
    {
        $html = '';
		$mediaUrl = $this->_storeManager->getStore()->getBaseUrl(\Magento\Framework\UrlInterface::URL_TYPE_MEDIA);
        if($image = $row->getSlideImage())
			$html = '<img height="50" src="' . $mediaUrl . $image . '" />';
        return $html;
    }
}
