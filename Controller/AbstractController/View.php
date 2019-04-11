<?php

namespace NowInStore\CatalogBuilder\Controller\AbstractController;

use Magento\Framework\App\Action;
use Magento\Framework\View\Result\PageFactory;

abstract class View extends Action\Action
{
    /**
     * @var \NowInStore\CatalogBuilder\Controller\AbstractController\CatalogBuilderLoaderInterface
     */
    protected $catalogbuilderLoader;
	
	/**
     * @var PageFactory
     */
    protected $resultPageFactory;

    /**
     * @param Action\Context $context
     * @param OrderLoaderInterface $orderLoader
	 * @param PageFactory $resultPageFactory
     */
    public function __construct(Action\Context $context, CatalogBuilderLoaderInterface $catalogbuilderLoader, PageFactory $resultPageFactory)
    {
        $this->catalogbuilderLoader = $catalogbuilderLoader;
		$this->resultPageFactory = $resultPageFactory;
        parent::__construct($context);
    }

    /**
     * CatalogBuilder view page
     *
     * @return void
     */
    public function execute()
    {
        if (!$this->catalogbuilderLoader->load($this->_request, $this->_response)) {
            return;
        }

        /** @var \Magento\Framework\View\Result\Page $resultPage */
        $resultPage = $this->resultPageFactory->create();
		return $resultPage;
    }
}
