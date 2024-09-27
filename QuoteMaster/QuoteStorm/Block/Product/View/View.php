<?php

namespace QuoteMaster\QuoteStorm\Block\Product\View;

use Magento\Backend\Block\Template\Context;
use Magento\Catalog\Model\Product;
use Magento\Framework\Registry;

class View extends \Magento\Framework\View\Element\Template
{
    /**
     * @var Registry
     */
    protected $registry;

    public function __construct(
        Context  $context,
        Registry $registry,
        array    $data = []
    )
    {
        $this->registry = $registry;
        parent::__construct($context, $data);
    }

    /**
     * Prepare layout method
     *
     * @return $this
     */
    public function _prepareLayout()
    {
        return parent::_prepareLayout();
    }

    /**
     * Retrieve the current product from the registry
     *
     * @return Product
     */
    public function getProduct()
    {
        return $this->registry->registry('current_product');
    }

    /**
     * Generate URL for adding the current product to the quote
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
