<?php
	namespace Dplus\Dpluso\General;
	
	use Purl\Url;
	use ProcessWire\WireInput;
	use Dplus\Content\HTMLWriter;
	use Dplus\Base\QueryBuilder;
	
	/**
	 * Class for dealing with the Signin Log
	 */
    class SigninLog {
        use \Dplus\Base\ThrowErrorTrait;
		use \Dplus\Base\MagicMethodTraits;
        use \Dplus\Base\AttributeParser;
		use \Dplus\Base\Filterable;
		
        /**
		 * Array of logins
		 * @var array
		 */
		protected $logs = array();
		/**
		 * Ajax Data 
		 * @var string
		 */
        protected $ajaxdata;
		
		/**
		 * Array of Filters (keys) and their values as values
		 * @var array
		 */
        protected $filters;
		
		/**
		 * Array of key->array of filterable columns
		 * @var array
		 */
        protected $filterable = array(
            'user' => array(
				'querytype' => 'between',
				'datatype' => 'char',
				'label' => 'User'
			),
			'date' => array(
				'querytype' => 'between',
				'datatype' => 'mysql-date',
				'label' => 'Date'
			)
        );
		
		/**
		 * Constructor
		 * @param string  $sessionID Session Identifier
		 * @param Url     $pageurl   Page URL
		 * @param string  $loadinto  HTML ID of element to load ajax data
		 * @param string  $focus     HTML ID of element to load ajax data
		 */
        public function __construct($sessionID, Url $pageurl, $loadinto, $focus) {
			$this->pageurl = new Url($pageurl->getUrl());
            $this->ajaxdata = "data-focus='$focus' data-loadinto='$loadinto'";
		}

        /**
		 * Returns the number of signins that will be found with the filters applied
		 * @param  string $day  Day to find Signins for
		 * @param  bool   $debug Whether or Not the Count will be returned
		 * @return int           Number of Signins | SQL Query
		 */
        public function count_day_signins($day, $debug = false) {
            $count = count_daysignins($day, $debug);
			return $debug ? $count : $this->count = $count;
        }

        /**
		 * Returns the Signins into the property $logs
		 * @param  string $day  Day to find Signins for
		 * @param  bool   $debug Whether to run query to return quotes
		 * @return array         Signins | SQL Query
		 * @uses
		 */
        public function get_daysignins($day, $debug = false) {
			$logs = get_daysignins($day, $debug);
			return $debug ? $logs : $this->logs = $logs;
        }

        /**
         * Returns the log_signin Records that match the filter from the Database
         * @param  bool   $debug Run in debug? If so, return SQL Query
         * @return array         log_signin records
         */
        public function get_signinlog($debug = false) {
            $logs = get_logsignins($this->filters, $this->filterable, $debug);
			return $debug ? $logs : $this->logs = $logs;
        }

        /**
         * Logs the Session Sign in, if not already created
         * @param  string $sessionID User Session ID
         * @param  string $userID    User ID
         * @return void
         */
        public static function log_signin($sessionID, $userID) {
            if (!self::has_loggedsignin($sessionID)) {
                self::insert_logsignin($sessionID, $userID);
            }
        }

        /**
         * Inserts a signin record into the log_signin table
         * @param  string $sessionID User Session ID
         * @param  string $userID    User ID
         * @param  bool   $debug     Run in debug? If so return SQL Query
         * @return string            SQL Query after Execution
         */
        public static function insert_logsignin($sessionID, $userID, $debug = false) {
            return insert_logsignin($sessionID, $userID, $debug);
        }

        /**
         * Returns if this session has a log_signin record attached
         * @param  string $sessionID User Session ID
         * @param  bool   $debug     Run in debug? If so return SQL Query
         * @return bool              Does sessionID have log_signin record?
         */
        public static function has_loggedsignin($sessionID, $debug = false) {
            return has_loggedsignin($sessionID, $debug);
        }

        /**
		 * Looks through the $input->get for properties that have the same name
		 * as filterable properties, then we populate $this->filter with the key and value
		 * @param  WireInput $input Use the get property to get at the $_GET[] variables
		 */
        public function generate_filter(WireInput $input) {
            if (!$input->get->filter) {
				$this->filters = false;
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
			}

			if (isset($this->filters['date'])) {
				if (empty($this->filters['date'][0])) {
					$this->filters['date'][0] = date('m/d/Y');
				}

				if (empty($this->filters['date'][1])) {
					$this->filters['date'][1] = date('m/d/Y');
				}
			}
		}

		/**
		 * // REVIEW: 
		 * Returns a descrption of the filters being applied to the orderpanel
		 * @return string Description of the filters
		 */
		public function generate_filterdescription() {
			if (empty($this->filters)) return '';
			$desc = 'Searching '.$this->generate_paneltypedescription().' with';

			foreach ($this->filters as $filter => $value) {
				$desc .= " " . QueryBuilder::generate_filterdescription($filter, $value, $this->filterable);
			}
			return $desc;
		}
		
		/**
		 * // TODO rename for URL()
		 * Returns URL to load Page
		 * @return string URL to load Page
		 */
        public function generate_loadurl() {
			$url = new Url($this->pageurl);
			$url->query->remove('filter');
			foreach (array_keys($this->filterable) as $filtercolumns) {
				$url->query->remove($filtercolumns);
			}
			return $url->getUrl();
		}
		
		/**
		 * // FIXME Remove, and make link at presentation level
		 * Returns HTML Link to remove search filters
		 * @return string HTML Link to remove search filters
		 */
		public function generate_clearsearchlink() {
			$bootstrap = new HTMLWriter();
			$href = $this->generate_loadurl();
			$icon = $bootstrap->icon('fa fa-search-minus');
            $ajaxdata = $this->generate_ajaxdataforcontento();
			return $bootstrap->a("href=$href|class=load-link btn btn-warning btn-block|$ajaxdata", "Clear Search $icon");
		}
    }
