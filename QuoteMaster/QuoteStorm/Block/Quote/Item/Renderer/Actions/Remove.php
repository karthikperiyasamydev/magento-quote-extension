<?php

namespace QuoteMaster\QuoteStorm\Block\Quote\Item\Renderer\Actions;

class Remove extends \Magento\Checkout\Block\Cart\Item\Renderer\Actions\Generic
{
    const DELETE_URL = 'quote/quote/delete';

    /**
     * Get the delete quote action URL with item data
     *
     * @return string
     */
    public function getDeleteQuote()
    {
        $url = $this->_urlBuilder->getUrl(self::DELETE_URL);
        $item = $this->getItem();
        $data = ['id' => $item->getId()];
        return json_encode(['action' => $url, 'data' => $data]);
    }
}
