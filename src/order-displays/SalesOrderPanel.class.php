<?php 	
	class SalesOrderPanel extends OrderPanel implements OrderDisplayInterface, SalesOrderDisplayInterface, OrderPanelInterface, SalesOrderPanelInterface {
		use SalesOrderDisplayTraits;
		
		/**
		 * Array of SalesOrders
		 * @var array
		 */
		public $orders = array();
		public $paneltype = 'sales-order';
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
			'ordertotal' => array(
				'querytype' => 'between',
				'datatype' => 'numeric',
				'label' => 'Order Total'
			),
			'orderdate' => array(
				'querytype' => 'between',
				'datatype' => 'date',
				'label' => 'Order Date'
			),
			'status' => array(
				'querytype' => 'in',
				'datatype' => 'char',
				'label' => 'Status'
			),
			'salesperson_1' => array(
				'querytype' => 'in',
				'datatype' => 'char',
				'label' => 'Sales Person 1'
			)
		);
		
		public function __construct($sessionID, \Purl\Url $pageurl, $modal, $loadinto, $ajax) {
			parent::__construct($sessionID, $pageurl, $modal, $loadinto, $ajax);
			$this->pageurl = new Purl\Url($pageurl->getUrl());
			$this->setup_pageurl();
		}
		
		/* =============================================================
			SalesOrderPanelInterface Functions
		============================================================ */
		public function get_ordercount($debug = false) {
			$count = count_userorders($this->sessionID, $this->filters, $this->filterable, $debug);
			return $debug ? $count : $this->count = $count;
		}
		
		public function get_orders($debug = false) {
			$useclass = true;
			if ($this->tablesorter->orderby) {
				if ($this->tablesorter->orderby == 'orderdate') {
					$orders = get_userordersorderdate($this->sessionID, DplusWire::wire('session')->display, $this->pagenbr, $this->tablesorter->sortrule, $this->filters, $this->filterable, $useclass, $debug);
				} else {
					$orders = get_userordersorderby($this->sessionID, DplusWire::wire('session')->display, $this->pagenbr, $this->tablesorter->sortrule, $this->tablesorter->orderby, $this->filters, $this->filterable, $useclass, $debug);
				}
			} else {
				// DEFAULT BY ORDER DATE SINCE SALES ORDER # CAN BE ROLLED OVER
				$this->tablesorter->sortrule = 'DESC'; 
				//$this->tablesorter->orderby = 'orderno';
				//$orders = get_salesrepordersorderby($this->sessionID, DplusWire::wire('session')->display, $this->pagenbr, $this->tablesorter->sortrule, $this->tablesorter->orderby, $useclass, $debug);
				$orders = get_userordersorderdate($this->sessionID, DplusWire::wire('session')->display, $this->pagenbr, $this->tablesorter->sortrule, $this->filters, $this->filterable, $useclass, $debug);
			}
			return $debug ? $orders : $this->orders = $orders;
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
		
		public function generate_expandorcollapselink(Order $order) {
			$bootstrap = new HTMLWriter();
			
			if ($order->orderno == $this->activeID) {
				$href = $this->generate_closedetailsurl($order);
				$ajaxdata = $this->generate_ajaxdataforcontento();
				$addclass = 'load-link';
				$icon = '-';
			} else {
				$href = $this->generate_loaddetailsurl($order);
				$ajaxdata = "data-loadinto=$this->loadinto|data-focus=#$order->orderno";
				$addclass = 'generate-load-link';
				$icon = '+';
			}
			return $bootstrap->create_element('a', "href=$href|class=btn btn-sm btn-primary $addclass|$ajaxdata", $icon);
		}
		
		public function generate_rowclass(Order $order) {
			return ($this->activeID == $order->orderno) ? 'selected' : '';
		}
		
		public function generate_loadurl() { 
			$url = new \Purl\Url($this->pageurl->getUrl());
			$url->path = DplusWire::wire('config')->pages->orders.'redir/';
			$url->query->setData(array('action' => 'load-orders'));
			return $url->getUrl();
		}
		
		public function generate_refreshlink() {
			$bootstrap = new HTMLWriter();
			$href = $this->generate_loadurl();
			$icon = $bootstrap->icon('fa fa-refresh');
			$ajaxdata = $this->generate_ajaxdataforcontento();
			return $bootstrap->create_element('a', "href=$href|class=generate-load-link|$ajaxdata", "$icon Refresh Orders");
		}
		
		public function generate_closedetailsurl() { 
			$url = new \Purl\Url($this->pageurl->getUrl());
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
			$url = new \Purl\Url($this->generate_loaddetailsurltrait($order));
			$url->query->set('page', $this->pagenbr);
			$url->query->set('orderby', $this->tablesorter->orderbystring);
			
			if (!empty($this->filters)) {
				$url->query->set('filter', 'filter');
				foreach ($this->filters as $filter => $value) {
					$url->query->set($filter, implode('|', $value));
				}
			}
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
			$id = $order->orderno.'-'.$detail->itemid.'-form';
			$form = new FormMaker("method=post|action=$action|class=item-reorder|id=$id");
			$form->input("type=hidden|name=action|value=add-to-cart");
			$form->input("type=hidden|name=ordn|value=$order->orderno");
			$form->input("type=hidden|name=custID|value=$order->custid");
			$form->input("type=hidden|name=itemID|value=$detail->itemid");
			$form->input("type=hidden|name=qty|value=".intval($detail->qty));
			$form->input("type=hidden|name=desc|value=$detail->desc1");
			$form->button("type=submit|class=btn btn-primary btn-xs", $form->bootstrap->icon('glyphicon glyphicon-shopping-cart'). $form->bootstrap->create_element('span', 'class=sr-only', 'Submit Reorder'));
			return $form->finish();
		}
		
		public function generate_filter(ProcessWire\WireInput $input) {
			$stringerbell = new StringerBell();
			parent::generate_filter($input);
			
			if (isset($this->filters['orderdate'])) {
				if (empty($this->filters['orderdate'][0])) {
					$this->filters['orderdate'][0] = date('m/d/Y', strtotime(get_minorderdate($this->sessionID, 'orderdate')));
				}
				
				if (empty($this->filters['orderdate'][1])) {
					$this->filters['orderdate'][1] = date('m/d/Y');
				}
			}
			
			if (isset($this->filters['ordertotal'])) {
				if (!strlen($this->filters['ordertotal'][0])) {
					$this->filters['ordertotal'][0] = '0.00';
				}
				
				for ($i = 0; $i < (sizeof($this->filters['ordertotal']) + 1); $i++) {
					if (isset($this->filters['ordertotal'][$i])) {
						if (strlen($this->filters['ordertotal'][$i])) {
							$this->filters['ordertotal'][$i] = number_format($this->filters['ordertotal'][$i], 2, '.', '');
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
			$url = new \Purl\Url($this->generate_trackingrequesturltrait($order));
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
			$url = new \Purl\Url($this->generate_documentsrequesturltrait($order, $orderdetail));
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
			} elseif ($order->editord == 'L') {
				if (DplusWire::wire('user')->hasorderlocked) {
					if ($order->orderno == DplusWire::wire('user')->lockedordn) {
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
			$href = $this->generate_editurl($order);
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
