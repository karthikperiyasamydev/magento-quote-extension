<?php

namespace QuoteMaster\QuoteStorm\Model\Quote;

class ConfigProvider extends \Magento\Checkout\Model\DefaultConfigProvider
{
    public function __construct(
        \QuoteMaster\QuoteStorm\Model\Session $quoteSession,
        \Magento\Checkout\Helper\Data $checkoutHelper,
        \Magento\Customer\Api\CustomerRepositoryInterface $customerRepository,
        \Magento\Customer\Model\Session $customerSession,
        \Magento\Customer\Model\Url $customerUrlManager,
        \Magento\Framework\App\Http\Context $httpContext,
        \Magento\Quote\Api\CartRepositoryInterface $quoteRepository,
        \Magento\Quote\Api\CartItemRepositoryInterface $quoteItemRepository,
        \Magento\Quote\Api\ShippingMethodManagementInterface $shippingMethodManager,
        \Magento\Catalog\Helper\Product\ConfigurationPool $configurationPool,
        \Magento\Quote\Model\QuoteIdMaskFactory $quoteIdMaskFactory,
        \Magento\Framework\Locale\FormatInterface $localeFormat,
        \Magento\Customer\Model\Address\Mapper $addressMapper,
        \Magento\Customer\Model\Address\Config $addressConfig,
        \Magento\Framework\Data\Form\FormKey $formKey,
        \Magento\Catalog\Helper\Image $imageHelper,
        \Magento\Framework\View\ConfigInterface $viewConfig,
        \Magento\Directory\Model\Country\Postcode\ConfigInterface $postCodesConfig,
        \Magento\Checkout\Model\Cart\ImageProvider $imageProvider,
        \Magento\Directory\Helper\Data $directoryHelper,
        \Magento\Quote\Api\CartTotalRepositoryInterface $cartTotalRepository,
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig,
        \Magento\Shipping\Model\Config $shippingMethodConfig,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Magento\Quote\Api\PaymentMethodManagementInterface $paymentMethodManagement,
        \Magento\Framework\UrlInterface $urlBuilder,
        \Magento\Captcha\Api\CaptchaConfigPostProcessorInterface $captchaConfigPostProcessor,
    ) {
            parent::__construct(
                $checkoutHelper,
                $quoteSession,
                $customerRepository,
                $customerSession,
                $customerUrlManager,
                $httpContext,
                $quoteRepository,
                $quoteItemRepository,
                $shippingMethodManager,
                $configurationPool,
                $quoteIdMaskFactory,
                $localeFormat,
                $addressMapper,
                $addressConfig,
                $formKey,
                $imageHelper,
                $viewConfig,
                $postCodesConfig,
                $imageProvider,
                $directoryHelper,
                $cartTotalRepository,
                $scopeConfig,
                $shippingMethodConfig,
                $storeManager,
                $paymentMethodManagement,
                $urlBuilder,
                $captchaConfigPostProcessor
            );
    }

    /**
     * Get the checkout URL for the quote checkout page
     *
     * @return string
     */
    public function getCheckoutUrl()
    {
        return $this->urlBuilder->getUrl('quote/quote/index');
    }

    /**
     * Get the URL for the page not found action
     *
     * @return string
     */
    public function pageNotFoundUrl()
    {
        return $this->urlBuilder->getUrl('quote/quote/index');
    }

    /**
     * Get the default success page URL
     *
     * @return string
     */
    public function getDefaultSuccessPageUrl()
    {
        return $this->urlBuilder->getUrl('quote/quote/success');
    }
}
