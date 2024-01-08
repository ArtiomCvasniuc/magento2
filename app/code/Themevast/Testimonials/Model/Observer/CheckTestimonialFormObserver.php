<?php
namespace Themevast\Testimonials\Model\Observer;

use Themevast\Testimonials\Model\TestimonialsFactory;
use Magento\Framework\Event\ObserverInterface;
use Magento\Framework\App\Request\DataPersistorInterface;
use Magento\Framework\App\ObjectManager;
use Magento\Captcha\Observer\CaptchaStringResolver;

class CheckTestimonialFormObserver implements ObserverInterface
{
   
    protected $_helper;

    protected $_actionFlag;

    protected $messageManager;

    protected $redirect;

    protected $captchaStringResolver;

    private $dataPersistor;

    protected $_customerSession;

    protected $_testimonialsFactory;

    public function __construct(
        \Magento\Captcha\Helper\Data $helper,
        \Magento\Customer\Model\Session $customerSession,
        \Themevast\Testimonials\Model\TestimonialsFactory $testimonialsFactory,
        \Magento\Framework\App\ActionFlag $actionFlag,
        \Magento\Framework\Message\ManagerInterface $messageManager,
        \Magento\Framework\App\Response\RedirectInterface $redirect,
        CaptchaStringResolver $captchaStringResolver
    ) {
        $this->_helper = $helper;
        $this->_actionFlag = $actionFlag;
        $this->messageManager = $messageManager;
        $this->redirect = $redirect;
        $this->captchaStringResolver = $captchaStringResolver;
        $this->_customerSession = $customerSession;
        $this->_testimonialsFactory = $testimonialsFactory;
    }

    public function execute(\Magento\Framework\Event\Observer $observer)
    {
        $formId = 'testimonial_new_form';
        $captcha = $this->_helper->getCaptcha($formId);
        if ($captcha->isRequired()) {
            $controller = $observer->getControllerAction();
            if (!$captcha->isCorrect($this->captchaStringResolver->resolve($controller->getRequest(), $formId))) {
                $this->messageManager->addError(__('Incorrect Captcha please try again!.'));
                $this->setFormData($controller->getRequest());
                $formId = $controller->getRequest()->getPostValue();
                $this->_actionFlag->set('', \Magento\Framework\App\Action\Action::FLAG_NO_DISPATCH, true);
                $this->redirect->redirect($controller->getResponse(), 'testimonials/index/new');
            }
        }
    }
	 private function getDataPersistor()
    {
        if ($this->dataPersistor === null) {
            $this->dataPersistor = ObjectManager::getInstance()
                ->get(DataPersistorInterface::class);
        }

        return $this->dataPersistor;
    }
    private function setFormData($request)
    {
        $testimonial = $this->_testimonialsFactory->create()->setData($request->getParams());
        $this->_customerSession->setData('testimonials_form_data', $testimonial);
        return true;
    }
}