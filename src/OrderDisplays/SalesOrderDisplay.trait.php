<?php
	namespace Dplus\Dpluso\OrderDisplays;
	
	use Purl\Url;
	use Dplus\ProcessWire\DplusWire;

	/**
	 * Use Statements for Model Classes which are non-namespaced
	 */
	use Order, OrderDetail;
	
	/**
	 * Traits that will be shared by Sales Order Displays like Displays or Panels
	 */
	trait SalesOrderDisplayTraits {

		/**
		 * Returns URL to request Dplus Notes
		 * @param  Order  $order   SalesOrder
		 * @param  int    $linenbr Line Number
		 * @return string          URL to request Dplus Notes
		 */
		public function generate_request_dplusnotesURL(Order $order, $linenbr = 0) {
			$url = new Url($this->pageurl->getUrl());
			$url->path = DplusWire::wire('config')->pages->notes."redir/";
			$url->query->setData(array('action' => 'get-order-notes', 'ordn' => $order->ordernumber, 'linenbr' => $linenbr));
			return $url->getUrl();
		}

		/**
		 * Sets up a common url function for getting documents request url, classes that have this trait
		 * will define generate_documentsrequestURLtr(Order $order)
		 * @param  Order       $order        SalesOrder
		 * @param  OrderDetail $orderdetail  SalesOrderDetail
		 * @return string		             URL to the order redirect to make the get order documents request
		 */
		public function generate_documentsrequestURL(Order $order, OrderDetail $orderdetail = null) {
			$url = $this->generate_ordersredirURL();
			$url->query->setData(array('action' => 'get-order-documents', 'ordn' => $order->ordernumber));
			if ($orderdetail) {
				$url->query->set('itemdoc', $orderdetail->itemid);
			}
			return $url->getUrl();
		}

		/**
		 * Returns URL to Request Edit Order
		 * @param  Order  $order SalesOrder
		 * @return string        URL to edit order page
		 */
		public function generate_editURL(Order $order) {
			$url = $this->generate_ordersredirURL();
			$url->query->setData(array('action' => 'get-order-edit','ordn' => $order->ordernumber));
			return $url->getUrl();
		}

		/**
		 * Returns URL to Request Release Order
		 * @param  Order  $order SalesOrder
		 * @return string        URL to edit order page
		 */
		public function generate_releaseurl(Order $order) {
			$url = $this->generate_ordersredirURL();
			$url->query->setData(array('action' => 'release-order','ordn' => $order->ordernumber));
			return $url->getUrl();
		}

		/**
		 * Returns URL to view print page for Sales Order
		 * @param  Order  $order SalesOrder
		 * @return string        URL to view print page
		 */
		public function generate_printURL(Order $order) {
			$url = $this->generate_ordersredirURL();
			$url->query->setData(array('action' => 'get-order-print','ordn' => $order->ordernumber));
			return $url->getUrl();
		}

		/**
		 * Returns URL to view print page for order
		 * NOTE USED for PDFMaker
		 * @param  Order  $order SalesOrder
		 * @return string        URL to view print page
		 */
		public function generate_printpageURL(Order $order) {
			$url = new Url($this->pageurl->getUrl());
			$url->path = DplusWire::wire('config')->pages->print."order/";
			$url->query->set('ordn', $order->ordernumber);
			$url->query->set('view', 'pdf');
			return $url->getUrl();
		}

		/**
		 * Returns URL to send email of this print page
		 * @param  Order  $order SalesOrder
		 * @return string        URL to email Order
		 */
		public function generate_sendemailURL(Order $order) {
			$url = new Url(DplusWire::wire('config')->pages->email."sales-order/");
			$url->query->set('ordn', $order->ordernumber);
			$url->query->set('referenceID', $this->sessionID);
			return $url->getUrl();
		}

		/**
		 * Returns URL to load linked UserActions
		 * @param  Order  $order SalesOrder | Quote
		 * @return string        URL to load linked UserActions
		 */
		public function generate_linkeduseractionsURL(Order $order) {
			$url = new Url($this->pageurl->getUrl());
			$url->path = DplusWire::wire('config')->pages->useractions;
			$url->query->setData(array('ordn' => $order->ordernumber));
			return $url->getUrl();
		}

		/**
		 * Returns URL to view SalesOrderDetail
		 * @param  Order       $order  SalesOrder
		 * @param  OrderDetail $detail SalesOrderDetail
		 * @return string              URL view detail
		 */
		public function generate_viewdetailURL(Order $order, OrderDetail $detail) {
			$url = new Url($this->pageurl->getUrl());
			$url->path = DplusWire::wire('config')->pages->ajax."load/view-detail/order/";
			$url->query->setData(array('ordn' => $order->ordernumber, 'line' => $detail->linenbr));
			return $url->getUrl();
		}

		/**
		 * Returns URL to load detail lines for Sales Order
		 * @param  Order  $order SalesOrder
		 * @return string        URL to load detail lines for Sales Order
		 */
		public function generate_request_detailsURL(Order $order) {
			$url = $this->generate_ordersredirURL();
			$url->query->setData(array('action' => 'get-order-details', 'ordn' => $order->ordernumber));
			return $url->getUrl();
		}

		/**
		 * Returns the URL to load the edit/view detail URL
		 * Checks if we are editing Sales Order to show edit functions
		 * @param  Order       $order  SalesOrder
		 * @param  OrderDetail $detail SalesOrderDetail
		 * @return string              URL to load the edit/view detail URL
		 * @uses $order->can_edit()
		 */
		public function generate_vieweditdetailURL(Order $order, OrderDetail $detail) {
			$url = new Url(DplusWire::wire('config')->pages->ajaxload.'edit-detail/order/');
			$url->query->setData(array('ordn' => $order->ordernumber, 'line' => $detail->linenbr));
			return $url->getUrl();
		}

		/* =============================================================
			SalesOrderDisplayInterface Functions
		============================================================ */

		/**
		 * Sets up a common url function for getting d request url, classes that have this trait
		 * @param  Order  $order Sales Order
		 * @return string        URL to the order redirect to make the get order documents request
		 */
		public function generate_request_trackingURL(Order $order) {
			$url = $this->generate_ordersredirURL();
			$url->query->setData(array('action' => 'get-order-tracking', 'ordn' => $order->ordernumber));
			return $url->getUrl();
		}

		/**
		 * Returns Sales Order Details
		 * @param  Order  $order SalesOrder
		 * @param  bool   $debug Whether to execute query and return Sales Order Details
		 * @return array        SalesOrderDetails Array | SQL Query
		 */
		public function get_orderdetails(Order $order, $debug = false) {
			return get_orderdetails($this->sessionID, $order->ordernumber, true, $debug);
		}

		/**
		 * Makes the URL to the orders redirect page,
		 * @return Url URL to REDIRECT page
		 */
		public function generate_ordersredirURL() {
			$url = new Url(DplusWire::wire('config')->pages->orders."redir/");
			return $url;
		}
	}
