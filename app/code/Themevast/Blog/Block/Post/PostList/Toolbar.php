<?php
namespace Themevast\Blog\Block\Post\PostList;

class Toolbar extends \Magento\Framework\View\Element\Template
{
    const PAGE_PARM_NAME = 'page';

    protected $_collection = null;

    protected $_template = 'post/list/toolbar.phtml';

    public function setCollection($collection)
    {
        $this->_collection = $collection;

        $this->_collection->setCurPage($this->getCurrentPage());

      
        $limit = (int)$this->getLimit();
        if ($limit) {
            $this->_collection->setPageSize($limit);
        }
        if ($this->getCurrentOrder()) {
            $this->_collection->setOrder($this->getCurrentOrder(), $this->getCurrentDirection());
        }
        return $this;
    }

    public function getCollection()
    {
        return $this->_collection;
    }

   
    public function getLimit()
    {
        return $this->_scopeConfig->getValue(
            'tvblog/post_list/posts_per_page', 
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        );
    }

   
    public function getCurrentPage()
    {
        $page = (int) $this->_request->getParam(self::PAGE_PARM_NAME);
        return $page ? $page : 1;
    }

    
    public function getPagerHtml()
    {
        $pagerBlock = $this->getChildBlock('post_list_toolbar_pager');
        if ($pagerBlock instanceof \Magento\Framework\DataObject) {
          
            $pagerBlock->setUseContainer(
                false
            )->setShowPerPage(
                false
            )->setShowAmounts(
                false
            )->setPageVarName(
                'page'
            )->setFrameLength(
                $this->_scopeConfig->getValue(
                    'design/pagination/pagination_frame',
                    \Magento\Store\Model\ScopeInterface::SCOPE_STORE
                )
            )->setJump(
                $this->_scopeConfig->getValue(
                    'design/pagination/pagination_frame_skip',
                    \Magento\Store\Model\ScopeInterface::SCOPE_STORE
                )
            )->setLimit(
                $this->getLimit()
            )->setCollection(
                $this->getCollection()
            );
            return $pagerBlock->toHtml();
        }

        return '';
    }

}
