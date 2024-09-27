<?php

namespace QuoteMaster\QuoteStorm\Controller\Quote;

use Magento\Catalog\Controller\Product\View\ViewInterface;
use Magento\Catalog\Helper\Product\View;
use Magento\Framework\App\RequestInterface;
use Magento\Framework\App\Response\RedirectInterface;
use Magento\Framework\Controller\Result\Redirect;
use Magento\Framework\Controller\ResultInterface;
use Psr\Log\LoggerInterface;
use QuoteMaster\QuoteStorm\Model\Quote;
use Magento\Framework\Message\ManagerInterface;
use Magento\Framework\Controller\ResultFactory;
use Magento\Framework\Controller\Result\RedirectFactory;

class Configure extends \Magento\Framework\App\Action\Action implements ViewInterface
{
    /**
     * @var LoggerInterface
     */
    private $logger;

    /**
     * @var Quote
     */
    protected $cart;

    /**
     * @var ManagerInterface
     */
    protected $messageManager;

    /**
     * @var RequestInterface
     */
    protected $request;

    /**
     * @var ResultFactory
     */
    protected $resultFactory;

    /**
     * @var RedirectFactory
     */
    protected $resultRedirectFactory;

    /**
     * @var RedirectInterface
     */
    protected $redirect;

    /**
     * @var View
     */
    protected $productViewHelper;

    public function __construct(
        \Magento\Framework\App\Action\Context $context,
        \QuoteMaster\QuoteStorm\Model\Quote $cart,
        LoggerInterface $logger,
        \Magento\Framework\Message\ManagerInterface $messageManager,
        \Magento\Framework\App\RequestInterface $request,
        \Magento\Framework\Controller\ResultFactory $resultFactory,
        \Magento\Framework\Controller\Result\RedirectFactory $resultRedirectFactory,
        \Magento\Framework\App\Response\RedirectInterface $redirect,
        \Magento\Catalog\Helper\Product\View $productViewHelper
    ) {
        parent::__construct(
            $context
        );
        $this->cart = $cart;
        $this->logger = $logger;
        $this->messageManager = $messageManager;
        $this->request = $request;
        $this->resultFactory = $resultFactory;
        $this->resultRedirectFactory = $resultRedirectFactory;
        $this->redirect = $redirect;
        $this->productViewHelper = $productViewHelper;
    }

    /**
     * Configure action
     *
     * @return ResultInterface
     */
    public function execute()
    {
        $id = (int)$this->request->getParam('id');
        $productId = (int)$this->request->getParam('product_id');
        $quoteItem = null;
        if ($id) {
            $quoteItem = $this->cart->getQuote()->getItemById($id);
        }

        try {
            if (!$quoteItem || $productId != $quoteItem->getProduct()->getId()) {
                $this->messageManager->addErrorMessage(__("We can't find the quote item."));

                return $this->resultFactory->create(\Magento\Framework\Controller\ResultFactory::TYPE_REDIRECT)->setPath('quote/quote');
            }

            $params = new \Magento\Framework\DataObject();
            $params->setCategoryId(false);
            $params->setConfigureMode(true);
            $params->setBuyRequest($quoteItem->getBuyRequest());

            $resultPage = $this->resultFactory->create(\Magento\Framework\Controller\ResultFactory::TYPE_PAGE);
            $this->productViewHelper
                ->prepareAndRender(
                    $resultPage,
                    $quoteItem->getProduct()->getId(),
                    $this,
                    $params
                );
            return $resultPage;
        } catch (\Exception $e) {
            $this->messageManager->addErrorMessage(__('We cannot configure the product.'));
            $this->logger->critical($e);

            return $this->goBack();
        }
    }

    /**
     * Redirect to the referer URL
     *
     * @return Redirect
     */
    protected function goBack()
    {
        $resultRedirect = $this->resultRedirectFactory->create();
        $resultRedirect->setUrl($this->redirect->getRefererUrl());
        return $resultRedirect;
    }
}
