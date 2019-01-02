<?php
	namespace Dplus\Dpluso\OrderDisplays;
	
	use Purl\Url;
	use Dplus\ProcessWire\DplusWire;
	use Dplus\Content\HTMLWriter;

	/**
	 * Use Statements for Model Classes which are non-namespaced
	 */
	use Order, OrderDetail;
	use SalesOrderEdit, OrderCreditCard;

	class EditSalesOrderDisplay extends SalesOrderDisplay {
		use SalesOrderDisplayTraits;

		public function __construct($sessionID, Url $pageurl, $modal, $ordn) {
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
		
		/**
		 * Returns if the credit card html should have a class of hidden
		 * // DEPRECATE 
		 * @param  Order  $order Sales Order
		 * @return string
		 */
		public function showhide_creditcard(Order $order) {
			return ($order->paymenttype == 'cc') ? '' : 'hidden';
		}
		
		/**
		 * Returns if the international phone html should have a class of hidden
		 * // DEPRECATE 
		 * @param  Order  $order Sales Order
		 * @return string
		 */
		public function showhide_phoneintl(Order $order) {
			return $order->is_phoneintl() ? '' : 'hidden';
		}
		
		/**
		 * Returns if the deomestic phone html should have a class of hidden
		 * // DEPRECATE 
		 * @param  Order  $order Sales Order
		 * @return string
		 */
		public function showhide_phonedomestic(Order $order) {
			return $order->is_phoneintl() ? 'hidden' : '';
		}

		/* =============================================================
			CLASS FUNCTIONS
		============================================================ */
		/**
		 * Returns URL to unlock Sales Order
		 * @param  Order  $order Sales Order
		 * @return string        Unlock Sales Order URL
		 */
		public function generate_unlockurl(Order $order) {
			$url = $this->generate_ordersredirurl();
			$url->query->set('action', 'unlock-order');
			$url->query->set('ordn', $order->ordernumber);
			return $url->getUrl();
		}
		
		/**
		 * Returns URL to Sales Order confirmation page
		 * @param  Order  $order Sales Order
		 * @return string        Sales Order confirmation page URL
		 */
		public function generate_confirmationURL(Order $order) {
			$url = new Url(DplusWire::wire('config')->pages->confirmorder);
			$url->query->set('ordn', $order->ordernumber);
			return $url->getUrl();
		}
		
		/**
		 * Returns HTML Link to discard changes
		 * @param  Order  $order Sales Order
		 * @return string        HTML Link to discard changes
		 */
		public function generate_discardchangeslink(Order $order) {
			$bootstrap = new HTMLWriter();
			$href = $this->generate_unlockurl($order);
			$icon = $bootstrap->icon('fa fa-times');
			return $bootstrap->a("href=$href|class=btn btn-block btn-warning", $icon. " Discard Changes, Unlock Order");
		}
		
		
		/**
		 * Returns URL to delete detail line
		 * // TODO rename for URL()
		 * @param  Order       $order  Order
		 * @param  OrderDetail $detail OrderDetail
		 * @return string              HTML Link to delete detail line
		 */
		public function generate_deletedetailurl(Order $order, OrderDetail $detail) {
			$url = $this->generate_ordersredirurl();
			$url->query->setData(array('action' => 'remove-line-get', 'ordn' => $order->ordernumber, 'linenbr' => $detail->linenbr, 'page' => $this->pageurl->getUrl()));
			return $url->getUrl();
		}

		/**
		 * Returns HTML Link to delete detail line
		 * // TODO REMOVE
		 * @param  Order       $order  Order
		 * @param  OrderDetail $detail OrderDetail
		 * @return string              HTML Link to delete detail line
		 */
		public function generate_deletedetaillink(Order $order, OrderDetail $detail) {
			$bootstrap = new HTMLWriter();
			$icon = $bootstrap->icon('fa fa-trash') . $bootstrap->span('class=sr-only', 'Delete Line');
			$url = $this->generate_ordersredirurl();
			$url->query->setData(array('action' => 'remove-line-get', 'ordn' => $order->ordernumber, 'linenbr' => $detail->linenbr, 'page' => $this->pageurl->getUrl()));
			$href = $url->getUrl();
			return $bootstrap->a("href=$href|class=btn btn-sm btn-danger|title=Delete Item", $icon);
		}

		/* =============================================================
			OrderDisplayInterface Functions
			LINKS ARE HTML LINKS, AND URLS ARE THE URLS THAT THE HREF VALUE
		============================================================ */
	}
