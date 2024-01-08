<?php
namespace Themevast\ThemevastUp\Model\Export\Source\Tv;

class Widgets implements \Magento\Framework\Option\ArrayInterface
{
	protected  $_widgetModel;

   
    public function __construct(
    	\Magento\Widget\Model\Widget\Instance $widgetModel
    	) {
    	$this->_widgetModel = $widgetModel;
    }

    public function toOptionArray()
    {
    	$collection = $this->_widgetModel->getCollection();
    	$blocks = array();
    	foreach ($collection as $_widget) {
    		$blocks[] = [
    		'value' => $_widget->getId(),
    		'label' => addslashes($_widget->getTitle())
    		];
    	}
        return $blocks;
    }

    public function toArray()
    {
        $collection = $this->_widgetModel->getCollection();
        $blocks = array();
        foreach ($collection as $_widget) {
            $blocks[$_widget->getId()] = addslashes($_widget->getTitle());
        }
        return $blocks;
    }
}