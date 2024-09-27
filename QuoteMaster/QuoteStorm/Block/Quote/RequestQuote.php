<?php

namespace QuoteMaster\QuoteStorm\Block\Quote;

use Magento\Framework\View\Element\Template;
use Magento\Customer\Model\Session;
use Magento\Customer\Model\CustomerFactory;
use Magento\Customer\Helper\Address;
use QuoteMaster\QuoteStorm\Model\CompositeConfigProvider;
use Magento\Checkout\Block\Checkout\LayoutProcessorInterface;
use Magento\Framework\Serialize\Serializer\JsonHexTag;

class RequestQuote extends Template
{
    /**
     * @var Session
     */
    protected $customerSession;

    /**
     * @var CustomerFactory
     */
    protected $customerFactory;

    /**
     * @var Address
     */
    protected $addressFormatHelper;

    /**
     * @var CompositeConfigProvider
     */
    protected $configProvider;

    /**
     * @var array|LayoutProcessorInterface[]
     */
    protected $layoutProcessors;

    /**
     * @var JsonHexTag
     */
    private $jsonHexTagSerializer;

    public function __construct(
        Template\Context $context,
        Session $customerSession,
        CustomerFactory $customerFactory,
        Address $addressFormatHelper,
        CompositeConfigProvider $configProvider,
        JsonHexTag $jsonHexTagSerializer,
        array $layoutProcessors = [],
        array $data = [],
    ) {
        parent::__construct($context, $data);
        $this->customerSession = $customerSession;
        $this->customerFactory = $customerFactory;
        $this->addressFormatHelper = $addressFormatHelper;
        $this->configProvider = $configProvider;
        $this->jsonHexTagSerializer = $jsonHexTagSerializer;
        $this->layoutProcessors = $layoutProcessors;
    }

    /**
     * Get the checkout configuration.
     *
     * @return array
     */
    public function getCheckoutConfig()
    {
        return $this->configProvider->getConfig();
    }

    /**
     * Get the JavaScript layout.
     *
     * @return string
     */
    public function getJsLayout()
    {
        foreach ($this->layoutProcessors as $processor) {
            $this->jsLayout = $processor->process($this->jsLayout);
        }

        return $this->jsonHexTagSerializer->serialize($this->jsLayout);
    }

    /**
     * Get the base URL of the store.
     *
     * @return string
     */
    public function getBaseUrl()
    {
        return $this->_storeManager->getStore()->getBaseUrl();
    }

    /**
     * Get the serialized checkout configuration.
     *
     * @return string
     */
    public function getSerializedCheckoutConfig()
    {
        return $this->jsonHexTagSerializer->serialize($this->getCheckoutConfig());
    }

    /**
     * Get the URL for requesting a quote.
     *
     * @return string
     */
    public function getRequestQuoteUrl()
    {
        return $this->getUrl('quote/quote/requestQuote');
    }

    /**
     * Check if the customer is logged in.
     *
     * @return bool
     */
    public function isLoggedIn()
    {
        return $this->customerSession->isLoggedIn();
    }

    /**
     * Get the addresses of the logged-in customer.
     *
     * @return array
     */
    public function getLoggedInCustomerAddress()
    {
        $customerId = $this->customerSession->getId();
        return $this->getAddresses($customerId);
    }

    /**
     * Retrieve addresses for the given customer ID.
     *
     * @param int $customerId
     * @return array
     */
    protected function getAddresses($customerId)
    {
        $customer = $this->customerFactory->create();
        $customerModel = $customer->load($customerId);

        $customerAddresses = [];

        if ($customerModel->getAddresses() !== null)
        {
            foreach ($customerModel->getAddresses() as $address) {
                $customerAddresses[$address->getId()] = $address->toArray();
            }
        }

        return $customerAddresses;
    }

    /**
     * Convert address array to a formatted string.
     *
     * @param array $address
     * @return string
     */
    public function getAddressAsString(array $address): string
    {
        $formatTypeRenderer = $this->addressFormatHelper->getFormatTypeRenderer('oneline');
        $result = '';
        if ($formatTypeRenderer) {
            $result = $formatTypeRenderer->renderArray($address);
        }

        return $result;
    }
}
