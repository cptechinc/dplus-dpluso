<?php 
	namespace Dplus\Dpluso\OrderDisplays;
	
	use Purl\Url;
	use Dplus\ProcessWire\DplusWire;
	
	/**
	 * Use Statements for Model Classes which are non-namespaced
	 */
	use Order;
	
	/**
	 * Blueprint for Order Display classes
	 */
	abstract class OrderDisplay {
		use \Dplus\Base\ThrowErrorTrait;
		use \Dplus\Base\MagicMethodTraits;
		use \Dplus\Base\AttributeParser;
		/**
		 * URL object that contains the Path to the page
		 * @var Url
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
		 * @param Url     $pageurl   URL object to get URL
		 * @param mixed   $modal     ID of modal to use or false
		 */
		public function __construct($sessionID, Url $pageurl, $modal = false) {
			$this->sessionID = $sessionID;
			$this->pageurl = new Url($pageurl->getUrl());
			$this->modal = $modal;
		}
		
		/* =============================================================
			OrderDisplay Interface Functions
		============================================================ */
		/**
		 * Returns the URL to the load customer page 
		 * @param  Order  $order Order to get the customer ID to load
		 * @return string        URL to load Customer Page from
		 */
		public function generate_customerURL(Order $order) {
			$url = new Url(DplusWire::wire('config')->pages->customer."redir/");
			$url->query->setData(array('action' => 'ci-customer', 'custID' => $order->custid));
			return $url->getUrl();
		}
		
		/**
		 * Returns the URL to the load customer shipto page 
		 * @param  Order  $order Order to get the customerID and shiptoID to load
		 * @return string        URL to load Customer shipto Page from
		 */
		public function generate_customershiptoURL(Order $order) {
			$url = new Url($this->generate_customerURL($order));
			if (!empty($order->shiptoid)) $url->query->set('shipID', $order->shiptoid);
			return $url->getUrl();
		}
	}
