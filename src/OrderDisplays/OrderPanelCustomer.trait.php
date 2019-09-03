<?php
	namespace Dplus\Dpluso\OrderDisplays;

	use Dplus\Base\QueryBuilder;
	use Dplus\Content\Paginator;

	/**
	 * Use Statements for Model Classes which are non-namespaced
	 */
	use LogmUser;

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
			$this->setup_pageURL();
		}

		/**
		 * Setup the Page URL then add the necessary components in the path and querystring
		 * @return void
		 * @uses parent::setup_pageURL()
		 */
		public function setup_pageURL() {
			parent::setup_pageURL();
			$pagenbr = Paginator::generate_pagenbr($this->pageurl);
			$regex = "((page)\d{1,3})";
			$this->pageurl->path = preg_replace($regex, "", $this->pageurl->path);
			$this->pageurl->path->add('customer');
			$this->pageurl->path->add($this->custID);
			$this->paginationinsertafter = $this->custID;

			if (!empty($this->shipID)) {
				$this->pageurl->path->add("shipto-$this->shipID");
				$this->paginationinsertafter = "shipto-$this->shipID";
			}

			$this->pageurl = Paginator::paginate_purl($this->pageurl, $pagenbr, $this->paginationinsertafter);
		}

		/**
		 * Returns a descrption of the filters being applied to the orderpanel
		 * @return string Description of the filters
		 */
		public function generate_filterdescription() {
			$user = LogmUser::load($this->userID);
			$filters = $this->filters;

			if ($user->is_salesrep()) {
				unset($filters['salesperson']);
			}

			unset($filters['custid']);
			unset($filters['shiptoid']);

			if (empty($filters)) {
				return '';
			} else {
				$desc = 'Searching '.$this->generate_paneltypedescription().' with';

				foreach ($filters as $filter => $value) {
					$desc .= " " . QueryBuilder::generate_filterdescription($filter, $value, $this->filterable);
				}
				return $desc;
			}
		}
	}
