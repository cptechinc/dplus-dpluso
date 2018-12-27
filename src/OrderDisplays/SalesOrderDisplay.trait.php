<?php
	namespace Dplus\Dpluso\OrderDisplays;
	
	use Purl\Url;
	use Dplus\ProcessWire\DplusWire;
	use Dplus\Content\HTMLWriter;

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
		 * @param  string $linenbr Line Number
		 * @return string          URL to request Dplus Notes
		 */
		public function generate_dplusnotesrequestURL(Order $order, $linenbr) {
			$url = new Url($this->pageurl->getUrl());
			$url->path = DplusWire::wire('config')->pages->notes."redir/";
			$url->query->setData(array('action' => 'get-order-notes', 'ordn' => $order->ordernumber, 'linenbr' => $linenbr));
			return $url->getUrl();
		}

		/**
		 * Returns HTML link that loads documents for Order
		 * @param  Order       $order   SalesOrder
		 * @param  OrderDetail $detail  Detail to load documents for SalesOrderDetail
		 * @return string               HTML link to view documents
		 * @uses
		 */
		public function generate_loaddocumentslink(Order $order, OrderDetail $orderdetail = null) {
			if ($orderdetail) {
				return $this->generate_loaddetaildocumentslink($order, $orderdetail);
			} else {
				return $this->generate_loadheaderdocumentslink($order, $orderdetail);
			}
		}

		/**
		 * Returns HTML link that loads documents for Order header
		 * @param  Order       $order   SalesOrder
		 * @param  OrderDetail $detail  Detail to load documents for SalesOrderDetail
		 * @return string               HTML link to view documents
		 * @uses
		 */
		public function generate_loadheaderdocumentslink(Order $order, OrderDetail $orderdetail = null) {
			$bootstrap = new HTMLWriter();
			$href = $this->generate_documentsrequesturl($order, $orderdetail);
			$icon = $bootstrap->icon('fa fa-file-text');
			$ajaxdata = "data-loadinto=.docs|data-focus=.docs|data-click=#documents-link";

			if ($order->has_documents()) {
				return $bootstrap->a("href=$href|class=btn btn-primary load-sales-docs|role=button|title=Click to view Documents|$ajaxdata", $icon. ' Show Documents');
			} else {
				return $bootstrap->a("href=#|class=btn btn-default|title=No Documents Available", $icon. ' 0 Documents Found');
			}
		}

		/**
		 * Returns HTML link that loads documents for Sales Order Details
		 * @param  Order       $order   SalesOrder
		 * @param  OrderDetail $detail  Detail to load documents for SalesOrderDetail
		 * @return string               HTML link to view documents
		 * @uses
		 */
		public function generate_loaddetaildocumentslink(Order $order, OrderDetail $orderdetail = null) {
			$bootstrap = new HTMLWriter();
			$href = $this->generate_documentsrequesturl($order, $orderdetail);
			$icon = $bootstrap->icon('fa fa-file-text');
			$ajaxdata = "data-loadinto=.docs|data-focus=.docs|data-click=#documents-link";
			$documentsTF = ($orderdetail) ? $orderdetail->has_documents() : $order->has_documents();

			if ($documentsTF) {
				return $bootstrap->a("href=$href|class=h3 load-sales-docs|role=button|title=Click to view Documents|$ajaxdata", $icon);
			} else {
				return $bootstrap->a("href=#|class=h3 text-muted|title=No Documents Available", $icon);
			}
		}

		/**
		 * Sets up a common url function for getting documents request url, classes that have this trait
		 * will define generate_documentsrequesturltr(Order $order)
		 * @param  Order       $order        SalesOrder
		 * @param  OrderDetail $orderdetail  SalesOrderDetail
		 * @return string		             URL to the order redirect to make the get order documents request
		 */
		public function generate_documentsrequesturltrait(Order $order, OrderDetail $orderdetail = null) {
			$url = $this->generate_ordersredirurl();
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
		public function generate_editurl(Order $order) {
			$url = $this->generate_ordersredirurl();
			$url->query->setData(array('action' => 'get-order-edit','ordn' => $order->ordernumber));
			return $url->getUrl();
		}

		/**
		 * Returns URL to Request Release Order
		 * @param  Order  $order SalesOrder
		 * @return string        URL to edit order page
		 */
		public function generate_releaseurl(Order $order) {
			$url = $this->generate_ordersredirurl();
			$url->query->setData(array('action' => 'release-order','ordn' => $order->ordernumber));
			return $url->getUrl();
		}

		/**
		 * Returns HTML link to view print page for Sales Order
		 * @param  Order  $order SalesOrder
		 * @return string        HTML link to view print page
		 */
		public function generate_viewprintlink(Order $order) {
			$bootstrap = new HTMLWriter();
			$href = $this->generate_viewprinturl($order);
			$icon = $bootstrap->span('class=h3', $bootstrap->icon('fa fa-print'));
			return $bootstrap->a("href=$href|target=_blank", $icon." View Printable Order");
		}

		/**
		 * Returns URL to view print page for Sales Order
		 * @param  Order  $order SalesOrder
		 * @return string        URL to view print page
		 */
		public function generate_viewprinturl(Order $order) {
			$url = $this->generate_ordersredirurl();
			$url->query->setData(array('action' => 'get-order-print','ordn' => $order->ordernumber));
			return $url->getUrl();
		}

		/**
		 * Returns URL to view print page for order
		 * NOTE USED by PDFMaker
		 * @param  Order  $order SalesOrder
		 * @return string        URL to view print page
		 */
		public function generate_viewprintpageurl(Order $order) {
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
		public function generate_sendemailurl(Order $order) {
			$url = new Url(DplusWire::wire('config')->pages->email."sales-order/");
			$url->query->set('ordn', $order->ordernumber);
			$url->query->set('referenceID', $this->sessionID);
			return $url->getUrl();
		}

		/**
		 * Returns HTML Link to view linked user actions
		 * @param  Order  $order SalesOrder | Quote
		 * @return string        HTML Link to view linked user actions
		 */
		public function generate_viewlinkeduseractionslink(Order $order) {
			$bootstrap = new HTMLWriter();
			$href = $this->generate_viewlinkeduseractionsurl($order);
			$icon = $bootstrap->span('class=h3', $bootstrap->icon('fa fa-check-square-o'));
			return $bootstrap->a("href=$href|target=_blank", $icon." View Associated Actions");
		}

		/**
		 * Returns URL to load linked UserActions
		 * @param  Order  $order SalesOrder | Quote
		 * @return string        URL to load linked UserActions
		 */
		public function generate_viewlinkeduseractionsurl(Order $order) {
			$url = new Url($this->pageurl->getUrl());
			$url->path = DplusWire::wire('config')->pages->useractions;
			$url->query->setData(array('ordn' => $order->ordernumber));
			return $url->getUrl();
		}

		/**
		 * Returns HTML link to view SalesOrderDetail
		 * @param  Order       $order  SalesOrder
		 * @param  OrderDetail $detail SalesOrderDetail
		 * @return string              HTML Link
		 */
		public function generate_viewdetaillink(Order $order, OrderDetail $detail) {
			$bootstrap = new HTMLWriter();
			$href = $this->generate_viewdetailurl($order, $detail);
			$icon = $bootstrap->icon('fa fa-info-circle');
			return $bootstrap->a("href=$href|class=h3 view-item-details|data-itemid=$detail->itemid|data-kit=$detail->kititemflag|data-modal=#ajax-modal", $icon);
		}

		/**
		 * Returns URL to view SalesOrderDetail
		 * @param  Order       $order  SalesOrder
		 * @param  OrderDetail $detail SalesOrderDetail
		 * @return string              URL view detail
		 */
		public function generate_viewdetailurl(Order $order, OrderDetail $detail) {
			$url = new Url($this->pageurl->getUrl());
			$url->path = DplusWire::wire('config')->pages->ajax."load/view-detail/order/";
			$url->query->setData(array('ordn' => $order->ordernumber, 'line' => $detail->linenbr));
			return $url->getUrl();
		}

		/**
		 * Return String URL to orders redir to request order details
		 * This is here for the use of getting the Print link
		 * will be used by classes to extend
		 * Extending classes : SalesOrderPanel
		 * @param  Order  $order
		 * @return string
		 */
		public function generate_loaddetailsURLtrait(Order $order) {
			$url = $this->generate_ordersredirurl();
			$url->query->setData(array('action' => 'get-order-details', 'ordn' => $order->ordernumber));
			return $url->getUrl();
		}

		/**
		 * Returns URL to load detail lines for Sales Order
		 * @param  Order  $order SalesOrder
		 * @return string        URL to load detail lines for Sales Order
		 */
		public function generate_loaddetailsURL(Order $order) {
			return $this->generate_loaddetailsURLtrait($order);
		}

		/**
		 * Returns the URL to load the edit/view detail URL
		 * Checks if we are editing Sales Order to show edit functions
		 * @param  Order       $order  SalesOrder
		 * @param  OrderDetail $detail SalesOrderDetail
		 * @return string              URL to load the edit/view detail URL
		 * @uses $order->can_edit()
		 */
		public function generate_detailviewediturl(Order $order, OrderDetail $detail) {
			$url = new Url(DplusWire::wire('config')->pages->ajaxload.'edit-detail/order/');
			$url->query->setData(array('ordn' => $order->ordernumber, 'line' => $detail->linenbr));
			return $url->getUrl();
		}

		/* =============================================================
			SalesOrderDisplayInterface Functions
		============================================================ */
		/**
		 * Returns HTML Link to load tracking for that Sales Orders
		 * @param  Order  $order Sales Order
		 * @return string        HTML Link
		 */
		public function generate_loadtrackinglink(Order $order) {
			$bootstrap = new HTMLWriter();
			$href = $this->generate_trackingrequesturl($order);
			$icon = $bootstrap->i('class=fa fa-plane hover|style=top: 3px; padding-right: 5px; font-size: 130%;|aria-hidden=true', '');
			$ajaxdata = "data-loadinto=.tracking|data-focus=.tracking|data-click=#tracking-tab-link";

			if ($order->has_tracking()) {
				return $bootstrap->a("href=$href|role=button|class=btn btn-primary load-sales-tracking|title=Click to load tracking|$ajaxdata", $icon. ' Show Documents');
			} else {
				return $bootstrap->a("href=#|class=btn btn-default|title=No Tracking Available", $icon. ' No Tracking Available');
			}
		}

		/**
		 * Sets up a common url function for getting d request url, classes that have this trait
		 * will definve generate_trackingrequesturl(Order $order)
		 * @param  Order  $order Sales Order
		 * @return string        URL to the order redirect to make the get order documents request
		 */
		public function generate_trackingrequesturltrait(Order $order) {
			$url = $this->generate_ordersredirurl();
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
		public function generate_ordersredirurl() {
			$url = new Url(DplusWire::wire('config')->pages->orders);
			$url->path = DplusWire::wire('config')->pages->orders."redir/";
			return $url;
		}
	}
