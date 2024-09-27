<?php

namespace QuoteMaster\QuoteStorm\Plugin\Quote;

use Magento\Authorization\Model\UserContextInterface;
use Magento\Checkout\Model\Session;
use Magento\Framework\App\RequestInterface;

class ParamOverriderCartId
{
    /**
     * @var UserContextInterface
     */
    protected $userContext;

    /**
     * @var RequestInterface
     */
    protected $request;

    /**
     * @var \QuoteMaster\QuoteStorm\Model\Session
     */
    protected $quoteSession;

    /**
     * @var Session
     */
    protected $session;

    public function __construct(
        \Magento\Authorization\Model\UserContextInterface $userContext,
        \Magento\Framework\App\RequestInterface $request,
        \QuoteMaster\QuoteStorm\Model\Session $quoteSession,
        \Magento\Checkout\Model\Session $session
    ) {
        $this->userContext = $userContext;
        $this->request = $request;
        $this->quoteSession = $quoteSession;
        $this->session = $session;
    }

    /**
     * This method modifies the behavior of retrieving the cart ID based on user type and referer URL.
     *
     * @param \Magento\Quote\Model\Webapi\ParamOverriderCartId $subject
     * @param callable $proceed
     * @return int|null
     */
    public function aroundGetOverriddenValue(
        \Magento\Quote\Model\Webapi\ParamOverriderCartId $subject,
        callable $proceed
    ) {
        try {
            if ($this->userContext->getUserType() === \Magento\Authorization\Model\UserContextInterface::USER_TYPE_CUSTOMER) {
                $referer = $this->request->getHeader('Referer');
                if (str_contains($referer, 'checkout')) {
                    $cart = $this->session->getQuote();
                    if ($cart) {
                        return $cart->getId();
                    }
                } else {
                    $quote = $this->quoteSession->getQuote();
                    if ($quote) {
                        return $quote->getId();
                    }
                }
            }
        } catch (\Magento\Framework\Exception\NoSuchEntityException $e) {
            throw new \Magento\Framework\Exception\NoSuchEntityException(__('Current customer does not have an active cart.'));
        }

        return null;
    }
}
