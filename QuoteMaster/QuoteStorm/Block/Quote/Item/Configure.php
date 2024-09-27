<?php

namespace QuoteMaster\QuoteStorm\Block\Quote\Item;

class Configure extends \Magento\Framework\View\Element\Template
{
    /**
     * Prepare layout method
     *
     * This method sets the submit route data for the product info block
     *
     * @return $this
     */
    protected function _prepareLayout()
    {
        $block = $this->getLayout()->getBlock('product.info');

        if ($block) {
            $block->setSubmitRouteData(
                [
                    'route'  => 'quote/quote/updateItemOptions',
                    'params' => ['id' => $this->getRequest()->getParam('id')],
                ]
            );
        }

        return parent::_prepareLayout();
    }
}
