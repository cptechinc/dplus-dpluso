<?php 
	/**
	 * Functions that need to be implemented by OrderPanel classes
	 */
	interface OrderPanelInterface {
		/**
		 * Returns a Manipulated Purl\Url object that is the base URL for that page
		 */
		public function setup_pageurl();
		
		/**
		 * Returns an HTML Link to expand or collapse details
		 * @param  Order  $order SalesOrder | Quote
		 * @return string        HTML Link to expand or collapse details
		 */
		public function generate_expandorcollapselink(Order $order);
		
		/**
		 * Returns the row class for that order
		 * class is determined if order number / quote number is the same as the active quote / order number
		 * @param  Order  $order SalesOrder | Quote
		 * @return string        HTML class e.g. selected | (blank)
		 */
		public function generate_rowclass(Order $order);
		
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
		 * Returns HTML Link to request the orders to be loaded
		 * @return string HTML Link to request the orders to be loaded
		 */
		public function generate_loadlink(); // OrderPanel
		
		/**
		 * Returns URL to request the orders to be loaded
		 * @return string URLto request the orders to be loaded
		 */
		public function generate_loadurl();
		
		/**
		 * Returns HTML link to refresh orders
		 * @return string HTML link to refresh orders
		 */
		public function generate_refreshlink();
		
		/**
		 * Returns HTML link to remove search parameters
		 * @return string HTML link to remove search parameters
		 */
		public function generate_clearsearchlink(); // OrderPanel
		
		/**
		 * Returns HTML Link to remove the sort on the panel
		 * @return string HTML Link to remove the sort on the panel
		 */
		public function generate_clearsortlink(); // OrderPanel
		
		/**
		 * Returns URL to remove the sort on the panel
		 * @return string URL to remove the sort on the panel
		 */
		public function generate_clearsorturl(); // OrderPanel
		
		/**
		 * Returns URL that sorts the list by column
		 * @param  string $column Column to sor by
		 * @return string         URL that sorts the list by column
		 */
		public function generate_tablesortbyurl($column); // OrderPanel
		
		/**
		 * Returns URL that closes the detail view for that listing
		 * @return string URL that closes the detail view for that listing
		 */
		public function generate_closedetailsurl();
		
		/**
		 * Returns URL to request order details
		 * @param  Order  $order SalesOrder | Quote
		 * @return string        URL to request order details
		 */
		public function generate_loaddetailsurl(Order $order);
		
		/**
		 * Returns description of the last time this was loaded from Dplus
		 * @return string description e.g. 10:53 AM
		 */
		public function generate_lastloadeddescription();
	}
