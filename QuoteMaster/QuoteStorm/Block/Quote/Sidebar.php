<?php

namespace QuoteMaster\QuoteStorm\Block\Quote;

use Magento\Framework\Serialize\Serializer\Json;

class Sidebar extends \Magento\Framework\View\Element\Template
{
    /**
     * @var Json
     */
    protected $serializer;

    public function __construct(
        \Magento\Framework\View\Element\Template\Context $context,
        \Magento\Customer\CustomerData\JsLayoutDataProviderPoolInterface $jsLayoutDataProvider,
        \Magento\Framework\Serialize\Serializer\Json $serializer,
        array $data = []
    ) {
        if (isset($data['jsLayout'])) {
            $this->jsLayout = array_merge_recursive($jsLayoutDataProvider->getData(), $data['jsLayout']);
            unset($data['jsLayout']);
        } else {
            $this->jsLayout = $jsLayoutDataProvider->getData();
        }
        $this->serializer = $serializer;
        parent::__construct($context, $data);

    }

    /**
     * Get the configuration for the sidebar.
     *
     * @return array
     */
    public function getConfig()
    {
        return [
            'quoteUrl' => $this->getQuoteCartUrl(),
        ];
    }

    /**
     * Get the URL for the quote cart.
     *
     * @return string
     */
    public function getQuoteCartUrl()
    {
        return $this->_urlBuilder->getUrl('quote/quote');
    }

    /**
     * Get the serialized configuration for the sidebar.
     *
     * @return string
     */
    public function getSerializedConfig()
    {
        return $this->serializer->serialize($this->getConfig());
    }
}
