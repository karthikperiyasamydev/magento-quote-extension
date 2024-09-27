<?php

namespace QuoteMaster\QuoteStorm\Observer;

use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;
use QuoteMaster\QuoteStorm\Model\Session;

class UnsetAllObserver implements ObserverInterface
{
    /**
     * @var Session
     */
    protected $quoteSession;

    public function __construct(Session $quoteSession)
    {
        $this->quoteSession = $quoteSession;
    }

    /**
     * It clears the current quote and the associated storage to reset the session data.
     *
     * @param Observer $observer
     * @return void
     */
    public function execute(Observer $observer)
    {
        $this->quoteSession->clearQuote()->clearStorage();
    }
}
