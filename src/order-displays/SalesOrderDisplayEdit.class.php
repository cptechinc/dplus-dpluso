<?php
	class EditSalesOrderDisplay extends SalesOrderDisplay {
		use SalesOrderDisplayTraits;

		protected $ordn;
		protected $modal;
		public $canedit;


		public function __construct($sessionID, \Purl\Url $pageurl, $modal, $ordn) {
			parent::__construct($sessionID, $pageurl, $modal, $ordn);
		}

		/* =============================================================
			Class Functions
		============================================================ */
		public function generate_unlockurl(Order $order) {
			$url = $this->generate_ordersredirurl();
			$url->query->set('action', 'unlock-order');
			$url->query->set('ordn', $order->orderno);
			return $url->getUrl();
		}

		public function generate_confirmationurl(Order $order) {
			$url = new \Purl\Url(Processwire\wire('config')->pages->confirmorder);
			$url->query->set('ordn', $order->orderno);
			return $url->getUrl();
		}

		public function generate_discardchangeslink(Order $order) {
			$bootstrap = new HTMLWriter();
			$href = $this->generate_unlockurl($order);
			$icon = $bootstrap->icon('glyphicon glyphicon-floppy-remove');
			return $bootstrap->create_element('a', "href=$href|class=btn btn-block btn-warning", $icon. " Discard Changes, Unlock Order");
		}

		public function generate_saveunlocklink(Order $order) {
			$bootstrap = new HTMLWriter();
			$href = $this->generate_unlockurl($order);
			$icon = $bootstrap->icon('fa fa-unlock');
			return $bootstrap->create_element('a', "href=$href|class=btn btn-block btn-emerald save-unlock-order|data-form=#orderhead-form", $icon. " Save and Exit");
		}

		public function generate_confirmationlink(Order $order) {
			$href = $this->generate_confirmationurl($order);
			$bootstrap = new HTMLWriter();
			$href = $this->generate_unlockurl($order);
			$icon = $bootstrap->icon('fa fa-arrow-right');
			return $bootstrap->create_element('a', "href=$href|class=btn btn-block btn-success", $icon. " Finished with Order");
		}

		public function generate_detailvieweditlink(Order $order, OrderDetail $detail) {
			$bootstrap = new HTMLWriter();
			$href = $this->generate_detailviewediturl($order, $detail);
			$icon = $bootstrap->icon('glyphicon glyphicon-pencil');
			return $bootstrap->create_element('a', "href=$href|class=btn btn-sm btn-warning update-line|title=Edit Line|data-kit=$detail->kititemflag|data-itemid=$detail->itemid|data-custid=$order->custid|aria-label=View Detail Line", $icon);
		}

		/**
		 * Returns HTML Link to delete detail line
		 * @param  Order       $order  Order
		 * @param  OrderDetail $detail OrderDetail
		 * @return string              HTML Link to delete detail line
		 */
		public function generate_deletedetaillink(Order $order, OrderDetail $detail) {
			$bootstrap = new HTMLWriter();
			$icon = $bootstrap->icon('glyphicon glyphicon-trash') . $bootstrap->create_element('span', 'class=sr-only', 'Delete Line');
			$url = $this->generate_ordersredirurl();
			$url->query->setData(array('action' => 'remove-line-get', 'ordn' => $order->orderno, 'linenbr' => $detail->linenbr, 'page' => $this->pageurl->getUrl()));
			$href = $url->getUrl();
			return $bootstrap->a("href=$href|class=btn btn-sm btn-danger|title=Delete Item", $icon);
		}

		public function generate_readonlyalert() {
			$bootstrap = new HTMLWriter();
			$msg = $bootstrap->create_element('b', '', 'Attention!') . ' This order will open in read-only mode, you will not be able to save changes.';
			return $bootstrap->alertpanel('warning', $msg);
		}

		public function generate_erroralert($order) {
			$bootstrap = new HTMLWriter();
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
			$bootstrap = new HTMLWriter();
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
