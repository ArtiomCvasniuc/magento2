<?php
/**
 *
 * Copyright © 2016 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Themevast\Themevast\Controller\Adminhtml\Themevast;

use Magento\Framework\App\Filesystem\DirectoryList;

class Importblock extends \Magento\Backend\App\Action
{
    /**
     * @var \Magento\Backend\App\Response\Http\FileFactory
     */
    protected $fileFactory;

    /**
     * @var \Magento\PageCache\Model\Config
     */
    protected $config;
	protected $_helperModule;

    /**
     * @param \Magento\Backend\App\Action\Context $context
     * @param \Magento\Framework\App\Response\Http\FileFactory $fileFactory
     * @param \Magento\PageCache\Model\Config $config
     */
    public function __construct(
        \Magento\Backend\App\Action\Context $context,
        \Magento\Framework\App\Response\Http\FileFactory $fileFactory,
        \Magento\PageCache\Model\Config $config,
		\Themevast\Themevast\Helper\Data $helperModule
    ) {
        parent::__construct($context);
		$this->_helperModule = $helperModule;
        $this->config = $config;
        $this->fileFactory = $fileFactory;
		$this->_importPath = BP . '/' . DirectoryList::VAR_DIR . '/';
        $this->_parser = new \Magento\Framework\Xml\Parser();
    }

    /**
     * Export Varnish Configuration as .vcl
     *
     * @return \Magento\Framework\App\ResponseInterface
     */
    public function execute()
    {
        try {
            $xmlPath = $this->_importPath . 'cms_blocks.xml';
            $overwrite = false;
            if($this->_helperModule->getConfigData("import_export/import/overwrite_blocks")) {
                $overwrite = true;
            }
            if (!is_readable($xmlPath))
            {
                throw new \Exception(
                    __("Can't get the data file for import cms blocks/pages: ".$xmlPath)
                );
            }
            $data = $this->_parser->load($xmlPath)->xmlToArray();
            $cms_collection = null;
            $conflictingOldItems = array();
            
            $i = 0;
            foreach($data['root']['blocks']['item'] as $_item) {
                $exist = false;
				$collection = $this->_objectManager->create('Magento\Cms\Model\Block')->getCollection();
				$collection->addFieldToFilter('identifier', $_item['identifier']);
				if($collection->getSize())
					$exist = true;
				$block = $this->_objectManager->create('Magento\Cms\Model\Block');
                if($overwrite) {
                    if($exist) {
                        $conflictingOldItems[] = $_item['identifier'];
                        $block->load($_item['identifier']);
                    }
                } else {
                    if($exist) {
                        continue;
                    }
                }
				$conflictingOldItems[] = $_item['identifier'];
				$_item['store_id'] = array(0);
				$block->addData($_item)->save();
                $i++;
            }
			$this->messageManager->addSuccess(__('Imported %1 Item(s). ( %2 )', $i, implode("\n", $conflictingOldItems)));
        } catch (\Exception $exception) {
			$this->messageManager->addError($exception->getMessage());
        }
		$this->_redirect('*/system_config/edit/section/import_export', array('website' => $this->getRequest()->getParam('website')));
    }
}
?>