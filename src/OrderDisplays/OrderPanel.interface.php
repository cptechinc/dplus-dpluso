<?php 
	namespace Dplus\Dpluso\OrderDisplays;
	
	/**
	 * Use Statements for Model Classes which are non-namespaced
	 */
	use Order;
	
	/**
	 * Functions that need to be implemented by OrderPanel classes
	 */
	interface OrderPanelInterface {
		/**
		 * Returns a Manipulated Purl\Url object that is the base URL for that page
		 */
		public function setup_pageURL();
		
		/**
		 * Returns HTML popover
		 * Shipto address information is extracted from the order
		 * @param  Order  $order SalesOrder | Quote
		 * @return string        HTML bootstrap popover element
		 */
		public function generate_shiptopopover(Order $order); // OrderPanel
		
		/**
		 * Generates HTML bootstrap popover that shows the icons meaning
		 * @return string HTML bootstrap popover element
		 */
		public function generate_iconlegend();
		
		/**
		 * // TODO rename for URL()
		 * Returns URL to request the orders to be loaded
		 * @return string URLto request the orders to be loaded
		 */
		public function generate_loadurl();
		
		/**
		 * Returns URL to remove the sort on the panel
		 * @return string URL to remove the sort on the panel
		 */
		public function generate_clearsortURL(); // OrderPanel
		
		/**
		 * Returns URL that sorts the list by column
		 * @param  string $column Column to sor by
		 * @return string         URL that sorts the list by column
		 */
		public function generate_sortbyURL($column); // OrderPanel
		
		/**
		 * Returns URL that closes the detail view for that listing
		 * @return string URL that closes the detail view for that listing
		 */
		public function generate_closedetailsURL();
		
		/**
		 * Returns URL to request order details
		 * @param  Order  $order SalesOrder | Quote
		 * @return string        URL to request order details
		 */
		public function generate_loaddetailsURL(Order $order);
		
		/**
		 * Returns description of the last time this was loaded from Dplus
		 * @return string description e.g. 10:53 AM
		 */
		public function generate_lastloadeddescription();
	}
