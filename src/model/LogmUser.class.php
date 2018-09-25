<?php
	/**
	 * Dplus User that has their email, name, loginid, role, company, fax, phone
	 */
	class LogmUser {
		use ThrowErrorTrait;
		use MagicMethodTraits;

		/**
		 * Login ID
		 * @var string
		 */
		protected $loginid;

		/**
		 * User's Name
		 * @var string
		 */
		protected $name;

		/**
		 * Warehouse ID
		 * @var string
		 */
		protected $whseid;

		/**
		 * Role in the Company
		 * MGMT -> Management
		 * PURCH -> Purchasing
		 * PURMGR -> Purchasing Manager
		 * SLSMGR -> SLSREP
		 * WHSE -> Warehouse
		 * WHSMGR -> Warehouse Manager
		 * NOTE to look up permissions by role, use this field but also use strtolower
		 * @var string
		 */
		protected $role;

		/**
		 * Company Name
		 * @var string
		 */
		protected $company;

		/**
		 * Fax #
		 * @var string
		 */
		protected $fax;

		/**
		 * Phone #
		 * @var string
		 */
		protected $phone;

		/**
		 * User Email
		 * @var string
		 */
		protected $email;

		/**
		 * Dummy
		 * @var string X
		 */
		protected $dummy;

		/**
		 * Property aliases
		 * @var array
		 */
		public $fieldaliases = array(
            'loginID' => 'loginid',
            'whseID' => 'whseid',
        );

		/* =============================================================
			CRUD FUNCTIONS
		============================================================ */
		/**
		 * Returns the Role giving to the LogM user and returns it in lower case
		 * so it can be looked at for role permissions
		 * @return string Role in lower case
		 */
		public function get_dplusrole() {
			return strtolower($this->role);
		}
		
		/**
		 * Returns the Dpluso Role for the user, so it can be looked at for 
		 * Navigation and user type customization
		 * @return string DPLUSO user type
		 */
		public function get_dplusorole() {
			return array_search(strtolower($this->role), DplusWire::wire('config')->roles);
		}

		/**
		 * Get the Loginid needed for the custperm table based on permissions
		 * @return string admin | $this->loginid
		 */
		public function get_custpermloginid() {
			return $this->get_dplusrole() == DplusWire::wire('config')->roles['sales-rep'] ? $this->loginid : 'admin';
		}

		/**
		 * Returns the custperm loginid that Anything above a salesrep uses
		 * @return string LoginID
		 */
		public static function get_toplevelcustpermloginid() {
			return 'admin';
		}

		/**
		 * Loads an object of this class
		 * @param  string   $loginID  User's Dplus Login ID
		 * @param  bool     $debug    Whether to return the SQL to create the object or the object
		 * @return LogmUser 
		 */
		public static function load($loginID, $debug = false) {
			return get_logmuser($loginID, $debug);
		}

		/**
		 * Looks for logmuser by login, then tries to get their name
		 * @param  string $loginID User LoginID
		 * @return string          The User's Name
		 */
		public static function find_username($loginID) {
			$user = self::load($loginID);
			return !empty($user) ? $user->name : 'User Not Found';
		}

		/**
		 * Returns an array of LogmUsers
		 * @param  bool   $debug Run in Debug? If so, return SQL Query
		 * @return array         Returns an array of LogmUsers
		 */
		public static function load_userlist($debug = false) {
			return get_logmuserlist($debug);
		}
	}
