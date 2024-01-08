<?php
 
namespace Themevast\SlideBanner\Controller\Adminhtml\Slider;
use Magento\Framework\App\Filesystem\DirectoryList;
class Save extends \Magento\Backend\App\Action
{
    /**
     * Core registry
     *
     * @var \Magento\Framework\Registry
     */
    protected $_coreRegistry;
 
    /**
     * Result page factory
     *
     * @var \Magento\Framework\View\Result\PageFactory
     */
    protected $_resultPageFactory;
 
    /**
     * News model factory
     *
     * @var \Tutorial\SimpleNews\Model\NewsFactory
     */
    protected $_slideFactory;
 
    /**
     * @param Context $context
     * @param Registry $coreRegistry
     * @param PageFactory $resultPageFactory
     * @param NewsFactory $newsFactory
     */
    public function __construct(
        \Magento\Backend\App\Action\Context $context,
        \Magento\Framework\Registry $coreRegistry,
        \Magento\Framework\View\Result\PageFactory $resultPageFactory
    ) {
        $this->_coreRegistry = $coreRegistry;
        $this->_resultPageFactory = $resultPageFactory;
       parent::__construct($context);
    }
 
    /**
     * News access rights checking
     *
     * @return bool
     */
    protected function _isAllowed()
    {
        return $this->_authorization->isAllowed('Themevast_SlideBanner::manage_slider');
    }
    /**
     * @return void
     */
	public function execute()
    {
        if ($data = $this->getRequest()->getPostValue('slider')) {
			$model = $this->_objectManager->create('Themevast\SlideBanner\Model\Slider');
			$storeViewId = $this->getRequest()->getParam("store");
			
			if ($id = $this->getRequest()->getParam('slider_id')) {
				$model->load($id);
			}
			if(isset($data['slider_setting']) && is_array($data['slider_setting']))
				$data['slider_setting'] = json_encode($data['slider_setting']);
			$model->addData($data);

			try {
				$model->save();

				$this->messageManager->addSuccess(__('The Slider has been saved.'));
				$this->_getSession()->setFormData(false);

				if ($this->getRequest()->getParam('back') === 'edit') {
					$this->_redirect(
						'*/*/edit',
						[
							'slider_id' => $model->getId(),
							'_current' => true,
							'current_slider_id' => $this->getRequest()->getParam('current_slider_id'),
							'saveandclose' => $this->getRequest()->getParam('saveandclose'),
						]
					);

					return;
				} elseif ($this->getRequest()->getParam('back') === "new") {
					$this->_redirect('*/*/new', array('_current' => true));
					return;
				}
				$this->_redirect('*/*/');
				return;
			} catch (\Magento\Framework\Model\Exception $e) {
				$this->messageManager->addError($e->getMessage());
			} catch (\RuntimeException $e) {
				$this->messageManager->addError($e->getMessage());
			} catch (\Exception $e) {
				$this->messageManager->addError($e->getMessage());
				$this->messageManager->addException($e, __('Something went wrong while saving the Slider.'));
			}

			$this->_getSession()->setFormData($data);
			$this->_redirect('*/*/edit', array('slider_id' => $this->getRequest()->getParam('slider_id')));
			return;
		}
		$this->_redirect('*/*/');
    }
}