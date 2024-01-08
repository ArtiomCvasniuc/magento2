<?php
namespace Themevast\Blog\Block\Post;

class PostList extends \Themevast\Blog\Block\Post\PostList\AbstractList
{
   
	protected $_defaultToolbarBlock = 'Themevast\Blog\Block\Post\PostList\Toolbar';

    public function getPostHtml($post)
    {
    	return $this->getChildBlock('blog.posts.list.item')->setPost($post)->toHtml();
    }
	
    public function getToolbarBlock()
    {
        $blockName = $this->getToolbarBlockName();

        if ($blockName) {
            $block = $this->getLayout()->getBlock($blockName);
            if ($block) {
                return $block;
            }
        }
        $block = $this->getLayout()->createBlock($this->_defaultToolbarBlock, uniqid(microtime()));
        return $block;
    }

    public function getToolbarHtml()
    {
        return $this->getChildHtml('toolbar');
    }

    protected function _beforeToHtml()
    {
        $toolbar = $this->getToolbarBlock();

        
        $collection = $this->getPostCollection();

      
        $toolbar->setCollection($collection);
        $this->setChild('toolbar', $toolbar);

        return parent::_beforeToHtml();
    }

}
