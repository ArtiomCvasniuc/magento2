<?php
namespace Themevast\Blog\Controller;

class Router implements \Magento\Framework\App\RouterInterface
{
    
    protected $actionFactory;

    
    protected $_eventManager;

   
    protected $_storeManager;

    
    protected $_postFactory;

    
    protected $_categoryFactory;

   
    protected $_appState;

    
    protected $_url;

    
    protected $_response;

    
    public function __construct(
        \Magento\Framework\App\ActionFactory $actionFactory,
        \Magento\Framework\Event\ManagerInterface $eventManager,
        \Magento\Framework\UrlInterface $url,
        \Themevast\Blog\Model\PostFactory $postFactory,
        \Themevast\Blog\Model\CategoryFactory $categoryFactory,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Magento\Framework\App\ResponseInterface $response
    ) {
        $this->actionFactory = $actionFactory;
        $this->_eventManager = $eventManager;
        $this->_url = $url;
        $this->_postFactory = $postFactory;
        $this->_categoryFactory = $categoryFactory;
        $this->_storeManager = $storeManager;
        $this->_response = $response;
    }

   
    public function match(\Magento\Framework\App\RequestInterface $request)
    {
        $_identifier = trim($request->getPathInfo(), '/');

        if (strpos($_identifier, 'blog') !== 0) {
            return;
        }
	
        $identifier = str_replace('blog/', '', $_identifier);
        $condition = new \Magento\Framework\DataObject(['identifier' => $identifier, 'continue' => true]);
        $this->_eventManager->dispatch(
            'Themevast_blog_controller_router_match_before',
            ['router' => $this, 'condition' => $condition]
        );
        if ($condition->getRedirectUrl()) {
            $this->_response->setRedirect($condition->getRedirectUrl());
            $request->setDispatched(true);
            return $this->actionFactory->create(
                'Magento\Framework\App\Action\Redirect',
                ['request' => $request]
            );
        }

        if (!$condition->getContinue()) {
            return null;
        }

        $identifier = $condition->getIdentifier();

        $success = false;
        $info = explode('/', $identifier);

        if (!$identifier) {
            $request->setModuleName('blog')->setControllerName('index')->setActionName('index');
            $success = true;
        } elseif (count($info) > 1) {
            
            $store = $this->_storeManager->getStore()->getId();

            switch ($info[0]) {
                case 'post' :
                    $post = $this->_postFactory->create();
                    $postId = $post->checkIdentifier($info[1], $this->_storeManager->getStore()->getId());
                    if (!$postId) {
                        return null;
                    }

                    $request->setModuleName('blog')->setControllerName('post')->setActionName('view')->setParam('id', $postId);
                    $success = true;
                    break;
                case 'category' :
                    $category = $this->_categoryFactory->create();
                    $categoryId = $category->checkIdentifier($info[1], $this->_storeManager->getStore()->getId());
                    if (!$categoryId) {
                        return null;
                    }

                    $request->setModuleName('blog')->setControllerName('category')->setActionName('view')->setParam('id', $categoryId);
                    $success = true;
                    break;
                case 'archive' :
                    $request->setModuleName('blog')->setControllerName('archive')->setActionName('view')
                        ->setParam('date', $info[1]);

                    $success = true;
                    break;

                case 'search' :
                    $request->setModuleName('blog')->setControllerName('search')->setActionName('index')
                        ->setParam('q', $info[1]);

                    $success = true;
                    break;
            }

        }

        if (!$success) {
            return null;
        }

        $request->setAlias(\Magento\Framework\Url::REWRITE_REQUEST_PATH_ALIAS, $_identifier);

        return $this->actionFactory->create(
            'Magento\Framework\App\Action\Forward',
            ['request' => $request]
        );
    }

}
