<?php
	namespace Dplus\Dpluso\OrderDisplays;

	use Purl\Url;
	use ProcessWire\WireInput;
	use Dplus\ProcessWire\DplusWire;
	use Dplus\Content\HTMLWriter;
	use Dplus\Content\FormMaker;

	/**
	 * Use Statements for Model Classes which are non-namespaced
	 */
	use Order, OrderDetail;

	class SalesOrderPanel extends OrderPanel implements OrderDisplayInterface, SalesOrderDisplayInterface, OrderPanelInterface, SalesOrderPanelInterface {
		use SalesOrderDisplayTraits;

		/**
		 * Array of SalesOrders
		 * @var array
		 */
		protected $orders = array();
		protected $paneltype = 'sales-order';
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
			),
			'holdstatus' => array(
				'querytype' => 'in',
				'datatype' => 'char',
				'label' => 'Hold Status'
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
			$count = count_salesorders($this->filters, $this->filterable, $debug);
			return $debug ? $count : $this->count = $count;
		}

		public function get_orders($debug = false) {
			if ($this->tablesorter->orderby) {
				$orders = get_salesorders_orderby(DplusWire::wire('session')->display, $this->pagenbr, $this->tablesorter->sortrule, $this->tablesorter->orderby, $this->filters, $this->filterable, $useclass = true, $debug);
			} else {
				// DEFAULT BY ORDER DATE SINCE SALES ORDER # CAN BE ROLLED OVER
				$this->tablesorter->orderby = 'order_date';
				$this->tablesorter->sortrule = 'DESC';
				$orders = get_salesorders_orderby(DplusWire::wire('session')->display, $this->pagenbr, $this->tablesorter->sortrule, $this->tablesorter->orderby, $this->filters, $this->filterable, $useclass = true, $debug);
			}
			return $debug ? $orders : $this->orders = $orders;
		}

		/**
		 * Returns the Max Sales Order Total
		 * @param  bool   $debug Return SQL Query?
		 * @return float         Max Sales Order Total
		 */
		public function get_maxsalesordertotal($debug = false) {
			return get_maxsalesordertotal($custID = '', $shipID = '', $this->filters, $this->filterable, $debug);
		}

		/**
		 * Returns the Min Sales Order Total
		 * @param  bool   $debug Return SQL Query?
		 * @return float         Min Sales Order Total
		 */
		public function get_minsalesordertotal($debug = false) {
			return get_minsalesordertotal($custID = '', $shipID = '', $this->filters, $this->filterable, $debug);
		}

		/**
		 * REturns the Min Sales Order Date field value for $field
		 * @param  string $field Date Column to return Min Date
		 * @param  bool   $debug Run in debug? If so, return SQL Query
		 * @return string        Min $field Date
		 */
		public function get_mindate($field = 'order_date', $debug = false) {
			return get_minsalesorderdate($field, $custID = '', $shipID = '', $this->filters, $this->filterable, $debug);
		}

		/* =============================================================
			OrderPanelInterface Functions
			LINKS ARE HTML LINKS, AND URLS ARE THE URLS THAT THE HREF VALUE
		============================================================ */
		// TODO rename for URL()
		public function setup_pageURL() {
			$this->pageurl->path = DplusWire::wire('config')->pages->ajax."load/sales-orders/";
			$this->pageurl->query->remove('display');
			$this->pageurl->query->remove('ajax');
			$this->paginationinsertafter = 'sales-orders';
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

		public function generate_iconlegend() {
			$bootstrap = new HTMLWriter();
			$content = $bootstrap->i('class=fa fa-shopping-cart|title=Re-order Icon', '') . ' = Re-order <br>';
			$content .= $bootstrap->i("class=material-icons|title=Documents Icon", '&#xE873;') . '&nbsp; = Documents <br>';
			$content .= $bootstrap->i('class=fa fa-plane hover|title=Tracking Icon', '') . ' = Tracking <br>';
			$content .= $bootstrap->i('class=material-icons|title=Notes Icon', '&#xE0B9;') . ' = Notes <br>';
			$content .= $bootstrap->i('class=fa fa-pencil|title=Edit Order Icon', '') . ' = Edit Order <br>';
			$content = str_replace('"', "'", $content);
			$attr = "tabindex=0|role=button|class=btn btn-sm btn-info|data-toggle=popover|data-placement=bottom|data-trigger=focus";
			$attr .= "|data-html=true|title=Icons Definition|data-content=$content";
			return $bootstrap->a( $attr, 'Icon Definitions');
		}
		
		// TODO rename for URL()
		public function generate_request_detailsURL(Order $order) {
			$pageurl = new Url($this->pageurl->getUrl());
			$pageurl->query->set('ordn', $order->ordernumber);
			$url = new Url($this->generate_loaddetailsURLtrait($order));
			$url->query->set('page', $pageurl->getUrl());
			return $url->getUrl();
		}

		public function generate_lastloadeddescription() {
			if (DplusWire::wire('session')->{'orders-loaded-for'}) {
				if (DplusWire::wire('session')->{'orders-loaded-for'} == DplusWire::wire('user')->loginid) {
					return 'Last Updated : ' . DplusWire::wire('session')->{'orders-updated'};
				}
			}
			return '';
		}

		/**
		 * Returns HTML form for reordering SalesOrderDetails
		 * @param  Order       $order  SalesOrder
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
					$this->filters['order_date'][0] = date('m/d/Y', strtotime($this->get_mindate('order_date')));
				}

				if (empty($this->filters['order_date'][1])) {
					$this->filters['order_date'][1] = date('m/d/Y');
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
		// FIXME Remove, and make link at presentation level
		public function generate_loadtrackinglink(Order $order) {
			$bootstrap = new HTMLWriter();
			if ($order->has_tracking()) {
				$href = $this->generate_trackingrequesturl($order);
				$content = $bootstrap->span("class=sr-only", 'View Tracking');
				$content .= $bootstrap->icon('fa fa-plane hover');
				$ajaxdata = $this->generate_ajaxdataforcontento();
				return $bootstrap->a("href=$href|class=h3 generate-load-link|title=Click to view Tracking|$ajaxdata", $content);
			} else {
				$content = $bootstrap->span("class=sr-only", 'No Tracking Information Available');
				$content .= $bootstrap->icon('fa fa-plane hover');
				return $bootstrap->a("href=#|class=h3 text-muted|title=No Tracking Info Available", $content);
			}
		}
		
		// TODO rename for URL()
		public function generate_trackingrequesturl(Order $order) {
			$url = new Url($this->generate_trackingrequesturltrait($order));
			$url->query->set('page', $this->pagenbr);
			$url->query->set('orderby', $this->tablesorter->orderbystring);
			return $url->getUrl();
		}

		/* =============================================================
			OrderDisplayInterface Functions
			URLS ARE THE URLS THAT THE HREF VALUE
		============================================================ */
		public function generate_request_documentsURL(Order $order, OrderDetail $orderdetail = null) {
			$url = new Url($this->generate_documentsrequestURLtrait($order, $orderdetail));
			$url->query->set('page', $this->pagenbr);
			$url->query->set('orderby', $this->tablesorter->orderbystring);
			return $url->getUrl();
		}
	}
