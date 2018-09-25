<?php
	class CustomerSalesOrderHistoryPanel extends SalesOrderHistoryPanel {
		use OrderPanelCustomerTraits;

		public $orders = array();
		public $paneltype = 'shipped-order';
		public $filterable = array(
			'custpo' => array(
				'querytype' => 'between',
				'datatype' => 'char',
				'label' => 'Cust PO'
			),
			'custid' => array(
				'querytype' => 'between',
				'datatype' => 'char',
				'label' => 'CustID'
			),
			'orderno' => array(
				'querytype' => 'between',
				'datatype' => 'char',
				'label' => 'Order #'
			),
			'total_order' => array(
				'querytype' => 'between',
				'datatype' => 'numeric',
				'label' => 'Order Total'
			),
			'orderdate' => array(
				'querytype' => 'between',
				'datatype' => 'date',
				'date-format' => 'Ymd',
				'label' => 'Order Date'
			),
			'invoice_date' => array(
				'querytype' => 'between',
				'datatype' => 'date',
				'date-format' => 'Ymd',
				'label' => 'Invoice Date'
			),
			'salesperson_1' => array(
				'querytype' => 'in',
				'datatype' => 'char',
				'label' => 'Sales Person 1'
			)
		);

		/* =============================================================
			SalesOrderPanelInterface Functions
		============================================================ */
		public function get_ordercount($loginID = '', $debug = false) {
			$count = count_customersaleshistory($this->custID, $this->shipID, $this->filters, $this->filterable, $loginID, $debug);
			return $debug ? $count : $this->count = $count;
		}

		public function get_orders($loginID = '', $debug = false) {
			$useclass = true;
			if ($this->tablesorter->orderby) {
				if ($this->tablesorter->orderby == 'orderdate') {
					$orders = get_customersaleshistoryorderdate($this->custID, $this->shipID, DplusWire::wire('session')->display, $this->pagenbr, $this->tablesorter->sortrule, $this->filters, $this->filterable, $loginID, $useclass, $debug);
				} elseif ($this->tablesorter->orderby == 'invoice_date') {
					$orders = get_customersaleshistoryinvoicedate($this->custID, $this->shipID, DplusWire::wire('session')->display, $this->pagenbr, $this->tablesorter->sortrule, $this->filters, $this->filterable, $loginID, $useclass, $debug);
				} else {
					$orders = get_customersaleshistoryorderby($this->custID, $this->shipID, DplusWire::wire('session')->display, $this->pagenbr, $this->tablesorter->sortrule, $this->tablesorter->orderby, $this->filters, $this->filterable, $loginID, $useclass, $debug);
				}
			} else {
				// DEFAULT BY ORDER DATE SINCE SALES ORDER # CAN BE ROLLED OVER
				$this->tablesorter->sortrule = 'DESC';
				$orders = get_customersaleshistoryorderdate($this->custID, $this->shipID, DplusWire::wire('session')->display, $this->pagenbr, $this->tablesorter->sortrule, $this->filters, $this->filterable, $loginID, $useclass, $debug);
			}
			return $debug ? $orders : $this->orders = $orders;
		}

		/* =============================================================
			OrderPanelInterface Functions
			LINKS ARE HTML LINKS, AND URLS ARE THE URLS THAT THE HREF VALUE
		============================================================ */

		public function generate_loaddetailsurl(Order $order) {
			$url = new \Purl\Url(parent::generate_loaddetailsurl($order));
			$url->query->set('custID', $this->custID);
			if (!empty($this->shipID)) {
				$url->query->set('shipID', $this->shipID);
			}
			return $url->getUrl();
		}

		public function generate_lastloadeddescription() {
			if (DplusWire::wire('session')->{'orders-loaded-for'}) {
				if (DplusWire::wire('session')->{'orders-loaded-for'} == $this->custID) {
					return 'Last Updated : ' . DplusWire::wire('session')->{'orders-updated'};
				}
			}
			return '';
		}

		public function generate_filter(ProcessWire\WireInput $input) {
			$this->generate_defaultfilter($input);

			if (isset($this->filters['orderdate'])) {
				if (empty($this->filters['orderdate'][0])) {
					$this->filters['orderdate'][0] = date('m/d/Y', strtotime(get_minsaleshistoryorderdate($this->sessionID, 'orderdate', $this->custID, $this->shipID)));
				}

				if (empty($this->filters['orderdate'][1])) {
					$this->filters['orderdate'][1] = date('m/d/Y');
				}
			}

			if (isset($this->filters['invoice_date'])) {
				if (empty($this->filters['invoice_date'][0])) {
					$this->filters['invoice_date'][0] = date('m/d/Y', strtotime(get_minsaleshistoryorderdate($this->sessionID, 'invoice_date', $this->custID, $this->shipID)));
				}

				if (empty($this->filters['invoice_date'][1])) {
					$this->filters['invoice_date'][1] = date('m/d/Y');
				}
			}

			if (isset($this->filters['total_order'])) {
				if (!strlen($this->filters['total_order'][0])) {
					$this->filters['total_order'][0] = '0.00';
				}

				if (!strlen($this->filters['total_order'][1])) {
					$this->filters['total_order'][1] = get_maxtotal_order($this->sessionID, $this->custID, $this->shipID);
				}
			}
		}

		/* =============================================================
			SalesOrderDisplayInterface Functions
			LINKS ARE HTML LINKS, AND URLS ARE THE URLS THAT THE HREF VALUE
		============================================================ */
		public function generate_trackingrequesturl(Order $order) {
			$url = new \Purl\Url(parent::generate_trackingrequesturl($order));
			$url->query->set('custID', $this->custID);
			if (!empty($this->shipID)) {
				$url->query->set('shipID', $this->shipID);
			}
			$url->query->set('type', 'history');
			return $url->getUrl();
		}

		/* =============================================================
			OrderDisplayInterface Functions
			LINKS ARE HTML LINKS, AND URLS ARE THE URLS THAT THE HREF VALUE
		============================================================ */
		public function generate_documentsrequesturl(Order $order, OrderDetail $orderdetail = null) {
			$url = new \Purl\Url(parent::generate_documentsrequesturl($order, $orderdetail));
			$url->query->set('custID', $this->custID);
			if (!empty($this->shipID)) {
				$url->query->set('shipID', $this->shipID);
			}
			$url->query->set('type', 'history');
			return $url->getUrl();
		}
	}
