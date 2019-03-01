<?php
	namespace Dplus\Dpluso\Configs;



	/**
	 * Internal Libraries
	 */
	use Dplus\Base\ThrowErrorTrait;

	/**
	 * Class for Providing URLs to Dplus Pages / Requests / Functions
	 */
	class DplusoRoles {
		/**
		 * Instance of  DplusoRoles
		 * @var  DplusoRoles
		 */
		private static $instance;

		private $roles = array(
			'default' => array(
				'dplus-code'    => '',
				'label'         => 'Default',
				'homepage-code' => 'user_dashboard',
				'homepage'      => ''
			),
			'sales-manager' => array(
				'dplus-code'    => 'slsmgr',
				'label'         => 'Sales Manager',
				'homepage-code' => 'user_dashboard',
				'homepage'      => ''
			),
			'sales-rep' => array(
				'dplus-code'    => 'slsrep',
				'label'         => 'Sales Rep',
				'homepage-code' => 'user_dashboard',
				'homepage'      => ''
			),
			'warehouse' => array(
				'dplus-code'    => 'whse',
				'label'         => 'Warehouse',
				'homepage-code' => 'warehouse',
				'homepage'      => ''
			),
			'warehouse-manager' => array(
				'dplus-code'    => 'whsmgr',
				'label'         => 'Warehouse Manager',
				'homepage-code' => 'warehouse',
				'homepage'      => ''
			)
		);

		private $dplus_todplusoroles = array(
			'slsrep' => 'sales-rep',
			'slsmgr' => 'sales-manager',
			'whse'   => 'warehouse',
			'whsmgr' => 'warehouse-manager'
		);

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
		private function __construct() {
			$urlconfig = DplusoConfigURLs::get_instance();
			
			foreach ($this->roles as $roleID => $role) {
				$this->roles[$roleID]['homepage'] = $urlconfig->find($role['home-code']);
			}
		}
	}