<?php

namespace QuoteMaster\QuoteStorm\Block\Quote\Item\Renderer\Actions;

class Edit extends \Magento\Checkout\Block\Cart\Item\Renderer\Actions\Generic
{
    /**
     * Get the URL for configuring the quote item
     *
     * @return string
     */
    public function getConfigureUrl()
    {
        return $this->getUrl(
            'quote/quote/configure',
            [
                'id'         => $this->getItem()->getId(),
                'product_id' => $this->getItem()->getProduct()->getId(),
            ]
        );
    }
}
