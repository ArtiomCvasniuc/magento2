<?php
/**
 * Created by PhpStorm.
 * User: Dinh Phuc Tran
 * Date: 05-Nov-18
 * Time: 15:28
 */

namespace Mageplaza\PdfInvoice\Controller\Adminhtml\MassAction;

use Exception;
use Magento\Backend\App\Action\Context;
use Magento\Backend\Model\View\Result\Redirect;
use Magento\Framework\App\Response\Http\FileFactory;
use Magento\Framework\Controller\ResultFactory;
use Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection;
use Magento\Framework\Stdlib\DateTime\DateTime;
use Magento\Sales\Controller\Adminhtml\Invoice\AbstractInvoice\Pdfinvoices;
use Magento\Sales\Model\Order\Pdf\Invoice;
use Magento\Sales\Model\OrderRepository;
use Magento\Sales\Model\ResourceModel\Order\Invoice\CollectionFactory;
use Magento\Ui\Component\MassAction\Filter;
use Mageplaza\PdfInvoice\Helper\PrintProcess;
use Mageplaza\PdfInvoice\Model\Source\Type;

/**
 * Class PrintFromOrder
 * @package Mageplaza\PdfInvoice\Controller\Adminhtml\MassAction
 */
class PrintFromOrder extends Pdfinvoices
{
    /**
     * @var string
     */
    protected $_redirectUrl = 'sales/order/view/order_id/';

    /**
     * @var OrderRepository
     */
    protected $_orderRepository;

    /**
     * @var PrintProcess
     */
    protected $_printProcess;

    /**
     * PrintFromOrder constructor.
     *
     * @param Context $context
     * @param Filter $filter
     * @param DateTime $dateTime
     * @param FileFactory $fileFactory
     * @param Invoice $pdfInvoice
     * @param CollectionFactory $collectionFactory
     * @param OrderRepository $orderRepository
     * @param PrintProcess $printProcess
     */
    public function __construct(
        Context $context,
        Filter $filter,
        DateTime $dateTime,
        FileFactory $fileFactory,
        Invoice $pdfInvoice,
        CollectionFactory $collectionFactory,
        OrderRepository $orderRepository,
        PrintProcess $printProcess
    ) {
        $this->_orderRepository = $orderRepository;
        $this->_printProcess = $printProcess;

        parent::__construct($context, $filter, $dateTime, $fileFactory, $pdfInvoice, $collectionFactory);
    }

    /**
     * Save collection items to pdf invoices
     *
     * @param AbstractCollection $collection
     *
     * @return Redirect
     * @throws Exception
     */
    public function massAction(AbstractCollection $collection)
    {
        try {
            /** @var Redirect $resultRedirect */
            $resultRedirect = $this->resultFactory->create(ResultFactory::TYPE_REDIRECT);

            $order_id = $this->getRequest()->getParam('order_id');
            $type = $this->getRequest()->getParam('type');

            $ids = [];
            $storeId = $this->_orderRepository->get($order_id)->getStoreId();

            switch ($type) {
                case Type::INVOICE:
                    foreach ($collection as $invoice) {
                        $ids[$storeId][] = $invoice->getId();
                    }
                    break;

                case Type::SHIPMENT:
                    foreach ($collection as $shipment) {
                        $ids[$storeId][] = $shipment->getId();
                    }
                    break;

                case Type::CREDIT_MEMO:
                    foreach ($collection as $creditMemo) {
                        $ids[$storeId][] = $creditMemo->getId();
                    }
                    break;
            }

            $this->_printProcess->printAllPdf($type, $ids, $storeId);
        } catch (Exception $e) {
            $this->messageManager->addError($e->getMessage());

            return $resultRedirect->setPath($this->_redirectUrl . $order_id);
        }
    }
}
