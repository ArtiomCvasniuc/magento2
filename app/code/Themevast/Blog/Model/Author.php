<?php
/*
 * custom tv
 * customer ducdevphp@gmail.com 
 */  
?>
<?php
namespace Themevast\Blog\Model;


class Author extends \Magento\Framework\Model\AbstractModel
{
    public function __construct(
        \Magento\Framework\Model\Context $context,
        \Magento\Framework\Registry $registry,
        \Magento\Framework\UrlInterface $url,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig,
        \Magento\Framework\Model\ResourceModel\AbstractResource $resource = null,
        \Magento\Framework\Data\Collection\AbstractDb $resourceCollection = null,
        array $data = []
    ) {
        parent::__construct($context, $registry, $resource, $resourceCollection, $data);
        $this->_url = $url;
    }

    protected function _construct()
    {
        $this->_init('Themevast\Blog\Model\ResourceModel\Author');
    }

    public function getTitle()
    {
        return $this->getName();
    }

    public function getIdentifier()
    {
        return preg_replace(
            "/[^A-Za-z0-9\-]/",
            '',
            strtolower($this->getName('-'))
        );
    }

    public function checkIdentifier($identifier)
    {
        $authors = $this->getCollection();
        foreach($authors as $author) {
            if ($author->getIdentifier() == $identifier) {
                return $author->getId();
            }
        }

        return 0;
    }

    public function getUrl()
    {
        return $this->_url->getUrlPath($this, URL::CONTROLLER_AUTHOR);
    }

    public function getAuthorUrl()
    {
        return $this->_url->getUrl($this, URL::CONTROLLER_AUTHOR);
    }

   /*  public function getName($separator = ' ')
    {
        return $this->getFirstname() . $separator . $this->getLastname();
    } */
     public function getName($separator = ' ')
    {
        return $this->getFirstname();
    }
}