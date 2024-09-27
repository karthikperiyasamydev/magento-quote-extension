<?php

namespace QuoteMaster\QuoteStorm\CustomerData;

use Magento\Catalog\Model\ResourceModel\Url;
use Magento\Checkout\CustomerData\ItemPoolInterface;
use Magento\Customer\CustomerData\SectionSourceInterface;
use Magento\Quote\Api\Data\CartInterface;
use QuoteMaster\QuoteStorm\Model\Session;

class Quote implements SectionSourceInterface
{
    /**
     * @var Session
     */
    protected $quoteSession;

    protected $quote = null;

    /**
     * @var ItemPoolInterface
     */
    protected $itemPoolInterface;

    /**
     * @var Url
     */
    protected $catalogUrl;

    public function __construct(
        \Magento\Catalog\Model\ResourceModel\Url $catalogUrl,
        \QuoteMaster\QuoteStorm\Model\Session $quotationSession,
        ItemPoolInterface $itemPoolInterface,
    ) {
        $this->quoteSession = $quotationSession;
        $this->itemPoolInterface = $itemPoolInterface;
        $this->catalogUrl = $catalogUrl;
    }

    /**
     * This method returns the data for the quote section, including the quote ID,
     * total items quantity, items count, and individual items' data.
     *
     * @return array
     */
    public function getSectionData()
    {
        return [
            'quote_id'       => $this->getQuote()->getId(),
            'items_quantity' => intval($this->getQuote()->getItemsQty()),
            'items_count'    => $this->getQuote()->getItemsCount(),
            'items'          => $this->getItems(),
        ];
    }

    /**
     * This method checks if the quote is already loaded; if not, it loads it
     * from the quote session.
     *
     * @return CartInterface|\Magento\Quote\Model\Quote
     */
    protected function getQuote()
    {
        if (null === $this->quote) {
            $this->quote = $this->quoteSession->getQuote();
        }
        return $this->quote;
    }

    /**
     * This method gathers the data for all items in the quote and
     * returns an array of item data.
     *
     * @return array
     */
    protected function getItems()
    {
        $items = [];

        foreach (array_reverse($this->getAllQuoteItems()) as $item) {
                $items[] = $this->itemPoolInterface->getItemData($item);
        }

        return $items;
    }

    /**
     * This method returns all visible items associated with the quote.
     *
     * @return array
     */
    protected function getAllQuoteItems()
    {
        return $this->getQuote()->getAllVisibleItems();
    }
}
