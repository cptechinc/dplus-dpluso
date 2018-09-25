<?php
	class CustomerActionsPanel extends ActionsPanel {
		/**
		* Customer Identifier
		* @var string
		*/
		protected $custID;

		/**
		* Ship-to Identifier
		* @var string
		*/
		protected $shiptoID;

		/**
		 * Array of filterable columns and their attributes
		 * @var array
		 */
		protected $filterable = array(
			'actiontype' => array(
				'querytype' => 'in',
				'datatype' => 'text',
				'label' => 'Action Type'
			),
			'completed' => array(
				'querytype' => 'in',
				'datatype' => 'text',
				'label' => 'Completed'
			),
			'assignedto' => array(
				'querytype' => 'in',
				'datatype' => 'text',
				'label' => 'Assigned To'
			),
			'datecreated' => array(
				'querytype' => 'between',
				'datatype' => 'mysql-date',
				'label' => 'Date Created',
				'date-format' => "m/d/Y H:i:s"
			),
			'datecompleted' => array(
				'querytype' => 'between',
				'datatype' => 'mysql-date',
				'label' => 'Date Completed',
				'date-format' => "m/d/Y H:i:s"
			),
			'dateupdated' => array(
				'querytype' => 'between',
				'datatype' => 'mysql-date',
				'label' => 'Date Updated',
				'date-format' => "m/d/Y H:i:s"
			),
			'duedate' => array(
				'querytype' => 'between',
				'datatype' => 'mysql-date',
				'label' => 'Due Date',
				'date-format' => "m/d/Y H:i:s"
			),
			'customerlink' => array(
				'querytype' => 'in',
				'datatype' => 'text',
				'label' => 'Customer Link'
			),
			'shiptolink' => array(
				'querytype' => 'in',
				'datatype' => 'text',
				'label' => 'Ship-to Link'
			)
		);

		/**
		 * Panel Type
		 * Who or what the actions are being filtered to
		 * @var string
		 */
		protected $paneltype = 'customer';

		/**
		 * Wire Input for generating filtter
		 * @var ProcessWire/WireInput
		 */
		protected $input;

		/**
		 * What kind of view to start with
		 * day | calendar | list
		 * @var string
		 */
		protected $view = 'list';


		/* =============================================================
			CONSTRUCTOR FUNCTIONS
		============================================================ */
		/**
		 * Constructor
		 * @param string                $sessionID   Session Identifier
		 * @param Purl\Url              $pageurl     Object that contains URL to Page
		 * @param ProcessWire\WireInput $input       Input such as the $_GET array to run generate_filter
		 * @param bool                  $throughajax If panel was loaded through ajax
		 * @param string                $panelID     Panel element ID
		 */
		public function __construct($sessionID, \Purl\Url $pageurl, ProcessWire\WireInput $input, $throughajax = false, $panelID = '') {
			parent::__construct($sessionID, $pageurl, $input, $throughajax, $panelID);
			$this->input = $input;
		}

		/* =============================================================
			GETTER FUNCTIONS
		============================================================ */
		/**
		* Generates title for Panel
		* @return string
		*/
		public function generate_title() {
			return 'Customer Actions';
		}

		/**
		* Returns if the panel should have the add link
		* @return bool
		*/
		public function should_haveaddlink() {
			return true;
		}

		/**
		 * Returns URL to load add new action[type=$this->actiontype] form
		 * @return string                 URL to load add new action[type=$this->actiontype] form
		 */
		public function generate_addactionurl() {
			$url = new Purl\Url(parent::generate_addactionurl());
			$url->query->set('custID', $this->custID);
			if (!empty($this->shiptoID)) {
				$url->query->set('shiptoID', $this->shiptoID);
			}
			return $url->getUrl();
		}

		public function generate_clearfilterurl() {
			$url = new Purl\Url(parent::generate_clearfilterurl());
			$url->query->set('custID', $this->custID);
			if (!empty($this->shiptoID)) {
				$url->query->set('shiptoID', $this->shiptoID);
			}
			return $url->getUrl();
		}
		/* =============================================================
			SETTER FUNCTIONS
		============================================================ */
		/**
		 * Manipulates $this->pageurl path and query data as needed
		 * then sets $this->paginateafter value
		 * @return void
		 */
		public function setup_pageurl() {
			$this->paginateafter = $this->paginateafter;
			$this->pageurl->query->set('custID', $this->custID);
			if (!(empty($this->shiptoID))) {
				$this->pageurl->query->set('shiptoID', $this->shiptoID);
			}
		}

		/**
		 * Set the Customer and Shipto IDs to set the filter and $this->pageurl
		 * @param string $custID   Customer Identifier
		 * @param string $shiptoID Shipto Identifier
		 */
		public function set_customer($custID, $shiptoID = '') {
			$this->custID = $custID;
			$this->shiptoID = $shiptoID;
			$this->generate_filter($this->input);
			$this->setup_pageurl();
		}

		/* =============================================================
			CLASS FUNCTIONS
		============================================================ */
		public function generate_filter(ProcessWire\WireInput $input) {
			parent::generate_filter($input);
			$this->filters['customerlink'] = array($this->custID);

			if (!empty($this->shiptoID)) {
				$this->filters['shiptolink'] = array($this->shiptoID);
			}

			if (isset($this->filters['datecreated'])) {
				if (empty($this->filters['datecreated'][0])) {
					$this->filters['datecreated'][0] = date('m/d/Y', strtotime(get_mindateuseractioncreated($this->custID, $this->shiptoID)));
				}

				if (empty($this->filters['datecreated'][1])) {
					$this->filters['datecreated'][1] = date('m/d/Y');
				}
			}

			if (isset($this->filters['datecompleted'])) {
				if (empty($this->filters['datecompleted'][0])) {
					$this->filters['datecompleted'][0] = date('m/d/Y', strtotime(get_mindateuseractioncompleted($this->custID, $this->shiptoID)));
				}

				if (empty($this->filters['datecompleted'][1])) {
					$this->filters['datecompleted'][1] = date('m/d/Y');
				}
			}
		}
	}
