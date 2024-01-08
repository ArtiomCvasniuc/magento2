<?php
namespace Themevast\Testimonials\Block;

use Magento\Framework\View\Element\Template;

class FormNew extends Template
{

    protected $_helper;

    protected $_customerSession;

    protected $_testmonialsFactory;
    
	protected $_objectManager;
	
    public function __construct(
         Template\Context $context,
        \Themevast\Testimonials\Helper\Data $helper,
        \Magento\Customer\Model\Session $customerSession,
        \Themevast\Testimonials\Model\TestimonialsFactory $testimonialsFactory,
		\Magento\Framework\ObjectManagerInterface $objectManagerInterface,
        array $data
    ) {
        parent::__construct($context, $data);
        $this->_helper = $helper;
        $this->_customerSession = $customerSession;
        $this->_testmonialsFactory = $testimonialsFactory;
		$this->_objectManager = $objectManagerInterface;
    }

    protected function _toHtml()
    {
        $html = parent::_toHtml();
        $this->unsetFormData('testimonials_form_data');
        return $html;
    }
    
	public function getStoreId()
	{
		return $this->_storeManager->getStore()->getId();
	}
	
    public function getFormAction()
    {
        return $this->getUrl('testimonials/index/post', ['_secure' => true]);
    }

    public function getFormData()
    {
        $formData = $this->_customerSession->getData('testimonials_form_data');
        if(!$formData) {
            $formData = $this->_testmonialsFactory->create();
        }
        $formData->setData('', '');

        return $formData;
    }
    
    public function unsetFormData($formData){
        return $this->_customerSession->clearStorage();
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

    public function getAmountWord()
    {
        return $this->_helper->getAmountWord();
    }

	public function getAllRating(){
		return $this->_objectManager->create('Themevast\Testimonials\Model\Config\Source\Rat')->getStarArray();
	}
    public function isCaptcha(){
        if($this->_helper->isCaptcha() == 0){
            return 'display: none;';
        }
        return '';
    }
}