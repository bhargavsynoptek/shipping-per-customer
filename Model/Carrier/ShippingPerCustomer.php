<?php

namespace Bhargav\ShippingPerCustomer\Model\Carrier;

use Magento\Customer\Model\Session;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Quote\Model\Quote\Address\RateRequest;
use Magento\Quote\Model\Quote\Address\RateResult\ErrorFactory;
use Magento\Quote\Model\Quote\Address\RateResult\MethodFactory;
use Magento\Shipping\Model\Carrier\AbstractCarrier;
use Magento\Shipping\Model\Carrier\CarrierInterface;
use Magento\Shipping\Model\Rate\ResultFactory;
use Psr\Log\LoggerInterface;
use \Bhargav\ShippingPerCustomer\Helper\Data as Helper;
use Magento\Checkout\Model\Cart;
use Magento\Framework\App\State;
use Magento\Backend\Model\Session\Quote as AdminCheckoutSession;

class ShippingPerCustomer extends AbstractCarrier implements CarrierInterface
{
    protected $_code = 'shippingpercustomer';

    protected $_isFixed = true;

    private $rateResultFactory;

    private $rateMethodFactory;

    private $helper;

    private $customerSession;
    protected $cart;
    /**
     * @var State
     */
    private $state;
    /**
     * @var AdminCheckoutSession
     */
    private $adminCheckoutSession;

    public function __construct(
        ScopeConfigInterface $scopeConfig,
        ErrorFactory $rateErrorFactory,
        LoggerInterface $logger,
        ResultFactory $rateResultFactory,
        MethodFactory $rateMethodFactory,
        Helper $helper,
        Session $customerSession,
        Cart $cart,
        State $state,
        AdminCheckoutSession $adminCheckoutSession,
        array $data = []
    ) {
        parent::__construct($scopeConfig, $rateErrorFactory, $logger, $data);

        $this->rateResultFactory = $rateResultFactory;
        $this->rateMethodFactory = $rateMethodFactory;
        $this->helper = $helper;
        $this->customerSession = $customerSession;
        $this->cart = $cart;
        $this->state = $state;
        $this->adminCheckoutSession = $adminCheckoutSession;
    }

    public function collectRates(RateRequest $request)
    {
        if (!$this->getConfigFlag('active')) {
            return false;
        }

        $result = $this->rateResultFactory->create();

        $method = $this->rateMethodFactory->create();

        $method->setCarrier($this->_code);
        $method->setCarrierTitle($this->getConfigData('title'));

        $method->setMethod($this->_code);
        $method->setMethodTitle($this->getConfigData('name'));

        if ($request->getFreeShipping()) {
            $method->setPrice(0);
            $method->setCost(0);

            $result->append($method);
            return $result;
        }

        $customerGroups = $this->getConfigData('shipping_cost');
        $currentCustomerId=$this->customerSession->getCustomerGroupId();
        $customerGroupsArray=$this->helper->getSerializedConfigValue($customerGroups);
        $customerGroupMatch=false;
        foreach ($customerGroupsArray as $customerGroup) {
            if ($customerGroup['customer_group']==$currentCustomerId) {
                $cost=(float)$customerGroup['cost'];
                $customerGroupMatch=true;
                break;
            }
        }
        if (!$customerGroupMatch) {
            $cost=$this->getConfigData('default_shipping_cost');
        }

        $shippingPerRate = $this->getConfigData('shippingrate');
        if ($shippingPerRate == 1){
            $items = $this->cart->getQuote()->getAllVisibleItems();
            if($this->state->getAreaCode() == \Magento\Framework\App\Area::AREA_ADMINHTML) {
                $items = $this->adminCheckoutSession->getQuote()->getAllVisibleItems();
            }

            $totalCost = 0;
            foreach($items as $item) {
                $totalCost += $cost * $item->getQty();
            }
            $cost = $totalCost;
        }

        if ($request->getFreeShipping()) {
            $cost=0;
        }

        $method->setPrice($cost);
        $method->setCost($cost);

        $result->append($method);
        return $result;
    }

    public function getAllowedMethods()
    {
        return [$this->_code => $this->getConfigData('name')];
    }
}
