<?php

namespace QuoteMaster\QuoteStorm\Controller\Quote;

use Magento\Framework\App\RequestInterface;
use Magento\Framework\App\Response\RedirectInterface;
use Magento\Framework\Controller\Result\Redirect;
use Magento\Framework\Controller\Result\RedirectFactory;
use Magento\Framework\Controller\ResultFactory;
use Magento\Framework\Controller\ResultInterface;
use Magento\Framework\DataObject;
use Magento\Framework\Escaper;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Filter\LocalizedToNormalized;
use Magento\Framework\Locale\ResolverInterface;
use Psr\Log\LoggerInterface;
use QuoteMaster\QuoteStorm\Model\Quote;
use Magento\Framework\Message\ManagerInterface;

class UpdateItemOptions implements \Magento\Framework\App\ActionInterface
{
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
     * @var ResolverInterface
     */
    protected $resolver;

    /**
     * @var LoggerInterface
     */
    protected $logger;

    /**
     * @var Escaper
     */
    protected  $escaper;

    public function __construct(
        \QuoteMaster\QuoteStorm\Model\Quote $cart,
        \Magento\Framework\Message\ManagerInterface $messageManager,
        \Magento\Framework\App\RequestInterface $request,
        \Magento\Framework\Controller\ResultFactory $resultFactory,
        \Magento\Framework\Controller\Result\RedirectFactory $resultRedirectFactory,
        \Magento\Framework\App\Response\RedirectInterface $redirect,
        ResolverInterface $resolver,
        LoggerInterface $logger,
        \Magento\Framework\Escaper $escaper
    ) {
        $this->cart = $cart;
        $this->messageManager = $messageManager;
        $this->request = $request;
        $this->resultFactory = $resultFactory;
        $this->resultRedirectFactory = $resultRedirectFactory;
        $this->redirect = $redirect;
        $this->resolver = $resolver;
        $this->logger = $logger;
        $this->escaper = $escaper;
    }

    /**
     * Update Item action
     *
     * @return ResultInterface
     */
    public function execute()
    {
        $id = (int)$this->request->getParam('id');
        $params = $this->request->getParams();
        try {
            if (isset($params['qty'])) {
                $inputFilter = new LocalizedToNormalized(
                    [
                        'locale' => $this->resolver->getLocale(),
                    ]
                );
                $params['qty'] = $inputFilter->filter($params['qty']);
            }
            $quoteItem = $this->cart->getQuote()->getItemById($id);
            if (!$quoteItem) {
                throw new LocalizedException(
                    __("The quote item isn't found. Verify the item and try again.")
                );
            }

            $item = $this->cart->updateItem($id, new DataObject($params));
            if (is_string($item)) {
                throw new LocalizedException(__($item));
            }
            $this->cart->saveQuote();

            $message = __(
                '%1 was updated in your quote.',

                    $this->escaper->escapeHtml($item->getProduct()->getName())
            );
            $this->messageManager->addSuccessMessage($message);

        } catch (LocalizedException $e) {
            $this->logger->error('Exception: ' . $e->getMessage(), ['exception' => $e]);
            return $this->goBack();
        } catch (\Exception $e) {
            $this->messageManager->addExceptionMessage($e, __('We can\'t update the item right now.'));
            $this->logger->error('Exception: ' . $e->getMessage(), ['exception' => $e]);
            return $this->goBack();
        }

        return $this->resultRedirectFactory->create()->setPath('*/*');
    }

    /**
     * Redirect to the previous URL
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
