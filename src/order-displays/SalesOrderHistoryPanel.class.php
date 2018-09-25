<?php
	class SalesOrderHistoryPanel extends SalesOrderPanel {
		/**
		 * Array of SalesOrderHistory
		 * @var array
		 */
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
		public function get_ordercount($loginID = '', $debug = false) {
			$count = count_usersaleshistory($this->filters, $this->filterable, $loginID, $debug);
			return $debug ? $count : $this->count = $count;
		}

		public function get_orders($loginID = '', $debug = false) {
			$useclass = true;
			if ($this->tablesorter->orderby) {
				if ($this->tablesorter->orderby == 'orderdate') {
					$orders = get_usersaleshistoryorderdate(DplusWire::wire('session')->display, $this->pagenbr, $this->tablesorter->sortrule, $this->filters, $this->filterable, $loginID, $useclass, $debug);
				} elseif ($this->tablesorter->orderby == 'invoice_date') {
					$orders = get_usersaleshistoryinvoicedate(DplusWire::wire('session')->display, $this->pagenbr, $this->tablesorter->sortrule, $this->filters, $this->filterable, $loginID, $useclass, $debug);
				} else {
					$orders = get_usersaleshistoryorderby(DplusWire::wire('session')->display, $this->pagenbr, $this->tablesorter->sortrule, $this->tablesorter->orderby, $this->filters, $this->filterable, $loginID, $useclass, $debug);
				}
			} else {
				// DEFAULT BY Invoice DATE SINCE SALES ORDER # CAN BE ROLLED OVER
				$this->tablesorter->sortrule = 'DESC';
				$orders = get_usersaleshistoryinvoicedate(DplusWire::wire('session')->display, $this->pagenbr, $this->tablesorter->sortrule, $this->filters, $this->filterable, $loginID, $useclass, $debug);
			}
			return $debug ? $orders : $this->orders = $orders;
		}

		/* =============================================================
			OrderPanelInterface Functions
			LINKS ARE HTML LINKS, AND URLS ARE THE URLS THAT THE HREF VALUE
		============================================================ */
		public function setup_pageurl() {
			$this->pageurl->path = DplusWire::wire('config')->pages->ajax."load/sales-history/";
			$this->pageurl->query->remove('display');
			$this->pageurl->query->remove('ajax');
			$this->paginationinsertafter = 'sales-history';
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
			$url = new \Purl\Url($this->pageurl);
			$url->query->remove('filter');
			foreach (array_keys($this->filterable) as $filtercolumns) {
				$url->query->remove($filtercolumns);
			}
			return $url->getUrl();
		}

		public function generate_clearsearchlink() {
			$bootstrap = new HTMLWriter();
			$href = $this->generate_loadurl();
			$icon = $bootstrap->icon('fa fa-search-minus');
			$ajaxdata = $this->generate_ajaxdataforcontento();
			return $bootstrap->create_element('a', "href=$href|class=load-link btn btn-warning btn-block|$ajaxdata", "Clear Search $icon");
		}

		public function generate_refreshlink() {
			$bootstrap = new HTMLWriter();
			$href = $this->generate_loadurl();
			$icon = $bootstrap->icon('fa fa-refresh');
			$ajaxdata = $this->generate_ajaxdataforcontento();
			return $bootstrap->create_element('a', "href=$href|class=load-and-show|$ajaxdata", "$icon Refresh History");
		}

		public function generate_closedetailsurl() {
			$url = new \Purl\Url($this->pageurl->getUrl());
			$url->query->setData(array('ordn' => false, 'show' => false));
			return $url->getUrl();
		}

		public function generate_loaddetailsurl(Order $order) {
			$url = new \Purl\Url($this->generate_loaddetailsurltrait($order));
			$url->query->set('page', $this->pagenbr);
			$url->query->set('orderby', $this->tablesorter->orderbystring);
			$url->query->set('type', 'history');

			if (!empty($this->filters)) {
				$url->query->set('filter', 'filter');
				foreach ($this->filters as $filter => $value) {
					$url->query->set($filter, implode('|', $value));
				}
			}
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
			$this->generate_defaultfilter($input);

			if (isset($this->filters['orderdate'])) {
				if (empty($this->filters['orderdate'][0])) {
					$this->filters['orderdate'][0] = DplusDateTime::format_date(get_minsaleshistoryorderdate($this->sessionID, 'orderdate'));
				}

				if (empty($this->filters['orderdate'][1])) {
					$this->filters['orderdate'][1] = date('m/d/Y');
				}
			}

			if (isset($this->filters['invoice_date'])) {
				if (empty($this->filters['invoice_date'][0])) {
					$this->filters['invoice_date'][0] = date('m/d/Y', strtotime(get_minsaleshistoryorderdate($this->sessionID, 'invoice_date')));
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
		public function generate_trackingrequesturl(Order $order) {
			$url = new \Purl\Url($this->generate_trackingrequesturltrait($order));
			$url->query->set('page', $this->pagenbr);
			$url->query->set('orderby', $this->tablesorter->orderbystring);
			$url->query->set('type', 'history');
			return $url->getUrl();
		}

		/* =============================================================
			OrderDisplayInterface Functions
			LINKS ARE HTML LINKS, AND URLS ARE THE URLS THAT THE HREF VALUE
		============================================================ */
		public function generate_documentsrequesturl(Order $order, OrderDetail $orderdetail = null) {
			$url = new \Purl\Url($this->generate_documentsrequesturltrait($order, $orderdetail));
			$url->query->set('page', $this->pagenbr);
			$url->query->set('orderby', $this->tablesorter->orderbystring);
			$url->query->set('type', 'history');
			return $url->getUrl();
		}
	}
