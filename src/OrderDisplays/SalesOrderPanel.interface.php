<?php 
	/**
	 * Functions that Sales Order Panels Must Implement or Extend
	 */
	interface SalesOrderPanelInterface {
		/**
		 * Returns the Number of Orders that match the filter parameters
		 * @param  bool   $debug Whether to run Query and return count
		 * @return int           Count | SQL Query
		 */
		public function get_ordercount($debug = false);
		
		/**
		 * Return Sales Orders that match the filter parameters
		 * @param  bool   $debug Whether to run Query and return sales orders
		 * @return array         Array of SalesOrders | SQL Query
		 */
		public function get_orders($debug = false);
	}
