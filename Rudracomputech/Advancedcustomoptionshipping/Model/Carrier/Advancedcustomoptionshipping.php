<?php


namespace Rudracomputech\Advancedcustomoptionshipping\Model\Carrier;

use Magento\Quote\Model\Quote\Address\RateRequest;
use Magento\Shipping\Model\Rate\Result;

class Advancedcustomoptionshipping extends \Magento\Shipping\Model\Carrier\AbstractCarrier implements
    \Magento\Shipping\Model\Carrier\CarrierInterface
{

    protected $_code = 'advancedcustomoptionshipping';

    protected $_isFixed = true;

    protected $_rateResultFactory;

    protected $_rateMethodFactory;

	protected $helper;
	
    /**
     * Constructor
     *
     * @param \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig
     * @param \Magento\Quote\Model\Quote\Address\RateResult\ErrorFactory $rateErrorFactory
     * @param \Psr\Log\LoggerInterface $logger
     * @param \Magento\Shipping\Model\Rate\ResultFactory $rateResultFactory
     * @param \Magento\Quote\Model\Quote\Address\RateResult\MethodFactory $rateMethodFactory
     * @param array $data
     */
    public function __construct(
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig,
        \Magento\Quote\Model\Quote\Address\RateResult\ErrorFactory $rateErrorFactory,
        \Psr\Log\LoggerInterface $logger,
        \Magento\Shipping\Model\Rate\ResultFactory $rateResultFactory,
        \Magento\Quote\Model\Quote\Address\RateResult\MethodFactory $rateMethodFactory,
		\Rudracomputech\Advancedcustomoptionshipping\Helper\Data $helperData,
        array $data = []
    ) {
		$this->helper = $helperData;
        $this->_rateResultFactory = $rateResultFactory;
        $this->_rateMethodFactory = $rateMethodFactory;
        parent::__construct($scopeConfig, $rateErrorFactory, $logger, $data);
    }

    /**
     * {@inheritdoc}
     */
    public function collectRates(RateRequest $request)
    {
        if (!$this->getConfigFlag('active')) {
            return false;
        }


       // $shippingPrice = $this->getConfigData('price');
	   $shippingPrice = 0;
	   $firstSizeSum = 0;
	   $secondSizeSum = 0;
	  
	    // exclude Virtual products price from Package value if pre-configured
        if ($request->getAllItems()) {
            foreach($request->getAllItems() as $item){
				$options = $item->getOptionByCode('additional_options');
				//$productOption = unserialize($item->getProductOptions());
				//$BuyRequest = $item->getBuyRequest();
				
				$options = $item->getProduct()->getTypeInstance(true)->getOrderOptions($item->getProduct());
    
				if($options["options"]){
					foreach($options["options"] as $customOption){
						$getShippingCodeFromMethod =  $this->helper->getShippingCodeFromMethod($customOption["value"],$request->getDestCountryId());
						
						
						if($getShippingCodeFromMethod){
							$shipPrice = $getShippingCodeFromMethod["price"];
							if($this->helper->getConfigData('multiplyinventory')){
								$shipPrice = $getShippingCodeFromMethod["price"]*$item->getqty();
							}
							$shippingPrice += $shipPrice;
							
							$explodeTitle = explode('x',$getShippingCodeFromMethod["title"]);
							
							if(is_array($explodeTitle)){
								$firstSizeSum += (int) $explodeTitle[0];
								 preg_match('!\d+!', $explodeTitle[1], $matches);
								 
								 $secondSizeSum += (int) $matches[0];
								 
								
							}
							
						}
						
						
						
					}
				}
	
				
			}
               
        }
		$fixed_size = $this->helper->getConfigData('fixed_size');
		if($fixed_size){
			if($firstSizeSum >= $fixed_size || $secondSizeSum >= $fixed_size){
				$shippingPrice = $this->helper->getConfigData('fixed_price');
			}
		}
		

        $result = $this->_rateResultFactory->create();

        if ($shippingPrice !== false) {
            $method = $this->_rateMethodFactory->create();

            $method->setCarrier($this->_code);
            $method->setCarrierTitle($this->getConfigData('title'));

            $method->setMethod($this->_code);
            $method->setMethodTitle($this->getConfigData('name'));

            

            $method->setPrice($shippingPrice);
            $method->setCost($shippingPrice);

            $result->append($method);
        }

        return $result;
    }

    /**
     * getAllowedMethods
     *
     * @param array
     */
    public function getAllowedMethods()
    {
        return [$this->_code => $this->getConfigData('name')];
    }
}
