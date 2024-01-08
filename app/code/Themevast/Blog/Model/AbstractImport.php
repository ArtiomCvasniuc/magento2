<?php
namespace Themevast\Blog\Model;

abstract class AbstractImport extends \Magento\Framework\Model\AbstractModel
{
    
    protected $_connect;

	
	protected $_requiredFields;

    
    protected $_postFactory;

    
    protected $_categoryFactory;

    
    protected $_importedPostsCount = 0;

    
    protected $_importedCategoriesCount = 0;

    
    protected $_skippedPosts = [];

   
    protected $_skippedCategories = [];

    
    public function __construct(
        \Magento\Framework\Model\Context $context,
        \Magento\Framework\Registry $registry,
        \Themevast\Blog\Model\PostFactory $postFactory,
        \Themevast\Blog\Model\CategoryFactory $categoryFactory,
        \Magento\Framework\Model\ResourceModel\AbstractResource $resource = null,
        \Magento\Framework\Data\Collection\AbstractDb $resourceCollection = null,
        array $data = []
    ) {
        $this->_postFactory = $postFactory;
        $this->_categoryFactory = $categoryFactory;

        parent::__construct($context, $registry, $resource, $resourceCollection, $data);
    }

    
    public function getImportStatistic()
    {
        return new \Magento\Framework\DataObject([
            'imported_posts_count'      => $this->_importedPostsCount,
            'imported_categories_count' => $this->_importedCategoriesCount,
            'skipped_posts'             => $this->_skippedPosts,
            'skipped_categories'        => $this->_skippedCategories,
            'imported_count'            => $this->_importedPostsCount + $this->_importedCategoriesCount,
            'skipped_count'              => count($this->_skippedPosts) + count($this->_skippedCategories),
        ]);
    }

	
    public function prepareData($data)
    {
        if (!is_array($data)) {
            $data = (array) $data;
        }

        foreach($this->_requiredFields as $field) {
            if (empty($data[$field])) {
            	throw new Exception(__('Parameter %1 is required', $field), 1);
            }
        }

        foreach($data as $field => $value) {
            if (!in_array($field, $this->_requiredFields)) {
                unset($data[$field]);
            }
        }

        $this->setData($data);

        return $this;
    }

    
    protected function _mysqliQuery($sql)
    {
        $result = mysqli_query($this->_connect, $sql);
        if (!$result) {
            throw new \Exception(
                __('Mysql error: %1.', mysqli_error($this->_connect))
            );
        }

        return $result;
    }
}
