<?php
	namespace Dplus\Dpluso\OrderDisplays;
	
	use Purl\Url;
	use ProcessWire\WireInput;
	use Dplus\ProcessWire\DplusWire;
	
	/**
	 * Use Statements for Model Classes which are non-namespaced
	 */
	use Order, OrderDetail;
	
	class CustomerSalesOrderHistoryPanel extends SalesOrderHistoryPanel {
		use OrderPanelCustomerTraits;

		protected $orders = array();
		protected $paneltype = 'shipped-order';
		protected $filterable = array(
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
			'shiptoid' => array(
				'querytype' => 'in',
				'datatype' => 'char',
				'label' => 'shiptoID'
			),
			'ordernumber' => array(
				'querytype' => 'between',
				'datatype' => 'char',
				'label' => 'Order #'
			),
			'total_order' => array(
				'querytype' => 'between',
				'datatype' => 'numeric',
				'label' => 'Order Total'
			),
			'order_date' => array(
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
			'salesperson' => array(
				'querytype' => 'in',
				'datatype' => 'char',
				'label' => 'Sales Person 1'
			)
		);
		
		/* =============================================================
			SalesOrderPanelInterface Functions
		============================================================ */
		/**
		 * Returns the Max Sales Order Total
		 * @param  bool   $debug Run in debug? IF so, return SQL Query
		 * @return float         Max Sales Order Total
		 */
		public function get_maxsalesordertotal($debug = false) {
			return get_maxsaleshistoryordertotal($this->custID, $this->shipID, $this->filters, $this->filterable, $debug);
		}

		/**
		 * Returns the Min Sales Order Total
		 * @param  bool   $debug Run in debug? IF so, return SQL Query
		 * @return float         Min Sales Order Total
		 */
		public function get_minsalesordertotal($debug = false) {
			return get_minsaleshistoryordertotal($this->custID, $this->shipID, $this->filters, $this->filterable, $debug);
		}
		
		/**
		 * REturns the Min Sales Order Date field value for $field
		 * @param  string $field Date Column to return Min Date
		 * @param  bool   $debug Run in debug? If so, return SQL Query
		 * @return string        Min $field Date
		 */
		public function get_mindate($field = 'order_date', $debug = false) {
			return get_minsaleshistoryorderdate($field, $this->custID, $this->shipID, $this->filters, $this->filterable, $debug);
		}

		/* =============================================================
			OrderPanelInterface Functions
			LINKS ARE HTML LINKS, AND URLS ARE THE URLS THAT THE HREF VALUE
		============================================================ */
		public function generate_request_detailsURL(Order $order) {
			$url = new Url(parent::generate_request_detailsURL($order));
			$url->query->set('custID', $this->custID);
			if (!empty($this->shipID)) {
				$url->query->set('shipID', $this->shipID);
			}
			return $url->getUrl();
		}

		public function generate_filter(WireInput $input) {
			parent::generate_filter($input);
			
			$this->filters['custid'][] = $this->custID;
			
			if (!empty($this->shipID)) {
				$this->filters['shiptoid'][] = $this->shipID;
			}
			
			
			if (isset($this->filters['order_date'])) {
				if (empty($this->filters['order_date'][0])) {
					$this->filters['order_date'][0] = date('m/d/Y', strtotime($this->get_minsaleshistoryorderdate('order_date')));
				}

				if (empty($this->filters['order_date'][1])) {
					$this->filters['order_date'][1] = date('m/d/Y');
				}
			}

			if (isset($this->filters['invoice_date'])) {
				if (empty($this->filters['invoice_date'][0])) {
					$this->filters['invoice_date'][0] = date('m/d/Y', strtotime($this->get_minsaleshistoryorderdate('invoice_date')));
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
					$this->filters['total_order'][1] = $this->get_maxsalesordertotal();
				}
			}
		}

		/* =============================================================
			SalesOrderDisplayInterface Functions
			LINKS ARE HTML LINKS, AND URLS ARE THE URLS THAT THE HREF VALUE
		============================================================ */
		public function generate_request_trackingURL(Order $order) {
			$url = new Url(parent::generate_request_trackingURL($order));
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
		public function generate_request_documentsURL(Order $order, OrderDetail $orderdetail = null) {
			$url = new Url(parent::generate_request_documentsURL($order, $orderdetail));
			$url->query->set('custID', $this->custID);
			if (!empty($this->shipID)) {
				$url->query->set('shipID', $this->shipID);
			}
			$url->query->set('type', 'history');
			return $url->getUrl();
		}
	}
