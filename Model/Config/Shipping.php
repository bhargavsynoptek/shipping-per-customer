<?php

namespace Bhargav\ShippingPerCustomer\Model\Config;

use Magento\Framework\Option\ArrayInterface;

/**
 * Class Messagetype
 * @package Bhargav\ShippingPerCustomer\Model\Config\Source
 */
class Shipping implements ArrayInterface
{
    /**
     * @return array
     */
    public function toOptionArray()
    {
        return [
            ['value' => '1', 'label' => __('Per Item(s)')],
            ['value' => '2', 'label' => __('Per Order')]
        ];
    }
}
