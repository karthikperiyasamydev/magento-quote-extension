<?php

namespace QuoteMaster\QuoteStorm\Observer;

use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;
use Magento\Framework\Message\ManagerInterface;
use QuoteMaster\QuoteStorm\Model\Session;

class LoadCustomerQuoteObserver implements ObserverInterface
{
    /**
     * @var Session
     */
    protected $quoteSession;

    /**
     * @var ManagerInterface
     */
    protected $messageManager;

    public function __construct(
        Session $quoteSession,
        ManagerInterface $messageManager
    ) {
        $this->quoteSession = $quoteSession;
        $this->messageManager = $messageManager;
    }

    /**
     * This method is to load the customer's quote
     *
     * @param Observer $observer
     * @return void
     */
    public function execute(Observer $observer)
    {
        try {
            $this->quoteSession->loadCustomerQuote();
        } catch (\Magento\Framework\Exception\LocalizedException $e) {
            $this->messageManager->addErrorMessage($e->getMessage());
        } catch (\Exception $e) {
            $this->messageManager->addExceptionMessage($e, __('Load customer quote error'));
        }
    }
}
