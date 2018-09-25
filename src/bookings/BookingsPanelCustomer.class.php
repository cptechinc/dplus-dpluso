<?php
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
		/**
		 * Defines the interval
		 * @param string $interval Must be one of the predefined interval types
		 */
		public function set_interval($interval) {
			if (in_array($interval, array_keys(self::$intervals))) {
				$this->interval = $interval;
			} else {
				$this->error("interval $interval is not valid");
			}
		}

		/* =============================================================
			CLASS FUNCTIONS
		============================================================ */
		/**
		 * Returns the URL to bookings panel's normal state
		 * @return string URL
		 */
		public function generate_refreshurl() {
			$url = new Purl\Url($this->pageurl->getURL());
			$url->query = '';
			return $url->getURL();
		}

		/**
		 * Returns the HTML link for refreshing bookings
		 * @return string HTML link
		 * @uses
		 */
		public function generate_refreshlink() {
			$bootstrap = new HTMLWriter();
			$href = $this->generate_refreshurl();
			$icon = $bootstrap->icon('fa fa-refresh');
			$ajaxdata = $this->generate_ajaxdataforcontento();
			return $bootstrap->create_element('a', "href=$href|class=load-link|$ajaxdata", "$icon Refresh Bookings");
		}

		/**
		 * Returns the HTML link for refreshing bookings
		 * @return string HTML link
		 * @uses
		 */
		public function generate_cleardateparameterslink() {
			$bootstrap = new HTMLWriter();
			$href = $this->generate_refreshurl();
			$icon = $bootstrap->icon('fa fa-times');
			$ajaxdata = $this->generate_ajaxdataforcontento();
			return $bootstrap->create_element('a', "href=$href|class=btn btn-xs btn-warning load-and-show|$ajaxdata", "$icon Remove Date Parameters");
		}

		/**
		 * Looks through the $input->get for properties that have the same name
		 * as filterable properties, then we populate $this->filter with the key and value
		 * @param  ProcessWire\WireInput $input Use the get property to get at the $_GET[] variables
		 */
		public function generate_filter(ProcessWire\WireInput $input) {
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
		 * @param  ProcessWire\WireInput $input Use the get property to get at the $_GET[] variables
		 * @param  string                $interval Allows to defined interval
		 * @return void
		 */
		protected function generate_defaultfilter(ProcessWire\WireInput $input, $interval = '') {
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
		 * Determines the interval needed if inteval not defined
		 * if there are more than 90 days between from and through dates then
		 * the interval is set to month
		 * @return void
		 */
		protected function determine_interval() {
			$days = DplusDateTime::subtract_days($this->filters['bookdate'][0], $this->filters['bookdate'][1]);

			if ($days >= 90 && empty($this->interval)) {
				$this->set_interval('month');
			} elseif (empty($this->interval)) {
				$this->set_interval('day');
			}
		}

		/**
		 * Returns the description for todays bookings
		 * @return string $bookingsamt booking amount for today
		 * @uses   $this->get_todaysbookingamount()
		 */
		public function generate_todaysbookingsdescription() {
			$bookingsamt = !empty($this->get_todaysbookingamount()) ? $this->get_todaysbookingamount() : '0.00';
			return "Dollars booked: $ $bookingsamt";
		}

		/**
		 * Returns the URL to view the date provided's bookings
		 * @param  string $date Date to view Orders for
		 * @return string       URL to view the date's booked orders
		 */
		public function generate_viewsalesordersbydayurl($date) {
			$url = new Purl\Url($this->pageurl->getUrl());
			$url->path = DplusWire::wire('config')->pages->ajaxload."bookings/sales-orders/";
			$url->query = '';
			$url->query->set('date', $date);
			$url->query->set('custID', $this->custID);
			if (!empty($this->shipID)) {
				$url->query->set('shipID', $this->shipID);
			}
			return $url->getUrl();
		}

		/**
		 * Returns HTML Link to view the days booked sales orders
		 * @param  string $date Date for viewing bookings
		 * @return string       HTML Link to view booked sales orders
		 * @uses   $this->generate_viewsalesordersbydayurl($date)
		 */
		public function generate_viewsalesordersbydaylink($date) {
			$bootstrap = new HTMLWriter();
			$href = $this->generate_viewsalesordersbydayurl($date);
			$icon = $bootstrap->icon('glyphicon glyphicon-new-window');
			$ajaxdata = "data-modal=$this->modal";
			return $bootstrap->create_element('a', "href=$href|class=load-into-modal btn btn-primary btn-sm|$ajaxdata", "$icon View Sales Orders");
		}

		/**
		 * Returns URL to view the bookingsfor a sales order on a particular date
		 * @param  string $ordn Sales Order #
		 * @param  string $date Date
		 * @return string       URL to view bookings for that order # and date
		 */
		public function generate_viewsalesorderdayurl($ordn, $date) {
			$url = new Purl\Url($this->pageurl->getUrl());
			$url->path = DplusWire::wire('config')->pages->ajaxload."bookings/sales-order/";
			$url->query = '';
			$url->query->set('ordn', $ordn);
			$url->query->set('date', $date);
			$url->query->set('custID', $this->custID);
			if (!empty($this->shipID)) {
				$url->query->set('shipID', $this->shipID);
			}
			return $url->getUrl();
		}

		/**
		 * Returns HTML Link to view the bookings bookingsfor a sales order on a particular date
		 * @param  string $ordn Sales Order #
		 * @param  string $date Date
		 * @return string       HTML Link to view bookings for that order # and date
		 * @uses $this->generate_viewsalesorderdayurl($ordn, $date);
		 */
		public function generate_viewsalesorderdaylink($ordn, $date) {
			$bootstrap = new HTMLWriter();
			$href = $this->generate_viewsalesorderdayurl($ordn, $date);
			$icon = $bootstrap->icon('glyphicon glyphicon-new-window');
			$ajaxdata = "data-modal=$this->modal";
			return $bootstrap->create_element('a', "href=$href|class=modal-load btn btn-primary btn-sm|$ajaxdata", "$icon View Sales Order changes on $date");
		}
	}
