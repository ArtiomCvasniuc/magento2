<?php
namespace Themevast\SearchbyCat\Block;

use Magento\Directory\Helper\Data;
use Magento\Store\Model\Group;

class Form extends \Magento\Framework\View\Element\Template
{
    protected $_storeInUrl;
    protected $_postDataHelper;
    protected $_categoryHelper;
    protected $_categoryFactory;

    public function __construct(
        \Magento\Framework\View\Element\Template\Context $context,
        \Magento\Framework\Data\Helper\PostHelper $postDataHelper,
		\Magento\Catalog\Helper\Category $categoryHelper,
		\Magento\Catalog\Model\CategoryFactory $categoryFactory,
        array $data = []
    ) {
        $this->_postDataHelper = $postDataHelper;
		$this->_categoryHelper = $categoryHelper;
        $this->_categoryFactory = $categoryFactory;
        parent::__construct($context, $data);
    }
	public function getCategories()
	{
		return $this->_categoryHelper->getStoreCategories(true , false, true);
	}

    public function getCurrentWebsiteId()
    {
        return $this->_storeManager->getStore()->getWebsiteId();
    }

    public function getCurrentGroupId()
    {
        return $this->_storeManager->getStore()->getGroupId();
    }

    public function getCurrentStoreId()
    {
        return $this->_storeManager->getStore()->getId();
    }

}
