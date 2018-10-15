<?php 
	namespace Dplus\Dpluso\OrderDisplays;
	
	class SalesOrderDisplay extends OrderDisplay implements OrderDisplayInterface, SalesOrderDisplayInterface {
		use SalesOrderDisplayTraits;
		
		protected $ordn;
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
		public function generate_documentsrequesturl(Order $order, OrderDetail $orderdetail = null) {
			return $this->generate_documentsrequesturltrait($order, $orderdetail);
		}
		
		public function generate_editlink(Order $order) {
			$bootstrap = new Dplus\Content\HTMLWriter();
			$href = $this->generate_editurl($order);
			$icon = $order->can_edit() ? $bootstrap->icon('material-icons', '&#xE150;') : $bootstrap->icon('glyphicon glyphicon-eye-open');
			$text = $order->can_edit() ? 'Edit' : 'View';
			return $bootstrap->create_element('a', "href=$href|class=btn btn-block btn-warning", $icon. " $text Sales Order");   
		}
		
		public function generate_detailvieweditlink(Order $order, OrderDetail $detail) {
			$bootstrap = new Dplus\Content\HTMLWriter();
			$href = $this->generate_detailviewediturl($order, $detail);
			$icon = $bootstrap->create_element('span', 'class=h3', $bootstrap->icon('glyphicon glyphicon-eye-open'));
			return $bootstrap->create_element('a', "href=$href|class=update-line|data-kit=$detail->kititemflag|data-itemid=$detail->itemid|data-custid=$order->custid|aria-label=View Detail Line", $icon);	
		}
		
		/* =============================================================
			SalesOrderDisplayInterface Functions
			LINKS ARE HTML LINKS, AND URLS ARE THE URLS THAT THE HREF VALUE
		============================================================ */
		public function generate_trackingrequesturl(Order $order) {
			return $this->generate_trackingrequesturltrait($order);
		}
	}
