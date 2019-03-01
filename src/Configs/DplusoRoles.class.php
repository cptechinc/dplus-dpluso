<?php
	namespace Dplus\Dpluso\Configs;



	/**
	 * Internal Libraries
	 */
	use Dplus\Base\ThrowErrorTrait;
	use Dplus\Base\MagicMethodTraits;

	/**
	 * Class for Providing URLs to Dplus Pages / Requests / Functions
	 */
	class DplusoRoles {
		use ThrowErrorTrait;
		use MagicMethodTraits;
		
		/**
		 * Instance of DplusoRoles
		 * @var  DplusoRoles
		 */
		private static $instance;

		/**
		 * Roles Keyed by Application role code
		 * Each Role has properties:
		 *  - dplus-code     : The Distribtion Plus Role Code
		 *  - label          : What the Application Calls the Role
		 *  - home-page-code : code to deciper the homepage url using <DplusoURLS>
		 * @var array
		 */
		private $roles = array(
			'default' => array(
				'dplus-code'    => '',
				'label'         => 'Default',
				'homepage-code' => 'user_dashboard',
			),
			'sales-manager' => array(
				'dplus-code'    => 'slsmgr',
				'label'         => 'Sales Manager',
				'homepage-code' => 'user_dashboard',
			),
			'sales-rep' => array(
				'dplus-code'    => 'slsrep',
				'label'         => 'Sales Rep',
				'homepage-code' => 'user_dashboard',
			),
			'warehouse' => array(
				'dplus-code'    => 'whse',
				'label'         => 'Warehouse',
				'homepage-code' => 'warehouse',
			),
			'warehouse-manager' => array(
				'dplus-code'    => 'whsmgr',
				'label'         => 'Warehouse Manager',
				'homepage-code' => 'warehouse',
			)
		);

		/**
		 * Dplus Roles and their Dplus Online Role Equivalent
		 * @var array
		 */
		private $dplus_todplusoroles = array(
			'slsrep' => 'sales-rep',
			'slsmgr' => 'sales-manager',
			'whse'   => 'warehouse',
			'whsmgr' => 'warehouse-manager'
		);
		
		/**
		 * Returns instance of this class
		 * @return DplusoRoles
		 */
		public static function get_instance() {
			if (empty(self::$instance)) {
				self::$instance = new DplusoRoles();
			}
			return self::$instance;
		}

		/**
		 * Constructor Function
		 * Sets the basepath from the static property
		 */
		private function __construct() {}

		/**
		 * Returns if User Role Exists
		 * @param  string $role Code for Role e.g. sales-manager
		 * @return bool
		 */
		public function does_role_exist($role) {
			return in_array(strtolower($role), array_keys($this->roles));
		}

		/**
		 * Returns if Dplus User Role exists or has an equivalent 
		 * @param  string $role Dplus User Role e.g. slsmgr
		 * @return bool         Does $role exist?
		 */
		public function dplus_role_exists($role) {
			return in_array(strtolower($role), array_keys($this->dplus_todplusoroles));
		}

		/**
		 * Returns the Role Key for the Provided Role code
		 * Example: slsmgr -> sales-manager
		 * @param  string $role  Dplus User Role
		 * @return string        Application Role
		 */
		public function translate_dplusrole($role) {
			return $this->dplus_todplusoroles[$role];
		}

		/**
		 * Returns the Homepage URL for a Role
		 * @param  string $role Role Key in the $this->roles e.g. sales-manager
		 * @return string
		 */
		public function get_role_homepage($role) {
			$urlconfig = DplusoConfigURLs::get_instance();
			return $urlconfig->find($this->roles[$role]['homepage-code']);
		}
	}
