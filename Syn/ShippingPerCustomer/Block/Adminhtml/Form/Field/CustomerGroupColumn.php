<?php

namespace Syn\ShippingPerCustomer\Block\Adminhtml\Form\Field;

use Magento\Customer\Model\ResourceModel\Group\Collection;
use Magento\Framework\View\Element\Context;
use Magento\Framework\View\Element\Html\Select;

class CustomerGroupColumn extends Select
{
    private $customerGroupCollection;

    public function __construct(Collection $customerGroupCollection, Context $context, array $data = [])
    {
        parent::__construct($context, $data);
        $this->customerGroupCollection = $customerGroupCollection;
    }

    public function setInputName($value)
    {
        return $this->setName($value);
    }

    public function setInputId($value)
    {
        return $this->setId($value);
    }

    public function _toHtml()
    {
        if (!$this->getOptions()) {
            $this->setOptions($this->getSourceOptions());
        }
        return parent::_toHtml();
    }

    private function getSourceOptions()
    {
        return $this->customerGroupCollection->toOptionArray();
    }
}
