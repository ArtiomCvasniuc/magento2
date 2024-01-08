<?php
/**
 *
 * Copyright © 2016 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Themevast\Themevast\Controller\Adminhtml\Themevast;

use Magento\Framework\App\Filesystem\DirectoryList;

class Exportpage extends \Magento\Backend\App\Action
{
    /**
     * @var \Magento\Backend\App\Response\Http\FileFactory
     */
    protected $fileFactory;
    protected $_parser;

    /**
     * @var \Magento\PageCache\Model\Config
     */
    protected $config;

    /**
     * @param \Magento\Backend\App\Action\Context $context
     * @param \Magento\Framework\App\Response\Http\FileFactory $fileFactory
     * @param \Magento\PageCache\Model\Config $config
     */
    public function __construct(
        \Magento\Backend\App\Action\Context $context,
        \Magento\Framework\App\Response\Http\FileFactory $fileFactory,
        \Magento\PageCache\Model\Config $config
    ) {
        parent::__construct($context);
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
        $fileName = 'cms_pages.xml';
        $varnishVersion = $this->getRequest()->getParam('varnish');
		$collection = $this->_objectManager->get('Magento\Cms\Model\Page')->getCollection();
		$dom = $this->_parser->getDom();
		$dom->formatOutput = true;
		$root = $dom->createElement('root');
		$blocks = $dom->createElement('pages');
		$skipKeys = array('page_id', 'creation_time', 'update_time', '_first_store_id', 'store_code');
		foreach($collection as $block)
		{
			$item = $dom->createElement('item');
			foreach($block->getData() as $key=>$value)
			{
				if(in_array($key, $skipKeys))
					continue;
				$element = $dom->createElement($key);
				if(is_array($value) && $key == 'store_id')
				{
					foreach($value as $key2=>$value2)
					{
						$element2 = $dom->createElement('id');
						$content = $dom->createCDATASection($value2);
						$element2->appendChild($content);
						$element->appendChild($element2);
					}
					$item->appendChild($element);
					continue;
				}
				if(is_array($value))
					$value = implode(',', $value);
				$content = $dom->createCDATASection($value);
				$element->appendChild($content);
				$item->appendChild($element);
			}
			$blocks->appendChild($item);
		}
		$root->appendChild($blocks);
		$dom->appendChild($root);
		$content = $dom->saveXML();
		$dom->save($this->_importPath . $fileName);
		$this->messageManager->addSuccess(__('Pages exported to folder var.'));
        $this->_redirect('adminhtml/system_config/edit/section/import_export');
    }
}
?>