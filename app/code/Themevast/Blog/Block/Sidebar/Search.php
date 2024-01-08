<?php
namespace Themevast\Blog\Block\Sidebar;

class Search extends  \Magento\Framework\View\Element\Template
{
	use Widget;

    protected $_widgetKey = 'search';

	public function getQuery()
	{
		return urldecode($this->getRequest()->getParam('q', ''));
	}

}
