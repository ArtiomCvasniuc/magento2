<?php
namespace Themevast\ThemevastUp\Model\Export\Source\Tv;
use Magento\Framework\App\Filesystem\DirectoryList;

class TvModules implements \Magento\Framework\Option\ArrayInterface
{
	
    protected $_moduleList;
 
    public function __construct(
        \Magento\Framework\Module\ModuleListInterface $moduleList
        ) {
    	$this->_moduleList = $moduleList;
    }

    public function toOptionArray()
    {
        $output = [];
        $modules = $this->_moduleList->getNames();
        sort($modules);
        foreach ($modules as $k => $v) {
            if(preg_match("/Themevast/", $v)){
                $output[$k] = [
                'value' => $v,
                'label' => $v
                ];
            }
        }
        return $output;
    }

    protected function getInstallConfig()
    {
        $file = $this->_filesystem->getDirectoryRead(DirectoryList::APP)->getAbsolutePath('etc/config.php');
        $installConfig = include $file;
        return $installConfig;
    }
}