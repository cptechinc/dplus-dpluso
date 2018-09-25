<?php 
	/**
	 * Functions that Customer OrderPanels have to implement
	 */
    interface OrderPanelCustomerInterface {
		/**
		 * Sets the customer ID and ShiptoID
		 * @param string $custID Customer ID
		 * @param string $shipID Customer Shipto ID
		 */
        public function set_customer($custID, $shipID);
    }
