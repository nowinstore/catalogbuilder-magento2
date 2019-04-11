<?php

namespace NowInStore\CatalogBuilder\Controller\Products;

use Magento\Framework\View\Result\PageFactory;

class Index extends \Magento\Framework\App\Action\Action
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

    private function count() {

        $product_collection = $this->_productCollectionFactory->create()
            ->addExpressionAttributeToSelect('lower_name', 'LOWER({{name}})', array('name'))
            ->addFieldToFilter('visibility', \Magento\Catalog\Model\Product\Visibility::VISIBILITY_BOTH)
            ->addAttributeToSelect(array('id', 'name', 'sku', 'price', 'group_price', 'image', 'description', 'short_description'));

        
        if (isset($_GET['keywords']) && !empty ($_GET['keywords'])) {
            $keywords = $_GET['keywords'];
            $product_collection = $product_collection->addAttributeToFilter('lower_name', array('like' => '%' . strtolower($keywords) . '%'));
        }

        
        if (isset($_GET['category_id']) &&  !empty ($_GET['category_id'])) {
            $category_id = $_GET['category_id'];
            $product_collection = $product_collection
                ->joinField('category_id', 'catalog_category_product', 'category_id', 'product_id=entity_id', null, 'left')
                ->addAttributeToFilter('category_id', array('in' => $category_id));
        }

        return $product_collection->getSize();
    }
    
    /**
     * Default CatalogBuilder Index page
     *
     * @return void
     */
    public function execute()
    {
        
        if (!isset($_GET['page']) && empty($_GET['page'])) {
            $page = 1;
        }else{
            $page = $_GET['page'];
        }

        
        if (!isset($_GET['limit']) && empty($_GET['limit'])) {
            $limit = 50;
        }else{
            $limit = $_GET['limit'];
        }
        
        $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
        $products = array();
        $productsCount = $this->count();
        if ($productsCount > ($page-1)*$limit) {
            $product_collection = $this->_productCollectionFactory->create()
                ->addExpressionAttributeToSelect('lower_name', 'LOWER({{name}})', array('name'))
                ->addFieldToFilter('visibility', \Magento\Catalog\Model\Product\Visibility::VISIBILITY_BOTH)
                ->setPageSize($limit)
                ->setCurPage($page)
                ->addAttributeToSelect(array('id', 'name', 'sku', 'price', 'group_price', 'image', 'description', 'short_description'));

            
            if (isset($_GET['keywords'])  && !empty ($_GET['keywords'])) {
                $keywords = $_GET['keywords'];
                $product_collection = $product_collection->addAttributeToFilter('lower_name', array('like' => '%' . strtolower($keywords) . '%'));
               
            }

           
            if (isset($_GET['category_id']) && !empty ($_GET['category_id'])) {
                 $category_id = $_GET['category_id'];
                $product_collection = $product_collection
                    ->joinField('category_id', 'catalog_category_product', 'category_id', 'product_id=entity_id', null, 'left')
                    ->addAttributeToFilter('category_id', array('in' => $category_id));
            }
            $currency = $this->_storeManager->getStore()->getCurrentCurrencyCode();
            $group_collection = $this->_customerGroup;
            $wholesaleGroup = null;
            foreach ($group_collection as $group) {
                if ($group->getCode() === 'Wholesale') {
                    $wholesaleGroup = $group;
                }
            }
             $_imagehelper = $objectManager->get('Magento\Catalog\Helper\Image');
            foreach ($product_collection as $product) {
                
                $product = $objectManager->create('Magento\Catalog\Model\Product')->load($product->getId());
                $attributeOptions = array();
                if ($product->getTypeId() == \Magento\ConfigurableProduct\Model\Product\Type\Configurable::TYPE_CODE) {
                    $productAttributeOptions = $product->getTypeInstance()->getConfigurableAttributesAsArray($product);
                    foreach ($productAttributeOptions as $productAttribute) {
                        foreach ($productAttribute['values'] as $attribute) {
                            $label = $productAttribute['label'];
                            $valueIndex = $attribute['value_index'];
                            $attributeOptions[$label][$valueIndex] = $attribute['store_label'];
                        }
                    }
                }
                
                $mainImage =  $_imagehelper->init($product, 'product_page_image_large')
                                        ->setImageFile($product->getFile())
                                        ->getUrl();
               // $product->load('media_gallery');
                $mediaGallery =  $product->getMediaGalleryImages();
                $images = array();
                foreach ($mediaGallery as $image) {
                    
                    array_push($images, $image->getUrl());
                }
               
                if (is_null($product->getImage()) || $product->getImage() == 'no_selection' && count($images) > 0) {
                   
                    $mainImage =  $_imagehelper->init($product, 'product_page_image_large')
                                        ->setImageFile($product->getFile())
                                        ->getUrl();
                }
                $price = floatval($product->getPrice());
                $wholesalePrice = 0;
                if (!is_null($wholesaleGroup)) {
                    $product->setCustomerGroupId($wholesaleGroup->getId());
                }
               
                $groupPrices = $product->getGroupPrice();
                if (is_null($groupPrices)) {
                    $attribute = $product->getResource()->getAttribute('group_price');
                    if ($attribute) {
                        $attribute->getBackend()->afterLoad($product);
                        $groupPrices = $product->getData('group_price');
                    }
                }
                 
                if (!is_null($groupPrices) || is_array($groupPrices)) {
                    $wholesalePrice = $groupPrices;
                }
               
               
                
                
                array_push($products, array(
                    "id" => $product->getId(),
                    "title" => $product->getName(),
                    "sku" => $product->getSku(),
                    "price" => $price,
                    "wholesale_price" => floatval($wholesalePrice),
                    "main_image" => $mainImage,
                    "images" => $images,
                    "description" => $product->getDescription(),
                    "short_description" => $product->getShortDescription(),
                    "thumbnail_image" => $_imagehelper->init($product, 'product_page_image_small')
                                        ->setImageFile($product->getFile())
                                        ->getUrl(),
                    "iso_currency_code" => $currency,
                    "url" => $product->getProductUrl(),
                    "variations" => $attributeOptions
                ));
            }
           
        }
        $jsonData = json_encode($products);
        $this->response->setHeader('Content-type', 'application/json');
        $this->response->setBody($jsonData);
    }
}
