<?php
	namespace Dplus\Dpluso\Bookings;

	/**
	 * External Libraries
	 */
	use ProcessWire\WireInput;

	/**
	 * Internal Libraries
	 */
	use Dplus\Dpluso\Configs\DplusoConfigURLs;
	use Dplus\Dpluso\OrderDisplays\OrderPanelCustomerTraits;

	/**
	 * Use Statements for Model Classes which are non-namespaced
	 */
	use Customer;

	/**
	 * Class for handling of getting and displaying booking records from the database for a Customer
	 * @author Barbara Bullemer barbara@cptechinc.com
	 */
	class CustomerBookingsPanel extends BookingsPanel {
		use OrderPanelCustomerTraits;

		/* =============================================================
			GETTER FUNCTIONS
		============================================================ */
		/**
		 * Queries the database and returns with booking records
		 * that meets the criteria defined in the $this->filters array
		 * @param  string $loginID  User LoginID, if blank will use current User
		 * @param  bool   $debug    Run debug? If so, will return SQL Query
		 * @return array            Booking records
		 */
		public function get_bookings($loginID = '', $debug = false) {
			$this->determine_interval();
			$bookings = get_customerbookings($this->custID, $this->shipID, $this->filters, $this->filterable, $this->interval, $loginID, $debug);
			return $debug ? $bookings : $this->bookings = $bookings;
		}

		/**
		 * Get the bookings made for that date
		 * @param  string $date     Date in m/d/Y format
		 * @param  string $loginID  User LoginID, if blank will use current User
		 * @param  bool   $debug    Run debug? If so, will return SQL Query
		 * @return array            Sales Order Booking Records for each Sales Order
		 */
		public function get_daybookingordernumbers($date, $loginID = '', $debug = false) {
			return get_customerdaybookingordernumbers($date, $this->custID, $this->shipID, $loginID, $debug);
		}

		/**
		 * Count the bookings made for that date
		 * @param  string $date     Date m/d/Y format
		 * @param  string $loginID  User Login ID, if blank, will use the current User
		 * @param  bool   $debug    Run debug? If so, will return SQL Query
		 * @return int              Number of distinct Sales Order Numbers
		 */
		public function count_daybookingordernumbers($date, $loginID = '', $debug = false) {
			return count_customerdaybookingordernumbers($date, $this->custID, $this->shipID, $loginID, $debug);
		}

		/**
		 * Count the booking records for that day
		 * @param  string $loginID  User Login ID, if blank, will use the current User
		 * @param  bool  $debug     Run debug? If so, will return SQL Query
		 * @return int              Count | SQL Query
		 */
		public function count_todaysbookings($loginID = '', $debug = false) {
			return count_customertodaysbookings($this->custID, $this->shipID, $loginID, $debug);
		}

		/**
		 * Get the Amount booked today
		 * @param  string $loginID  User LoginID, if blank will use current User
		 * @param  bool   $debug    Run in debug? If true, return SQL Query
		 * @return int              Amount booked today
		 */
		 public function get_todaysbookingamount($loginID = '', $debug = false) {
 			return get_customertodaybookingamount($this->custID, $this->shipID, $loginID, $debug);
 		}

		/**
		 * Return total bookings amounts foreach Shipto for defined Customer
		 * @param  string $loginID  User LoginID, if blank will use current User
		 * @param  bool   $debug    Run in debug? If true, return SQL Query
		 * @return array            Totals by shipto
		 */
		public function get_bookingtotalsbyshipto($loginID = '', $debug = false) {
			$bookings = get_bookingtotalsbyshipto($this->custID, $this->shipID, $this->filters, $this->filterable, $this->interval, $loginID, $debug);
			return $debug ? $bookings : $this->bookings = $bookings;
		}

		/**
		 * Get the detail lines for a booking
		 * @param  string $ordn     Sales Order Number
		 * @param  string $date     Datetime string usually in m/d/Y format
		 * @param  string $loginID  User LoginID, if blank will use current User
		 * @param  bool   $debug    Run in debug? If true, return SQL Query
		 * @return array            Booking Order Details
		 */
		public function get_bookingdayorderdetails($ordn, $date, $loginID = '', $debug = false) {
			return get_bookingdayorderdetails($ordn, $date, false, false, $loginID, $debug);
		}

		/**
		 * Determines the interval to use based on the filters
		 * and based on the interval it creates the title description
		 * @return string  "Viewing (daily | weekly | monthly) bookings between $from and $through"
		 */
		public function generate_title() {
			$this->determine_interval();

			if (!empty($this->interval)) {
				$intervaldesc = self::$intervals[$this->interval];
				$from = $this->filters['bookdate'][0];
				$through = $this->filters['bookdate'][1];
				$customer = Customer::load($this->custID, $this->shipID);
				return "Viewing {$customer->get_customername()} $intervaldesc bookings between $from and $through";
			}
		}

		/* =============================================================
			SETTER FUNCTIONS
		============================================================ */


		/* =============================================================
			CLASS FUNCTIONS
		============================================================ */

		/**
		 * Looks through the $input->get for properties that have the same name
		 * as filterable properties, then we populate $this->filter with the key and value
		 * @param  WireInput $input Use the get property to get at the $_GET[] variables
		 */
		public function generate_filter(WireInput $input) {
			if (!$input->get->filter) {
				$this->filters = array(
					'bookdate' => array(date('m/d/Y', strtotime('-1 year')), date('m/d/Y'))
				);
			} else {
				$this->filters = array();

				foreach ($this->filterable as $filter => $type) {
					if (!empty($input->get->$filter)) {
						if (!is_array($input->get->$filter)) {
							$value = $input->get->text($filter);
							$this->filters[$filter] = explode('|', $value);
						} else {
							$this->filters[$filter] = $input->get->$filter;
						}
					} elseif (is_array($input->get->$filter)) {
						if (strlen($input->get->$filter[0])) {
							$this->filters[$filter] = $input->get->$filter;
						}
					}
				}

				if (!isset($this->filters['bookdate'])) {
					$this->generate_defaultfilter($input);
				}
			}
		}

		/**
		 * Defines the filter for default
		 * Goes back one year
		 * @param  WireInput $input Use the get property to get at the $_GET[] variables
		 * @param  string                $interval Allows to defined interval
		 * @return void
		 */
		protected function generate_defaultfilter(WireInput $input, $interval = '') {
			if (!empty($inteval)) {
				$this->set_interval($interval);
			}

			if (!$input->get->filter) {
				$this->filters = array(
					'bookdate' => array(date('m/d/Y', strtotime('-1 year')), date('m/d/Y'))
				);
			} else {
				$this->filters['bookdate'] = array(date('m/d/Y', strtotime('-1 year')), date('m/d/Y'));
			}
		}

		/**
		 * Returns the URL to view the date provided's bookings
		 * @param  string $date Date to view Orders for
		 * @return string       URL to view the date's booked orders
		 */
		public function generate_viewsalesordersbydayURL($date) {
			$urlconfig = DplusoConfigURLs::get_instance();
			return $urlconfig->get_bookings_salesorders_bydayURL($date, $this->custID, $this->shipID);
		}

		/**
		 * Returns URL to view the bookingsfor a sales order on a particular date
		 * @param  string $ordn Sales Order #
		 * @param  string $date Date
		 * @return string       URL to view bookings for that order # and date
		 */
		public function generate_viewsalesorderdayURL($ordn, $date) {
			$urlconfig = DplusoConfigURLs::get_instance();
			return $urlconfig->get_bookings_day_salesorderURL($ordn, $date, $this->custID, $this->shipID);
		}
	}
