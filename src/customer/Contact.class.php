<?php
	/**
	 * Class for dealing with Contacts in Dpluso
	 * Contacts are loaded from custindex
	 */
    class Contact {
        use CreateFromObjectArrayTraits;
		use CreateClassArrayTraits;
		use ThrowErrorTrait;
		use MagicMethodTraits;

		public $recno;
		public $date;
		public $time;
		public $splogin1;
		public $splogin2;
		public $splogin3;
		public $custid;
		public $shiptoid;
		public $name;
		public $addr1;
		public $addr2;
		public $city;
		public $state;
		public $zip;
		public $phone;
		public $cellphone;
		public $contact;
		public $source;
		public $extension;
		public $email;
		public $typecode;
		public $faxnbr;
		public $title;

		/**
		 * Contact for Accounts Receivable [Billto only]
		 * @var string Y | N
		 */
		public $arcontact;

		/**
		 * Contact for Dunning [Billto only]
		 * @var string Y | N
		 */
		public $dunningcontact;

		/**
		 * Contact for Buying
		 * NOTE each Customer and Customer Shipto may have one [P]rimary buyer
		 * @var string P | Y | N
		 */
		public $buyingcontact;

		/**
		 * Contact for Certificates
		 * @var string Y | N
		 */
		public $certcontact;

		/**
		 * Contact for Acknowledgments [Billto only]
		 * @var string Y | N
		 */
		public $ackcontact;

		public $dummy;
        public $fieldaliases = array(
            'custID' => 'custid',
            'shipID' => 'shiptoid',
        );

		/**
		 * Contact Types
		 * @var array
		 */
		public static $types = array(
			'customer' => 'C',
			'customer-contact' => 'CC',
			'customer-shipto' => 'CS',
			'shipto-contact' => 'SC'
		);

        /* =============================================================
			GETTER FUNCTIONS
		============================================================ */
		/**
		 * Grabs the name of the customer off the contact object, and if blank,
		 * it will just return custid
		 * @return string customername
		 */
        public function get_customername() {
            return (!empty($this->name)) ? $this->name : $this->custid;
        }

        /**
         * Returns if Contact has a shiptoid
         * @return bool Has shiptoid?
         */
        public function has_shipto() {
            return (!empty($this->shiptoid));
        }

        /**
         * Returns if contact has phone extension
         * @return bool Has Phone Extension?
         */
        public function has_extension() {
            return (!empty($this->extension)) ? true : false;
        }

		/**
         * Returns if contact has cell phone
         * @return bool Has Cellphone Number?
         */
        public function has_cellphone() {
            return (!empty($this->cellphone)) ? true : false;
        }

		/**
		 * Returns if Contact is the AR Contact
		 * @return bool is AR contact?
		 */
		public function is_arcontact() {
			return ($this->arcontact == 'Y') ? true : false;
		}

		/**
		 * Returns if Contact is the Dunning Contact
		 * @return bool is dunning contact?
		 */
		public function is_dunningcontact() {
			return ($this->dunning == 'Y') ? true : false;
		}

		/**
		 * Returns if Contact is a buying Contact
		 * @return bool is buying contact?
		 */
		public function is_buyingcontact() {
			return ($this->buyingcontact == 'Y' || $this->buyingcontact == 'P') ? true : false;
		}

		/**
		 * Returns if Contact is the Primary Buyer Contact
		 * @return bool is this contact the primary buyer?
		 */
		public function is_primarybuyer() {
			return ($this->buyingcontact == 'P') ? true : false;
		}

		/**
		 * Returns if Contact is the Certificate Contact
		 * At Stat it's the End User
		 * @return bool is this the Cert contact or End user?
		 */
		public function is_certcontact() {
			return ($this->certcontact == 'Y') ? true : false;
		}

		/**
		 * Returns if Contact is the Acknowledgment Contact
		 * @return bool Is this the Acknowledgement Contact
		 */
		public function is_ackcontact() {
			return ($this->ackcontact == 'Y') ? true : false;
		}

        /**
		 * Returns AR Contact value
		 * // NOTE if empty will return 'N'
		 * @return string Is this the AR Contact
		 */
		public function get_arcontact() {
            return !empty($this->arcontact) ? $this->arcontact : 'N';
        }

        /**
		 * Returns Dunning Contact value
		 * // NOTE if empty will return 'N'
		 * @return string Is this the Dunning Contact
		 */
		public function get_dunningcontact() {
            return !empty($this->dunningcontact) ? $this->dunningcontact : 'N';
        }

        /**
		 * Returns Buying Contact value
		 * // NOTE if empty will return 'N'
		 * @return string Is this the Buying Contact
		 */
		public function get_buyingcontact() {
            return !empty($this->buyingcontact) ? $this->buyingcontact : 'N';
        }

        /**
		 * Returns Certificate Contact value
		 * // NOTE if empty will return 'N'
		 * @return string Is this the Certificate Contact
		 */
		public function get_certcontact() {
            return !empty($this->certcontact) ? $this->certcontact : 'N';
        }

        /**
		 * Returns Acknowledgement Contact value
		 * // NOTE if empty will return 'N'
		 * @return string Is this the Acknowledgement Contact
		 */
		public function get_ackcontact() {
            return !empty($this->ackcontact) ? $this->ackcontact : 'N';
        }

        /**
         * Returns if User can edit this contact
         * @param  string $loginID User loginID
         * @return bool          Does the user have the right permissions to edit this contact
         */
        public function can_edit($loginID = '') {
            $loginID = (!empty($loginID)) ? $loginID : DplusWire::wire('user')->loginid;
    		$user = LogmUser::load($loginID);

            if ($user->get_dplusrole() == DplusWire::wire('config')->roles['sales-rep']) {
                return ($this->is_ackcontact() || $this->is_certcontact() || $this->is_buyingcontact()) ? true : false;
            } else {
                return true;
            }
        }


		/* =============================================================
			SETTER FUNCTIONS
		============================================================ */
		/**
		 * Determines the Source of the Contact
		 * CS means shipto CC is Contact Customer
		 */
		public function set_contacttype() {
			$this->source = $this->has_shipto() ? 'CS' : 'CC';
		}

		/* =============================================================
			CLASS FUNCTIONS
		============================================================ */
		/**
		 * Generates the URL to the customer page which currently
		 * goes to load the CI Page.
		 * @return string Customer Page URL
		 */
        public function generate_customerurl() {
            return $this->generate_ciloadurl();
        }

		/**
		 * Generates the customer URL but also defines the Shiptoid in the URL
		 * @return string Customer Shipto Page URL
		 */
        public function generate_shiptourl() {
            return $this->generate_customerurl() . "&shipID=".urlencode($this->shiptoid);
        }

		/**
		 * Generates URL to the contact page
		 * @return string Contact Page URL
		 */
        public function generate_contacturl() {
            $url = new \Purl\Url(DplusWire::wire('config')->pages->contact);
            $url->query->set('custID', $this->custid);

            if ($this->has_shipto()) {
                $url->query->set('shipID', $this->shiptoid);
            }
            $url->query->set('contactID', $this->contact);
            return $url->getUrl();
        }

        /**
		 * Generates URL to the edit contact page
		 * @return string Contact Page URL
		 */
        public function generate_contactediturl() {
            $url = new \Purl\Url($this->generate_contacturl());
            $url->path->add('edit');
            return $url->getUrl();
        }

		/**
		 * Generates the load customer URL to get to the CI PAGE
		 * @return string CI PAGE URL
		 */
	    public function generate_ciloadurl() {
            $url = $this->generate_redirurl();
            $url->query->set('action', 'ci-customer');
            $url->query->set('custID', $this->custid);

			if ($this->has_shipto()) {
                $url->query->set('shipID', $this->shiptoid);
            }
            return $url->getUrl();
		}

		/**
		 * URL to redirect page to set the customer for the cart,
		 * redirects to the cart
		 * @return string
		 */
        public function generate_setcartcustomerurl() {
            $url = $this->generate_redirurl();
            $url->query->set('action', 'shop-as-customer');
            $url->query->set('custID', $this->custid);

			if ($this->has_shipto()) {
                $url->query->set('shipID', $this->shiptoid);
            }
            return $url->getUrl();
        }

		/**
		 * URL to the customer redirect page, will be used by other functions to extend on
		 * @return string Customer redirect URL
		 */
        public function generate_redirurl() {
            return new \Purl\Url(DplusWire::wire('config')->pages->customer."redir/");
        }

        /**
         * Outputs the javascript function name with parameter
         * @param  string $function which II function
         * @return string Function name with parameter for the call
         */
        public function generate_iifunction($function) {
            switch ($function) {
                case 'ii':
                    return "ii_customer('".$this->custid."')";
                    break;
                case 'ii-pricing':
                    return "chooseiipricingcust('".$this->custid."', '')";
                    break;
                case 'ii-item-hist':
                    return "chooseiihistorycust('".$this->custid."', '')";
                    break;
            }
        }

		/**
		 * Returns Phone with extension
		 * or without it depending if it has one
		 * @return string Phone (with extension)
		 */
		public function generate_phonedisplay() {
			if ($this->has_extension()) {
				return $this->phone . ' Ext. ' . $this->extension;
			} else {
				return $this->phone;
			}
		}

		/**
		 * Takes the method type and makes a proper URL depending on the method
		 * @param  string $method two main groups : phone / email
		 * @return string         url with with the protocol defined
		 */
		public function generate_contactmethodurl($method = false) {
			switch ($method) {
				case 'cell':
					return "tel:".str_replace('-', '', $this->cellphone);
					break;
				case 'phone':
					return "tel:".str_replace('-', '', $this->phone);
					break;
				case 'email':
					return "mailto:".$this->email;
					break;
				default:
					return "tel:".str_replace('-', '', $this->phone);
					break;
			}
		}

		/**
		 * Generates a one line address string
		 * @return string
		 */
		public function generate_address() {
			return $this->addr1 . ' ' . $this->addr2. ' ' . $this->city . ', ' . $this->state . ' ' . $this->zip;
		}

		/**
		 * Returns a display title for end users or buyers
		 * @return string display title
		 * @uses
		 */
		public function generate_buyerorenduserdisplay() {
			$title = '';
			if ($this->is_buyingcontact()) {
				$title = $this->is_primarybuyer() ? 'Primary Buyer' : 'Buyer';
			} elseif ($this->is_certcontact()) {
				if (DplusWire::wire('config')->dpluscustomer == 'stat') {
					$title = 'End User';
				}
			}
			return $title;
		}

		/* =============================================================
			CRUD FUNCTIONS
		============================================================ */
		/**
		 * Creates a new contact in the database
		 * Custid is trimmed to match the character length in the Cobol Dplus
		 * @param  bool $debug Determines if query will execute and if SQL is returned or Contact object
		 * @return Contact         OR SQL QUERY
		 */
		public function create($debug = false) {
            $this->custid = substr($this->custid, 0, 6);
            return insert_customerindexrecord($this, $debug);
		}

		/**
		 * Loads an object with this class using the parameters as provided
		 * @param  string  $custID    CustomerID
		 * @param  string  $shiptoID  ShiptoID  **Optional
		 * @param  string  $contactID Contact Name **Optional
		 * @param  bool $debug     Determines if query will execute and if sQL is returned or Contact object
		 * @return Contact           Or SQL query string
		 */
        public static function load($custID, $shiptoID = '', $contactID = '', $debug = false) {
            return get_customercontact($custID, $shiptoID, $contactID, $debug);
        }

        /**
         * Returns if User has access to contact
         * @param  string $custID    Customer ID
         * @param  string $shiptoID  Customer shipto ID
         * @param  string $contactID Customer (shipto) Contact ID
         * @param  string $loginID   User Login ID
         * @param  bool   $debug     Run in debug?
         * @return bool              TRUE | FALSE | SQL QUERY
         */
        public static function can_useraccess($custID, $shiptoID = '', $contactID = '', $loginID = '', $debug = false) {
            return can_accesscustomercontact($custID, $shiptoID, $contactID, $loginID, $debug);
        }

		/**
		 * Returns the primary Contact of a Customer Shipto
		 * ** NOTE each Customer and Customer Shipto may have one Primary buyer
		 * @param  string  $custID CustomerID
		 * @param  string  $shiptoID ShiptoID **Optional
		 * @param  bool $debug  Determines if query will execute and if sQL is returned or Contact object
		 * @return Contact          Or SQL query string
		 */
		public static function load_primarycontact($custID, $shiptoID = '', $debug = false) {
			return get_primarybuyercontact($custID, $shiptoID, $debug);
		}

		/**
		 * Updates the Contact in the database
		 * @param  bool $debug Determines if query will execute and if sQL is returned or Contact object
		 * @return Contact         SQL query string
		 */
		public function update($debug = false) {
			return update_contact($this, $debug);
		}

		/**
		 * Updates the Contact ID
		 * @param  string  $contactID Contact ID
		 * @param  bool $debug     Determines if query will execute and if sQL is returned or Contact object
		 * @return string            SQL Query
		 */
		public function change_contactid($contactID, $debug = false) {
			return change_contactid($this, $contactID, $debug);
		}

		/**
		 * Checks if there are changes between this contact and the database record
		 * @return bool Whether contact has changes from database
		 */
		public function has_changes() {
			$properties = array_keys($this->_toArray());
			$contact = self::load($this->custid, $this->shiptoid, $this->contact);

			foreach ($properties as $property) {
				if ($this->$property != $contact->$property) {
					return true;
				}
			}
			return false;
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
			unset($array['fieldaliases']);
			unset($array['types']);
			return $array;
		}
    }
