<?php

/**
 * CatalogBuilder data helper
 */
namespace NowInStore\CatalogBuilder\Helper;

use Magento\Framework\App\Filesystem\DirectoryList;

class Data extends \Magento\Framework\App\Helper\AbstractHelper
{
    
    /**
     * Store manager
     *
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    protected $_storeManager;
    
    /**
     * @param \Magento\Framework\App\Helper\Context $context
     */
    public function __construct(
        \Magento\Framework\App\Helper\Context $context,
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig,
        \Magento\Store\Model\StoreManagerInterface $storeManager
    ) {
        $this->_scopeConfig = $scopeConfig;
        $this->_storeManager = $storeManager;
        parent::__construct($context);
    }
    
    /**
     * Remove CatalogBuilder debug
     *
     * @param string 
     */
    public function debug($debug)
    {
        if ($debug) {
            ini_set('display_errors', 1);
            ini_set('display_startup_errors', 1);
            if ($debug == 'all')
                error_reporting(E_ALL);
            if ($debug == 'info')
                error_reporting(E_ERROR | E_WARNING | E_PARSE);
            if ($debug == 'warning')
                error_reporting(E_ERROR | E_WARNING);
            if ($debug == 'error')
                error_reporting(E_ERROR);
        }
    }
    
}
