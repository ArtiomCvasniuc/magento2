<?php
namespace Tukan\Checkout\Observer;

class SaveTaxNumberInOrder implements \Magento\Framework\Event\ObserverInterface
{
    public function execute(\Magento\Framework\Event\Observer $observer) {
        $order = $observer->getEvent()->getOrder();
        $quote = $observer->getEvent()->getQuote();
         if ($quote->getBillingAddress()) {
              $order->getBillingAddress()->setTaxNumber($quote->getBillingAddress()->getExtensionAttributes()->getTaxNumber());
          }
          if (!$quote->isVirtual()) {            
              $order->getShippingAddress()->setTaxNumber($quote->getShippingAddress()->getTaxNumber());
          }
        return $this;
    }
}
