<?php 
	namespace Dplus\Dpluso\OrderDisplays;
	
	/**
	 * Use Statements for Model Classes which are non-namespaced
	 */
	use Order, OrderDetail;
	
	/**
	 * List of functions that need to be implemented by OrderDisplays
	 */
	interface OrderDisplayInterface {
		
		/**
		 * Returns URL to request Dplus Notes
		 * @param  Order  $order   SalesOrder | Quote
		 * @param  int    $linenbr Line Number
		 * @return string          URL to request Dplus Notes
		 */
		public function generate_request_dplusnotesURL(Order $order, $linenbr = 0);
		
		/**
		 * Returns URL to request Order Documents
		 * @param  Order        $order  Order
		 * @param  OrderDetail  $detail Detail to load documents for
		 * @return string               Request Order Documents URL
		 */
		public function generate_request_documentsURL(Order $order, OrderDetail $detail = null);
		
		
		/**
		 * Returns URL to edit order page
		 * @param  Order  $order SalesOrder | Quote
		 * @return string        URL to edit order page
		 */
		public function generate_editURL(Order $order);
		
		/**
		 * Returns URL to view print page for order
		 * @param  Order  $order SalesOrder | Quote
		 * @return string        URL to view print page
		 */
		public function generate_printURL(Order $order);
		
		/**
		 * Returns URL to view print page for order
		 * NOTE USED for PDFMaker
		 * @param  Order  $order SalesOrder | Quote
		 * @return string        URL to view print page
		 */
		public function generate_printpageURL(Order $order);
		
		/**
		 * Returns URL to send email of this print page
		 * @param  Order  $order SalesOrder | Quote
		 * @return string        URL to email Order
		 */
		public function generate_sendemailURL(Order $order);
		
		/**
		 * Returns URL to load linked UserActions
		 * @param  Order  $order SalesOrder | Quote
		 * @return string        URL to load linked UserActions
		 */
		public function generate_linkeduseractionsURL(Order $order);
		
		// FUNCTIONS FOR DETAIL LINES 
		/**
		 * Returns URL to load detail lines for order
		 * @param  Order  $order SalesOrder | Quote
		 * @return string        URL to load detail lines for order
		 */
		public function generate_request_detailsURL(Order $order);
		
		/**
		 * Returns the URL to load the edit/view detail URL
		 * Checks if we are editing order to show edit functions
		 * @param  Order       $order  SalesOrder | Quote
		 * @param  OrderDetail $detail SalesOrderDetail | QuoteDetail
		 * @return string              URL to load the edit/view detail URL
		 * @uses $order->can_edit()
		 */
		public function generate_vieweditdetailURL(Order $order, OrderDetail $detail); // SalesOrderDisplayTraits
	}
