<?php

namespace QuoteMaster\QuoteStorm\Controller\Quote;

class UpdateQuote extends \Magento\Checkout\Controller\Cart\UpdatePost
{
    public function __construct(
        \Magento\Framework\App\Action\Context $context,
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig,
        \QuoteMaster\QuoteStorm\Model\Session $checkoutSession,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Magento\Framework\Data\Form\FormKey\Validator $formKeyValidator,
        \QuoteMaster\QuoteStorm\Model\Quote $cart,
        \Magento\Checkout\Model\Cart\RequestQuantityProcessor $quantityProcessor
    ) {
        parent::__construct(
            $context,
            $scopeConfig,
            $checkoutSession,
            $storeManager,
            $formKeyValidator,
            $cart,
            $quantityProcessor
        );
    }
}
