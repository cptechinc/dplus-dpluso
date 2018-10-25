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
			)
		);

		public function __construct($sessionID, Url $pageurl, $modal, $loadinto, $ajax) {
			parent::__construct($sessionID, $pageurl, $modal, $loadinto, $ajax);
			$this->pageurl = new Url($pageurl->getUrl());
			$this->setup_pageurl();
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
		public function setup_pageurl() {
			$this->pageurl->path = DplusWire::wire('config')->pages->ajax."load/sales-orders/";
			$this->pageurl->query->remove('display');
			$this->pageurl->query->remove('ajax');
			$this->paginationinsertafter = 'sales-orders';
		}

		public function generate_loadurl() {
			$url = new Url($this->pageurl);
			$url->query->remove('filter');
			foreach (array_keys($this->filterable) as $filtercolumns) {
				$url->query->remove($filtercolumns);
			}
			return $url->getUrl();
		}

		public function generate_closedetailsurl() {
			$url = new Url($this->pageurl->getUrl());
			$url->query->setData(array('ordn' => false, 'show' => false));
			return $url->getUrl();
		}

		public function generate_iconlegend() {
			$bootstrap = new HTMLWriter();
			$content = $bootstrap->create_element('i', 'class=glyphicon glyphicon-shopping-cart|title=Re-order Icon', '') . ' = Re-order <br>';
			$content .= $bootstrap->create_element('i', "class=material-icons|title=Documents Icon", '&#xE873;') . '&nbsp; = Documents <br>'; 
			$content .= $bootstrap->create_element('i', 'class=glyphicon glyphicon-plane hover|title=Tracking Icon', '') . ' = Tracking <br>';
			$content .= $bootstrap->create_element('i', 'class=material-icons|title=Notes Icon', '&#xE0B9;') . ' = Notes <br>';
			$content .= $bootstrap->create_element('i', 'class=glyphicon glyphicon-pencil|title=Edit Order Icon', '') . ' = Edit Order <br>'; 
			$content = str_replace('"', "'", $content);
			$attr = "tabindex=0|role=button|class=btn btn-sm btn-info|data-toggle=popover|data-placement=bottom|data-trigger=focus";
			$attr .= "|data-html=true|title=Icons Definition|data-content=$content";
			return $bootstrap->create_element('a', $attr, 'Icon Definitions');
		}

		public function generate_loaddetailsurl(Order $order) {
			$pageurl = new Url($this->pageurl->getUrl());
			$pageurl->query->set('ordn', $order->ordernumber);
			$url = new Url($this->generate_loaddetailsurltrait($order));
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
			$form->button("type=submit|class=btn btn-primary btn-xs", $form->bootstrap->icon('glyphicon glyphicon-shopping-cart'). $form->bootstrap->create_element('span', 'class=sr-only', 'Submit Reorder'));
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
		public function generate_loadtrackinglink(Order $order) { 
			$bootstrap = new HTMLWriter();
			if ($order->has_tracking()) {
				$href = $this->generate_trackingrequesturl($order);
				$content = $bootstrap->create_element('span', "class=sr-only", 'View Tracking');
				$content .= $bootstrap->icon('glyphicon glyphicon-plane hover');
				$ajaxdata = $this->generate_ajaxdataforcontento();
				return $bootstrap->create_element('a', "href=$href|class=h3 generate-load-link|title=Click to view Tracking|$ajaxdata", $content);
			} else {
				$content = $bootstrap->create_element('span', "class=sr-only", 'No Tracking Information Available');
				$content .= $bootstrap->icon('glyphicon glyphicon-plane hover');
				return $bootstrap->create_element('a', "href=#|class=h3 text-muted|title=No Tracking Info Available", $content);
			}
		}

		public function generate_trackingrequesturl(Order $order) {
			$url = new Url($this->generate_trackingrequesturltrait($order));
			$url->query->set('page', $this->pagenbr);
			$url->query->set('orderby', $this->tablesorter->orderbystring);
			return $url->getUrl();
		}

		/* =============================================================
			OrderDisplayInterface Functions
			LINKS ARE HTML LINKS, AND URLS ARE THE URLS THAT THE HREF VALUE
		============================================================ */
		public function generate_loaddplusnoteslink(Order $order, $linenbr = '0') {
			$bootstrap = new HTMLWriter();
			$href = $this->generate_dplusnotesrequesturl($order, $linenbr);

			if ($order->can_edit()) {
				$title = ($order->has_notes()) ? "View and Create Order Notes" : "Create Order Notes";
				$addclass = ($order->has_notes()) ? '' : 'text-muted';
			} else {
				$title = ($order->has_notes()) ? "View Order Notes" : "View Order Notes";
				$addclass = ($order->has_notes()) ? '' : 'text-muted';
			}
			$content = $bootstrap->icon('material-icons md-36', '&#xE0B9;');
			$link = $bootstrap->create_element('a', "href=$href|class=load-notes $addclass|title=$title|data-modal=$this->modal", $content);
			return $link;
		}

		public function generate_loaddocumentslink(Order $order, OrderDetail $orderdetail = null) {
			$bootstrap = new HTMLWriter();
			$href = $this->generate_documentsrequesturl($order, $orderdetail);
			$icon = $bootstrap->icon('fa fa-file-text');
			$ajaxdata = $this->generate_ajaxdataforcontento();
			$documentsTF = ($orderdetail) ? $orderdetail->has_documents() : $order->has_documents();
			if ($documentsTF) {
				return $bootstrap->create_element('a', "href=$href|class=h3 generate-load-link|title=Click to view Documents|$ajaxdata", $icon);
			} else {
				return $bootstrap->create_element('a', "href=#|class=h3 text-muted|title=No Documents Available", $icon);
			}
		}

		public function generate_documentsrequesturl(Order $order, OrderDetail $orderdetail = null) {
			$url = new Url($this->generate_documentsrequesturltrait($order, $orderdetail));
			$url->query->set('page', $this->pagenbr);
			$url->query->set('orderby', $this->tablesorter->orderbystring);
			return $url->getUrl();
		}

		public function generate_editlink(Order $order) {
			$bootstrap = new HTMLWriter();
			/*
				ORDER LOCK LOGIC
				-------------------------------------
				N = PICKED, INVOICED, ETC CANNOT EDIT
				Y = CAN EDIT
				L = YOU'VE LOCKED THIS ORDER
			*/

			if ($order->can_edit()) {
				$icon = $bootstrap->icon('glyphicon glyphicon-pencil');
				$title = "Edit this Order";
			} elseif ($order->is_lockedbyuser()) {
				if (DplusWire::wire('user')->hasorderlocked) {
					if ($order->ordernumber == DplusWire::wire('user')->lockedordn) {
						$icon = $bootstrap->icon('glyphicon glyphicon-wrench');
						$title = "Edit this Order";
					} else {
						$icon = $bootstrap->icon('material-icons md-36', '&#xE897;');
						$title = "You have this order locked, but you can still view it";
					}
				} else {
					$icon = $bootstrap->icon('material-icons md-36', '&#xE897;');
					$title = "You have this order locked, but you can still view it";
				}
			} else {
				$icon = $bootstrap->icon('glyphicon glyphicon-eye-open');
				$title = "Open in read-only mode";
			}
			$url = new Url($this->generate_editurl($order));
			
			if ($order->can_edit() || $order->is_lockedbyuser()) {
				$url->query->set('edit', 'edit');
			}
			$href = $url->getUrl();
			return $bootstrap->create_element('a', "href=$href|class=edit-order h3|title=$title", $icon);
		}

		public function generate_viewlinkeduseractionslink(Order $order) {
			$bootstrap = new HTMLWriter();
			$href = $this->generate_viewlinkeduseractionsurl($order);
			$icon = $bootstrap->create_element('span','class=h3', $bootstrap->icon('glyphicon glyphicon-check'));
			return $bootstrap->create_element('a', "href=$href|class=load-into-modal|data-modal=$this->modal", $icon." View Associated Actions");
		}

		public function generate_detailvieweditlink(Order $order, OrderDetail $detail) {
			$bootstrap = new HTMLWriter();
			$href = $this->generate_detailviewediturl($order, $detail);
			return $bootstrap->create_element('a', "href=$href|class=update-line|data-kit=$detail->kititemflag|data-itemid=$detail->itemid|data-custid=$order->custid|aria-label=View Detail Line", $detail->itemid);	
		}
	}
