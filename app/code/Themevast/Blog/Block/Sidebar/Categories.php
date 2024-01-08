<?php
namespace Themevast\Blog\Block\Sidebar;

use Magento\Store\Model\ScopeInterface;

class Categories extends \Magento\Framework\View\Element\Template
{
    use Widget;

    protected $_widgetKey = 'categories';

    protected $_categoryCollection;
    
    public function __construct(
        \Magento\Framework\View\Element\Template\Context $context,
        \Themevast\Blog\Model\ResourceModel\Category\Collection $categoryCollection,
        array $data = []
    ) {
        parent::__construct($context, $data);
        $this->_categoryCollection = $categoryCollection;
    }

    public function getGroupedChilds()
    {
        $k = 'grouped_childs';
        if (!$this->hasDat($k)) {
            $array = $this->_categoryCollection
                ->addActiveFilter()
                ->addStoreFilter($this->_storeManager->getStore()->getId())
                ->setOrder('position')
                ->getTreeOrderedArray();

            $this->setData($k, $array);
        }

        return $this->getData($k);
    }
	
    public function getIdentities()
    {
        return [\Magento\Cms\Model\Block::CACHE_TAG . '_blog_categories_widget'  ];
    }
}
