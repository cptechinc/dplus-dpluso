<?php
	namespace Dplus\Dpluso\Configs;
	
	use Dplus\Base\ThrowErrorTrait;
	
	/**
	 * Class for Accessing Dpluso URLs and Config Values such as Dpluso Server Address, Root Path
	 */
	class DplusoConfigURLs {
		use ThrowErrorTrait;
		
		/**
		 * Root Path to build off from
		 * @var string
		 */
		static $_rootpath;
		
		/**
		 * Domain or IP address to send Requests to 
		 * @var string
		 */
		static $_serveraddress;
		
		/**
		 * Root Directory for Server
		 * NOTE Mainly used if IP address is used
		 * @var string
		 */
		static $_serverdirectory;
		
		/**
		 * Base Path, is built off self::$_rootpath
		 * @var string
		 */
		protected $_path;
		
		/**
		 * Constructor Function
		 * Sets the basepath from the static property
		 */
		function __construct() {
			$this->_path = self::$_rootpath;
			$this->urls = new DplusoURLS();
		}
		
		/**
		 * Returns root path
		 * @return string Root Path
		 */
		function __toString() {
			return $this->_path;
		}
		
		/**
		 * Returns the path that is found for the given $key
		 * @param  string $key Path Key 
		 * @return string      URL Path
		 */
		function __get($key) {
			$path = $this->urls->get_pathfromkey($key);
			return "{$this->_path}$path/";
		}
		
		
		static function set_rootpath($path) {
			self::$_rootpath = $path;
		}
	}
	
	/**
	 * Class for parsing keys and looking up the path by traveling down its urls property
	 */
	class DplusoURLS implements \ArrayAccess {
		use ThrowErrorTrait;

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
				'_self' => 'user',
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
		
		/* =============================================================
			Array Access Interface Methods
		============================================================ */
		public function offsetSet($offset, $value) {
	        if (is_null($offset)) {
	            $this->urls[] = $value;
	        } else {
	            $this->urls[$offset] = $value;
	        }
	    }

	    public function offsetExists($offset) {
	        return isset($this->urls[$offset]);
	    }

	    public function offsetUnset($offset) {
	        unset($this->urls[$offset]);
	    }

	    public function offsetGet($offset) {
	        return isset($this->urls[$offset]) ? $this->urls[$offset] : null;
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
