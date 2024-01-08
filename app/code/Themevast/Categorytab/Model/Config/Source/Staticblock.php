<?php
namespace Themevast\Categorytab\Model\Config\Source;
class Staticblock implements \Magento\Framework\Option\ArrayInterface
{
	protected  $_blockModel;

    public function __construct(
    	\Magento\Cms\Model\Block $blockModel
    	) {
    	$this->_groupModel = $blockModel;
    }

    public function toOptionArray()
    {
    	$collection = $this->_groupModel->getCollection();
    	$blocks = array();
    	foreach ($collection as $_block) {
    		$blocks[] = [
    		'value' => $_block->getId(),
    		'label' => addslashes($_block->getTitle())
    		];
    	}
        array_unshift($blocks, array(
                'value' => '',
                'label' => '-- Please Select --',
                ));
        return $blocks;
    }
}