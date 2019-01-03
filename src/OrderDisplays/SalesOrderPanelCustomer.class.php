<?php
	namespace Dplus\Dpluso\OrderDisplays;
	
	use Purl\Url;
	use ProcessWire\WireInput;
	use Dplus\ProcessWire\DplusWire;
	
	/**
	 * Use Statements for Model Classes which are non-namespaced
	 */
	use Order, OrderDetail;
	
	class CustomerSalesOrderPanel extends SalesOrderPanel implements OrderPanelCustomerInterface {
		use OrderPanelCustomerTraits;

		protected $orders = array();
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
				'label' => 'ShiptoID'
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
			'status' => array(
				'querytype' => 'in',
				'datatype' => 'char',
				'label' => 'Status'
			),
			'salesperson' => array(
				'querytype' => 'in',
				'datatype' => 'char',
				'label' => 'Sales Rep'
			)
		);

		/* =============================================================
			OrderPanelInterface Functions
			LINKS ARE HTML LINKS, AND URLS ARE THE URLS THAT THE HREF VALUE
		============================================================ */
		public function generate_loadURL() {
			$url = new Url(parent::generate_loadURL());
			$url->query->set('action', 'load-cust-orders');
			$url->query->set('custID', $this->custID);
			return $url->getUrl();
		}
		
		public function generate_request_detailsURL(Order $order) {
			$url = new Url(parent::generate_request_detailsURL($order));
			$url->query->set('custID', $order->custid);
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
		
		/**
		 * Returns Min Date for $datetype
		 * @param  string $datetype Date Column
		 * @param  bool   $debug    Run in debug? If so, return SQL Query
		 * @return string           Min Date
		 */
		public function get_mindate($datetype = 'quotdate', $debug = false) {
			return get_minquotedate($this->sessionID, $this->custID,  $this->shiptoID, $datetype, $this->filters, $this->filterable, $debug);
		}
		
		/**
		 * Returns Max Quote Total
		 * @param  bool   $debug  Run in debug? If so, return SQL Query
		 * @return float          Max Quote Total
		 */
		public function get_maxquotetotal($debug = false) {
			return get_maxquotetotal($this->sessionID, $this->custID, $this->shiptoID, $this->filters, $this->filterable, $debug);
		}
		
		/**
		 * Returns Min Quote Total
		 * @param  bool   $debug  Run in debug? If so, return SQL Query
		 * @return float          Miin Quote Total
		 */
		public function get_minquotetotal($debug = false) {
			return get_minquotetotal($this->sessionID, $this->custID, $this->shiptoID, $this->filters, $this->filterable, $debug);
		}

		public function generate_filter(WireInput $input) {
			parent::generate_filter($input);
			$this->filters['custid'][] = $this->custID;
			
			if (!empty($this->shipID)) {
				$this->filters['shiptoid'][] = $this->shipID;
			}

			if (isset($this->filters['order_date'])) {
				if (empty($this->filters['order_date'][0])) {
					$this->filters['order_date'][0] = date('m/d/Y', strtotime($this->get_minsalesorderdate('order_date')));
				}

				if (empty($this->filters['order_date'][1])) {
					$this->filters['order_date'][1] = date('m/d/Y');
				}
			}

			if (isset($this->filters['total_order'])) {
				if (!strlen($this->filters['total_order'][0])) {
					$this->filters['total_order'][0] = '0.00';
				}

				if (!strlen($this->filters['total_order'][1])) {
					$this->filters['total_order'][1] = $this->get_maxsalesordertotal($this->custID);
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
			return $url->getUrl();
		}

		/* =============================================================
			OrderDisplayInterface Functions
			LINKS ARE HTML LINKS, AND URLS ARE THE URLS THAT THE HREF VALUE
		============================================================ */
		public function generate_request_documentsURL(Order $order, OrderDetail $orderdetail = null) {
			$url = new Url(parent::generate_request_documentsURL($order, $orderdetail));
			$url->query->set('custID', $this->custID);
			return $url->getUrl();
		}
	}
