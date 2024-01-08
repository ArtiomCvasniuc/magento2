<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2020 Amasty (https://www.amasty.com)
 * @package Amasty_InvisibleCaptcha
 */


namespace Amasty\InvisibleCaptcha\Plugin;

use Amasty\InvisibleCaptcha\Model\ConfigProvider;
use Magento\Customer\Model\Group;

class Predispatch
{
    /**
     * @var \Magento\Framework\UrlInterface
     */
    protected $urlBuilder;

    /**
     * @var \Magento\Framework\Message\ManagerInterface
     */
    protected $messageManager;

    /**
     * @var \Magento\Framework\App\Response\RedirectInterface
     */
    private $redirect;

    /**
     * @var \Magento\Framework\App\ResponseFactory
     */
    private $responseFactory;

    /**
     * Captcha model instance
     *
     * @var \Amasty\InvisibleCaptcha\Model\Captcha
     */
    private $captchaModel;

    /**
     * @var \Magento\Customer\Model\Session
     */
    private $session;

    /**
     * Ignore list of URLs for logged in users
     *
     * @var array
     */
    private $ignoreListForLoggedIn = ['productalert/add'];

    /**
     * @var ConfigProvider
     */
    private $configProvider;

    public function __construct(
        \Magento\Framework\UrlInterface $urlBuilder,
        \Magento\Framework\Message\ManagerInterface $messageManager,
        \Magento\Framework\App\Response\RedirectInterface $redirect,
        \Magento\Framework\App\ResponseFactory $responseFactory,
        \Amasty\InvisibleCaptcha\Model\Captcha $captchaModel,
        \Magento\Customer\Model\Session $session,
        ConfigProvider $configProvider
    ) {
        $this->urlBuilder = $urlBuilder;
        $this->messageManager = $messageManager;
        $this->redirect = $redirect;
        $this->responseFactory = $responseFactory;
        $this->captchaModel = $captchaModel;
        $this->session = $session;
        $this->configProvider = $configProvider;
    }

    /**
     * @param \Magento\Framework\App\FrontControllerInterface $subject
     * @param \Closure                                        $proceed
     * @param \Magento\Framework\App\RequestInterface         $request
     *
     * @return \Magento\Framework\App\ResponseInterface|mixed
     */
    public function aroundDispatch(
        \Magento\Framework\App\FrontControllerInterface $subject,
        \Closure $proceed,
        \Magento\Framework\App\RequestInterface $request
    ) {
        if ($this->captchaModel->isNeedToShowCaptcha()) {
            foreach ($this->configProvider->getAllUrls() as $captchaUrl) {
                if ($request->isPost()
                    && !$this->isInIgnoreList($captchaUrl)
                    && false !== strpos($this->urlBuilder->getCurrentUrl(), $captchaUrl)
                ) {
                    $token = $request->getPost('amasty_invisible_token');
                    $validation = $this->captchaModel->verify($token);

                    if (!$validation['success']) {
                        $this->messageManager->addErrorMessage($validation['error']);
                        $response = $this->responseFactory->create();
                        $response->setRedirect($this->redirect->getRefererUrl());
                        $response->setNoCacheHeaders();

                        return $response;
                    }

                    break;
                }
            }
        }

        $result = $proceed($request);

        return $result;
    }

    private function isInIgnoreList($captchaUrl)
    {
        if ($this->session->getCustomerGroupId() != Group::NOT_LOGGED_IN_ID) {
            foreach ($this->ignoreListForLoggedIn as $ignoredUrl) {
                if (false !== strpos($captchaUrl, $ignoredUrl)) {
                    return true;
                }
            }
        }

        return false;
    }
}
