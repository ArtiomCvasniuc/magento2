<?php
namespace Themevast\ThemevastUp\Model\Import\Source\Tv;
use Magento\Framework\App\Filesystem\DirectoryList;

class Files
{

    protected $_filesystem;
    public function __construct(
    	\Magento\Framework\Filesystem $filesystem
    	) {
        $this->_filesystem = $filesystem;
    }

    public function toOptionArray($key)
    {
        $path = "design" . DIRECTORY_SEPARATOR . "frontend" . DIRECTORY_SEPARATOR . $key . DIRECTORY_SEPARATOR . 'backup' . DIRECTORY_SEPARATOR;
        $files = [];
        $importFolderDir = $this->_filesystem->getDirectoryWrite(DirectoryList::APP)->getAbsolutePath($path);
        $files = glob($importFolderDir.'*.json');
        $outputs = [];
        foreach ($files as $k => $v) {
            $labelFile = str_replace($importFolderDir, "", $v);
            $file_content = file_get_contents($v);
            $file_content =  \Zend_Json::decode($file_content);
            $created_at = $comment = '';
            if(isset($file_content['created_at']) && $file_content['created_at']!=''){
                $created_at = ' - '.$file_content['created_at'];
            }
            if(isset($file_content['comment']) && $file_content['comment']!=''){
                $comment = ' - '.$file_content['comment'];
            }
            $labelFile = $labelFile.' '.$created_at.' '.$comment;
            $outputs[] = array(
                'label' => $labelFile,
                'value' => $v,
                );
        }
        $outputs[] = [
            'value' => 'data_import_file',
            'label' => __('Upload file...')];

        return $outputs;
    }

    public function toArray()
    {
        $collection = $this->_pageModel->getCollection();
        $blocks = array();
        foreach ($collection as $_page) {
            $blocks[$_page->getId()] = addslashes($_page->getTitle());
        }
        return $blocks;
    }
}