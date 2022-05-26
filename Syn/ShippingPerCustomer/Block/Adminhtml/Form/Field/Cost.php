<?php
namespace Syn\ShippingPerCustomer\Block\Adminhtml\Form\Field;

use Magento\Config\Block\System\Config\Form\Field\FieldArray\AbstractFieldArray;
use Magento\Framework\DataObject;

class Cost extends AbstractFieldArray
{
    private $costRenderer;

    protected function _prepareToRender()
    {
        $this->addColumn('customer_group', [
            'label' => __('Customer Group'),
            'renderer' => $this->getCostRenderer()
        ]);
        $this->addColumn('cost', ['label' => __('Cost'), 'class' => 'validate-number validate-zero-or-greater']);

        $this->_addAfter = false;
        $this->_addButtonLabel = __('Add');
    }

    protected function _prepareArrayRow(DataObject $row)
    {
        $options = [];

        $cusotmer_group = $row->getCustomerGroup();
        if ($cusotmer_group !== null) {
            $options['option_' . $this->getCostRenderer()->calcOptionHash($cusotmer_group)] = 'selected="selected"';
        }

        $row->setData('option_extra_attrs', $options);
    }

    private function getCostRenderer()
    {
        if (!$this->costRenderer) {
            $this->costRenderer = $this->getLayout()->createBlock(
                CustomerGroupColumn::class,
                '',
                ['data' => ['is_render_to_js_template' => true]]
            );
        }
        return $this->costRenderer;
    }
}
