<?php
	/**
	 * Traits that will be shared by Sales Order Displays like Displays or Panels
	 */
	trait SalesOrderDisplayTraits {
		/* =============================================================
			OrderDisplayInterface Functions
			LINKS ARE HTML LINKS, AND URLS ARE THE URLS THAT THE HREF VALUE
		============================================================ */
		/**
		 * Returns HTNL Link to load Dplus Notes
		 * @param  Order  $order   SalesOrder
		 * @param  string $linenbr Line Number
		 * @return string          HTML link to view Dplus Notes
		 */
		public function generate_loaddplusnoteslink(Order $order, $linenbr = '0') {
			$bootstrap = new HTMLWriter();
			$href = $this->generate_dplusnotesrequesturl($order, $linenbr);

			if ($order->can_edit()) {
				$title = ($order->has_notes()) ? "View and Create Order Notes" : "Create Order Notes";
			} else {
				$title = ($order->has_notes()) ? "View Order Notes" : "View Order Notes";
			}
			$content = $bootstrap->icon('material-icons', '&#xE0B9;') . ' ' . $title;
			$link = $bootstrap->create_element('a', "href=$href|class=btn btn-default load-notes|title=$title|data-modal=$this->modal", $content);
			return $link;
		}

		/**
		 * Returns URL to request Dplus Notes
		 * @param  Order  $order   SalesOrder
		 * @param  string $linenbr Line Number
		 * @return string          URL to request Dplus Notes
		 */
		public function generate_dplusnotesrequesturl(Order $order, $linenbr) {
			$url = new \Purl\Url($this->pageurl->getUrl());
			$url->path = DplusWire::wire('config')->pages->notes."redir/";
			$url->query->setData(array('action' => 'get-order-notes', 'ordn' => $order->orderno, 'linenbr' => $linenbr));
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
				return $bootstrap->create_element('a', "href=$href|class=btn btn-primary load-sales-docs|role=button|title=Click to view Documents|$ajaxdata", $icon. ' Show Documents');
			} else {
				return $bootstrap->create_element('a', "href=#|class=btn btn-default|title=No Documents Available", $icon. ' 0 Documents Found');
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
				return $bootstrap->create_element('a', "href=$href|class=h3 load-sales-docs|role=button|title=Click to view Documents|$ajaxdata", $icon);
			} else {
				return $bootstrap->create_element('a', "href=#|class=h3 text-muted|title=No Documents Available", $icon);
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
			$url->query->setData(array('action' => 'get-order-documents', 'ordn' => $order->orderno));
			if ($orderdetail) {
				$url->query->set('itemdoc', $orderdetail->itemid);
			}
			return $url->getUrl();
		}

		/**
		 * Returns URL to edit order page
		 * @param  Order  $order SalesOrder
		 * @return string        URL to edit order page
		 */
		public function generate_editurl(Order $order) {
			$url = $this->generate_ordersredirurl();
			$url->query->setData(array('action' => 'get-order-details','ordn' => $order->orderno));

			if ($order->can_edit()) {
				$url->query->set('lock', 'lock');
			} elseif ($order->editord == 'L') {
				if (DplusWire::wire('user')->hasorderlocked) {
					$queryset = ($order->orderno == DplusWire::wire('user')->lockedordn) ?  'lock' : 'readonly';
					$url->query->set($queryset, $queryset);
				} else {
					$url->query->set('readonly', 'readonly');
				}
			} else {
				$url->query->set('readonly', 'readonly');
			}
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
			$icon = $bootstrap->create_element('span','class=h3', $bootstrap->icon('glyphicon glyphicon-print'));
			return $bootstrap->create_element('a', "href=$href|target=_blank", $icon." View Printable Order");
		}

		/**
		 * Returns URL to view print page for Sales Order
		 * @param  Order  $order SalesOrder
		 * @return string        URL to view print page
		 */
		public function generate_viewprinturl(Order $order) {
			$url = new \Purl\Url($this->generate_loaddetailsurl($order));
			$url->query->set('print', 'true');
			return $url->getUrl();
		}

		/**
		 * Returns URL to view print page for order
		 * NOTE USED by PDFMaker
		 * @param  Order  $order SalesOrder
		 * @return string        URL to view print page
		 */
		public function generate_viewprintpageurl(Order $order) {
			$url = new \Purl\Url($this->pageurl->getUrl());
			$url->path = DplusWire::wire('config')->pages->print."order/";
			$url->query->set('ordn', $order->orderno);
			$url->query->set('view', 'pdf');
			return $url->getUrl();
		}

		/**
		 * Returns URL to send email of this print page
		 *
		 * @param  Order  $order SalesOrder
		 * @return string        URL to email Order
		 */
		public function generate_sendemailurl(Order $order) {
			$url = new \Purl\Url(DplusWire::wire('config')->pages->email."sales-order/");
			$url->query->set('ordn', $order->orderno);
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
			$icon = $bootstrap->create_element('span','class=h3', $bootstrap->icon('glyphicon glyphicon-check'));
			return $bootstrap->create_element('a', "href=$href|target=_blank", $icon." View Associated Actions");
		}

		/**
		 * Returns URL to load linked UserActions
		 * @param  Order  $order SalesOrder | Quote
		 * @return string        URL to load linked UserActions
		 */
		public function generate_viewlinkeduseractionsurl(Order $order) {
			$url = new \Purl\Url($this->pageurl->getUrl());
			$url->path = DplusWire::wire('config')->pages->useractions;
			$url->query->setData(array('ordn' => $order->orderno));
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
			return $bootstrap->create_element('a', "href=$href|class=h3 view-item-details|data-itemid=$detail->itemid|data-kit=$detail->kititemflag|data-modal=#ajax-modal", $icon);
		}

		/**
		 * Returns URL to view SalesOrderDetail
		 * @param  Order       $order  SalesOrder
		 * @param  OrderDetail $detail SalesOrderDetail
		 * @return string              URL view detail
		 */
		public function generate_viewdetailurl(Order $order, OrderDetail $detail) {
			$url = new \Purl\Url($this->pageurl->getUrl());
			$url->path = DplusWire::wire('config')->pages->ajax."load/view-detail/order/";
			$url->query->setData(array('ordn' => $order->orderno, 'line' => $detail->linenbr));
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
		public function generate_loaddetailsurltrait(Order $order) {
			$url = $this->generate_ordersredirurl();
			$url->query->setData(array('action' => 'get-order-details', 'ordn' => $order->orderno));
			return $url->getUrl();
		}

		/**
		 * Returns URL to load detail lines for Sales Order
		 * @param  Order  $order SalesOrder
		 * @return string        URL to load detail lines for Sales Order
		 */
		public function generate_loaddetailsurl(Order $order) {
			return $this->generate_loaddetailsurltrait($order);
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
			$url = new \Purl\Url(DplusWire::wire('config')->pages->ajaxload.'edit-detail/order/');
			$url->query->setData(array('ordn' => $order->orderno, 'line' => $detail->linenbr));
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
			$icon = $bootstrap->create_element('i','class=glyphicon glyphicon-plane hover|style=top: 3px; padding-right: 5px; font-size: 130%;|aria-hidden=true', '');
			$ajaxdata = "data-loadinto=.tracking|data-focus=.tracking|data-click=#tracking-tab-link";

			if ($order->has_tracking()) {
				return $bootstrap->create_element('a', "href=$href|role=button|class=btn btn-primary load-sales-tracking|title=Click to load tracking|$ajaxdata", $icon. ' Show Documents');
			} else {
				return $bootstrap->create_element('a', "href=#|class=btn btn-default|title=No Tracking Available", $icon. ' No Tracking Available');
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
			$url->query->setData(array('action' => 'get-order-tracking', 'ordn' => $order->orderno));
			return $url->getUrl();
		}

		/**
		 * Returns Sales Order Details
		 * @param  Order  $order SalesOrder
		 * @param  bool   $debug Whether to execute query and return Sales Order Details
		 * @return array        SalesOrderDetails Array | SQL Query
		 */
		public function get_orderdetails(Order $order, $debug = false) {
			return get_orderdetails($this->sessionID, $order->orderno, true, $debug);
		}

		/**
		 * Makes the URL to the orders redirect page,
		 * @return Purl\Url URL to REDIRECT page
		 */
		public function generate_ordersredirurl() {
			$url = new \Purl\Url(DplusWire::wire('config')->pages->orders);
			$url->path = DplusWire::wire('config')->pages->orders."redir/";
			return $url;
		}
	}
