<?php
	/**
	 * Class for Notes and Tasks
	 */
	class UserAction {
		use ThrowErrorTrait;
		use MagicMethodTraits;
		use CreateFromObjectArrayTraits;
		use CreateClassArrayTraits;

		protected $id;
		protected $datecreated;
		protected $actiontype;
		protected $actionsubtype;
		protected $duedate;
		protected $createdby;
		protected $assignedto;
		protected $assignedby;
		protected $title;
		protected $textbody;
		protected $reflectnote;
		protected $completed;
		protected $datecompleted;
		protected $dateupdated;
		protected $customerlink;
		protected $shiptolink;
		protected $contactlink;
		protected $salesorderlink;
		protected $quotelink;
		protected $vendorlink;
		protected $vendorshipfromlink;
		protected $purchaseorderlink;
		protected $actionlink;
		protected $rescheduledlink;

		protected $actionlineage = array();
		public static $dateformat = "Y-m-d H:i:s";
		public static $datedisplayformat = 'm/d/Y g:i A';
		public static $types = array(
			'task' => 'task',
			'actions' => 'action',
			'notes' => 'note'
		);
		/* =============================================================
			SETTER FUNCTIONS
		============================================================ */
		/* =============================================================
			GETTER FUNCTIONS
		============================================================ */
		/**
		 * Returns if UserAction has something in the ID property
		 * @return bool
		 */
		public function has_id() {
			return (!empty($this->id)) ? true : false;
		}

		/**
		 * Returns if UserAction is linked to a Customer
		 * @return bool
		 */
		public function has_customerlink() {
			return (!empty($this->customerlink)) ? true : false;
		}

		/**
		 * Returns if UserAction is linked to a Customer Shipto
		 * @return bool
		 */
		public function has_shiptolink() {
			return (!empty($this->shiptolink)) ? true : false;
		}

		/**
		 * Returns if UserAction is linked to a Customer Contact
		 * @return bool
		 */
		public function has_contactlink() {
			return (!empty($this->contactlink)) ? true : false;
		}

		/**
		 * Returns if UserAction is linked to a Sales Order
		 * @return bool
		 */
		public function has_salesorderlink() {
			return (!empty($this->salesorderlink)) ? true : false;
		}

		/**
		 * Returns if UserAction is linked to a Quote
		 * @return bool
		 */
		public function has_quotelink() {
			return (!empty($this->quotelink)) ? true : false;
		}

		/**
		 * Returns if UserAction is linked to another UserAction
		 * @return bool
		 */
		public function has_actionlink() {
			return (!empty($this->actionlink)) ? true : false;
		}

		/**
		 * Returns if UserAction has the completed field 'Y'
		 * @return bool
		 */
		public function is_completed() {
			return ($this->completed == 'Y') ? true : false;
		}

		/**
		 * Returns if UserAction has the completed field 'R'
		 * @return bool
		 */
		public function is_rescheduled() {
			return ($this->completed == 'R') ? true : false;
		}

		/**
		 * Checks if the UserAction has a due date and if the due date has passed
		 * @return bool
		 */
		public function is_overdue() {
			if ($this->actiontype == 'task') {
				return (strtotime($this->duedate) < strtotime("now") && (!$this->is_completed())) ? true : false;
			} else {
				return false;
			}
		}

		/**
		 * Returns the links that have values in an array
		 * @return array UserAction links
		 */
		public function get_linkswithvaluesarray() {
			$array = $this->_toArray();
			foreach ($array as $key => $value){
				if (empty($array[$key])) {
					unset($array[$key]);
				}
			}
			return $array;
		}

		/* =============================================================
			CLASS FUNCTIONS
		============================================================ */
		/**
		 * Returns a title that is already given to the UserAction or generates one
		 * based on the links and their order of specificity
		 * @return string
		 */
		public function generate_regardingdescription() {
			$desc = '';
			if (!empty($this->title)) {
				return $this->title;
			}
			$desc = $this->has_customerlink() ? 'CustID: '. get_customername($this->customerlink) : '';
			$desc .=  $this->has_shiptolink() ? ' ShipID: '. get_shiptoname($this->customerlink, $this->shiptolink, false) : '';
			$desc .=  $this->has_contactlink() ? ' Contact: '. $this->contactlink : '';
			$desc .=  $this->has_salesorderlink() ? ' Sales Order #' . $this->salesorderlink : '';
			$desc .=  $this->has_quotelink() ? ' Quote #' . $this->quotelink : '';
			$desc .=  $this->has_actionlink() ? ' ActionID: ' . $this->actionlink: '';
			return $desc;
		}

		/**
		 * Returns a title that is already given to the UserAction or generates one
		 * based on the links and their order of specificity and replaces it in a regex message
		 * @param  string $message
		 * @return string          $message but with replaced description
		 */
		public function generate_message($message) {
			$regex = '/({replace})/i';
			$replace = $this->has_customerlink() ? get_customername($this->customerlink)." ($this->customerlink)" : '';
			$replace .= $this->has_shiptolink() ? " Shipto: " . get_shiptoname($this->customerlink, $this->shiptolink, false)." ($this->shiptolink)" : '';
			$replace .= $this->has_contactlink() ? " Contact: " . $this->contactlink : '';
			$replace .= $this->has_salesorderlink() ? " Sales Order #" . $this->salesorderlink : '';
			$replace .= $this->has_quotelink() ? " Quote #" . $this->quotelink : '';
			// $replace .= $this->has_actionlink() ? " Action #" . $this->actionlink : '';
			$replace = trim($replace);

			if (empty($replace)) {
				if (empty($this->assignedto)) {
					$replace = 'Yourself ';
				} else {
					if ($this->assignedto != DplusWire::wire('user')->loginid) {
						$replace = 'User: ' . DplusWire::wire('user')->loginid;
					} else {
						$replace = 'Yourself ';
					}
				}
			}
			return preg_replace($regex, $replace, $message);
		}

		/**
		 * Returns Due Date in a specified format if the UserAction type is a task
		 * @param  string $format PHP Date Format e.g. m/d/Y
		 * @return string         N/A or formatted date
		 */
		public function generate_duedatedisplay($format) {
			switch ($this->actiontype) {
				case 'task':
					return DplusDateTime::format_date($this->duedate, $format);
					break;
				default:
					return 'N/A';
					break;
			}
		}

		/**
		 * Return Completion Status description based on Completed Flag
		 * @return string R = Rescheduled | Y = Completed | Incomplete
		 */
		public function generate_taskstatusdescription() {
			switch (trim($this->completed)) {
				case 'R':
					return 'rescheduled';
				case 'Y':
					return 'completed';
				default:
					return 'incomplete';
			}
		}

		/**
		 * Gets the label and icon for the action type defined in Processwire by Customer
		 * @return string icon + label
		 */
		public function generate_actionsubtypedescription() {
			$page = DplusWire::wire('pages')->get("/config/actions/types/$this->actiontype/$this->actionsubtype");
			return ($page instanceof NullPage) ? '' : $page->subtypeicon.' '.$page->actionsubtypelabel;
		}

		/**
		 * Returns an array of UserActions that are linked by parentage
		 * @return array UserActions
		 */
		public function get_actionlineage() {
			$lineage = array();
			if ($this->has_actionlink()) {
				$parentid = $this->actionlink;
				while ($parentid != '') {
					$lineage[] = $parentid;
					$parent = UserAction::load($parentid);
					$parentid = $parent->actionlink;
				}
			}
			return $lineage;
		}
		/* =============================================================
			CRUD FUNCTIONS
		============================================================ */
		/**
		 * Returns SQL Query for Creating with the properties
		 * @param  bool $debug Determines if query will execute
		 * @return string         SQL INSERT QUERY
		 * @uses Create (CRUD)
		 */
		public function create($debug = false) {
			return create_useraction($this, $debug);
		}

		/**
		 * Retrieves an object of this Class from the Database
		 * @param  int  $id    ID of the UserAction to load
		 * @param  bool $debug Determines if query will execute
		 * @return UserAction  Or SQL QUERY
		 * @uses Read (CRUD)
		 */
		public static function load($id, $debug = false) {
			return get_useraction($id, $debug);
		}

		/**
		 * Returns SQL Query for Updating Actions with the properties
		 * @param  bool $debug Determines if query will execute
		 * @return string      SQL UPDATE QUERY updating changed properties
		 * @uses Update (CRUD)
		 */
		public function update($debug = false) {
			return update_useraction($this, $debug);
		}

		/**
		 * Fast way to save UserAction to database
		 * function determines if user action needs to be updated or created
		 * @param  bool $debug Determines if query will execute
		 * @return string         SQL UPDATE | INSERT QUERY updating changed properties
		 * @uses Update OR Create (CRUD)
		 */
		public function save($debug = false) {
			if ($this->has_id()) {
				return update_useraction($this, $debug);
			} else {
				return create_useraction($this, $debug);
			}
		}

		/**
		 * Returns the Newest ID for the login provided
		 * @param  string $loginID User Login
		 * @param  bool   $debug   Return SQL Query?
		 * @return int             Max User ID
		 */
		public static function get_maxid($loginID, $debug = false) {
			return get_maxuseractionid($loginID, $debug);
		}

		/* =============================================================
			GENERATE ARRAY FUNCTIONS
			The following are defined CreateClassArrayTraits
			public static function generate_classarray()
			public function _toArray()
		============================================================ */
		/**
		 * Mainly called by the _toArray() function which makes an array
		 * based of the properties of the class, but this function filters the array
		 * to remove keys that are not in the database
		 * This is used by database classes for update
		 * @param  array $array array of the class properties
		 * @return array        with certain keys removed
		 */
 		public static function remove_nondbkeys($array) {
			unset($array['actionlineage']);
			unset($array['dateformat']);
			unset($array['types']);
			unset($array['datedisplayformat']);
 			return $array;
 		}
	}
