<?php
namespace Themevast\ThemevastUp\Model\Export\Source\Tv;

class StaticBlocks implements \Magento\Framework\Option\ArrayInterface
{
	protected  $_blockModel;

    public function __construct(
    	\Magento\Cms\Model\Block $blockModel
    	) {
    	$this->_blockModel = $blockModel;
    }

    public function toOptionArray()
    {
    	$collection = $this->_blockModel->getCollection();
    	$blocks = array();
    	foreach ($collection as $_block) {
    		$blocks[] = [
    		'value' => $_block->getId(),
    		'label' => addslashes($_block->getTitle())
    		];
    	}
        return $blocks;
    }

    public function toArray()
    {
        $collection = $this->_blockModel->getCollection();
        $blocks = array();
        foreach ($collection as $_block) {
            $blocks[$_block->getId()] = addslashes($_block->getTitle());
        }
        return $blocks;
    }
}