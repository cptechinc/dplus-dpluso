<?php
	use Dplus\ProcessWire\DplusWire as DplusWire;
	
	class EditSalesOrderDisplay extends SalesOrderDisplay {
		use SalesOrderDisplayTraits;
		
		public function __construct($sessionID, \Purl\Url $pageurl, $modal, $ordn) {
			parent::__construct($sessionID, $pageurl, $modal, $ordn);
		}
		
		/* =============================================================
			GETTER FUNCTIONS
		============================================================ */
		/**
		 * Returns EditableSales Order from database
		 * @param  bool             $debug Run in debug? If So, returns SQL Query 
		 * @return SalesOrderEdit          Sales Order
		 */
		public function get_order($debug = false) {
			$ordn = str_pad($this->ordn, 10, '0', STR_PAD_LEFT);
			return SalesOrderEdit::load($this->sessionID, $ordn, $debug);
		}
		
		/**
		 * Returns Credit Card details for this Sales Order
		 * @param  bool   $debug    Run in Debug? If so, will return SQL Query
		 * @return OrderCreditCard  Credit Card Details
		 */
		public function get_creditcard($debug = false) {
			return get_orderhedcreditcard($this->sessionID, $this->ordn, $debug);
		}
		
		public function showhide_creditcard(Order $order) {
			return ($order->paymenttype == 'cc') ? '' : 'hidden';
		}
		
		public function showhide_phoneintl(Order $order) {
			return $order->is_phoneintl() ? '' : 'hidden';
		}
		
		public function showhide_phonedomestic(Order $order) {
			return $order->is_phoneintl() ? 'hidden' : '';
		}

		/* =============================================================
			CLASS FUNCTIONS
		============================================================ */
		public function generate_unlockurl(Order $order) {
			$url = $this->generate_ordersredirurl();
			$url->query->set('action', 'unlock-order');
			$url->query->set('ordn', $order->ordernumber);
			return $url->getUrl();
		}

		public function generate_confirmationurl(Order $order) {
			$url = new \Purl\Url(DplusWire::wire('config')->pages->confirmorder);
			$url->query->set('ordn', $order->ordernumber);
			return $url->getUrl();
		}

		public function generate_discardchangeslink(Order $order) {
			$bootstrap = new Dplus\Content\HTMLWriter();
			$href = $this->generate_unlockurl($order);
			$icon = $bootstrap->icon('glyphicon glyphicon-floppy-remove');
			return $bootstrap->create_element('a', "href=$href|class=btn btn-block btn-warning", $icon. " Discard Changes, Unlock Order");
		}

		public function generate_saveunlocklink(Order $order) {
			$bootstrap = new Dplus\Content\HTMLWriter();
			$href = $this->generate_unlockurl($order);
			$icon = $bootstrap->icon('fa fa-unlock');
			return $bootstrap->create_element('a', "href=$href|class=btn btn-block btn-emerald save-unlock-order|data-form=#orderhead-form", $icon. " Save and Exit");
		}

		public function generate_confirmationlink(Order $order) {
			$href = $this->generate_confirmationurl($order);
			$bootstrap = new Dplus\Content\HTMLWriter();
			$href = $this->generate_unlockurl($order);
			$icon = $bootstrap->icon('fa fa-arrow-right');
			return $bootstrap->create_element('a', "href=$href|class=btn btn-block btn-success", $icon. " Finished with Order");
		}

		public function generate_detailvieweditlink(Order $order, OrderDetail $detail) {
			$bootstrap = new Dplus\Content\HTMLWriter();
			$href = $this->generate_detailviewediturl($order, $detail);
			if ($order->can_edit()) {
				$icon = $bootstrap->icon('glyphicon glyphicon-pencil');
				return $bootstrap->create_element('a', "href=$href|class=btn btn-sm btn-warning update-line|title=Edit Line|data-kit=$detail->kititemflag|data-itemid=$detail->itemid|data-custid=$order->custid|aria-label=View Detail Line", $icon);
			} else {
				$icon = $bootstrap->icon('fa fa-eye');
				return $bootstrap->a("href=$href|class=update-line|title=Edit Line|data-kit=$detail->kititemflag|data-itemid=$detail->itemid|data-custid=$order->custid|aria-label=View Detail Line", $icon);
			}
		}

		/**
		 * Returns HTML Link to delete detail line
		 * @param  Order       $order  Order
		 * @param  OrderDetail $detail OrderDetail
		 * @return string              HTML Link to delete detail line
		 */
		public function generate_deletedetaillink(Order $order, OrderDetail $detail) {
			$bootstrap = new Dplus\Content\HTMLWriter();
			$icon = $bootstrap->icon('fa fa-trash') . $bootstrap->create_element('span', 'class=sr-only', 'Delete Line');
			$url = $this->generate_ordersredirurl();
			$url->query->setData(array('action' => 'remove-line-get', 'ordn' => $order->ordernumber, 'linenbr' => $detail->linenbr, 'page' => $this->pageurl->getUrl()));
			$href = $url->getUrl();
			return $bootstrap->a("href=$href|class=btn btn-sm btn-danger|title=Delete Item", $icon);
		}

		public function generate_readonlyalert() {
			$bootstrap = new Dplus\Content\HTMLWriter();
			$msg = $bootstrap->create_element('b', '', 'Attention!') . ' This order will open in read-only mode, you will not be able to save changes.';
			return $bootstrap->alertpanel('warning', $msg);
		}

		public function generate_erroralert($order) {
			$bootstrap = new Dplus\Content\HTMLWriter();
			$msg = $bootstrap->create_element('b', '', 'Error!') .' '. $order->errormsg;
			return $bootstrap->alertpanel('danger', $msg, false);
		}

		/* =============================================================
			OrderDisplayInterface Functions
			LINKS ARE HTML LINKS, AND URLS ARE THE URLS THAT THE HREF VALUE
		============================================================ */

		/**
		 * Overrides SalesOrderDisplayTraits
		 * Makes a button link to request dplus notes
		 * @param  Order  $order
		 * @param  string $linenbr 0 for header, anything else is detail line #
		 * @return string		  html for button link
		 */
		public function generate_loaddplusnoteslink(Order $order, $linenbr = '0') {
			$bootstrap = new Dplus\Content\HTMLWriter();
			$href = $this->generate_dplusnotesrequesturl($order, $linenbr);

			if ($order->can_edit()) {
				$title = ($order->has_notes()) ? "View and Create Order Notes" : "Create Order Notes";
			} else {
				$title = ($order->has_notes()) ? "View Order Notes" : "View Order Notes";
			}

			if (intval($linenbr) > 0) {
				$content = $bootstrap->icon('material-icons md-36', '&#xE0B9;');
				$link = $bootstrap->create_element('a', "href=$href|class=load-notes|title=$title|data-modal=$this->modal", $content);
			} else {
				$content = $bootstrap->icon('material-icons', '&#xE0B9;') . ' ' . $title;
				$link = $bootstrap->create_element('a', "href=$href|class=btn btn-default load-notes|title=$title|data-modal=$this->modal", $content);
			}
			return $link;
		}
	}
