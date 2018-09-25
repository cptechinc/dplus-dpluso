<?php
	/**
	 * Class for dealing with Customers
	 * Derived from the custindex Table
	 */
    class Customer extends Contact {
        use CreateFromObjectArrayTraits;
		use CreateClassArrayTraits;
        
        /* =============================================================
			GETTER FUNCTIONS
		============================================================ */
		/**
		 * Returns the customer name or just return the custid
		 * @return string Name or Customer ID
		 */
        public function get_name() {
            return (!empty($this->name)) ? $this->name : $this->custid;
        }

		/**
		 * Return the number of shiptos this Customer has
		 * it also takes in account if user has customer restrictions
		 * @param  string  $loginID  Login ID to count shiptos for
		 * @param  bool    $debug    Whether to return Number or SQL Query
		 * @return int               # of Shiptos or SQL Query
		 */
        public function count_shiptos($loginID = '', $debug = false) {
            return count_shiptos($this->custid, $loginID, $debug);
        }

		/**
		 * Gets the next shiptoid for this Customer
		 * if shiptoid is defined then we get the next one
		 * if no next one, go to the first one
		 * Used for CI page
		 * @param  string $loginID Login ID
		 * @return string shiptoid
		 */
        public function get_nextshiptoid($loginID = '') {
            $shiptos = get_customershiptos($this->custid, $loginID);
            if (sizeof($shiptos) < 1) {
                return false;
            } else {
                if ($this->has_shipto()) {
                    for ($i = 0; $i < sizeof($shiptos); $i++) {
                        if ($shiptos[$i]->shiptoid == $this->shiptoid) {
                            break;
                        }
                    }
                    $i++; // Get the next

                    if ($i > sizeof($shiptos)) {
                        return $shiptos[0]->shiptoid;
                    } elseif ($i == sizeof($shiptos)) {
                        return $shiptos[$i - 1]->shiptoid;
                    } else {
                        $shiptos[$i]->shiptoid;
                    }
                } else {
                    return $shiptos[0]->shiptoid;
                }
            }
        }

        /**
         * Returns the number of contacts for this Customer
         * @param  string $loginID User Login ID, Can be blank, will assume current user
         * @param  bool   $debug   Run in debug? if true, will return SQL Query
         * @return int             Number of Contacts
         */
        public function count_contacts($loginID = '', $debug = false) {
            return count_customercontacts($this->custid, $loginID, $debug);
        }

        /**
         * Returns the contacts for this Customer
         * @param  string $loginID User Login ID, Can be blank, will assume current user
         * @param  bool   $debug   Run in debug? if true, will return SQL Query
         * @return array           Array of Contact
         */
        public function get_contacts($loginID = '', $debug = false) {
            return get_customercontacts($this->custid, $loginID, $debug);
        }

        /**
         * Returns amount sold for this customer, will default to current user if no ID supplied
         * @param  string $loginID User Login
         * @param  bool   $debug   Run in debug? If true, will return SQL Query
         * @return float           Amount Sold
         */
        public function get_amountsold($loginID = '', $debug = false) {
            return $debug ? get_custperm($this, $loginID, $debug) : (get_custperm($this, $loginID, $debug))['amountsold'];
        }

        /**
         * Returns Number of Times sold (Last 12 Months) for this customer, will default to current user if no ID supplied
         * @param  string $loginID User Login
         * @param  bool   $debug   Run in debug? If true, will return SQL Query
         * @return int           Number of Times sold
         */
        public function get_timesold($loginID = '', $debug = false) {
            return $debug ? get_custperm($this, $loginID, $debug) : (get_custperm($this, $loginID, $debug))['timesold'];
        }

        /**
         * Returns Last Sale Date for this customer, will default to current user if no ID supplied
         * @param  string $loginID User Login
         * @param  bool   $debug   Run in debug? If true, will return SQL Query
         * @return string          Last Sale Date
         */
        public function get_lastsaledate($loginID = '', $debug = false) {
            return $debug ? get_custperm($this, $loginID, $debug) : (get_custperm($this, $loginID, $debug))['lastsaledate'];
        }
        /* =============================================================
			CLASS FUNCTIONS
		============================================================ */
		/**
		 * Generates Title for CI page
		 * @return string title for CI page
		 */
        public function generate_title() {
            return $this->get_name() . (($this->has_shipto()) ? ' Ship-to: ' . $this->shiptoid : '');
        }

		/**
		 * Generates an array for the Sales Data for this Customer
		 * so it can be used in Morris.js to draw up a pie chart
		 * @param  float $value Amount
		 * @return array        has the Name, value, custid and shiptoid in an array
		 */
		public function generate_piesalesdata($value) {
			return array(
				'label' => $this->get_name(),
				'value' => $value,
				'custid' => $this->custid,
				'shiptoid' => $this->shiptoid
			);
		}

		/**
		 * Return URL to the add Contact form
		 * @return string  Add Contact URL
		 */
		public function generate_addcontacturl() {
			$url = new \Purl\Url(DplusWire::wire('config')->pages->contact.'add/');
            $url->query->set('custID', $this->custid);

            if ($this->has_shipto()) {
                $url->query->set('shipID', $this->shiptoid);
            }
            return $url->getUrl();
		}

		/* =============================================================
			CRUD FUNCTIONS
		============================================================ */
        /**
         * Inserts custperm record for user
         * @param  string $loginID User Login ID
         * @param  bool   $debug   Run in debug?
         * @return string          SQL Query
         */
        public function create_custpermpermission($loginID = '', $debug = false) {
            if (!can_accesscustomer($this->custid, $this->shiptoid, $loginID)) {
                insert_custperm($this, LogmUser::get_toplevelcustpermloginid(), $debug);
                return insert_custperm($this, $loginID, $debug);
            }
        }
		/**
		 * Loads an object with this class using the parameters as provided
		 * @param  string $custID    CustomerID
		 * @param  string $shiptoID  Shipto ID (can be blank)
		 * @param  string $contactID Contact ID (can be blank)
		 * @param  bool   $debug     Determines if Query Runs and if Customer Object is returned or SQL Query
		 * @return Customer
		 */
        public static function load($custID, $shiptoID = '', $contactID = '', $debug = false) {
            if ($debug) {
                return get_customer($custID, $shiptoID, $contactID, $debug);
            } else {
                $customer = get_customer($custID, $shiptoID, $contactID, $debug);
                
                if (empty($customer)) {
                    $customer = new NonExistingCustomer();
                    $customer->set('custid', $custID);
                    $customer->set('shiptoid', $shiptoID);
                    $customer->set('contact', $contactID);
                }
                return $customer;
            }
            
        }

        /**
         * Returns if User has access to customer
         * @param  string $custID   Customer ID
         * @param  string $shiptoID Customer shipto ID
         * @param  string $contactID Contact ID
         * @param  string $loginID  User Login ID
         * @param  bool   $debug    Run in debug?
         * @return bool             TRUE | FALSE | SQL QUERY
         */
        public static function can_useraccess($custID, $shiptoID = '', $contactID = '', $loginID = '', $debug = false) {
            return can_accesscustomer($custID, $shiptoID, $loginID,  $debug);
        }

		/**
		 * Changes customer's custid
		 * @param  string $currentID current customerID
		 * @param  string $newcustID new customerID
		 * @param  bool   $debug     Whether to execute change
		 * @return string            SQL Query
		 */
		public static function change_custid($currentID, $newcustID, $debug = false) {
            $currentID = substr($currentID, 0, 6);
			return change_custindexcustid($currentID, $newcustID, $debug). " - " . change_custpermcustid($currentID, $newcustID, $debug);
		}

		/* =============================================================
			STATIC FUNCTIONS
		============================================================ */
		/**
		 * Uses the static function load() function to load the customer
		 * then uses, the Customer->get_customername() to return the name
		 * @param  string $custID   customerID
		 * @param  string $shiptoID customer shiptoID
		 * @return string           Customer Name
		 * @uses load()
		 */
		public static function get_customernamefromid($custID, $shiptoID = '') {
			$customer = self::load($custID, $shiptoID);
			return $customer ? $customer->get_customername() : $custID;
		}

		/**
		 * Generates an array for the bookings Data for this Customer
		 * so it can be used in Morris.js to draw up a pie chart
		 * if customer can't be found, then return data with as much
		 * info as possible
		 * @param  string $custID   Customer
		 * @param  string $shiptoID Shipto ID
		 * @param  float  $value    Amount
		 * @return array        has the Name, value, custid and shiptoid in an array
		 */
		public static function generate_bookingsdata($custID, $shiptoID, $value) {
			$customer = self::load($custID, $shiptoID);
            
			return array(
				'label' => $customer ? $customer->get_name() : $custID,
				'value' => $value,
				'custid' => $custID,
				'shiptoid' => $shiptoID
			);
		}
    }

	class NonExistingCustomer extends Customer {
        public $name = 'Not Available';
    }
