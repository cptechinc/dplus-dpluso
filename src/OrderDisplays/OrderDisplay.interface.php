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
		 * Returns URL to load the Customer Page
		 * @param  Order  $order SalesOrder | Quote
		 * @return string        Load Customer Page URL
		 */
		public function generate_customerURL(Order $order);
		
		/**
		 * Returns URL to load the Customer Shipto Page
		 * @param  Order  $order SalesOrder | Quote
		 * @return string        Load Customer Shipto Page URL
		 */
		public function generate_customershiptoURL(Order $order);
		
		/**
		 * Returns URL to request Dplus Notes
		 * @param  Order  $order   SalesOrder | Quote
		 * @param  string $linenbr Line Number
		 * @return string          URL to request Dplus Notes
		 */
		public function generate_dplusnotesrequestURL(Order $order, $linenbr);
		
		/**
		 * Returns URL to request Order Documents
		 * @param  Order        $order  Order
		 * @param  OrderDetail  $detail Detail to load documents for
		 * @return string               Request Order Documents URL
		 */
		public function generate_documentsrequestURL(Order $order, OrderDetail $detail = null);
		
		
		/**
		 * Returns URL to edit order page
		 * @param  Order  $order SalesOrder | Quote
		 * @return string        URL to edit order page
		 */
		public function generate_editURL(Order $order);
		
		/**
		 * // FIXME Remove, and make link at presentation level
		 * Returns HTML link to view print page for order
		 * @param  Order  $order SalesOrder | Quote
		 * @return string        HTML link to view print page
		 */
		public function generate_viewprintlink(Order $order);
		
		/**
		 * // TODO rename for URL()
		 * Returns URL to view print page for order
		 * @param  Order  $order SalesOrder | Quote
		 * @return string        URL to view print page
		 */
		public function generate_viewprinturl(Order $order);
		
		/**
		 * // TODO rename for URL()
		 * Returns URL to view print page for order
		 * USED by PDFMaker
		 * @param  Order  $order SalesOrder | Quote
		 * @return string        URL to view print page
		 */
		public function generate_viewprintpageurl(Order $order);
		
		/**
		 * // TODO rename for URL()
		 * Returns URL to send email of this print page
		 * @param  Order  $order SalesOrder | Quote
		 * @return string        URL to email Order
		 */
		public function generate_sendemailurl(Order $order);
		
		/**
		 * // FIXME Remove, and make link at presentation level
		 * Returns HTML Link to view linked user actions
		 * @param  Order  $order SalesOrder | Quote
		 * @return string        HTML Link to view linked user actions
		 */
		public function generate_viewlinkeduseractionslink(Order $order);
		
		/**
		 * // TODO rename for URL()
		 * Returns URL to load linked UserActions
		 * @param  Order  $order SalesOrder | Quote
		 * @return string        URL to load linked UserActions
		 */
		public function generate_viewlinkeduseractionsurl(Order $order);
		
		// FUNCTIONS FOR DETAIL LINES 
		/**
		 * Returns URL to load detail lines for order
		 * @param  Order  $order SalesOrder | Quote
		 * @return string        URL to load detail lines for order
		 */
		public function generate_loaddetailsURL(Order $order);
		
		/**
		 * // FIXME Remove, and make link at presentation level
		 * Returns HTML link to view/edit OrderDetail
		 * @param  Order       $order  SalesOrder | Quote
		 * @param  OrderDetail $detail SalesOrderDetail | QuoteDetail
		 * @return string              HTML Link
		 */
		public function generate_detailvieweditlink(Order $order, OrderDetail $detail);
		
		/**
		 * // TODO rename for URL()
		 * Returns the URL to load the edit/view detail URL
		 * Checks if we are editing order to show edit functions
		 * @param  Order       $order  SalesOrder | Quote
		 * @param  OrderDetail $detail SalesOrderDetail | QuoteDetail
		 * @return string              URL to load the edit/view detail URL
		 * @uses $order->can_edit()
		 */
		public function generate_detailviewediturl(Order $order, OrderDetail $detail); // SalesOrderDisplayTraits
	}
