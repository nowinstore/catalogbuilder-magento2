<?php

namespace NowInStore\CatalogBuilder\Controller\Profile;

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
        \Magento\Directory\Model\CountryFactory $countryFactory,
        PageFactory $resultPageFactory
    ) {
        $this->_storeManager=$storeManager;
        $this->scopeConfig = $scopeConfig;
        $this->_httpRequest = $request;
        $this->response = $response;
        $this->_countryFactory = $countryFactory;
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
        $hostname = $this->_httpRequest->getHttpHost();
        $country = $this->_countryFactory->create()->loadByCode($this->scopeConfig->getValue(
                    'general/store_information/country_id',
                    \Magento\Store\Model\ScopeInterface::SCOPE_STORE
                    ));
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
                    ).'<br/>'.$country->getName();
        
        $jsonData = json_encode(array(
            "business_name" => $this->scopeConfig->getValue(
                            'general/store_information/name',
                            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
                        ),
            "name" => $this->scopeConfig->getValue(
                            'trans_email/ident_general/name',
                            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
                        ),
            "email" => $this->scopeConfig->getValue(
                            'trans_email/ident_general/email',
                            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
                        ),
            "baseUrl" =>  $this->_storeManager->getStore()->getBaseUrl(),
            "phone" => $this->scopeConfig->getValue(
                    'general/store_information/phone',
                    \Magento\Store\Model\ScopeInterface::SCOPE_STORE
                ),
            "address" => $address
        ));
        $this->response->setHeader('Content-type', 'application/json');
        $this->response->setBody($jsonData);
    }
}
