<?php

namespace QuoteMaster\QuoteStorm\ViewModel\Category;

use Magento\Framework\UrlInterface;
use Magento\Framework\View\Element\Block\ArgumentInterface;

class AddToQuote implements ArgumentInterface
{
    /**
     * @var UrlInterface
     */
    protected $urlBuilder;

    public function __construct(UrlInterface $urlBuilder)
    {
        $this->urlBuilder = $urlBuilder;
    }

    /**
     * Generate the URL for adding a product to the quote
     *
     * @param int $productId
     * @return string
     */
    public function getAddToQuoteUrl($productId)
    {
        $params = [
            'product' => $productId
        ];
        return $this->urlBuilder->getUrl('quote/quote/add',$params);
    }
}
