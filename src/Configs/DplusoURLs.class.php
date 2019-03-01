<?php
	namespace Dplus\Dpluso\Configs;

	/**
	 * External Libraries
	 */
	use Purl\Url;

	/**
	 * Internal Libraries
	 */
	use Dplus\Base\ThrowErrorTrait;

	/**
	 * Class for Providing URLs to Dplus Pages / Requests / Functions
	 */
	class DplusoConfigURLs {
		use ThrowErrorTrait;
		use CustomerURLsTraits;
		use OrderURLsTraits;
		use BookingsURLsTraits;
		use CartURLsTraits;
		use ItemLookupURLsTraits;
		use VendorURLsTraits;

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

		/**
		 * Returns Instance of Self
		 * @return string DplusoConfigURLs
		 */
		public static function get_instance() {
			if (empty(self::$instance)) {
				self::$instance = new DplusoConfigURLs();
			}
			return self::$instance;
		}

		/**
		 * Constructor Function
		 * Initializes $this->paths
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
		 *  Parse URL Path from $key
		 * @param  string $key URL Path String
		 * @return string      URL Path
		 */
		function find($key) {
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

	

	
	

	

	
