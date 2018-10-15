<?php 
	/**
	 * List of functions that need to be implemented by OrderDisplays
	 */
	interface OrderDisplayInterface {
		/**
		 * Returns URL to load the Customer Page
		 * @param  Order  $order SalesOrder | Quote
		 * @return string        Load Customer Page URL
		 */
		public function generate_customerurl(Order $order);
		
		/**
		 * Returns URL to load the Customer Shipto Page
		 * @param  Order  $order SalesOrder | Quote
		 * @return string        Load Customer Shipto Page URL
		 */
		public function generate_customershiptourl(Order $order);
		
		/**
		 * Returns HTNL Link to load Dplus Notes
		 * @param  Order  $order   SalesOrder | Quote
		 * @param  string $linenbr Line Number
		 * @return string          HTML link to view Dplus Notes
		 */
		public function generate_loaddplusnoteslink(Order $order, $linenbr = '0');
		
		/**
		 * Returns URL to request Dplus Notes
		 * @param  Order  $order   SalesOrder | Quote
		 * @param  string $linenbr Line Number
		 * @return string          URL to request Dplus Notes
		 */
		public function generate_dplusnotesrequesturl(Order $order, $linenbr);
		
		/**
		 * Returns HTML link that loads documents for Order
		 * @param  Order       $order  SalesOrder | Quote
		 * @param  OrderDetail         $detail Detail to load documents for SalesOrderDetail | QuoteDetail
		 * @return string              HTML link to view documents
		 * @uses
		 */
		public function generate_loaddocumentslink(Order $order, OrderDetail $detail = null);
		
		/**
		 * Returns URL to request Order Documents
		 * @param  Order        $order  Order
		 * @param  OrderDetail  $detail Detail to load documents for
		 * @return string               Request Order Documents URL
		 */
		public function generate_documentsrequesturl(Order $order, OrderDetail $detail = null);
		
		/**
		 * Returns HTML link to edit order page
		 * @param  Order  $order SalesOrder | Quote
		 * @return string        HTML link to edit order page
		 */
		public function generate_editlink(Order $order);
		
		/**
		 * Returns URL to edit order page
		 * @param  Order  $order SalesOrder | Quote
		 * @return string        URL to edit order page
		 */
		public function generate_editurl(Order $order);
		
		/**
		 * Returns HTML link to view print page for order
		 * @param  Order  $order SalesOrder | Quote
		 * @return string        HTML link to view print page
		 */
		public function generate_viewprintlink(Order $order);
		
		/**
		 * Returns URL to view print page for order
		 * @param  Order  $order SalesOrder | Quote
		 * @return string        URL to view print page
		 */
		public function generate_viewprinturl(Order $order);
		
		/**
		 * Returns URL to view print page for order
		 * USED by PDFMaker
		 * @param  Order  $order SalesOrder | Quote
		 * @return string        URL to view print page
		 */
		public function generate_viewprintpageurl(Order $order);
		
		/**
		 * Returns URL to send email of this print page
		 * 
		 * @param  Order  $order SalesOrder | Quote
		 * @return string        URL to email Order
		 */
		public function generate_sendemailurl(Order $order);
		
		/**
		 * Returns HTML Link to view linked user actions
		 * @param  Order  $order SalesOrder | Quote
		 * @return string        HTML Link to view linked user actions
		 */
		public function generate_viewlinkeduseractionslink(Order $order);
		
		/**
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
		public function generate_loaddetailsurl(Order $order);
		
		/**
		 * Returns HTML link to view/edit OrderDetail
		 * @param  Order       $order  SalesOrder | Quote
		 * @param  OrderDetail $detail SalesOrderDetail | QuoteDetail
		 * @return string              HTML Link
		 */
		public function generate_detailvieweditlink(Order $order, OrderDetail $detail);
		
		/**
		 * Returns the URL to load the edit/view detail URL
		 * Checks if we are editing order to show edit functions
		 * @param  Order       $order  SalesOrder | Quote
		 * @param  OrderDetail $detail SalesOrderDetail | QuoteDetail
		 * @return string              URL to load the edit/view detail URL
		 * @uses $order->can_edit()
		 */
		public function generate_detailviewediturl(Order $order, OrderDetail $detail); // SalesOrderDisplayTraits
	}
