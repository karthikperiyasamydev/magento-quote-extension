<?php

namespace QuoteMaster\QuoteStorm\Controller\Quote;

class UpdateItemQty extends \Magento\Checkout\Controller\Cart\UpdateItemQty
{
    public function __construct(
        \Magento\Framework\App\Action\Context $context,
        \Magento\Checkout\Model\Cart\RequestQuantityProcessor $quantityProcessor,
        \Magento\Framework\Data\Form\FormKey\Validator $formKeyValidator,
        \QuoteMaster\QuoteStorm\Model\Session $checkoutSession,
        \Magento\Framework\Serialize\Serializer\Json $json,
        \Psr\Log\LoggerInterface $logger
    ) {
        parent::__construct(
            $context,
            $quantityProcessor,
            $formKeyValidator,
            $checkoutSession,
            $json,
            $logger
        );
    }
}
