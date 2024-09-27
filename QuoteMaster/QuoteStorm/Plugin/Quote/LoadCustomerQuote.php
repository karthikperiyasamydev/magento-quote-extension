<?php

namespace QuoteMaster\QuoteStorm\Plugin\Quote;
use Magento\Framework\DB\Select;
use Magento\Quote\Model\Quote;
use QuoteMaster\QuoteStorm\Model\Session;

class LoadCustomerQuote
{
    /**
     * @var Session
     */
    protected $quoteSession;

    public function __construct(
        Session $quoteSession,
    ) {
        $this->quoteSession = $quoteSession;
    }

    /**
     * This method modifies the behavior of loading a quote by customer ID. It checks if the current
     * session is for a quotation and adjusts the load query accordingly.
     *
     * @param mixed $subject
     * @param callable $proceed
     * @param Quote $quote
     * @param int $customerId
     * @return Quote
     */
    public function aroundLoadByCustomerId(
        $subject,
        $proceed,
        $quote,
        $customerId
    ) {
        $quotationQuote = $this->quoteSession->getQuotationQuote();
        if ((isset($quotationQuote) && $quotationQuote > 0 )) {
            $quotationQuote = 1;
        } else {
            $quotationQuote = 0;
        }

        $connection = $subject->getConnection();
        $select = $this->_getLoadSelect(
            'customer_id',
            $customerId,
            $quote,
            $subject
        )->where(
            'is_active = ?',
            1
        )->where(
            'is_quote = ?',
            $quotationQuote
        )->order(
            'updated_at ' . Select::SQL_DESC
        )->limit(
            1
        );

        $data = $connection->fetchRow($select);
        if ($data) {
            $quote->setData($data);
            $quote->setOrigData();
        }
        $subject->afterLoad($quote);

        return $quote;
    }

    /**
     * Prepare the select statement for loading the quote
     *
     * @param string $field
     * @param mixed $value
     * @param Quote $object
     * @param mixed $subject
     * @return Select
     */
    protected function _getLoadSelect(
        $field,
        $value,
        $object,
        $subject
    ) {
        $field = $subject->getConnection()->quoteIdentifier(sprintf('%s.%s', $subject->getMainTable(), $field));
        $select = $subject->getConnection()->select()->from($subject->getMainTable())->where($field . '=?', $value);
        $storeIds = $object->getSharedStoreIds();
        if ($storeIds) {
            $select->where('store_id IN (?)', $storeIds);
        } else {
            $select->where('store_id < ?', 0);
        }
        return $select;
    }
}
