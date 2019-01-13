<?php
/**
 * Copyright © 2016 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Rudracomputech\Advancedcustomoptionshipping\Ui\DataProvider\Product\Form\Modifier;


use Magento\Catalog\Model\Locator\LocatorInterface;
use Magento\Store\Model\StoreManagerInterface;
use Magento\Catalog\Model\ProductOptions\ConfigInterface;
use Magento\Catalog\Model\Config\Source\Product\Options\Price as ProductOptionsPrice;
use Magento\Framework\UrlInterface;
use Magento\Framework\Stdlib\ArrayManager;
use Magento\Ui\Component\Modal;
use Magento\Ui\Component\Container;
use Magento\Ui\Component\DynamicRows;
use Magento\Ui\Component\Form\Fieldset;
use Magento\Ui\Component\Form\Field;
use Magento\Ui\Component\Form\Element\Input;
use Magento\Ui\Component\Form\Element\Select;
use Magento\Ui\Component\Form\Element\Checkbox;
use Magento\Ui\Component\Form\Element\ActionDelete;
use Magento\Ui\Component\Form\Element\DataType\Text;
use Magento\Ui\Component\Form\Element\DataType\Number;
use Magento\Framework\Locale\CurrencyInterface;
class CustomOptions extends \Magento\Catalog\Ui\DataProvider\Product\Form\Modifier\CustomOptions
{

  protected $helper;
  protected $_priceCurrency;

    public function __construct(
		 LocatorInterface $locator,
        StoreManagerInterface $storeManager,
        ConfigInterface $productOptionsConfig,
        ProductOptionsPrice $productOptionsPrice,
        UrlInterface $urlBuilder,
        ArrayManager $arrayManager,
		 \Magento\Framework\Pricing\PriceCurrencyInterface $priceCurrency, 
		\Rudracomputech\Advancedcustomoptionshipping\Helper\Data $helperData
       
    ) {
        $this->helper = $helperData;
		$this->_priceCurrency = $priceCurrency;
		 parent::__construct(
            $locator,
            $storeManager,
            $productOptionsConfig,
            $productOptionsPrice,
            $urlBuilder,
            $arrayManager
        );
		
    }
    /**#@+
     * Field values
     * 
     */
    const FIELD_SHIPPING_NAME = 'shippingoption_id';

    /**
     * Get config for grid for "select" types
     * @param int $sortOrder
     * @return array
     */
    protected function getSelectTypeGridConfig($sortOrder)
    {
        return [
            'arguments' => [
                'data' => [
                    'config' => [
                        'addButtonLabel' => __('Add Value'),
                        'componentType' => DynamicRows::NAME,
                        'component' => 'Magento_Ui/js/dynamic-rows/dynamic-rows',
                        'additionalClasses' => 'admin__field-wide',
                        'deleteProperty' => static::FIELD_IS_DELETE,
                        'deleteValue' => '1',
                        'renderDefaultRecord' => false,
                        'sortOrder' => $sortOrder,
                    ],
                ],
            ],
            'children' => [
                'record' => [
                    'arguments' => [
                        'data' => [
                            'config' => [
                                'componentType' => Container::NAME,
                                'component' => 'Magento_Ui/js/dynamic-rows/record',
                                'positionProvider' => static::FIELD_SORT_ORDER_NAME,
                                'isTemplate' => true,
                                'is_collection' => true,
                            ],
                        ],
                    ],
                    'children' => [
                        static::FIELD_TITLE_NAME => $this->getTitleFieldConfig(10),
                        static::FIELD_PRICE_NAME => $this->getPriceFieldConfig(20),
                        static::FIELD_PRICE_TYPE_NAME => $this->getPriceTypeFieldConfig(30, ['fit' => true]),
                        static::FIELD_SKU_NAME => $this->getSkuFieldConfig(40),
                        static::FIELD_SORT_ORDER_NAME => $this->getPositionFieldConfig(50),
                        static::FIELD_SHIPPING_NAME => $this->getShippingOptionConfig(55),
                        static::FIELD_IS_DELETE => $this->getIsDeleteFieldConfig(60)
                    ]
                ]
            ]
        ];
    }

    /**
     * Get config for "Inventory" fields
     * 
     * @param $sortOrder 
     * @param array $options
     * @return array
     */
    protected function getShippingOptionConfig($sortOrder, array $options = [])
    {
		$Currency_symbol = $this->_priceCurrency->getCurrency()->getCurrencySymbol();
		//print_r($Currency_symbol);die;
		$_ShippingTypes = $this->helper->getShippingType();
		
		foreach($_ShippingTypes as $key => $_ShippingType){
			$optionHtml[] = ['value' =>$key, 'label'=>$_ShippingType['title'].' + '.$Currency_symbol.' '.$_ShippingType['price']];
		}
        
        return array_replace_recursive(
            [
                'arguments' => [
                    'data' => [
                        'config' => [
                            'label' => __('Ship Type'),
                           // 'component' => 'Magento_Catalog/js/components/custom-options-price-type',
                            'componentType' => Field::NAME,
                            'formElement' => Select::NAME,
                            'dataScope' => static::FIELD_SHIPPING_NAME,
                            'dataType' => Text::NAME,
                            'sortOrder' => $sortOrder,
                            'options' => $optionHtml,
                           // 'imports' => [
                           //     'priceIndex' => self::FIELD_PRICE_NAME,
                           // ],
                        ],
                    ],
                ],
            ],
            $options
        );
    }
}