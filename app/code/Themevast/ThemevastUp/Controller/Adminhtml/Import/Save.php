<?php
namespace Themevast\ThemevastUp\Controller\Adminhtml\Import;
use Magento\Framework\Json\EncoderInterface;
use Magento\Framework\App\Filesystem\DirectoryList;

class Save extends \Magento\Backend\App\Action
{
    
    protected $_coreRegistry = null;

    protected $resultPageFactory;

    protected $_filesystem;

    protected $_scopeConfig;

    protected $_storeManager;

    protected $_tvImport;

    protected $_configResource;

    public function __construct(
        \Magento\Backend\App\Action\Context $context,
        \Magento\Framework\View\Result\PageFactory $resultPageFactory,
        \Themevast\ThemevastUp\Helper\Data\Import $tvImport,
        \Magento\Framework\Filesystem $filesystem,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig,
        \Magento\Framework\App\ResourceConnection $resource,
        \Magento\Framework\App\Config\ConfigResource\ConfigInterface $configResource
        ) {
        parent::__construct($context);
        $this->resultPageFactory = $resultPageFactory;
        $this->_filesystem = $filesystem;
        $this->_storeManager = $storeManager;
        $this->_scopeConfig = $scopeConfig;
        $this->_configResource = $configResource;
        $this->_resource = $resource;
        $this->_tvImport = $tvImport;
    }

    public function execute()
    {
        $data = $this->getRequest()->getParams();

        $fileContent = '';
        if(isset($_FILES['data_import_file']['name']) && $_FILES['data_import_file']['name'] != '')
        {   
            $fileContent = file_get_contents($_FILES['data_import_file']['tmp_name']);
        }else{
            $folder = $data['folder'];
            if(isset($data[$folder])){
                $filePath = $data[$folder];
            }
            if($filePath!=''){
                $fileContent = file_get_contents($filePath);
            }
        }

        $importData = \Zend_Json::decode($fileContent);

        $overwrite = false;
        if($data['overwrite_blocks']){
            $overwrite = true;
        }

        $store = $this->_storeManager->getStore($data['store_id']);
        $connection = $this->_resource->getConnection();
        if(!empty($importData)){
            try{
                foreach ($importData as $_module) {
                    if(isset($_module['tables'])){
                        $tables = $_module['tables'];
                        foreach ($tables as $tablename => $rows) {
                            $table_name = $this->_resource->getTableName($tablename);
                            $connection->query("SET FOREIGN_KEY_CHECKS=0;");
                            /* if(!$overwrite) {
                                $connection->query("TRUNCATE `".$table_name."`");
                            }
                            if($overwrite) {
                                if( $table_name == 'cms_page_store' ){
                                    $connection->query(" DELETE FROM ".$table_name." WHERE page_id = ".$row['page_id']);
                                }
                                if( $table_name == 'cms_block_store' ){
                                    $connection->query(" DELETE FROM ".$table_name." WHERE block_id = ".$row['block_id']);
                                }
                            } */
                            foreach ($rows as $row) {
                                $where = '';
                                $query_data = $this->_tvImport->buildQueryImportData($row, $table_name, $overwrite, $data['store_id']); 
                                $connection->query($query_data[0].$where, $query_data[1]);
                            }
                        }
                        $connection->query("SET FOREIGN_KEY_CHECKS=1;");
                    }
                    if(isset($_module['system_configs'])){
                        foreach ($_module['system_configs'] as $_config) {
                            if(isset($_config['key'])){
                                $result = $this->_scopeConfig->getValue(
                                    $_config['key'],
                                    \Magento\Store\Model\ScopeInterface::SCOPE_STORE,
                                    $store);
                                if($result != $_config['value']){
                                    if((int)$data['store_id'] == 0){
                                        $this->_configResource->saveConfig($_config['key'], $_config['value'], "default", (int)$data['store_id'] );
                                    }else{
                                        $this->_configResource->saveConfig($_config['key'], $_config['value'], "stores", (int)$data['store_id'] );
                                    }
                                }
                            }
                        }
                    }
                }
                $this->messageManager->addSuccess(__("Import data successfully"));
            }catch(\Exception $e){
                $this->messageManager->addSuccess(__("Can't import data <br/> %1", $e->getMessage()));
            }
        }
        $resultRedirect = $this->resultRedirectFactory->create();
        return $resultRedirect->setPath('*/*/');
    }
}
