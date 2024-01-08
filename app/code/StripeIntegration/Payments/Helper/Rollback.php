<?php

namespace StripeIntegration\Payments\Helper;

use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Exception\CouldNotSaveException;
use Magento\Framework\Validator\Exception;

class Rollback
{
    protected $data;

    public function __construct(
        \StripeIntegration\Payments\Model\Config $config,
        \StripeIntegration\Payments\Helper\Generic $helper,
        \Magento\Framework\Session\Generic $session,
        \Psr\Log\LoggerInterface $logger,
        \StripeIntegration\Payments\Model\SubscriptionFactory $subscriptionFactory
    ) {
        $this->helper = $helper;
        $this->config = $config;
        $this->session = $session;
        $this->logger = $logger;
        $this->subscriptionFactory = $subscriptionFactory;

        $this->data = $this->session->getRollbackData();

        if (empty($this->data))
            $this->reset();
    }

    public function reset()
    {
        $this->data = [
            'subscriptions' => [],
            'authorizations' => [],
            'charges' => [],
            'cards' => [],
            'sources' => []
        ];
        $this->session->setRollbackData($this->data);
    }

    public function addSubscription($id)
    {
        $this->data['subscriptions'][$id] = $id;
        $this->session->setRollbackData($this->data);
    }

    public function addCharge($id)
    {
        $this->data['charges'][$id] = $id;
        $this->session->setRollbackData($this->data);
    }

    public function addAuthorization($id)
    {
        $this->data['authorizations'][$id] = $id;
        $this->session->setRollbackData($this->data);
    }

    public function addCard($customerId, $cardId)
    {
        $this->data['cards'][$cardId] = $customerId;
        $this->session->setRollbackData($this->data);
    }

    public function addSource($customerId, $sourceId)
    {
        $this->data['sources'][$sourceId] = $customerId;
        $this->session->setRollbackData($this->data);
    }

    public function logException($e)
    {
        $log = false;

        // Only log it if the rollback will run
        foreach ($this->data as $key => $value)
        {
            if (!empty($value))
            {
                $log = true;
                break;
            }
        }

        if ($log)
        {
            $this->logger->addInfo("ROLLBACK: An error has occurred while placing an order and a rollback will be initiated.");
            $this->logger->addInfo("ROLLBACK: ERROR: " . $e->getMessage());
            $this->logger->addInfo("ROLLBACK: STACK TRACE:");
            $this->logger->addInfo($e->getTraceAsString());
        }
    }

    public function run($e)
    {
        $this->logException($e);

        foreach ($this->data['authorizations'] as $id)
        {
            try
            {
                \StripeIntegration\Payments\Model\Config::$stripeClient->paymentIntents->cancel($id, []);
            }
            catch (\Exception $e)
            {
                $this->logger->addInfo("Error while canceling authorization $id: " . $e->getMessage());
            }
        }

        foreach ($this->data['charges'] as $id)
        {
            try
            {
                \StripeIntegration\Payments\Model\Config::$stripeClient->refunds->create(['charge' => $id]);
            }
            catch (\Exception $e)
            {
                $this->logger->addInfo("Error while refunding charge $id: " . $e->getMessage());
            }
        }

        foreach ($this->data['subscriptions'] as $id)
        {
            try
            {
                $this->subscriptionFactory->create()->cancel($id);
            }
            catch (\Exception $e)
            {
                $this->logger->addInfo("Error while canceling subscription $id: " . $e->getMessage());
            }
        }

        foreach ($this->data['cards'] as $id => $customer)
        {
            try
            {
                \StripeIntegration\Payments\Model\Config::$stripeClient->customers->deleteSource($customer, $id, []);
            }
            catch (\Exception $e)
            {
                $this->logger->addInfo("Error while deleting saved card $id: " . $e->getMessage());
            }
        }

        foreach ($this->data['sources'] as $id => $customer)
        {
            try
            {
                \Stripe\Customer::deleteSource($customer, $id);
            }
            catch (\Exception $e)
            {
                $this->logger->addInfo("Error while deleting source $id: " . $e->getMessage());
            }
        }

        $this->reset();
    }
}
