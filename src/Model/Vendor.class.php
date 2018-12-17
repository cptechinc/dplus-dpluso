<?php
	use Dplus\ProcessWire\DplusWire;
	
	use Purl\Url;
	
	/**
	 * Deals with Vendors from vendors Table
	 */
	class Vendor {
		use Dplus\Base\ThrowErrorTrait;
		use Dplus\Base\MagicMethodTraits;
		use Dplus\Base\CreateFromObjectArrayTraits;
		use Dplus\Base\CreateClassArrayTraits;
		
		/**
		 * Vendor ID
		 * @var string
		 */
		protected $vendid;
		
		/**
		 * Vendor Ship From
		 * @var string
		 */
		protected $shipfrom;
		
		/**
		 * Vendor Name
		 * @var string
		 */
		protected $name;
		
		/**
		 * Vendor Address 1
		 * @var string
		 */
		protected $address1;
		
		/**
		 * Vendor Address 2
		 * @var string
		 */
		protected $address2;
		
		/**
		 * Vendor Address 3
		 * @var string
		 */
		protected $address3;
		
		/**
		 * City
		 * @var string
		 */
		protected $city;
		
		/**
		 * State
		 * @var string
		 */
		protected $state;
		
		/**
		 * Zip Code
		 * @var string
		 */
		protected $zip;
		
		/**
		 * Country
		 * @var string
		 */
		protected $country;
		
		/**
		 * Phone Number
		 * @var string
		 */
		protected $phone;
		
		/**
		 * Fax Number
		 * @var string
		 */
		protected $fax;
		
		/**
		 * Email Address
		 * @var string
		 */
		protected $email;
		
		/**
		 * Updated Time
		 * @var int HHMMSSSS
		 */
		protected $createtime;
		
		/**
		 * Updated Date
		 * @var int YYYYMMDD
		 */
		protected $createdate;
		
		/**
		 * Property Aliases
		 * @var array
		 */
		protected $fieldaliases = array(
			'vendorID'   => 'vendid',
			'vendorid'   => 'vendid',
			'shipfromID' => 'shipfrom'
		);

		/* =============================================================
			GETTER FUNCTIONS
		============================================================ */
		/**
		 * Return Vendor Name
		 * @return string Vendor Name or Vendor ID
		 */
		public function get_name() {
			return (!empty($this->name)) ? $this->name : $this->vendid;
		}

		/**
		 * Returns Vendor Name and Shipfrom
		 * Used for Vendor Information
		 * @return string
		 */
		public function generate_title() {
			return $this->get_name() . (($this->has_shipfrom()) ? ' Shipfrom: '.$this->shipfrom : '');
		}

		/**
		 * Is Shipfrom defined?
		 * @return bool
		 */
		public function has_shipfrom() {
			return (!empty($this->shipfrom));
		}

		/**
		 * // TODO rename for URL()
		 * Returns URL to load Vendor Information page for that Vendor
		 * @param  bool   $withshipfrom Whether or not to use $this->shipfromid
		 * @return string URL to load Vendor Information page
		 */
		public function generate_viurl($withshipfrom = true) {
			$url = new Url(DplusWire::wire('config')->pages->vendorinfo);
			$url->path->add($this->vendid);

			if ($withshipfrom) {
				$url->path->add('shipfrom-'.$this->shipfrom);
			}
			return $url->getUrl();
		}

		/* =============================================================
			CRUD FUNCTIONS
		============================================================ */
		/**
		 * Returns Vendor from database
		 * @param  string $vendorID   Vendor ID
		 * @param  string $shipfromID Vendor Shipfrom ID
		 * @param  bool   $debug      Whether to return Vendor or SQL Query
		 * @return Vendor             or SQL Query
		 */
		public static function load($vendorID, $shipfromID = '', $debug = false) {
			return get_vendor($vendorID, $shipfromID, $debug);
		}

		/* =============================================================
			GENERATE ARRAY FUNCTIONS
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
			return $array;
		}
	}
