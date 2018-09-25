<?php
	/**
	 * Deals with Vendors from vendors Table
	 */
	class Vendor {
		use ThrowErrorTrait;
		use MagicMethodTraits;
		use CreateFromObjectArrayTraits;
		use CreateClassArrayTraits;

		protected $vendid;
		protected $shipfrom;
		protected $name;
		protected $address1;
		protected $address2;
		protected $address3;
		protected $city;
		protected $state;
		protected $zip;
		protected $country;
		protected $phone;
		protected $fax;
		protected $email;
		protected $createtime;
		protected $createdate;
		protected $fieldaliases = array(
			'vendorID' => 'vendid',
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
		 * @return [type] [description]
		 */
		public function generate_title() {
			return $this->get_name() . (($this->has_shipfrom()) ? ' Shipfrom: '.$this->shipfrom : '');
		}

		/**
		 * IF Shipfrom is defined
		 * @return bool
		 */
		public function has_shipfrom() {
			return (!empty($this->shipfrom));
		}

		/**
		 * Returns URL to load Vendor Information page for that Vendor
		 * @param  bool   $withshipfrom Whether or not to use $this->shipfromid
		 * @return string URL to load Vendor Information page
		 */
		public function generate_viurl($withshipfrom = true) {
			$url = new \Purl\Url(DplusWire::wire('config')->pages->vendorinfo);
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
