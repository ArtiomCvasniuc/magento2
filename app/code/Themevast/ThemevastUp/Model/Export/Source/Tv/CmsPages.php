<?php
namespace Themevast\ThemevastUp\Model\Export\Source\Tv;

class CmsPages implements \Magento\Framework\Option\ArrayInterface
{
	protected  $_pageModel;

    
    public function __construct(
    	\Magento\Cms\Model\Page $pageModel
    	) {
    	$this->_pageModel = $pageModel;
    }

    public function toOptionArray()
    {
    	$collection = $this->_pageModel->getCollection();
    	$blocks = array();
    	foreach ($collection as $_page) {
    		$blocks[] = [
    		'value' => $_page->getId(),
    		'label' => addslashes($_page->getTitle())
    		];
    	}
        return $blocks;
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