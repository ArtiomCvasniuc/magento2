<?php
namespace Themevast\ThemevastUp\Model\Export\Source\Tv;
use Magento\Framework\App\Filesystem\DirectoryList;

class ExportPaths implements \Magento\Framework\Option\ArrayInterface
{
	protected  $_blockModel;

    
    protected $_collectionThemeFactory;

    protected $_filesystem;

    public function __construct(
    	\Magento\Theme\Model\ResourceModel\Theme\CollectionFactory $collectionThemeFactory,
        \Magento\Framework\Filesystem $filesystem
        ) {
    	$this->_collectionThemeFactory = $collectionThemeFactory;
        $this->_filesystem = $filesystem;
    }

    public function toOptionArray(){
        $themes = $this->_collectionThemeFactory->create();
        $file = $this->_filesystem->getDirectoryRead(DirectoryList::APP)->getAbsolutePath('design/frontend/');
        $tvTemplatePaths = glob($file . '*/*/themevastcf.xml');
        $output = [];
        foreach ($tvTemplatePaths as $k => $v) {
            $v = str_replace("/themevastcf.xml", "", $v);
            $output[] = [
                'label' => ucfirst(str_replace($file, "", $v)),
                'value' => str_replace($file, "", $v)
                ];
        }
        return $output;
    }

    public function toArray(){
        $themes = $this->_collectionThemeFactory->create();
        $file = $this->_filesystem->getDirectoryRead(DirectoryList::APP)->getAbsolutePath('design/frontend/');
        $tvTemplatePaths = glob($file . '*/*/themevastcf.xml');
        $output = [];
        foreach ($tvTemplatePaths as $k => $v) {
            $v = str_replace("/themevastcf.xml", "", $v);
            $output[str_replace($file, "", $v)] = ucfirst(str_replace($file, "", $v));
        }
        return $output;
    }
}