<?php
	class ContactActionsPanel extends CustomerActionsPanel {

		/**
		* Contact Identifier
		* @var string
		*/
		protected $contactID;

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
			),
			'contactlink' => array(
				'querytype' => 'in',
				'datatype' => 'text',
				'label' => 'Contact Link'
			)
		);

		/**
		 * Panel Type
		 * Who or what the actions are being filtered to
		 * @var string
		 */
		protected $paneltype = 'contact';

		/* =============================================================
			GETTER FUNCTIONS
		============================================================ */
		/**
		* Generates title for Panel
		* @return string
		*/
		public function generate_title() {
			return 'Contact Actions';
		}

		/**
		 * Returns URL to load add new action[type=$this->actiontype] form
		 * @return string                 URL to load add new action[type=$this->actiontype] form
		 */
		public function generate_addactionurl() {
			$url = new Purl\Url(parent::generate_addactionurl());
			$url->query->set('contactID', $this->contactID);
			return $url->getUrl();
		}

		public function generate_clearfilterurl() {
			$url = new Purl\Url(parent::generate_clearfilterurl());
			$url->query->set('contactID', $this->contactID);
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
			parent::setup_pageurl();
			$this->pageurl->query->set('contactID', $this->contactID);
		}

		/**
		 * Sets the Contact ID, Customer, ShiptoID for the filter, and $this->pageurl
		 * @param string $custID    Customer Identifier
		 * @param string $shiptoID  ShiptoID Identifier
		 * @param string $contactID Contact Identifier
		 */
		public function set_contact($custID, $shiptoID = '', $contactID) {
			$this->set_customer($custID, $shiptoID);
			$this->contactID = $contactID;
			$this->generate_filter($this->input);
			$this->setup_pageurl();
		}

		/* =============================================================
			CLASS FUNCTIONS
		============================================================ */
		public function generate_filter(ProcessWire\WireInput $input) {
			parent::generate_filter($input);
			$this->filters['contactlink'] = array($this->contactID);
		}
	}
