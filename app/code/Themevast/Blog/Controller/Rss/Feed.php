<?php
namespace Themevast\Blog\Controller\Rss;

class Feed extends \Magento\Framework\App\Action\Action
{
    public function execute()
    {
        $this->_view->loadLayout();
        $this->getResponse()
            ->setHeader('Content-type', 'text/xml; charset=UTF-8')
            ->setBody(
                $this->_view->getLayout()->getBlock('blog.rss.feed')->toHtml()
            );
    }

}
