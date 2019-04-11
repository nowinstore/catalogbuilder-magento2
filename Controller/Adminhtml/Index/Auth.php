<?php

namespace NowInStore\CatalogBuilder\Controller\Adminhtml\Index;

use Magento\Backend\App\Action\Context;
use Magento\Framework\View\Result\PageFactory;

class Auth extends \Magento\Backend\App\Action
{
	/**
     * @var PageFactory
     */
    protected $resultPageFactory;

    /**
     * @param Context $context
     * @param PageFactory $resultPageFactory
     */
    public function __construct(
        Context $context,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig,
        \Magento\Framework\App\RequestInterface $request,
        \Magento\Framework\App\Response\Http $response,
        \Magento\Directory\Model\CountryFactory $countryFactory,
        PageFactory $resultPageFactory
    ) {
        parent::__construct($context);
        $this->_storeManager=$storeManager;
        $this->scopeConfig = $scopeConfig;
        $this->_httpRequest = $request;
        $this->response = $response;
        $this->_countryFactory = $countryFactory;
        $this->resultPageFactory = $resultPageFactory;
    }
	
    /**
     * Check the permission to run it
     *
     * @return bool
     */
    protected function _isAllowed()
    {
        return $this->_authorization->isAllowed('NowInStore_CatalogBuilder::catalogbuilder_manage');
    }

    /**
     * CatalogBuilder List action
     *
     * @return void
     */
    public function execute()
    {
        $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
        $productMetadata = $objectManager->get('Magento\Framework\App\ProductMetadataInterface');
        $version = $productMetadata->getVersion(); 
        
        $baseUrl = urlencode($this->_storeManager->getStore()->getBaseUrl());
        
        $hostname = urlencode($this->_httpRequest->getHttpHost());
        $countryId = $this->scopeConfig->getValue(
            'general/store_information/country_id',
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        );
        $country = strlen($countryId) < 2 ? '' : $this->_countryFactory->create()->loadByCode($countryId)->getName();
        $address = $this->scopeConfig->getValue(
                    'general/store_information/street_line1',
                    \Magento\Store\Model\ScopeInterface::SCOPE_STORE
                    ).'<br/>'.$this->scopeConfig->getValue(
                    'general/store_information/street_line2',
                    \Magento\Store\Model\ScopeInterface::SCOPE_STORE
                    ).'<br/>'.$this->scopeConfig->getValue(
                    'general/store_information/city',
                    \Magento\Store\Model\ScopeInterface::SCOPE_STORE
                    ).'<br/>'.$this->scopeConfig->getValue(
                    'general/store_information/postcode',
                    \Magento\Store\Model\ScopeInterface::SCOPE_STORE
                    ).'<br/>'.$this->scopeConfig->getValue(
                    'general/store_information/region_id',
                    \Magento\Store\Model\ScopeInterface::SCOPE_STORE
                    ).'<br/>'.$country;
        $address = urlencode($address);
       
        $email = urlencode($this->scopeConfig->getValue(
                            'trans_email/ident_general/email',
                            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
                        ));
        
        $businessName = urlencode($this->scopeConfig->getValue(
                            'general/store_information/name',
                            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
                        ));
        
        $name = urlencode($this->scopeConfig->getValue(
                            'trans_email/ident_general/name',
                            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
                        ));
        
        $phone = urlencode($this->scopeConfig->getValue(
                    'general/store_information/phone',
                    \Magento\Store\Model\ScopeInterface::SCOPE_STORE
                ));
        $version = urlencode($version);
        $destinationUrl = "https://www.nowinstore.com/auth/magento/callback?baseUrl=$baseUrl&hostname=$hostname&address=$address&email=$email&businessName=$businessName&name=$name&phone=$phone&version=$version";
        $this->response->setRedirect($destinationUrl);
    }
}
