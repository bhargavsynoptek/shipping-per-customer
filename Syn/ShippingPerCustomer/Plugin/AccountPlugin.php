<?php

namespace Syn\ShippingPerCustomer\Plugin;

use Magento\Sales\Block\Adminhtml\Order\Create\Form\Account;
use Magento\Customer\Model\Session;

class AccountPlugin
{
    private $customerSession;
    private $quoteRepository;

    public function __construct(Session $customerSession, \Magento\Quote\Api\CartRepositoryInterface $quoteRepository)
    {
        $this->customerSession=$customerSession;
        $this->quoteRepository = $quoteRepository;
    }

    public function afterGetFormValues(Account $account, $result)
    {
        if (isset($result['group_id'])) {
            $this->customerSession->setCustomerGroupId($result['group_id']);
        }
        return $result;
    }
}
