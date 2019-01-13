<?php

namespace Rudracomputech\Advancedcustomoptionshipping\Block\Adminhtml\System\Config\Form\Field;

class Countrylist extends \Magento\Framework\View\Element\Html\Select
{
    /**
     * @var $_attributesRenderer \Magently\Tutorial\Block\Adminhtml\Form\Field\Activation
     */
     protected $_countryList;
/**
     * Activation constructor.
     *
     * @param \Magento\Framework\View\Element\Context $context
     * @param \Magento\Config\Model\Config\Source\_countryList $_countryList $_countryList
     * @param array $data
     */
    public function __construct(
        \Magento\Framework\View\Element\Context $context,
        \Magento\Directory\Model\Config\Source\Country $_countryList,
        array $data = []
    ) {
        parent::__construct($context, $data);

        $this->_countryList = $_countryList;
    }

    /**
     * @param string $value
     * @return Magently\Tutorial\Block\Adminhtml\Form\Field\Activation
     */
    public function setInputName($value)
    {
        return $this->setName($value);
    }

    /**
     * Parse to html.
     *
     * @return mixed
     */
    public function _toHtml()
    {
        if (!$this->getOptions()) {
            $attributes = $this->_countryList->toOptionArray();

            foreach ($attributes as $attribute) {
                $this->addOption($attribute['value'], $attribute['label']);
            }
        }

        return parent::_toHtml();
    }

}