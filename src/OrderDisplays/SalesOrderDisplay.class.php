<?php
	namespace Dplus\Dpluso\OrderDisplays;
	
	/**
	 * External Libraries
	 */
	use Purl\Url;
	
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
		 * Sales Order
		 * @var SalesOrder
		 */
		protected $order;

		public function __construct($sessionID, Url $pageurl, $modal, $ordn) {
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
	}
