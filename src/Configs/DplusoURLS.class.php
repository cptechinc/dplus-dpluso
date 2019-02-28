<?php
	namespace Dplus\Dpluso\Configs;

	use Purl\Url;
	use Dplus\Base\ThrowErrorTrait;

	/**
	 * Class for Providing URLs to Dplus Pages / Requests / Functions
	 */
	class DplusoConfigURLs {
		use ThrowErrorTrait;
		use CustomerURLsTraits;
		use OrderURLsTraits;
		use BookingsURLTraits;

		/**
		 * Root Path to build off from
		 * @var string
		 */
		private static $_rootpath;

		/**
		 * Instance of DplusoConfigURLs
		 * @var DplusoConfigURLs
		 */
		private static $instance;

		/**
		 * Paths
		 * @var DplusoPaths
		 */
		private $paths;

		public static function get_instance() {
			if (empty(self::$instance)) {
				self::$instance = new DplusoConfigURLS();
			}
			return self::$instance;
		}

		/**
		 * Constructor Function
		 * Sets the basepath from the static property
		 */
		private function __construct() {
			$this->paths = DplusoPaths::get_instance();
		}

		/**
		 * Automatically Parse URL Path from Call
		 * @param  string $key URL Path String
		 * @return string      URL Path
		 */
		function __get($key) {
			return $this->paths->get_urlpath($key);
		}

		/**
		 * Sets the static self::$rootpath
		 * @param string $path Root Path
		 */
		static function set_rootpath($path) {
			self::$_rootpath = $path;
		}

		/**
		 * Returns self::$rootpath
		 * @param string self::$rootpath
		 */
		static function get_rootpath() {
			return self::$_rootpath;
		}
	}

	/**
	 * Functions that provide URLs to Customer Functions / Pages
	 */
	trait CustomerURLsTraits {
		public function get_customer_redirURL() {
			$url = new Url($this->paths->get_urlpath('customer'));
			$url->path->add('redir');
			return $url->getUrl();
		}

		/**
		 * Returns the URL for CI
		 * @param  string $custID   Customer ID
		 * @param  string $shiptoID Shipto ID
		 * @return string           URL PATH
		 */
		public function get_ciURL($custID, $shiptoID = '') {
			$url = new Url($this->get_customer_redirURL());
			$url->query->set('action', 'ci-customer');
			$url->query->set('custID', $custID);

			if (!empty($shiptoID)) {
				$url->query->set('shipID', $shiptoID);
			}
			return $url->getUrl();
		}
	}

	/**
	 * Functions that provide URLS to Order Pages / Functions
	 */
	trait OrderURLsTraits {
		/**
		 * Returns the URL to the Sales Order Redirect for Requests
		 * @return string Sales Order Redirect
		 */
		public function get_salesorders_redirURL() {
			$url = new Url($this->paths->get_urlpath('orders'));
			$url->path->add('redir');
			return $url->getUrl();
		}
	}

	/**
	 * Functions that provide URLs to Booking Functions / Pages
	 */
	trait BookingsURLTraits {
		/**
		 * Returns the URL for Loading bookings through Ajax
		 * @return string Bookings ajax URL
		 */
		public function get_bookings_ajaxURL() {
			$url = new Url($this->paths->get_urlpath('ajax_load'));
			$url->path->add('bookings');
			return $url->getUrl();
		}

		/**
		 * Returns the Sales Orders Bookings Ajax URL
		 * @return string Sales Order bookings Ajax URL
		 */
		public function get_bookings_orders_ajaxURL() {
			$url = new Url($this->get_bookings_ajaxURL());
			$url->path->add('sales-orders');
			return $url->getUrl();
		}

		/**
		 * Returns the URL to view the date provided's bookings
		 * @param  string $date     Date to view Orders for
		 * @param  string $custID   Customer ID
		 * @param  string $shiptoID Customer Shipto ID
		 *
		 * @return string           URL to view the date's booked orders
		 */
		public function get_bookings_salesorders_bydayURL($date, $custID = '', $shiptoID = '') {
			$url = new Url($this->get_bookings_orders_ajaxURL());
			$url->query->set('date', $date);

			if (!empty($custID)) {
				$url->query->set('custID', $custID);
				if (!empty($shipID)) {
					$url->query->set('shipID', $shipID);
				}
			}

			return $url->getUrl();
		}

		/**
		 * Returns URL to view the bookingsfor a sales order on a particular date
		 * @param  string $ordn Sales Order #
		 * @param  string $date Date
		 * @param  string $custID   Customer ID
		 * @param  string $shiptoID Customer Shipto ID
		 * @return string       URL to view bookings for that order # and date
		 */
		public function get_bookings_day_salesorderURL($ordn, $date, $custID = '', $shiptoID = '') {
			$url = new Url($this->get_bookings_ajaxURL());
			$url->path->add('sales-order');
			$url->query->set('ordn', $ordn);
			$url->query->set('date', $date);
			if (!empty($custID)) {
				$url->query->set('custID', $custID);
				if (!empty($shiptoID)) {
					$url->query->set('shipID', $shiptoID);
				}
			}
			return $url->getUrl();
		}

		/**
		 * Returns URL to Bookings Page with a filter between Book Dates
		 * @param  string $start Start Date m/d/Y
		 * @param  string $end   End Date m/d/Y
		 * @return string        URL to Bookings
		 */
		public function get_bookings_filter_bookdatesURL($start, $end) {
			$url = new Url($this->get_bookings_ajaxURL());
			$url->query->set('filter', 'filter');
			$url->query->set('bookdate', "$start|$end");
			return $url->getUrl();
		}
 	}

	class DplusoPaths {
		use ThrowErrorTrait;

		/**
		 * Root Path to build off from
		 * @var string
		 */
		private $rootpath;

		private static $instance;

		/**
		 * URL Paths
		 * NOTE if an element is an array then that could mean the element is a menu with subelements making references to subpages of that menu
		 * the '_self' value is the value for the key it traveled from
		 * so $urls['activity']['_self'] would point to where activity page is supposed to be
		 * @var array
		 */
		private $urls = array(
			'actions'  => 'activity',
			'activity' => array(
				'_self'       => 'activity',
				'useractions' => 'user-actions'
			),
			'ajax' => array(
				'_self' => 'ajax',
				'json'  => 'json',
				'load'  => 'load'
			),
			'cart'     => array('_self' => 'cart'),
			'customer' => array(
				'_self'    => 'customers',
				'custinfo' => 'cust-info', // NOTE CHANGE $config->custinfo to this
				'contact'  => 'contact'
			),
			'confirm' => array(
				'_self'      => 'edit',
				'quote'      => 'quote', // NOTE $config->pages->confirmquote
				'order'      => 'order', // NOTE $config->pages->confirmorder
			),
			'documentation' => array('_self' => 'documentation'),
			'edit' => array(
				'_self'      => 'edit',
				'quote'      => 'quote', // NOTE $config->pages->editquote
				'order'      => 'order', // NOTE $config->pages->editorder
				'orderquote' => 'quote-to-order'
			),
			'notes'    => array('_self' => 'notes'),
			'orders'   => array('_self' => 'orders'),
			'print'    => array('_self' => 'print'),
			'products' => array(
				'_self'    => 'products',
				'iteminfo' => 'item-info', // NOTE $config->pages->iteminfo
			),
			'reports'   => array('_self' => 'reports'),
			'sys'       => array(
				'_self' => 'sys',
				'email' => 'email', // NOTE $config->pages->email
			),
			'user' => array(
				'_self'   => 'user',
				'account' => array(
					'_self' => 'account',
					'login' => 'login',
				),
				'dashboard'       => 'dashboard',   // NOTE $config->pages->dashboard
				'configs'         => 'user-config', // NOTE $config->pages->userconfigs
				'screens'         => 'user-screens',
				'tableformatters' => 'table-formatters',
				'orders'          => 'orders',
				'quotes'          => 'quotes'
			),
			'vendor'        => array(
				'_self'      => 'vendors',
				'vendorinfo' => 'vend-info',
			),
			'warehouse' => array(
				'_self'   => 'warehouse',
				'picking' => array(
					'_self'    => 'picking',    // NOTE $config->pages->warehousepicking
					'order'    => 'pick-order', // NOTE $config->pages->salesorderpicking
					'pickpack' => 'pick-pack'   // NOTE $config->pages->salesorderpickpacking
				),
				'binr' => array(
					'_self' => 'binr',
					'binr'  => array('_self' => 'binr'),  // NOTE $config->pages->binr
				),
				'inventory' => array(
					'_self'         => 'inventory',
					'physicalcount' => 'physical-count'
				)
			),
		);

		public function __get($key) {
			return $this->get_urlpath($key);
		}

		private function __construct() {
			$this->rootpath = DplusoConfigURLs::get_rootpath();
		}

		public static function get_instance() {
			if (empty(self::$instance)) {
				self::$instance = new DplusoPaths();
			}
			return self::$instance;
		}

		public function get_urlpath($key) {
			$path = $this->get_pathfromkey($key);
			return "{$this->rootpath}$path";
		}

		/**
		 * Returns the paths found by parsing the key into an array
		 * 1. $key is parsed into an array delimited by an underscore (_)
		 *      Example $key = "vendor"       -> $keys = array('vendor');
		 *      Example $key = "vendor_info"  -> $keys = array('vendor', 'info')
		 * 2.   Then we iterate through the $keys and go down the $this->urls array
		 * @param  string $key Key to Page
		 * @return string      Page Path
		 */
		public function get_pathfromkey(string $key) {
			$keys = explode('_', $key);
			$paths = array();

			// Check that this key exists else throw error
			if (isset($this->urls[$keys[0]])) {

				// Check if this $keys[0] is an array, then travel down
				if (is_array($this->urls[$keys[0]])) {

					// Set array to the array value found at $keys[0]
					$level = $this->urls[$keys[0]];

					// Automatically append the level's _self value to $paths, then remove it from $keys
					$paths[] = $level['_self'];
					array_shift($keys);

					while (is_array($level) && !empty($keys)) {

						// Check that this key exists else throw error
						if (key_exists($keys[0], $level)) {
							if (is_array($level[$keys[0]])) {
								// Add $levels[$key[0]]['_self'] to Paths
								// Set $level to $levels[$key[0]] to travel down to
								$paths[] = array($level[$keys[0]]['_self']);
								$level = $level[$keys[0]];
								array_shift($keys);
							} else {
								// $level[$keys[0]] is a value so we just append that value to paths
								$paths[] = $level[$keys[0]];
								break;
							}
						} else {
							$this->error_keypath($keys, $paths);
						}
					}
					return implode('/', $paths);
				} else {
					return $this->urls[$keys[0]];
				}
			} else {
				$this->error_keypath($keys, $paths);
			}
		}

		/**
		 * Throws an E_USER_ERROR because Key was not found in array
		 * @param  array  $keys  Keys Array
		 * @param  array  $paths Paths array
		 * @return void
		 */
		protected function error_keypath(array $keys, array $paths) {
			$currentkey = $keys[0];
			$path = implode('/', $paths);
			$this->error("$currentkey was not found under $path");
		}
	}
