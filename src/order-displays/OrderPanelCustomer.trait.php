<?php
	/**
	 * Traits for defining a Customer on the orderpanels
	 */
	trait OrderPanelCustomerTraits {
		/**
		 * Customer ID
		 * @var string
		 */
		protected $custID;
		
		/**
		 * Shipto ID
		 * @var string
		 */
		protected $shipID;
		
		/**
		 * Sets the customer and shipto for the OrderPanel
		 * @param string $custID Customer ID
		 * @param string $shipID Customer ShiptoID
		 */
		public function set_customer($custID, $shipID) {
			$this->custID = $custID;
			$this->shipID = $shipID;
			$this->setup_pageurl();
		}
		
		/**
		 * Setup the Page URL then add the necessary components in the path and querystring
		 * @return void
		 * @uses parent::setup_pageurl()
		 */
		public function setup_pageurl() {
			parent::setup_pageurl();
			$this->pageurl->path->add('customer');
			$this->pageurl->path->add($this->custID);
			$this->paginationinsertafter = $this->custID;
			
			if (!empty($this->shipID)) {
				$this->pageurl->path->add("shipto-$this->shipID");
				$this->paginationinsertafter = "shipto-$this->shipID";
			}
		}
	}
