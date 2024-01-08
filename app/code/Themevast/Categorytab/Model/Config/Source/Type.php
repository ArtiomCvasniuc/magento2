<?php
	/*
		*ducdevphp@gmail.com
	*/
?>
<?php
namespace Themevast\Categorytab\Model\Config\Source;
use Magento\Framework\App\Filesystem\DirectoryList;
class Type implements \Magento\Framework\Option\ArrayInterface
{
	public function __construct(
        \Magento\Framework\Filesystem $filesystem
        ) {
			$this->_filesystem = $filesystem;
    }
    public function toOptionArray()
    {
		    $_parser = new \Magento\Framework\Xml\Parser();
		    $_importPath = $this->_filesystem->getDirectoryRead(DirectoryList::VAR_DIR)->getAbsolutePath('themevast/');
    	    $xmlPath = $_importPath . 'modules_config.xml';
			$options = array();
			if(file_exists($xmlPath)){
				 $data = $_parser->load($xmlPath)->xmlToArray();
				foreach($data['root']['categorytab']['widget']['template'] as $_item) {
					$options[] = [
						'value' => trim($_item['value']),
						'label' => $_item['label']
						];
				}
			}
			array_unshift($options, array(
					'value' => '',
					'label' => 'Default',
					));
        return $options;
    }
}