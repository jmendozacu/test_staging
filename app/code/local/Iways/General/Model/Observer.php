<?php
/**
 * 
 * Iways_General_Model_Observer
 *
 *	@var $observer Varien_Event_Obserer
 */
class Iways_General_Model_Observer {
	
	/**
	 * Prevent Configurable Products in Cart
	 * @param Varien_Event_Observer $observer
	 * @return boolean|Iways_Cargo_Model_Observer
	 */
	public function preventConfigurablesInCart(Varien_Event_Observer $observer) {
		if($observer->getEvent()->getProduct()->isConfigurable()) {
			Mage::log(Mage::app()->getRequest(), 0, 'configurable.log', true);
			Mage::throwException('Could not add product to cart.');
		}
		return $this;
	}
}