<?php 
	/**
	 * Functions that Sales Order Display classes must implement or extend
	 */
	interface SalesOrderDisplayInterface {
		/**
		 * Returns HTML Link to load tracking for that Sales Orders
		 * @param  Order  $order Sales Order
		 * @return string        HTML Link
		 */
		public function generate_loadtrackinglink(Order $order);
		
		/**
		 * Returns URL to load tracking for that Sales Orders
		 * @param  Order  $order Sales Order
		 * @return string        Sales Order Tracking Request URL
		 */
		public function generate_trackingrequesturl(Order $order);
		
		/**
		 * Returns Sales Order Details
		 * @param  Order  $order SalesOrder
		 * @param  bool   $debug Whether to execute query and return Sales Order Details
		 * @return array         SalesOrderDetail Array | SQL Query
		 */
		public function get_orderdetails(Order $order, $debug = false);
	}
