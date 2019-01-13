<?php
/**
 * Copyright © Rudracomputech LLC. All rights reserved.
 * See COPYING.txt for license details.
 * http://www.magepal.com | support@magepal.com
 */

namespace Rudracomputech\Advancedcustomoptionshipping\Block\Adminhtml\System\Config\Form\Field;

use Magento\Config\Block\System\Config\Form\Field\FieldArray\AbstractFieldArray;

/**
 * Class Locations Backend system config array field renderer
 */
class ShippingList extends AbstractFieldArray
{
    protected $helper;
    protected $_typeRenderer;


    /**
     * ShippingList constructor.
     * @param \Magento\Backend\Block\Template\Context $context
     * @param \Rudracomputech\Advancedcustomoptionshipping\Helper\Data $helperData
     * @param array $data
     */
    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \Rudracomputech\Advancedcustomoptionshipping\Helper\Data $helperData,
        array $data = []
    ) {
        $this->helper = $helperData;
        parent::__construct($context, $data);
    }

    /**
     * Initialise columns for 'Store Locations'
     * Label is name of field
     * Class is storefront validation action for field
     *
     * @return void
     */
    protected function _prepareToRender()
    {
        foreach ($this->helper->getHeaderColumns() as $key => $column) {
			$style_width = "width:70px";
			if($key =="title"){
				$style_width = "width:220px";
			}
            $this->addColumn(
                $key,
                [
                    'label' => __($column['label']),
                    'class' => $column['class'],
					'style' => $style_width
                ]
            );
        }
		
		$this->addColumn(
            'countrycode',
            [
                'label' => __('Country'),
                
                'renderer' => $this->_getComponentCodeRenderer()
            
            ]
        );

        $this->_addAfter = false;
        parent::_construct();
    }
	
	 protected function  _getComponentCodeRenderer()
{
    if (!$this->_typeRenderer) {
        $this->_typeRenderer = $this->getLayout()->createBlock(
            '\Rudracomputech\Advancedcustomoptionshipping\Block\Adminhtml\System\Config\Form\Field\Countrylist',
            '',
            ['data' => ['is_render_to_js_template' => true]]

        );
    }
    return $this->_typeRenderer;
}

protected function _prepareArrayRow(\Magento\Framework\DataObject $row)
{
    $componentCode = $row->getData('countrycode');
    $options = [];
    if ($componentCode) {
        $key = 'option_' . $this->_getComponentCodeRenderer()->calcOptionHash($componentCode);
        $options[$key] = 'selected="selected"';
    }
    $row->setData('option_extra_attrs', $options);
}
   
}
