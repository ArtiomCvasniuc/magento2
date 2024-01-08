<?php
namespace Themevast\Testimonials\Controller\Index;

use Themevast\Testimonials\Model\TestimonialsFactory;
use Magento\Framework\App\Action\Action;
use Magento\Framework\App\Action\Context;
use Magento\Framework\Filesystem;
use Magento\Framework\View\Result\PageFactory;

class Post extends Action
{

    protected $cacheTypeList;
    protected $resultPageFactory;
    protected $testimonialFactory;
    protected $forwordFactory;
    protected $adapterFactory;
    protected $uploader;
    protected $filesystem;
    protected $_helper;


    const XML_PATH_EMAIL_RECIPIENT = 'contact/email/recipient_email';
    
    protected $_transportBuilder;

    protected $inlineTranslation;

    protected $scopeConfig;

    protected $storeManager;
   
    protected $_escaper;

    protected $_filesystem;
    protected $_storeManager;
    protected $_directory;
    protected $_imageFactory;

    public function __construct(
        Context $context,
        PageFactory $pageFactory,
        TestimonialsFactory $testimonialFactory,
        \Magento\Framework\App\Cache\TypeListInterface $cacheTypeList,
        \Magento\Framework\Image\AdapterFactory $adapterFactory,
        \Magento\MediaStorage\Model\File\UploaderFactory $uploader,
        \Magento\Framework\Filesystem $filesystem,
        \Themevast\Testimonials\Helper\Data $helper,
        \Magento\Framework\Mail\Template\TransportBuilder $transportBuilder,
        \Magento\Framework\Translate\Inline\StateInterface $inlineTranslation,
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Magento\Framework\Escaper $escaper,
        \Magento\Store\Model\StoreManagerInterface $storeManager2,
        \Magento\Framework\Filesystem $filesystem2,
        \Magento\Framework\Image\AdapterFactory $imageFactory
    )
    {
        $this->_filesystem = $filesystem;
        $this->_storeManager = $storeManager;
        $this->_directory = $filesystem->getDirectoryWrite(\Magento\Framework\App\Filesystem\DirectoryList::MEDIA);
        $this->_imageFactory = $imageFactory;

        $this->cacheTypeList = $cacheTypeList;
        $this->_helper = $helper;
        $this->adapterFactory = $adapterFactory;
        $this->uploader = $uploader;
        $this->filesystem = $filesystem;
        $this->testimonialFactory = $testimonialFactory;
        $this->resultPageFactory = $pageFactory;
        parent::__construct($context);
        $this->_transportBuilder = $transportBuilder;
        $this->inlineTranslation = $inlineTranslation;
        $this->scopeConfig = $scopeConfig;
        $this->storeManager = $storeManager;
        $this->_escaper = $escaper;
    }

    public function execute()
    {
        $data = $this->getRequest()->getPostValue();
        $resultRedirect = $this->resultRedirectFactory->create();
        if ($data) {
            if (isset($_FILES['avatar']) && isset($_FILES['avatar']['name']) && strlen($_FILES['avatar']['name'])) {
                try {
                    $base_media_path = \Themevast\Testimonials\Model\Testimonials::BASE_MEDIA_PATH;
                    $uploader = $this->uploader->create(
                        ['fileId' => 'avatar']
                    );
                    $uploader->setAllowedExtensions(['jpg', 'jpeg', 'gif', 'png']);
                    $imageAdapter = $this->adapterFactory->create();
                    $uploader->addValidateCallback('image', $imageAdapter, 'validateUploadFile');
					$uploader->setAllowRenameFiles(true);
					$uploader->setFilesDispersion(true);
                    $mediaDirectory = $this->filesystem->getDirectoryRead(\Magento\Framework\App\Filesystem\DirectoryList::MEDIA);
					$result = $uploader->save($mediaDirectory->getAbsolutePath(\Themevast\Testimonials\Model\Testimonials::BASE_MEDIA_PATH));
                    $data['avatar'] = \Themevast\Testimonials\Model\Testimonials::BASE_MEDIA_PATH . $result['file'];
                } catch (\Exception $e) {
                    if ($e->getCode() == 0) {
                        $this->messageManager->addError($e->getMessage());
                    }
                }
            }
            $model = $this->_objectManager->create('Themevast\Testimonials\Model\Testimonials');

            $id = $this->getRequest()->getParam('testimonial_id');
            if ($id) {
                $model->load($id);
            }

            $model->setData($data);

            if ($this->_helper->isApprove() == 1) {
                $model->setIsActive(\Themevast\Testimonials\Model\Config\Source\Status::APPROVE);
                if($this->_helper->isApproveEmail() ==1){
                    //$model->sendApprovedEmailToCustomer();
                }
            }

            try {
                $model->save();
                if($this->_helper->isSubmitEmail() ==1){
                    //$model->sendSubmittedEmailToCustomer();
                }
                $this->cacheTypeList->invalidate('full_page');
                $this->messageManager->addSuccess(__($this->_helper->getMessageThankYou()));
                $this->_objectManager->get('Magento\Backend\Model\Session')->setFormData(false);
                if ($this->getRequest()->getParam('back')) {
                    return $resultRedirect->setPath('*/*/edit', ['testimonial_id' => $model->getId(), '_current' => true]);
                }
                return $resultRedirect->setPath('*/*/');
            } catch (\Magento\Framework\Exception\LocalizedException $e) {
                $this->messageManager->addError($e->getMessage());
            } catch (\RuntimeException $e) {
                $this->messageManager->addError($e->getMessage());
            } catch (\Exception $e) {
                $this->messageManager->addException($e, __('Something went wrong while saving the testimonial.'));
            }

            return $resultRedirect->setPath('*/*/index', ['testimonial_id' => $this->getRequest()->getParam('testimonial_id')]);
        }
        return $resultRedirect->setPath('*/*/');
    }
}