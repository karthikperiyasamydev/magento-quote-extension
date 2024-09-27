<?php

namespace QuoteMaster\QuoteStorm\Controller\Quote;

use Magento\Framework\App\RequestInterface;
use Magento\Framework\Controller\Result\Redirect;
use Magento\Framework\Controller\Result\RedirectFactory;
use Magento\Framework\Message\ManagerInterface;
use Psr\Log\LoggerInterface;
use QuoteMaster\QuoteStorm\Model\Quote;

class Delete implements \Magento\Framework\App\ActionInterface
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
     * @var RedirectFactory
     */
    protected $resultRedirectFactory;

    public function __construct(
        \QuoteMaster\QuoteStorm\Model\Quote $cart,
        LoggerInterface $logger,
        \Magento\Framework\Message\ManagerInterface $messageManager,
        \Magento\Framework\App\RequestInterface $request,
        \Magento\Framework\Controller\ResultFactory $resultFactory,
        \Magento\Framework\Controller\Result\RedirectFactory $resultRedirectFactory,
        \Magento\Framework\App\Response\RedirectInterface $redirect,
        \Magento\Catalog\Helper\Product\View $productViewHelper
    ) {
        $this->cart = $cart;
        $this->logger = $logger;
        $this->messageManager = $messageManager;
        $this->request = $request;
        $this->resultRedirectFactory = $resultRedirectFactory;
    }

    /**
     * Delete action
     *
     * @return Redirect
     */
    public function execute()
    {
        $id = (int)$this->request->getParam('id');
        if ($id) {
            try {
                $this->cart->removeItem($id);
                $this->cart->saveQuote();
            } catch (\Exception $e) {
                $this->messageManager->addErrorMessage(__('We can\'t remove the item.'));
                $this->logger->critical($e);
            }
        }
        return $this->resultRedirectFactory->create()->setPath('*/*');
    }
}
