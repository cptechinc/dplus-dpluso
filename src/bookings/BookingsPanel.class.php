<?php
	/**
	 * Class for handling of getting and displaying booking records from the database
	 * @author Paul Gomez paul@cptechinc.com
	 */
	class BookingsPanel {
		use ThrowErrorTrait;
		use MagicMethodTraits;
		use AttributeParser;

		/**
		 * Object that stores page location and where to load
		 * and search from
		 * @var Purl\Url
		 */
		protected $pageurl;
		/**
		 * Session Identifier
		 * @var string
		 */
		protected $sessionID;

		/**
		 * Modal to use
		 * @var string
		 */
		protected $modal;

		/**
		 * String that contains attributes for ajax loading
		 * @var string
		 */
		protected $ajaxdata;

		/**
		 * What path segment to paginate after
		 * @var string
		 */
		protected $paginateafter = 'bookings';

		/**
		 * Array of booking records
		 * @var string
		 */
		protected $bookings;
		/**
		 * What interval to Use
		 * day | week | month
		 * // NOTE if blank, the default is day unless there's more than 90 days then we switch to month
		 * @var string
		 */
		protected $interval;

		/**
		 * Array of filters to filterdown the data
		 * @var array
		 */
		protected $filters = false;

		/**
		 * Array of filterable fields and the attributes
		 * for each filterable
		 * @var array
		 */
		protected $filterable = array(
			'bookdate' => array(
				'querytype' => 'between',
				'datatype' => 'date',
				'date-format' => 'Ymd',
				'label' => 'Book Date'
			),
			'amount' => array(
				'querytype' => 'between',
				'datatype' => 'decimal',
				'label' => 'Amount'
			)
		);

		/**
		 * Time intervals used in the filtering of data
		 * @var array
		 */
		public static $intervals = array('day' => 'daily', 'week' => 'weekly', 'month' => 'monthly');

		/* =============================================================
			CONSTRUCTOR FUNCTIONS
		============================================================ */
		/**
		 * Constructor
		 * @param string  $sessionID Session Identifier
		 * @param Purl\Url $pageurl  Object that contains the URL of the page
		 * @param string  $modal     ID of Modal to use
		 * @param bool    $ajaxdata  Attributes used for ajax loading
		 * @uses
		 */
		public function __construct($sessionID, Purl\Url $pageurl, $modal = '', $ajaxdata = false) {
			$this->sessionID = $sessionID;
			$this->pageurl = new Purl\Url($pageurl->getUrl());
			$this->modal = $modal;
			$this->ajaxdata = $this->parse_ajaxdata($ajaxdata);
			$this->setup_pageurl();
		}

		/* =============================================================
			GETTER FUNCTIONS
		============================================================ */
		/**
		 * Queries the database and returns with booking records
		 * that meets the criteria defined in the $this->filters array
		 * @param  string $loginID  User LoginID, if blank will use current User
		 * @param  bool   $debug    Run in debug? If so, will return SQL Query
		 * @return array            Booking records | SQL Query
		 */
		public function get_bookings($loginID = '', $debug = false) {
			$this->determine_interval();
			$bookings = get_userbookings($this->filters, $this->filterable, $this->interval, $loginID, $debug);
			return $debug ? $bookings : $this->bookings = $bookings;
		}

		/**
		 * Get the bookings made for that date
		 * @param  string $date     Datetime string usually in m/d/Y format
		 * @param  string $loginID  User LoginID, if blank will use current User
		 * @param  bool   $debug    Run in debug? If so, will return SQL Query
		 * @return array            bookingd records | SQL Query
		 */
		public function get_daybookingordernumbers($date, $loginID = '', $debug = false) {
			return get_daybookingordernumbers($date, $custID = false, $shiptoID = false, $loginID, $debug);
		}

		/**
		 * Count the bookings made for that date
		 * @param  string $date     Datetime string usually in m/d/Y format
		 * @param  string $loginID  User LoginID, if blank will use current User
		 * @param  bool   $debug    Run in debug? if so, will return SQL Query
		 * @return int              count| SQL Query
		 */
		public function count_daybookingordernumbers($date, $loginID = '',  $debug = false) {
			return count_daybookingordernumbers($date, false, false, $loginID, $debug);
		}

		/**
		 * Count the booking records for that day
		 * @param  string $loginID  User LoginID, if blank will use current User
		 * @param  bool   $debug    Run in debug? If so, return SQL Query
		 * @return int              Count | SQL Query
		 */
		public function count_todaysbookings($loginID = '', $debug = false) {
			return count_todaysbookings(false, false, $loginID, $debug);
		}

		/**
		 * Get the Amount booked today
		 * @param  string $loginID User Login ID to get bookings amount, leave blank for current user
		 * @param  bool   $debug   Run in debug? If true, return SQL Query
		 * @return int             Amount booked today
		 */
		public function get_todaysbookingamount($loginID = '', $debug = false) {
			return get_todaysbookingsamount('', '', $loginID, $debug);
		}

		/**
		 * Returns total bookings amounts for each customer during the timeframe specifeld
		 * @param  string $loginID  User Login ID to get bookings amount, leave blank for current user
		 * @param  bool   $debug    Run in debug? If true, return SQL Query
		 * @return array            Results | SQL Query
		 */
		public function get_bookingtotalsbycustomer($loginID = '', $debug = false) {
			$bookings = get_bookingtotalsbycustomer($this->filters, $this->filterable, $this->interval, $loginID, $debug);
			return $debug ? $bookings : $this->bookings = $bookings;
		}

		/**
		 * Get the detail lines for a booking
		 * @param  string $ordn  Sales Order #
		 * @param  string $date  Datetime string usually in m/d/Y format
		 * @param  string $loginID User Login ID to get bookings amount, leave blank for current user
		 * @param  bool   $debug Run in debug? If true, return SQL Query
		 * @return array         bookingd records | SQL query
		 * @uses
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
				return "Viewing $intervaldesc bookings between $from and $through";
			}
		}

		/* =============================================================
			SETTER FUNCTIONS
		============================================================ */
		/**
		 * Used when constructed, this sets the PageURL path to point at the bookings ajax URL
		 * @return void
		 */
		public function setup_pageurl() {
			$this->pageurl->path = DplusWire::wire('config')->pages->ajaxload."bookings/";
		}
		/**
		 * Defines the interval
		 * @param string $interval Must be one of the predefined interval types
		 * @uses
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
		 * @uses
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
		 * Grab the value of the filter at index
		 * Goes through the $this->filters array, looks at index $filtername
		 * grabs the value at index provided
		 * @param  string $key        Key in filters
		 * @param  int    $index      Which index to look at for value
		 * @return mixed              value of key index
		 */
		public function get_filtervalue($key, $index = 0) {
			if (empty($this->filters)) return '';
			if (isset($this->filters[$key])) {
				return (isset($this->filters[$key][$index])) ? $this->filters[$key][$index] : '';
			}
			return '';
		}

		/**
		 * Checks if $this->filters has value of $value
		 * @param  string $key        string
		 * @param  mixed $value       value to look for
		 * @return bool               whether or not if value is in the filters array at the key $key
		 */
		public function has_filtervalue($key, $value) {
			if (empty($this->filters)) return false;
			return (isset($this->filters[$key])) ? in_array($value, $this->filters[$key]) : false;
		}

		/**
		 * Defines the filter for default
		 * Goes back one year
		 * @param  ProcessWire\WireInput $input Use the get property to get at the $_GET[] variables
		 * @param  string                $interval Allows to defined interval
		 * @return void
		 */
		protected function generate_defaultfilter(ProcessWire\WireInput $input, $interval = '') {
			if (!empty($interval)) {
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
			return $bootstrap->create_element('a', "href=$href|class=btn btn-primary btn-sm load-into-modal info-screen|$ajaxdata", "$icon View Sales Orders");
		}
		
		/**
		 * Returns HTML Link to view the days booked sales orders
		 * @param  string $date Date for viewing bookings
		 * @return string       HTML Link to view booked sales orders
		 * @uses   $this->generate_viewsalesordersbydayurl($date)
		 */
		public function generate_viewsalesordersbydaybacklink($date) {
			$bootstrap = new HTMLWriter();
			$href = $this->generate_viewsalesordersbydayurl($date);
			$icon = $bootstrap->icon('fa fa-arrow-circle-left');
			$ajaxdata = "data-modal=$this->modal";
			return $bootstrap->create_element('a', "href=$href|class=btn btn-primary btn-sm load-into-modal info-screen|$ajaxdata", "$icon Back to Bookings on $date");
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
		
		/**
		 * Returns URL to view bookings for that month
		 * @param  string $date Date usually in m/d/Y format
		 * @return string       URL to view bookings for that month
		 */
		public function generate_viewmonthurl($date) {
			$firstofmonth = date('m/01/Y', strtotime($date));
			$daysinmonth = cal_days_in_month(CAL_GREGORIAN, date('m', strtotime($date)), date('Y', strtotime($date)));
			$lastofmonth = date("m/$daysinmonth/Y", strtotime($date));
			
			$url = new Purl\Url($this->pageurl->getUrl());
			$url->path = DplusWire::wire('config')->pages->ajaxload."bookings/";
			$url->query = '';
			$url->query->set('filter', 'filter');
			$url->query->set('bookdate', "$firstofmonth|$lastofmonth");
			return $url->getUrl();
		}
		
		/**
		 * Creates HTML link to view bookings for that month
		 * @param  string $date Date usually in m/d/Y format
		 * @return string       HTML Link
		 */
		public function generate_viewmonthlink($date) {
			$bootstrap = new HTMLWriter();
			$href = $this->generate_viewmonthurl($date);
			$ajaxdata = $this->generate_ajaxdataforcontento();
			$monthyear = DplusDateTime::format_date($date, 'F Y');
			return $bootstrap->a("href=$href|class=load-and-show|$ajaxdata|title=View $monthyear bookings", "$monthyear");
		}
	}
