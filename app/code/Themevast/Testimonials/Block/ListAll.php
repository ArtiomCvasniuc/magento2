<?php
namespace Themevast\Testimonials\Block;

use Magento\Framework\View\Element\Template;

class ListAll extends Template
{
    protected $testimonialsFactory;

    protected $testimonialsCollection;

    protected $_helper;

    protected $storeManager;
	protected $_objectManager;
	protected $_filterProvider;
    public function __construct(
        Template\Context $context,
        \Themevast\Testimonials\Model\ResourceModel\Testimonials\CollectionFactory $collection,
        \Themevast\Testimonials\Helper\Data $helper,
		\Magento\Cms\Model\Template\FilterProvider $filterProvider,
		\Magento\Framework\ObjectManagerInterface $objectManagerInterface,
        array $data
    )
    {
        parent::__construct($context, $data);
        $this->testimonialsCollection = $collection;
        $this->_helper = $helper;
		$this->storeManager = $context->getStoreManager();
		$this->_filterProvider = $filterProvider;
		$this->_objectManager = $objectManagerInterface;
        $collection = $this->testimonialsCollection->create()
						->addActiveFilter()
						->addStoreFilter($this->storeManager->getStore()->getId())
						->setOrder('position', 'DESC');
        
        $this->setCollection($collection);
    }

    protected function _prepareLayout()
    {
		$this->_addBreadcrumbs();
		$this->pageConfig->getTitle()->set($this->_helper->getTitlePage());
        parent::_prepareLayout();
        $pager = $this->getLayout()->createBlock(
            'Magento\Theme\Block\Html\Pager',
            'themevast.testimonials.pager'
        );

        $pager->setAvailableLimit($this->getPerPage());
        $pager->setShowPerPage(true);
        $pager->setCollection($this->getCollection());

        $this->setChild('pager', $pager);
        $this->getCollection()->load();

        return $this;
    }
	  protected function _addBreadcrumbs()
    {
			$breadcrumbsBlock = $this->getLayout()->getBlock('breadcrumbs');
        
            $breadcrumbsBlock->addCrumb(
                'home',
                [
                    'label' => __('Home'),
                    'title' => __('Go to Home Page'),
                    'link' => $this->_storeManager->getStore()->getBaseUrl()
                ]
            );
            $breadcrumbsBlock->addCrumb(
                'testimonials',
                [
                    'label' => __('Testimonials'),
                    'title' => __(sprintf('Go to Testimonials'))
                ]
            );
    }
    public function getPagerHtml()
    {
        return $this->getChildHtml('pager');
    }

    public function isEnabledInPaging()
    {
        return $this->_helper->isEnabledInPaging();
    }

    public function getPerPage()
    {
        $values = trim($this->_helper->getPerPage());
        $tmp = explode(",", $values);
        $tmp2 = [];
        foreach ($tmp as $key => $value) {
            $tmp2[$value] = $value;
        }
        return $tmp2;
    }

    public function isAllowCustomerSubmit()
    {
        return $this->_helper->isAllowCustomerSubmit();
    }
    
	 public function isAllowGuestSubmit()
    {
        return $this->_helper->isAllowGuestSubmit();
    }
	public function getAllRating(){
		return $this->_objectManager->create('Themevast\Testimonials\Model\Config\Source\Rat')->getStarArray();
	} 
    public function checkLogin()
    {
        $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
        $customerSession = $objectManager->create('Magento\Customer\Model\Session');

        if ($customerSession->isLoggedIn()) {
            $customerSession->getCustomerId();  
            $customerSession->getCustomerGroupId();
            $customerSession->getCustomer();
            $customerSession->getCustomerData();

            return true;
        }
        return false;
    }
    public function getNewUrl()
    {
        return $this->getUrl('testimonials/index/new');
    }
	
	public function getFilterTestimonials($value)
    {
        return $this->_filterProvider->getPageFilter()->filter(
            $value
        );
    }
}