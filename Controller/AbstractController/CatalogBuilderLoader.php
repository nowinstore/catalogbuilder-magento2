<?php

namespace NowInStore\CatalogBuilder\Controller\AbstractController;

use Magento\Framework\App\RequestInterface;
use Magento\Framework\App\ResponseInterface;
use Magento\Framework\Registry;

class CatalogBuilderLoader implements CatalogBuilderLoaderInterface
{
    /**
     * @var \NowInStore\CatalogBuilder\Model\CatalogBuilderFactory
     */
    protected $catalogbuilderFactory;

    /**
     * @var \Magento\Framework\Registry
     */
    protected $registry;

    /**
     * @var \Magento\Framework\UrlInterface
     */
    protected $url;

    /**
     * @param \NowInStore\CatalogBuilder\Model\CatalogBuilderFactory $catalogbuilderFactory
     * @param OrderViewAuthorizationInterface $orderAuthorization
     * @param Registry $registry
     * @param \Magento\Framework\UrlInterface $url
     */
    public function __construct(
        \NowInStore\CatalogBuilder\Model\CatalogBuilderFactory $catalogbuilderFactory,
        Registry $registry,
        \Magento\Framework\UrlInterface $url
    ) {
        $this->catalogbuilderFactory = $catalogbuilderFactory;
        $this->registry = $registry;
        $this->url = $url;
    }

    /**
     * @param RequestInterface $request
     * @param ResponseInterface $response
     * @return bool
     */
    public function load(RequestInterface $request, ResponseInterface $response)
    {
        $id = (int)$request->getParam('id');
        if (!$id) {
            $request->initForward();
            $request->setActionName('noroute');
            $request->setDispatched(false);
            return false;
        }

        $catalogbuilder = $this->catalogbuilderFactory->create()->load($id);
        $this->registry->register('current_catalogbuilder', $catalogbuilder);
        return true;
    }
}
