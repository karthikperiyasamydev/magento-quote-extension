<?php

namespace QuoteMaster\QuoteStorm\Block\Product\ProductList;

class AddToQuote extends \Magento\Catalog\Block\Product\ProductList\Item\Block
{
    /**
     * Generate URL for adding the product to the quote
     *
     * @return string
     */
    public function getAddToQuoteUrl()
    {
        $params = [
            'product' => $this->getProduct()->getId()
        ];
        return $this->_urlBuilder->getUrl('quote/quote/add',$params);
    }
}
