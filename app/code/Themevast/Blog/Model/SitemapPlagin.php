<?php
namespace Themevast\Blog\Model;

use Magento\Sitemap\Model\Sitemap;

class SitemapPlagin
{
    
    protected $_postFactory;

    protected $_sitemapData;

    protected $_sitemapItemsAdded = false;

    public function __construct(
        \Magento\Sitemap\Helper\Data $sitemapData,
        \Themevast\Blog\Model\PostFactory $postFactory
    ) {
        $this->_sitemapData = $sitemapData;
        $this->_postFactory = $postFactory;
    }

    public function beforeGetSitemapItems(Sitemap $subject)
    {
        if ($this->_sitemapItemsAdded) {
            return;
        }

        $helper = $this->_sitemapData;
        $storeId = $subject->getStoreId();

        $sitemapItem =  new \Magento\Framework\DataObject(
            [
                'changefreq' => 'weekly',
                'priority' => '0.25',
                'collection' => $this->_postFactory->create()->getCollection($storeId),
            ]
        );

        $subject->addSitemapItems($sitemapItem);

        $this->_sitemapItemsAdded = true;
    }
}
