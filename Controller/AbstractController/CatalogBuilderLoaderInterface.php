<?php

namespace NowInStore\CatalogBuilder\Controller\AbstractController;

use Magento\Framework\App\RequestInterface;
use Magento\Framework\App\ResponseInterface;

interface CatalogBuilderLoaderInterface
{
    /**
     * @param RequestInterface $request
     * @param ResponseInterface $response
     * @return \NowInStore\CatalogBuilder\Model\CatalogBuilder
     */
    public function load(RequestInterface $request, ResponseInterface $response);
}
