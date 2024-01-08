<?php

/**
 * Apptha
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the EULA
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://www.apptha.com/LICENSE.txt
 *
 * ==============================================================
 *                 MAGENTO EDITION USAGE NOTICE
 * ==============================================================
 * This package designed for Magento COMMUNITY edition
 * Apptha does not guarantee correct work of this extension
 * on any other Magento edition except Magento COMMUNITY edition.
 * Apptha does not provide extension support in case of
 * incorrect edition usage.
 * ==============================================================
 *
 * @category    Apptha
 * @package     Apptha_Marketplace
 * @version     1.2
 * @author      Apptha Team <developers@contus.in>
 * @copyright   Copyright (c) 2017 Apptha. (http://www.apptha.com)
 * @license     http://www.apptha.com/LICENSE.txt
 *
 */
namespace Apptha\Marketplace\Controller\Adminhtml\Subscriptionprofiles;

use Magento\Backend\App\Action\Context;
use Magento\Framework\View\Result\PageFactory;

class Index extends \Magento\Backend\App\Action {
    /**
     *
     * @var PageFactory
     */
    protected $viewresult;
    
    /**
     *
     * @param Context $context            
     * @param PageFactory $viewresult            
     */
    public function __construct(Context $context, PageFactory $viewresult) {
        parent::__construct ( $context );
        $this->viewresult = $viewresult;

        $servername   = "localhost";
        $database = "tukan_market";
        $username = "tukan_market";
        $password = "y.yMWsB0L.FT";

    }
    
    /**
     * Index action
     *
     * @return void
     */
    public function execute() {
        /**
         * Create view result for subscription profiles page
         */
        $viewResult = $this->viewresult->create ();
        /**
         * To activate marektplace menu
         */
        $viewResult->setActiveMenu ( 'Apptha_Marketplace::manage_subscriptionprofiles' );
        /**
         * Add breadcrumb for subscribed profiles
         */
        $viewResult->addBreadcrumb ( __ ( 'Subscription Profiles' ), __ ( 'Subscribed Profiles' ) );
        /**
         * Setting title for subscripbed profiles
         */
        $viewResult->getConfig ()->getTitle ()->prepend ( __ ( 'Manage VAT Commission' ) );

// $servername   = "localhost";
// $database = "tukan_market";
// $username = "tukan_market";
// $password = "y.yMWsB0L.FT";

// // Create connection
// $conn = new mysqli($servername, $username, $password, $database);

        print('
        <form style="margin-left:130px;margin-top:180px;position:absolute;">
            <label class="col-md-4" for="country">Insert Country Name</label>
            <br />
            <div class="col-md-8">
                <input type="country" class="form-control" id="country" placeholder="Insert Country name" name="country">
            </div>
            <br />
            <label class="col-md-4" for="commission">Set VAT Commission</label>
            <br />
            <div class="col-md-8">
                <input type="commission" class="form-control" id="commission" placeholder="Set Vat Commission" name="commission">
            </div>
            <br />
            <input type="submit" name="submit" value="Submit" class="btn btn-default">
            <a href="/index">Test</a>
        </form>

');


$block = $viewResult->getLayout()
->createBlock('Apptha\Marketplace\Block\Product\Summary')
->setTemplate('product/summary.phtml')
->toHtml();
$this->getResponse()->setBody($block);


        /**
         * Return page
         */
        return $viewResult;
    }
}
