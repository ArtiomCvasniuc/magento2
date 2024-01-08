<?php
/**
 * Mageplaza
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Mageplaza.com license that is
 * available through the world-wide-web at this URL:
 * https://www.mageplaza.com/LICENSE.txt
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade this extension to newer
 * version in the future.
 *
 * @category    Mageplaza
 * @package     Mageplaza_QuickCart
 * @copyright   Copyright (c) Mageplaza (https://www.mageplaza.com/)
 * @license     https://www.mageplaza.com/LICENSE.txt
 */

namespace Mageplaza\QuickCart\Test\Unit\Plugin\CustomerData;

use Magento\Checkout\Model\Session;
use Magento\Framework\UrlInterface;
use Magento\Quote\Api\CartTotalRepositoryInterface;
use Magento\Quote\Api\Data\TotalsInterface;
use Magento\Quote\Model\Cart\TotalSegment;
use Magento\Quote\Model\Quote;
use Magento\Quote\Model\QuoteIdMaskFactory;
use Magento\Store\Model\Store;
use Mageplaza\QuickCart\Helper\Data;
use Mageplaza\QuickCart\Plugin\CustomerData\Cart;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use PHPUnit_Framework_MockObject_MockObject;
use Psr\Log\LoggerInterface;

/**
 * Class CartTest
 * @package Mageplaza\QuickCart\Test\Unit\Plugin\CustomerData
 */
class CartTest extends TestCase
{
    /**
     * @var Data|PHPUnit_Framework_MockObject_MockObject
     */
    private $helper;

    /**
     * @var Session|PHPUnit_Framework_MockObject_MockObject
     */
    private $session;

    /**
     * @var LoggerInterface|PHPUnit_Framework_MockObject_MockObject
     */
    private $logger;

    /**
     * @var \Magento\Customer\Model\Session|MockObject
     */
    private $customerSession;

    /**
     * @var CartTotalRepositoryInterface|PHPUnit_Framework_MockObject_MockObject
     */
    private $cartTotalRepository;

    /**
     * @var QuoteIdMaskFactory|PHPUnit_Framework_MockObject_MockObject
     */
    private $quoteIdMaskFactory;

    /**
     * @var UrlInterface|PHPUnit_Framework_MockObject_MockObject
     */
    private $urlBuilder;

    /**
     * @var Cart
     */
    private $object;

    protected function setUp()
    {
        $this->helper  = $this->getMockBuilder(Data::class)->disableOriginalConstructor()->getMock();
        $this->session = $this->getMockBuilder(Session::class)->disableOriginalConstructor()->getMock();
        $this->logger  = $this->getMockBuilder(LoggerInterface::class)->disableOriginalConstructor()->getMock();

        $this->customerSession = $this->getMockBuilder(\Magento\Customer\Model\Session::class)
            ->disableOriginalConstructor()->getMock();

        $this->cartTotalRepository = $this->getMockBuilder(CartTotalRepositoryInterface::class)->getMock();

        $this->quoteIdMaskFactory = $this->getMockBuilder(QuoteIdMaskFactory::class)
            ->setMethods(['create'])
            ->disableOriginalConstructor()->getMock();

        $this->urlBuilder = $this->getMockBuilder(UrlInterface::class)->disableOriginalConstructor()->getMock();

        $this->object = new Cart(
            $this->helper,
            $this->session,
            $this->logger,
            $this->customerSession,
            $this->cartTotalRepository,
            $this->quoteIdMaskFactory,
            $this->urlBuilder
        );
    }

    public function testAfterGetSectionData()
    {
        /** @var \Magento\Checkout\CustomerData\Cart|PHPUnit_Framework_MockObject_MockObject $subject */
        $subject = $this->getMockBuilder(\Magento\Checkout\CustomerData\Cart::class)
            ->disableOriginalConstructor()->getMock();

        $result = [];

        $this->appendSegment($result);

        $this->assertEquals($result, $this->object->afterGetSectionData($subject, $result));
    }

    /**
     * @param array $result
     */
    protected function appendSegment(&$result)
    {
        $quote = $this->getMockBuilder(Quote::class)
            ->setMethods(['getStoreId', 'getCouponCode', 'getId', 'getStore'])
            ->disableOriginalConstructor()->getMock();
        $this->session->expects($this->once())->method('getQuote')->willReturn($quote);

        $storeId = 'store id';
        $quote->expects($this->once())->method('getStoreId')->willReturn($storeId);

        $this->helper->method('isEnabled')->with($storeId)->willReturn(1);

        $couponCode = 'coupon code';
        $quote->expects($this->once())->method('getCouponCode')->willReturn($couponCode);

        $this->helper->method('isShowFull')->with($storeId)->willReturn(1);

        $quoteId = 'quote id';
        $quote->method('getId')->willReturn($quoteId);

        $isLoggedIn = true;
        $this->customerSession->method('isLoggedIn')->willReturn($isLoggedIn);

        $store = $this->getMockBuilder(Store::class)->disableOriginalConstructor()->getMock();
        $quote->expects($this->once())->method('getStore')->willReturn($store);

        $storeCode = 'store code';
        $store->method('getCode')->willReturn($storeCode);

        $apiUrl = sprintf('rest/%s/V1', $storeCode);
        $this->urlBuilder->expects($this->once())->method('getUrl')->with($apiUrl, ['_secure' => true])
            ->willReturn($apiUrl);

        $result['mpquickcart'] = [
            'couponCode' => $couponCode,
            'isLoggedIn' => $isLoggedIn,
            'quoteId'    => $quoteId,
            'totals'     => [],
            'apiUrl'     => $apiUrl
        ];

        $totals = $this->getMockBuilder(TotalsInterface::class)->getMock();
        $this->cartTotalRepository->expects($this->once())->method('get')->with($quoteId)->willReturn($totals);

        $segment = $this->getMockBuilder(TotalSegment::class)->disableOriginalConstructor()->getMock();
        $totals->expects($this->once())->method('getTotalSegments')->willReturn([$segment]);

        $data = [
            'code'  => 'code',
            'label' => 'label',
            'value' => 'value',
        ];
        $segment->method('toArray')->willReturn($data);
        $segment->method('getValue')->willReturn($data['value']);
        $segment->method('getCode')->willReturn($data['code']);

        $this->helper->method('formatPrice')->with($data['value'], false, $storeId)->willReturn($data['value']);

        $result['mpquickcart']['totals'][] = $data;
    }
}
