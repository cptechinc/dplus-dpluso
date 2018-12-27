<?php
	namespace Dplus\Dpluso\OrderDisplays;

	use Dplus\Content\HTMLWriter;

	// DEPRECATE

	/**
	 * Use Statements for Model Classes which are non-namespaced
	 */
	use SalesOrder, Order, OrderDetail;

	class SalesOrderDisplay extends OrderDisplay implements OrderDisplayInterface, SalesOrderDisplayInterface {
		use SalesOrderDisplayTraits;
		
		/**
		 * Sales Order Number
		 * @var string
		 */
		protected $ordn;
		
		/**
		 * Sales Order Number
		 * @var string
		 */
		protected $order;

		public function __construct($sessionID, \Purl\Url $pageurl, $modal, $ordn) {
			parent::__construct($sessionID, $pageurl, $modal);
			$this->ordn = $ordn;
		}

		/* =============================================================
			GETTER FUNCTIONS
		============================================================ */
		/**
		 * Returns Sales Order from database
		 * @param  bool             $debug Run in debug? If So, returns SQL Query
		 * @return SalesOrder        Sales Order
		 */
		public function get_order($debug = false) {
			return SalesOrder::load($this->ordn, $debug);
		}

		/* =============================================================
			OrderDisplayInterface Functions
			LINKS ARE HTML LINKS, AND URLS ARE THE URLS THAT THE HREF VALUE
		============================================================ */
		public function generate_documentsrequestURL(Order $order, OrderDetail $orderdetail = null) {
			return $this->generate_documentsrequestURLtrait($order, $orderdetail);
		}

		public function generate_detailvieweditlink(Order $order, OrderDetail $detail) {
			$bootstrap = new HTMLWriter();
			$href = $this->generate_detailviewediturl($order, $detail);
			$icon = $bootstrap->span('class=h3', $bootstrap->icon('fa fa-eye'));
			return $bootstrap->a("href=$href|class=update-line|data-kit=$detail->kititemflag|data-itemid=$detail->itemid|data-custid=$order->custid|aria-label=View Detail Line", $icon);
		}

		/* =============================================================
			SalesOrderDisplayInterface Functions
			LINKS ARE HTML LINKS, AND URLS ARE THE URLS THAT THE HREF VALUE
		============================================================ */
		public function generate_trackingrequesturl(Order $order) {
			return $this->generate_trackingrequesturltrait($order);
		}
	}
