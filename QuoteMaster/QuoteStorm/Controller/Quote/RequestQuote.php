<?php

namespace QuoteMaster\QuoteStorm\Controller\Quote;

use Exception;
use Magento\Customer\Model\Session;
use Magento\Framework\Controller\Result\Json;
use Magento\Framework\Controller\Result\Raw;
use Magento\Framework\Controller\Result\RawFactory;
use Magento\Framework\DataObject;
use Magento\Framework\App\RequestInterface;
use Magento\Framework\Data\Form\FormKey\Validator;
use Magento\Framework\Controller\Result\JsonFactory;
use Magento\Framework\Controller\Result\RedirectFactory;
use Magento\Framework\Exception\LocalizedException;
use QuoteMaster\QuoteStorm\Model\RequestQuote as QuoteRequest;
use QuoteMaster\QuoteStorm\Model\Session as QuoteSession;
use Magento\Quote\Model\ResourceModel\Quote;
use Psr\Log\LoggerInterface;

class RequestQuote implements \Magento\Framework\App\ActionInterface
{
    /**
     * @var Validator
     */
    protected $formKeyValidator;

    /**
     * @var RawFactory
     */
    protected $resultRawFactory;

    /**
     * @var JsonFactory
     */
    protected $resultJsonFactory;

    /**
     * @var RedirectFactory
     */
    protected $resultRedirectFactory;

    /**
     * @var RequestInterface
     */
    protected $request;

    /**
     * @var QuoteRequest
     */
    protected $quoteRequestModel;

    /**
     * @var Session
     */
    protected $customerSession;

    /**
     * @var QuoteSession
     */
    protected $quoteSession;

    /**
     * @var Quote
     */
    protected $quoteResourceModel;

    /**
     * @var LoggerInterface
     */
    protected $logger;

    public function __construct(
        Validator $formKeyValidator,
        RawFactory $resultRawFactory,
        JsonFactory $resultJsonFactory,
        RedirectFactory $resultRedirectFactory,
        RequestInterface $request,
        QuoteRequest $quoteRequestModel,
        Session $customerSession,
        QuoteSession $quoteSession,
        Quote $quoteResourceModel,
        LoggerInterface $logger
    ) {
        $this->formKeyValidator = $formKeyValidator;
        $this->resultRawFactory = $resultRawFactory;
        $this->resultJsonFactory = $resultJsonFactory;
        $this->resultRedirectFactory = $resultRedirectFactory;
        $this->request = $request;
        $this->quoteRequestModel = $quoteRequestModel;
        $this->customerSession = $customerSession;
        $this->quoteSession = $quoteSession;
        $this->quoteResourceModel = $quoteResourceModel;
        $this->logger = $logger;
    }

    /**
     * Request quote action
     *
     * @return Json
     */
    public function execute()
    {
        if (!$this->formKeyValidator->validate($this->request)) {
            return $this->resultRedirectFactory->create()->setPath('*/*/');
        }

        if ($this->expireAjax()) {
            return $this->ajaxRedirectResponse();
        }

        $result = new DataObject();
        try {
            $this->processAction();
            $result->setData('success', true);
            $result->setData('error', false);
        } catch (LocalizedException $e) {
            $result->setData('success', false);
            $result->setData('error', true);
            $result->setData('error_messages', $e->getMessage());
        } catch (Exception $e) {
            $result->setData('success', false);
            $result->setData('error', true);
            $result->setData(
                'error_messages',
                __('Something went wrong while processing your order. Please try again later.')
            );
        }
        return $this->resultJsonFactory->create()->setData($result->getData());
    }

    /**
     * Process the action of requesting a quote
     *
     * @throws LocalizedException|Exception
     */
    protected function processAction()
    {
        $file = fopen('observer-log.txt', 'a');
        fwrite($file, print_r(debug_backtrace(DEBUG_BACKTRACE_IGNORE_ARGS), true) . PHP_EOL);
        fclose($file);
        try {
            $quote = $this->quoteRequestModel->getQuote();
            $fieldData = json_decode($this->request->getContent(),true);
            if(isset($fieldData[\QuoteMaster\QuoteStorm\Model\Session::QUOTATION_FIELD_DATA]))
            {
                $customerNote = json_decode($fieldData[\QuoteMaster\QuoteStorm\Model\Session::QUOTATION_FIELD_DATA],true);
                if(isset($customerNote))
                {
                    $quote->setCustomerNote($customerNote['customer_note']);
                }
            }
            $this->saveCustomer($quote);
            $clearQuote = $this->request->getParam('clear_quote');
            if($clearQuote)
            {
                $quote->setIsActive(false);
                $this->quoteSession->clearQuote()->clearStorage();
            }
            $this->quoteResourceModel->save($quote);
        } catch (LocalizedException $e)
        {
            $this->logger->error($e->getMessage());
        }
    }

    /**
     * Check if the AJAX request has expired
     *
     * @return bool
     */
    protected function expireAjax()
    {
        $quote = $this->quoteRequestModel->getQuote();
        if (!$quote->hasItems() || $quote->getHasError()) {
            return true;
        }
        $action = $this->request->getActionName();
        if (
            !in_array($action, ['index', 'requestQuote'])
        ) {
            return true;
        }
        return false;
    }

    /**
     * Create a raw response for AJAX expiration
     *
     * @return Raw
     */
    protected function ajaxRedirectResponse()
    {
        $resultRaw = $this->resultRawFactory->create();
        $resultRaw->setStatusHeader(403, '1.1', 'Session Expired')
            ->setHeader('Login-Required', 'true');
        return $resultRaw;
    }

    /**
     * Save customer information to the quote
     *
     * @param \Magento\Quote\Model\Quote $quote
     * @throws LocalizedException
     */
    protected function saveCustomer(\Magento\Quote\Model\Quote $quote)
    {
        if ($this->customerSession->isLoggedIn())
        {
            $this->quoteRequestModel->saveCustomer();
            return;
        }
        $customerEmail = $this->request->getParam('customer_email');
        if (isset($customerEmail))
        {
            $quote = $this->setCustomerName($quote);
            $quote->setCustomerEmail($customerEmail);
            $quote->getShippingAddress()->setEmail($customerEmail);
            $quote->getBillingAddress()->setEmail($customerEmail);
            $this->quoteRequestModel->saveAsGuest();
        } else {
            throw new LocalizedException(
                __('Email address is mandatory for a quote.')
            );
        }
    }

    /**
     * Set customer name in the quote
     *
     * @param \Magento\Quote\Model\Quote $quote
     * @return \Magento\Quote\Model\Quote
     * @throws LocalizedException
     */
    protected function setCustomerName(\Magento\Quote\Model\Quote $quote)
    {
        $firstName = null;
        $lastName = null;
        $checkAsGuest = filter_var(
            $this->request->getParam('check_as_guest'),
            FILTER_VALIDATE_BOOLEAN
        );
        if ($checkAsGuest) {
            $guestData = json_decode($this->request->getContent(),true);
            if(isset($guestData[\QuoteMaster\QuoteStorm\Model\Session::QUOTATION_GUEST_FIELD_DATA]))
            {
                $customerName = json_decode($guestData[\QuoteMaster\QuoteStorm\Model\Session::QUOTATION_GUEST_FIELD_DATA],true);
                if(isset($customerName))
                {
                    $firstName = $customerName['first_name'];
                    $lastName = $customerName['last_name'];
                }
            }
        } else {
            $firstName = $quote->getShippingAddress()->getFirstname();
            $lastName = $quote->getShippingAddress()->getLastname();
            if (!$firstName && !$lastName) {
                $firstName = $quote->getBillingAddress()->getFirstname();
                $lastName = $quote->getBillingAddress()->getLastname();
            }
        }
        if ($firstName && $lastName) {
            $quote->setCustomerFirstname($firstName);
            $quote->setCustomerLastname($lastName);
        } else {
            throw new LocalizedException(
                __('First and Last name are mandatory for a quote.')
            );
        }
        return $quote;
    }
}
