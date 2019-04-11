<?php

namespace NowInStore\CatalogBuilder\Controller\Products;

use Magento\Framework\View\Result\PageFactory;

class Count extends \Magento\Framework\App\Action\Action
{
	/**
     * @var PageFactory
     */
    protected $resultPageFactory;
	
	/**
     * @param \Magento\Framework\App\Action\Context $context
     * @param PageFactory $resultPageFactory
     */
    public function __construct(
        \Magento\Framework\App\Action\Context $context,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig,
        \Magento\Framework\App\RequestInterface $request,
        \Magento\Framework\App\Response\Http $response,
        \Magento\Catalog\Model\ResourceModel\Product\CollectionFactory $productCollectionFactory,
        \Magento\Customer\Model\ResourceModel\Group\Collection $customerGroup,
        PageFactory $resultPageFactory
    ) {
        $this->_storeManager=$storeManager;
        $this->scopeConfig = $scopeConfig;
        $this->_httpRequest = $request;
        $this->response = $response;
        $this->_productCollectionFactory = $productCollectionFactory;
        $this->_customerGroup = $customerGroup;
        $this->resultPageFactory = $resultPageFactory;
        parent::__construct($context);
    }

      
    /**
     * Default CatalogBuilder Index page
     *
     * @return void
     */
    public function execute()
    {
        $product_collection = $this->_productCollectionFactory->create()
            ->addExpressionAttributeToSelect('lower_name', 'LOWER({{name}})', array('name'))
            ->addFieldToFilter('visibility', \Magento\Catalog\Model\Product\Visibility::VISIBILITY_BOTH)
            ->addAttributeToSelect(array('id', 'name', 'sku', 'price', 'image'));

        
        if (!empty ($_GET['query']) && isset($_GET['query'])) {
            $query = $_GET['query'];
            $product_collection = $product_collection->addAttributeToFilter('lower_name', array('like' => '%' . strtolower($query) . '%'));
        }

        
        if (isset($_GET['category_id']) && !empty ($_GET['category_id'])) {
            $category_id = $_GET['category_id'];
            $product_collection = $product_collection
                ->joinField('category_id', 'catalog_category_product', 'category_id', 'product_id=entity_id', null, 'left')
                ->addAttributeToFilter('category_id', array('in' => $category_id));
        }
        $jsonData = json_encode(array("count" => $product_collection->count()));
        $this->response->setHeader('Content-type', 'application/json');
        $this->response->setBody($jsonData);
    }
}
