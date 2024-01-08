<?php
namespace Themevast\Blog\Block\Post;

use Magento\Store\Model\ScopeInterface;

abstract class AbstractPost extends \Magento\Framework\View\Element\Template
{

    
    protected $_filterProvider;

    
    protected $_post;

    
    protected $_postFactory;

    
    protected $_coreRegistry;

    
    protected $_defaultPostInfoBlock = 'Themevast\Blog\Block\Post\Info';

   
    public function __construct(
        \Magento\Framework\View\Element\Template\Context $context,
        \Themevast\Blog\Model\Post $post,
        \Magento\Framework\Registry $coreRegistry,
        \Magento\Cms\Model\Template\FilterProvider $filterProvider,
        \Themevast\Blog\Model\PostFactory $postFactory,
        array $data = []
    ) {
        parent::__construct($context, $data);
        $this->_post = $post;
        $this->_coreRegistry = $coreRegistry;
        $this->_filterProvider = $filterProvider;
        $this->_postFactory = $postFactory;
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

    
    public function getShorContent()
    {
        $content = $this->getPost()->getContent();
        $pageBraker = '<!-- pagebreak -->';
        
        $isMb = function_exists('mb_strpos');
        $p = $isMb ? strpos($content, $pageBraker) : mb_strpos($content, $pageBraker);

        if ($p) {
            $content = substr($content, 0, $p);
        }

        return $this->_filterProvider->getPageFilter()->filter($content);
    }

   
    public function getContent()
    {
        return $this->_filterProvider->getPageFilter()->filter(
            $this->getPost()->getContent()
        );

        return $this->getData($k);
    }

   
    public function getInfoHtml()
    {
        return $this->getInfoBlock()->toHtml();
    }

    
    public function getInfoBlock()
    {
        $k = 'info_block';
        if (!$this->hasData($k)) {
            $blockName = $this->getPostInfoBlockName();
            if ($blockName) {
                $block = $this->getLayout()->getBlock($blockName);
            }

            if (empty($block)) {
                $block = $this->getLayout()->createBlock($this->_defaultPostInfoBlock, uniqid(microtime()));
            }

            $this->setData($k, $block);
        }

        return $this->getData($k)->setPost($this->getPost());
    }

}
