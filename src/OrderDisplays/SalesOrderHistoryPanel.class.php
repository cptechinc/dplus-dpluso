<?php
	namespace Dplus\Dpluso\OrderDisplays;

	use Purl\Url;
	use ProcessWire\WireInput;
	use Dplus\ProcessWire\DplusWire;
	use Dplus\Content\FormMaker;
	use Dplus\Base\DplusDateTime;

	/**
	 * Use Statements for Model Classes which are non-namespaced
	 */
	use Order, OrderDetail;

	class SalesOrderHistoryPanel extends SalesOrderPanel {
		/**
		 * Array of SalesOrderHistory
		 * @var array
		 */
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
				'querytype' => 'between',
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
			'invoice_date' => array(
				'querytype' => 'between',
				'datatype' => 'date',
				'date-format' => 'Ymd',
				'label' => 'Invoice Date'
			),
			'status' => array(
				'querytype' => 'in',
				'datatype' => 'char',
				'label' => 'Status'
			),
			'salesperson' => array(
				'querytype' => 'in',
				'datatype' => 'char',
				'label' => 'Sales Person'
			)
		);

		public function __construct($sessionID, Url $pageurl, $modal, $loadinto, $ajax) {
			parent::__construct($sessionID, $pageurl, $modal, $loadinto, $ajax);
			$this->pageurl = new Url($pageurl->getUrl());
			$this->setup_pageURL();
		}

		/* =============================================================
			SalesOrderPanelInterface Functions
		============================================================ */
		public function get_ordercount($debug = false) {
			$count = count_saleshistory($this->filters, $this->filterable, $debug);
			return $debug ? $count : $this->count = $count;
		}

		public function get_orders($debug = false) {
			$useclass = true;
			if ($this->tablesorter->orderby) {
				$orders = get_saleshistory_orderby(DplusWire::wire('session')->display, $this->pagenbr, $this->tablesorter->sortrule, $this->tablesorter->orderby, $this->filters, $this->filterable, $useclass, $debug);
			} else {
				// DEFAULT BY Invoice DATE SINCE SALES ORDER # CAN BE ROLLED OVER
				$this->tablesorter->orderby = 'invoice_date';
				$this->tablesorter->sortrule = 'DESC';
				$orders = get_saleshistory_orderby(DplusWire::wire('session')->display, $this->pagenbr, $this->tablesorter->sortrule, $this->tablesorter->orderby, $this->filters, $this->filterable, $useclass, $debug);
			}
			return $debug ? $orders : $this->orders = $orders;
		}

		/**
		 * Returns the Max Sales Order Total
		 * @param  bool   $debug Return SQL Query?
		 * @return float         Max Sales Order Total
		 */
		public function get_maxsalesordertotal($debug = false) {
			return get_maxsaleshistoryordertotal($custID = '', $shipID = '', $this->filters, $this->filterable, $debug);
		}

		/**
		 * Returns the Min Sales Order Total
		 * @param  bool   $debug Return SQL Query?
		 * @return float         Min Sales Order Total
		 */
		public function get_minsalesordertotal($debug = false) {
			return get_minsaleshistoryordertotal($custID = '', $shipID = '', $this->filters, $this->filterable, $debug);
		}

		/**
		 * Returns the Min Sales Order Date field value for $field
		 * @param  string $field Date Column to return Min Date
		 * @param  bool   $debug Run in debug? If so, return SQL Query
		 * @return string        Min $field Date
		 */
		public function get_mindate($field = 'order_date', $debug = false) {
			return get_minsaleshistoryorderdate($field, $custID = '', $shipID = '', $this->filters, $this->filterable, $debug);
		}

		/* =============================================================
			OrderPanelInterface Functions
			LINKS ARE HTML LINKS, AND URLS ARE THE URLS THAT THE HREF VALUE
		============================================================ */
		public function setup_pageURL() {
			$this->pageurl->path = DplusWire::wire('config')->pages->ajax."load/sales-history/";
			$this->pageurl->query->remove('display');
			$this->pageurl->query->remove('ajax');
			$this->paginationinsertafter = 'sales-history';
		}
		
		// TODO rename for URL()
		public function generate_loadurl() {
			$url = new Url($this->pageurl);
			$url->query->remove('filter');
			foreach (array_keys($this->filterable) as $filtercolumns) {
				$url->query->remove($filtercolumns);
			}
			return $url->getUrl();
		}
		
		public function generate_closedetailsURL() {
			$url = new Url($this->pageurl->getUrl());
			$url->query->setData(array('ordn' => false, 'show' => false));
			return $url->getUrl();
		}

		/**
		 * Returns HTML form for reordering SalesOrderDetails
		 * @param  Order       $order  SalesOrderHistory
		 * @param  OrderDetail $detail SalesOrderDetail
		 * @return string              HTML Form
		 */
		public function generate_detailreorderform(Order $order, OrderDetail $detail) {
			if (empty(($detail->itemid))) {
				return '';
			}
			$action = DplusWire::wire('config')->pages->cart.'redir/';
			$id = $order->ordernumber.'-'.$detail->itemid.'-form';
			$form = new FormMaker("method=post|action=$action|class=item-reorder|id=$id");
			$form->input("type=hidden|name=action|value=add-to-cart");
			$form->input("type=hidden|name=ordn|value=$order->ordernumber");
			$form->input("type=hidden|name=custID|value=$order->custid");
			$form->input("type=hidden|name=itemID|value=$detail->itemid");
			$form->input("type=hidden|name=qty|value=".intval($detail->qty));
			$form->input("type=hidden|name=desc|value=$detail->desc1");
			$form->button("type=submit|class=btn btn-primary btn-xs", $form->bootstrap->icon('fa fa-shopping-cart'). $form->bootstrap->span('class=sr-only', 'Submit Reorder'));
			return $form->finish();
		}

		public function generate_filter(WireInput $input) {
			parent::generate_filter($input);

			if (isset($this->filters['order_date'])) {
				if (empty($this->filters['order_date'][0])) {
					$this->filters['order_date'][0] = DplusDateTime::format_date($this->get_mindate('order_date'));
				}

				if (empty($this->filters['order_date'][1])) {
					$this->filters['order_date'][1] = date('m/d/Y');
				}
			}

			if (isset($this->filters['invoice_date'])) {
				if (empty($this->filters['invoice_date'][0])) {
					$this->filters['invoice_date'][0] = date('m/d/Y', strtotime($this->get_mindate('invoice_date')));
				}

				if (empty($this->filters['invoice_date'][1])) {
					$this->filters['invoice_date'][1] = date('m/d/Y');
				}
			}

			if (isset($this->filters['total_order'])) {
				if (!strlen($this->filters['total_order'][0])) {
					$this->filters['total_order'][0] = '0.00';
				}

				for ($i = 0; $i < (sizeof($this->filters['total_order']) + 1); $i++) {
					if (isset($this->filters['total_order'][$i])) {
						if (strlen($this->filters['total_order'][$i])) {
							$this->filters['total_order'][$i] = number_format($this->filters['total_order'][$i], 2, '.', '');
						}
					}
				}
			}
		}

		/* =============================================================
			SalesOrderDisplayInterface Functions
			LINKS ARE HTML LINKS, AND URLS ARE THE URLS THAT THE HREF VALUE
		============================================================ */
		// TODO rename for URL()
		public function generate_trackingrequesturl(Order $order) {
			$url = new Url($this->generate_trackingrequesturltrait($order));
			$url->query->set('page', $this->pagenbr);
			$url->query->set('orderby', $this->tablesorter->orderbystring);
			$url->query->set('type', 'history');
			return $url->getUrl();
		}

		/* =============================================================
			OrderDisplayInterface Functions
			LINKS ARE HTML LINKS, AND URLS ARE THE URLS THAT THE HREF VALUE
		============================================================ */
		public function generate_request_documentsURL(Order $order, OrderDetail $orderdetail = null) {
			$url = new Url($this->generate_documentsrequestURLtrait($order, $orderdetail));
			$url->query->set('page', $this->pagenbr);
			$url->query->set('orderby', $this->tablesorter->orderbystring);
			$url->query->set('type', 'history');
			return $url->getUrl();
		}
	}
