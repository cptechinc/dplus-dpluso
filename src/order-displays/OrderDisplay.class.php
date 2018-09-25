<?php 
	/**
	 * Blueprint for Order Display classes
	 */
	abstract class OrderDisplay {
		use ThrowErrorTrait;
		use MagicMethodTraits;
		
		/**
		 * URL object that contains the Path to the page
		 * @var Purl\Url
		 */
		protected $pageurl;
		
		/**
		 * Session Identifier
		 * @var string
		 */
		protected $sessionID;
		
		/**
		 * ID of Modal to use
		 * @var string or False
		 */
		protected $modal;
		
		/**
		 * Base Constructor
		 * @param string  $sessionID  Session Identifier
		 * @param Purl\Url $pageurl   URL object to get URL
		 * @param mixed    $modal     ID of modal to use or false
		 */
		public function __construct($sessionID, \Purl\Url $pageurl, $modal = false) {
			$this->sessionID = $sessionID;
			$this->pageurl = new \Purl\Url($pageurl->getUrl());
			$this->modal = $modal;
		}
		
		/* =============================================================
			Helper Functions
		============================================================ */
		/**
		 * Returns HTML Link to load the customer shipto page
		 * @param  Order  $order Order to get the customerID and shiptoID to load
		 * @return string        HTML Link
		 */
		public function generate_customershiptolink(Order $order) {
			$bootstrap = new HTMLWriter();
			$href = $this->generate_customershiptourl($order);
			$icon = $bootstrap->icon('fa fa-user');
			return $bootstrap->create_element('a', "href=$href|class=btn btn-block btn-primary", $icon. " Go to Customer Page");   
		}
		
		/**
		 * Returns URL to the customer redirect page
		 * @return string URL to Customer Redirect
		 * @uses
		 */
		public function generate_customerredirurl() {
			$url = new \Purl\Url(DplusWire::wire('config')->pages->orders);
			$url->path = DplusWire::wire('config')->pages->customer."redir/";
			return $url;
		}
		
		/* =============================================================
			OrderDisplay Interface Functions
		============================================================ */
		/**
		 * Returns the URL to the load customer page 
		 * @param  Order  $order Order to get the customer ID to load
		 * @return string        URL to load Customer Page from
		 */
		public function generate_customerurl(Order $order) {
			$url = $this->generate_customerredirurl();
			$url->query->setData(array('action' => 'ci-customer', 'custID' => $order->custid));
			return $url->getUrl();
		}
		
		/**
		 * Returns the URL to the load customer shipto page 
		 * @param  Order  $order Order to get the customerID and shiptoID to load
		 * @return [type]        URL to load Customer shipto Page from
		 */
		public function generate_customershiptourl(Order $order) {
			$url = new \Purl\Url($this->generate_customerurl($order));
			if (!empty($order->shiptoid)) $url->query->set('shipID', $order->shiptoid);
			return $url->getUrl();
		}
	}
