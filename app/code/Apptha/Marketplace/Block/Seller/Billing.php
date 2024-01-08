<?php
namespace Apptha\Marketplace\Block\Seller;

class Billing extends \Magento\Directory\Block\Data {

public function _prepareLayout() {

        $this->pageConfig->getTitle ()->set ( __ ( "All Sellers" ) );

        return parent::_prepareLayout ();
    }
    
}
