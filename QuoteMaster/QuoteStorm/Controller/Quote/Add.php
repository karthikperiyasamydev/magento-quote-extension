<?php

namespace QuoteMaster\QuoteStorm\Controller\Quote;

use Magento\Catalog\Api\Data\ProductInterface;
use Magento\Catalog\Api\ProductRepositoryInterface;
use Magento\Framework\App\RequestInterface;
use Magento\Framework\Controller\Result\Redirect;
use Magento\Framework\Controller\ResultFactory;
use Magento\Framework\Data\Form\FormKey\Validator;
use Magento\Framework\Serialize\Serializer\Json;
use QuoteMaster\QuoteStorm\Model\Quote;
use Magento\Framework\App\ResponseInterface;
use Magento\Framework\Message\ManagerInterface;
use Magento\Framework\UrlInterface;
use Psr\Log\LoggerInterface;
use Magento\Framework\Controller\Result\RedirectFactory;
use QuoteMaster\QuoteStorm\Model\Session;
use Magento\Framework\Escaper;

class Add implements \Magento\Framework\App\ActionInterface
{
    /**
     * @var RequestInterface
     */
    protected $request;

    /**
     * @var Quote
     */
    protected $cart;

    /**
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    protected $_storeManager;

    /**
     * @var ProductRepositoryInterface
     */
    protected $productRepository;

    /**
     * @var ResultFactory
     */
    protected $resultFactory;

    /**
     * @var \Magento\Framework\App\Response\RedirectInterface
     */
    protected $_redirect;

    /**
     * @var ResponseInterface
     */
    protected $response;

    /**
     * @var UrlInterface
     */
    protected $url;

    /**
     * @var ManagerInterface
     */
    protected $messageManager;

    /**
     * @var Json
     */
    protected $serializer;

    /**
     * @var LoggerInterface
     */
    protected $logger;

    /**
     * @var Validator
     */
    protected  $formKeyValidator;

    /**
     * @var RedirectFactory
     */
    protected $resultRedirectFactory;

    /**
     * @var Session
     */
    protected  $quoteSession;

    /**
     * @var Escaper
     */
    protected  $escaper;

    public function __construct(
        \Magento\Framework\App\RequestInterface $request,
        \QuoteMaster\QuoteStorm\Model\Quote $cart,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        ProductRepositoryInterface $productRepository,
        ResultFactory $resultFactory,
        \Magento\Framework\App\Response\RedirectInterface $redirect,
        \Magento\Framework\App\ResponseInterface $response,
        \Magento\Framework\UrlInterface $url,
        \Magento\Framework\Message\ManagerInterface $messageManager,
        \Magento\Framework\Serialize\Serializer\Json $serializer,
        LoggerInterface $logger,
        Validator $formKeyValidator,
        RedirectFactory $resultRedirectFactory,
        Session $quoteSession,
        Escaper $escaper
    ) {
        $this->request = $request;
        $this->cart = $cart;
        $this->_storeManager = $storeManager;
        $this->productRepository = $productRepository;
        $this->resultFactory = $resultFactory;
        $this->_redirect = $redirect;
        $this->response = $response;
        $this->url = $url;
        $this->messageManager = $messageManager;
        $this->serializer = $serializer;
        $this->logger = $logger;
        $this->formKeyValidator = $formKeyValidator;
        $this->resultRedirectFactory = $resultRedirectFactory;
        $this->quoteSession = $quoteSession;
        $this->escaper = $escaper;
    }

    /**
     * Add action.
     *
     * @return ResponseInterface
     */
    public function execute()
    {
        if (!$this->formKeyValidator->validate($this->request)) {
            $this->messageManager->addErrorMessage(
                __('Your session has expired')
            );
            return $this->resultRedirectFactory->create()->setPath('*/*/');
        }

        $params = $this->request->getParams();

        try {
            if(!isset($params['qty'])){
                $params['qty'] = 1;
            }

            $product = $this->_initProduct();

            $this->cart->addProduct($product, $params);
            $this->cart->getQuote()->setIsQuote(true);
            $this->cart->save();

            $this->messageManager->addComplexSuccessMessage(
                'addQuoteSuccessMessage',
                [
                    'product_name' => $product->getName(),
                    'quote_url' => $this->getQuoteUrl(),
                ]
            );

            $result['message'] = 'Product added to quote successfully!';
            $this->response->representJson($this->serializer->serialize($result));

        } catch (\Magento\Framework\Exception\LocalizedException $e) {
            if ($this->quoteSession->getUseNotice(true)) {
                $this->messageManager->addNoticeMessage(
                    $this->escaper->escapeHtml($e->getMessage())
                );
            } else {
                $messages = array_unique(explode("\n", $e->getMessage()));
                foreach ($messages as $message) {
                    $this->messageManager->addErrorMessage(
                        $this->escaper->escapeHtml($message)
                    );
                }
            }

            $url = $this->quoteSession->getRedirectUrl(true);
            if (!$url) {
                $url = $this->_redirect->getRedirectUrl($this->getQuoteUrl());
            }
            $this->logger->error('LocalizedException: ' . $e->getMessage(), ['exception' => $e]);

            return $this->goBack($url);
        } catch (\Exception $e) {
            $this->messageManager->addExceptionMessage(
                $e,
                __('We can\'t add this item to your shopping cart right now.')
            );
            $this->logger->error('Exception: ' . $e->getMessage(), ['exception' => $e]);

            return $this->goBack();
        }

        return $this->response;
    }

    /**
     * Initialize the product based on the product ID from the request.
     *
     * @return false|ProductInterface
     */
    protected function _initProduct()
    {
        $productId = (int)$this->request->getParam('product');
        if ($productId) {
            $storeId = $this->_storeManager->getStore()->getId();
            try {
                return $this->productRepository->getById($productId, false, $storeId);
            } catch (\Magento\Framework\Exception\NoSuchEntityException $e) {
                return false;
            }
        }
        return false;
    }

    /**
     * Get the quote URL.
     *
     * @return string
     */
    private function getQuoteUrl()
    {
        return $this->url->getUrl('quote/quote',['_secure' => true]);
    }

    /**
     * Go back to the previous URL or a specified one.
     *
     * @param string|null $backUrl
     * @param null $product
     * @return ResponseInterface
     */
    protected function goBack($backUrl = null, $product = null)
    {
        if (!$this->request->isAjax()) {
            $this->_goBack($backUrl);
        }

        $result = [];

        if ($backUrl || $backUrl = $this->_redirect->getRefererUrl()) {
            $result['backUrl'] = $backUrl;
        } else {
            if ($product && !$product->getIsSalable()) {
                $result['product'] = [
                    'statusText' => __('Out of stock')
                ];
            }
        }

        $this->response->representJson($this->serializer->serialize($result));

        return $this->response;
    }

    /**
     * Redirect to the previous URL.
     *
     * @param string|null $backUrl
     * @return Redirect
     */
    protected function _goBack($backUrl = null)
    {
        $resultRedirect = $this->resultRedirectFactory->create();

        if ($backUrl || $backUrl = $this->_redirect->getRefererUrl()) {
            $resultRedirect->setUrl($backUrl);
        }

        return $resultRedirect;
    }
}
