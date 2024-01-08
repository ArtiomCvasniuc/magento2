<?php
namespace Themevast\ThemevastUp\Controller\Adminhtml\Export;
use Magento\Framework\Json\EncoderInterface;
use Magento\Framework\App\Filesystem\DirectoryList;


class Save extends \Magento\Backend\App\Action
{
   
    protected $_coreRegistry = null;

   
    protected $resultPageFactory;

   
    protected $_exportHelper;

   
    protected $_filesystem;

    public function __construct(
        \Magento\Backend\App\Action\Context $context,
        \Magento\Framework\View\Result\PageFactory $resultPageFactory,
        \Themevast\ThemevastUp\Helper\Data\Export $exportHelper,
        \Magento\Framework\Filesystem $filesystem
        ) {
        parent::__construct($context);
        $this->resultPageFactory = $resultPageFactory;
        $this->_exportHelper = $exportHelper;
        $this->_filesystem = $filesystem;
    }

    public function execute()
    {
        $params = $this->getRequest()->getParams();
        $content = [];

        if($params){
            if(isset($params['modules'])){
                $content = array_merge ($content, $this->_exportHelper->exportModules($params));
            }
            if(isset($params['cmspages'])){
                $content = array_merge($content, $this->_exportHelper->exportCmsPages($params));
            }
            if(isset($params['cmsblocks'])){
                $content = array_merge($content, $this->_exportHelper->exportStaticBlocks($params));
            }
            if(isset($params['widgets'])){
                $content = array_merge($content, $this->_exportHelper->exportWidgets($params));
            }
        }

        //$fileName = strtolower(str_replace("/", "_",$params['folder'])).'_'.date('Ymd_His').'.json';
		$fileName = trim($params['file_name']).'_'.date('Ymd_His').'.json';
        $fileName = str_replace(" ", "-", $fileName);
		$folder = $params['folder'];
		$dir = $this->_filesystem->getDirectoryWrite(DirectoryList::APP);
		$file = null;
		$content['created_at'] = date("m/d/Y h:i:s a");
		$content = \Zend_Json::encode($content);
		$filePath = "design/frontend/{$folder}/backup/".$fileName;
		try{
			$dir->writeFile($filePath, $content);
			$backupFilePath = $dir->getAbsolutePath($filePath);
			$this->_sendUploadResponse($fileName, $content);
			$this->messageManager->addSuccess(__('Successfully exported to file "%1"',$backupFilePath));
		} catch (\Exception $e) {
			$this->messageManager->addError(__('Can not save export file "%1".<br/>"%2"', $filePath, $e->getMessage()));
		}
        $resultRedirect = $this->resultRedirectFactory->create();
        return $resultRedirect->setPath('*/*/');
    }

    protected function _sendUploadResponse($fileName, $content, $contentType='application/octet-stream')
    {
        $this->_response->setHttpResponseCode(200)
        ->setHeader('Pragma', 'public', true)
        ->setHeader('Cache-Control', 'must-revalidate, post-check=0, pre-check=0', true)
        ->setHeader('Content-type', $contentType, true)
        ->setHeader('Content-Length', strlen($content))
        ->setHeader('Content-Disposition', 'attachment; filename="' . $fileName . '"', true)
        ->setHeader('Last-Modified', date('r'), true);
        $this->_response->setBody($content);
        $this->_response->sendResponse();
        die;
        return $this->_response;
    }
}
