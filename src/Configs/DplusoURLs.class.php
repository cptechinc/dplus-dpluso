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
		use BookingsURLsTraits;
		use CartURLsTraits;
		use ItemLookupURLsTraits;

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

	

	
	

	

	
