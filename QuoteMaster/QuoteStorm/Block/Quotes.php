<?php

namespace QuoteMaster\QuoteStorm\Block;

use Magento\Framework\View\Element\AbstractBlock;
use Magento\Framework\View\Element\Template\Context;
use Magento\Quote\Model\Quote\Item;
use QuoteMaster\QuoteStorm\Model\Session;
use Magento\Quote\Model\Quote;
use RuntimeException;

class Quotes extends \Magento\Framework\View\Element\Template
{

    const  DEFAULT_TYPE = 'default';

    /**
     * @var Quote
     */
    protected $_quote;

    /**
     * @var Session
     */
    protected $quotationSession;

    public function __construct(
        Context $context,
        Session $quotationSession,
        array $data = []
    ) {
        $this->quotationSession = $quotationSession;
        parent::__construct($context, $data);
    }

    /**
     * Retrieve the current quote from the session
     *
     * @return Quote|null
     */
    public function getQuote()
    {
        if (!$this->_quote) {
            $this->_quote = $this->quotationSession->getQuote();
        }
        return $this->_quote;
    }

    /**
     * Get the count of items in the quote
     *
     * @return int
     */
    public function getItemsCount()
    {
        return $this->getQuote()->getItemsCount();
    }

    /**
     * Get all visible items in the quote
     *
     * @return Item[]
     */
    public function getItems()
    {
        return $this->getQuote()->getAllVisibleItems();
    }

    /**
     * Generate HTML for a specific quote item
     *
     * @param Item $item
     * @return string
     */
    public function getItemHtml(Item $item)
    {
        $renderer = $this->getItemRenderer($item->getProductType())->setItem($item);
        return $renderer->toHtml();
    }

    /**
     * Get the renderer for a specific item type
     *
     * @param string|null $type
     * @return mixed
     * @throws RuntimeException
     */
    public function getItemRenderer($type = null)
    {
        if ($type === null) {
            $type = self::DEFAULT_TYPE;
        }
        $rendererList = $this->_getRendererList();
        if (!$rendererList) {
            throw new RuntimeException('Renderer list for block "' . $this->getNameInLayout() . '" is not defined');
        }
        return $rendererList->getRenderer($type, self::DEFAULT_TYPE);
    }

    /**
     * Retrieve the renderer list child block
     *
     * @return bool|AbstractBlock
     */
    protected function _getRendererList()
    {
        return  $this->getChildBlock('renderer.list');
    }

}
