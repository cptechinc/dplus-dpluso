<?php 
	/**
	 * ItemLookupModal provides functions, and holds data for the item lookup form and 
	 * generates the specific data needed when the results need to be for a worksheet, sales order, or quote. 
	 */
	class ItemLookupModal {
		use ThrowErrorTrait;
		use MagicMethodTraits;
		
		/**
		 * Type
		 * Used for description
		 * @var string
		 */
		protected $type = 'worksheet';
		
		/**
		 * Customer to lookup items for
		 * @var string
		 */
		protected $custID;
		
		/**
		 * Customer Shipto to lookup items for
		 * @var string
		 */
		protected $shipID;
		
		/* =============================================================
			GETTER FUNCTIONS 
		============================================================ */
		/**
		* If a property is not accessible then try to give them the property through
		* a already defined method or to give them the property value
		* @param  string $property property name
		* @return 
		*     1. Value returned in value call
		*     2. Returns the value of the property_exists
		*     3. Throw Error
		*/
		public function __get($property) {
			$method = "get_{$property}";
			if (method_exists($this, $method)) {
				return $this->$method();
			} elseif (property_exists($this, $property)) {
				return $this->$property;
			} else {
				$this->error("This property ($property) does not exist");
				return false;
			}
		}
		
		/**
		 * Return the type of Item Lookup Modal
		 * @return string worksheet|order|quote
		 */
		public function get_type() {
			return $this->type;
		}
		
		/* =============================================================
			SETTER FUNCTIONS
		============================================================ */
		/**
		 * Set the customer and Shipto Properties
		 * @param string $custID Customer ID
		 * @param string $shipID Customer Shipto ID
		 */
		public function set_customer($custID, $shipID) {
			$this->custID = $custID;
			$this->shipID = $shipID;
		}
		
		/**
		 * This creates a Lookup Modal Sales Order and replaces $this with it
		 * @param string $ordn Sales Order #
		 */
		public function set_ordn($ordn) {
			$lookup = new ItemLookupModalOrder($ordn);
			$lookup->set_customer($this->custID, $this->shipID);
			return $lookup;
		}
		
		/**
		 * This creates a Lookup Modal Quote and replaces $this with it
		 * @param string $qnbr Quote #
		 */
		public function set_qnbr($qnbr) {
			$lookup = new ItemLookupModalQuote($qnbr);
			$lookup->set_customer($this->custID, $this->shipID);
			return $lookup;
		}
		
		/* =============================================================
			CLASS FUNCTIONS 
		============================================================ */
		/**
		 * Return the URL where the results are going to be loaded from
		 * @return string URL
		 */
		public function generate_resultsurl() {
			$url = new \Purl\Url(DplusWire::wire('config')->pages->ajax.'load/products/item-search-results/cart/');
			$url->query->set('custID', $this->custID)->set('shipID', $this->shipID);
			return $url->getUrl();
		}
		
		/**
		 * Return the URL where the nonstock form will be loaded from
		 * @return string URL
		 */
		public function generate_nonstockformurl() {
			$url = new \Purl\Url(DplusWire::wire('config')->pages->ajax.'load/products/non-stock/form/cart/');
			$url->query->set('custID', $this->custID)->set('shipID', $this->shipID);
			return $url->getUrl();
		}
		
		/**
		 * Return the URL where the add multiple item form will be loaded from
		 * @return string URL
		 */
		public function generate_addmultipleurl() {
			$url = new \Purl\Url(DplusWire::wire('config')->pages->ajax.'load/add-detail/cart/');
			$url->query->set('custID', $this->custID)->set('shipID', $this->shipID);
			return $url->getUrl();
		}
	}
	
	/**
	 * Item Lookup modal for Sales Orders
	 */
	class ItemLookupModalOrder extends ItemLookupModal  {
		/**
		 * Type of Look up modal
		 * Used for description
		 * @var string
		 */
		protected $type = 'order';
		
		/**
		 * Order Number
		 * @var string
		 */
		protected $ordn;
		
		/* =============================================================
			CONSTRUCTOR FUNCTIONS 
		============================================================ */
		/**
		 * Constructor for Item Lookup for Sales Orders
		 * @param string $ordn Sales Order #
		 */
		public function __construct($ordn) {
			$this->ordn = $ordn;
		}
		
		/* =============================================================
			CLASS FUNCTIONS 
		============================================================= */
		/**
		 * Return the URL where the results are going to be loaded from
		 * @return string URL
		 */
		public function generate_resultsurl() {
			$url = new \Purl\Url(DplusWire::wire('config')->pages->ajax.'load/products/item-search-results/order/');
			$url->query->setData(array('ordn' => $this->ordn,'custID' => $this->custID, 'shipID' => $this->shipID));
			$url->query->set('ordn', $this->ordn)->set('custID', $this->custID)->set('shipID', $this->shipID);
			return $url->getUrl();
		}
		
		/**
		 * Return the URL where the nonstock form will be loaded from
		 * @return string URL
		 */
		public function generate_nonstockformurl() {
			$url = new \Purl\Url(DplusWire::wire('config')->pages->ajax.'load/products/non-stock/form/order/');
			$url->query->set('ordn', $this->ordn)->set('custID', $this->custID)->set('shipID', $this->shipID);
			return $url->getUrl();
		}
		
		/**
		 * Return the URL where the add multiple item form will be loaded from
		 * @return string URL
		 */
		public function generate_addmultipleurl() {
			$url = new \Purl\Url(DplusWire::wire('config')->pages->ajax.'load/add-detail/order/');
			$url->query->set('ordn', $this->ordn)->set('custID', $this->custID)->set('shipID', $this->shipID);
			return $url->getUrl();
		}
	}
	
	/**
	 * Item Lookup modal for Quotes
	 */
	class ItemLookupModalQuote extends ItemLookupModal {
		/**
		 * Type of Look up modal
		 * Used for description
		 * @var string
		 */
		protected $type = 'quote';
		
		/**
		 * Quote Number
		 * @var string
		 */
		protected $qnbr;
		
		/**
		 * Is this Quote to Order?
		 * @var bool
		 */
		protected $to_order  = false;
		
		/* =============================================================
			CONSTRUCTOR FUNCTIONS
		============================================================= */
		/**
		 * Primary Constructor
		 * @param string $qnbr Quote Nbr
		 */
		public function __construct($qnbr) {
			$this->qnbr = $qnbr;
		}
		
		/* =============================================================
			CLASS FUNCTIONS
		============================================================ */
		/**
		 * Return the URL where the results are going to be loaded from
		 * @return string URL
		 */
		public function generate_resultsurl() {
			$url = new \Purl\Url(DplusWire::wire('config')->pages->ajax.'load/products/item-search-results/quote/');
			$url->query->set('qnbr', $this->qnbr)->set('custID', $this->custID)->set('shipID', $this->shipID);
			
			if ($this->to_order) {
				$url->query->set('order', 'true');
			}
			return $url->getUrl();
		}
		
		/**
		 * Returns the URL where the nonstock form can be loaded
		 * @return string URL
		 */
		public function generate_nonstockformurl() {
			$url = new \Purl\Url(DplusWire::wire('config')->pages->ajax.'load/products/non-stock/form/quote/');
			$url->query->set('qnbr', $this->qnbr)->set('custID', $this->custID)->set('shipID', $this->shipID);
			if ($this->to_order) {
				$url->query->set('order', 'true');
			}
			return $url->getUrl();
		}
		
		/**
		 * Returns the URL where the add multiple items form can be loaded
		 * @return string URL
		 */
		public function generate_addmultipleurl() {
			$url = new \Purl\Url(DplusWire::wire('config')->pages->ajax.'load/add-detail/quote/');
			$url->query->set('qnbr', $this->qnbr)->set('custID', $this->custID)->set('shipID', $this->shipID);
			if ($this->to_order) {
				$url->query->set('order', 'true');
			}
			return $url->getUrl();
		}
	}
