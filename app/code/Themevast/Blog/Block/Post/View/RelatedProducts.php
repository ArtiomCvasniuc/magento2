<?php
namespace Themevast\Blog\Block\Post\View;

use Magento\Catalog\Model\ResourceModel\Product\Collection;
use Magento\Framework\View\Element\AbstractBlock;
class RelatedProducts extends \Magento\Catalog\Block\Product\AbstractProduct
    implements \Magento\Framework\DataObject\IdentityInterface
{
  
    protected $_itemCollection;

    protected $_catalogProductVisibility;

    protected $_productCollectionFactory;

    protected $_moduleManager;

    public function __construct(
        \Magento\Catalog\Block\Product\Context $context,
        \Magento\Catalog\Model\Product\Visibility $catalogProductVisibility,
        \Magento\Framework\Module\Manager $moduleManager,
        \Magento\Catalog\Model\ResourceModel\Product\CollectionFactory $productCollectionFactory,
        array $data = []
    ) {
        $this->_catalogProductVisibility = $catalogProductVisibility;
        $this->_moduleManager = $moduleManager;
        $this->_productCollectionFactory = $productCollectionFactory;
        parent::__construct($context, $data );
    }

    protected function _prepareCollection()
    {
        $post = $this->getPost();

        $this->_itemCollection = $this->_productCollectionFactory->create()
            ->addAttributeToSelect('required_options')
            ->addStoreFilter()
            ->addAttributeToFilter('entity_id', array('in' => $post->getRelatedProductIds() ?: array(0) ));

        if ($this->_moduleManager->isEnabled('Magento_Checkout')) {
            $this->_addProductAttributesAndPrices($this->_itemCollection);
        }

        $this->_itemCollection->setVisibility($this->_catalogProductVisibility->getVisibleInCatalogIds());

        $this->_itemCollection->setPageSize(
            (int) $this->_scopeConfig->getValue(
                'tvblog/post_view/related_products/number_of_products',
                \Magento\Store\Model\ScopeInterface::SCOPE_STORE
            )
        );

        $this->_itemCollection->load();

        foreach ($this->_itemCollection as $product) {
            $product->setDoNotUseCategoryId(true);
        }

        return $this;
    }

    public function displayProducts()
    {
        return (bool) $this->_scopeConfig->getValue(
            'tvblog/post_view/related_products/enabled',
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        );
    }

    public function getItems()
    {
        if (is_null($this->_itemCollection)) {
            $this->_prepareCollection();
        }
        return $this->_itemCollection;
    }

    public function getPost()
    {
        if (!$this->hasData('post')) {
            $this->setData('post',
                $this->_coreRegistry->registry('current_blog_post')
            );
        }
        return $this->getData('post');
    }

    public function getIdentities()
    {
        return [\Magento\Cms\Model\Page::CACHE_TAG . '_relatedproducts_'.$this->getPost()->getId()  ];
    }
}
