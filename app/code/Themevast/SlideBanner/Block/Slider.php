<?php
namespace Themevast\SlideBanner\Block;

/**
 * Cms block content block
 */
class Slider extends \Magento\Framework\View\Element\Template 
{
    protected $_filterProvider;
	protected $_sliderFactory;
	protected $_bannerFactory;

	protected $_scopeConfig;

	/**
	 * @var \Magento\Store\Model\StoreManagerInterface
	 */
	protected $_storeManager;

    /**
     * @param Context $context
     * @param array $data
     */
	
   public function __construct(
		\Magento\Framework\View\Element\Template\Context $context,
		\Themevast\SlideBanner\Model\SliderFactory $sliderFactory,
		\Themevast\SlideBanner\Model\SlideFactory $slideFactory,
		\Magento\Cms\Model\Template\FilterProvider $filterProvider,
		array $data = []
	) {
		parent::__construct($context, $data);
		$this->_sliderFactory = $sliderFactory;
		$this->_bannerFactory = $slideFactory;
		$this->_scopeConfig = $context->getScopeConfig();
		$this->_storeManager = $context->getStoreManager();
		$this->_filterProvider = $filterProvider;
	}

    /**
     * Prepare Content HTML
     *
     * @return string
     */
    protected function _beforeToHtml()
    {
        $sliderId = $this->getSliderId();
        if ($sliderId && !$this->getTemplate()) {
			$this->setTemplate("Themevast_SlideBanner::slider.phtml");
        }
        return parent::_beforeToHtml();
    }

    /**
     * Return identifiers for produced content
     *
     * @return array
     */
	public function getImageElement($src)
	{
		$mediaUrl = $this->_storeManager->getStore()->getBaseUrl(\Magento\Framework\UrlInterface::URL_TYPE_MEDIA);
		return '<img src="'. $mediaUrl . $src . '" alt="" />';
	}
	public function getMediaFolder(){
		$mediaUrl = $this->_storeManager->getStore()->getBaseUrl(\Magento\Framework\UrlInterface::URL_TYPE_MEDIA);
		return $mediaUrl;
	}
	public function getBannerCollection()
	{
		$sliderId = $this->getSlider()->getId();
		if(!$sliderId)
			return [];
		$collection = $this->_bannerFactory->create()->getCollection()->addFieldToFilter('slide_status',1);
		$collection->addFieldToFilter('slider_id', $sliderId);
		return $collection;
	}
	public function getSlider()
	{
		$sliderId = $this->getSliderId();
		$slider = $this->_sliderFactory->create();
		$slider->load($sliderId);
		return $slider;
	}
	public function getContentText($html)
	{
		$html = $this->_filterProvider->getPageFilter()->filter($html);
        return $html;
	}
}
