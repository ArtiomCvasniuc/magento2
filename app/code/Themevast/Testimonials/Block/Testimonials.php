<?php
namespace Themevast\Testimonials\Block;

class Testimonials extends \Magento\Framework\View\Element\Template implements \Magento\Widget\Block\BlockInterface
{
    protected $_filterProvider;
    protected $_testimonialCollection;
    protected $storeManager;
	protected $_scopeConfig;
	protected $_objectManager;
    public function __construct(
        \Magento\Framework\View\Element\Template\Context $context,
		\Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig,
		\Magento\Cms\Model\Template\FilterProvider $filterProvider,
        \Themevast\Testimonials\Model\ResourceModel\Testimonials\CollectionFactory $testimonialCollection,
		\Magento\Framework\ObjectManagerInterface $objectManagerInterface,
        array $data = []
    ) {
        parent::__construct($context, $data);
        $this->_testimonialCollection = $testimonialCollection;
		$this->_scopeConfig = $scopeConfig;
		$this->storeManager = $context->getStoreManager();
		$this->_filterProvider = $filterProvider;
		$this->_objectManager = $objectManagerInterface;
    }
    public function getAllRating(){
		return $this->_objectManager->create('Themevast\Testimonials\Model\Config\Source\Rat')->getStarArray();
	}
	public function getConfigSlider($value=''){

	   $config =  $this->_scopeConfig->getValue('testimonials_setting/slide_testimonial_configuration/'.$value, \Magento\Store\Model\ScopeInterface::SCOPE_STORE);
	   return $config; 
	 
	}
    public function getTestimonials()
    {
        $brand = $this->_testimonialCollection->create()
            ->addActiveFilter()
            ->addStoreFilter($this->storeManager->getStore()->getId())
            ->setOrder('position', 'DESC');
		$brand->setPageSize($this->getConfigSlider('qty'))->setCurPage(1);
		return $brand;
    }
	public function getLinkAllAction()
    {
        return $this->getUrl('testimonials/index/index');
    }
	public function _formatDate($dateString)
    {
        $date = new \DateTime($dateString);
        if ($date == new \DateTime('today')) {
            return $this->_localeDate->formatDateTime(
                $date,
                \IntlDateFormatter::NONE,
                \IntlDateFormatter::SHORT
            );
        }
        return $this->_localeDate->formatDateTime(
            $date,
            \IntlDateFormatter::MEDIUM,
            \IntlDateFormatter::MEDIUM
        );
    }
	public function getTitle()
    {
        return $this->getData('title');
    }
	public function getIdentify()
    {
        return $this->getData('identify');
    }
	public function getMediaFolder() {
		$media_folder = $this->storeManager->getStore()->getBaseUrl(\Magento\Framework\UrlInterface::URL_TYPE_MEDIA);
		return $media_folder;
	}
	public function getContentText($html)
	{
		$html = $this->_filterProvider->getPageFilter()->filter($html);
        return $html;
	}
}
