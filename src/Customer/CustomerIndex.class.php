<?php
    namespace Dplus\Dpluso\Customer;

    use ProcessWire\WireInput;
    use Dplus\ProcessWire\DplusWire;
    use Dplus\Content\TablePageSorter;
    use Purl\Url;

    /**
     * Class for dealing with the Customer Index database table
     */

    use LogmUser;

    class CustomerIndex {
        use \Dplus\Base\ThrowErrorTrait;
		use \Dplus\Base\MagicMethodTraits;
        use \Dplus\Base\AttributeParser;
        use \Dplus\Base\Filterable;

        /**
		 * Array of filters that will apply to the orders
		 * @var array
		 */
		protected $filters = array();

		/**
		 * Array of key->array of filterable columns
		 * @var array
		 */
		protected $filterable = array(
			'state' => array(
				'querytype' => 'in',
				'datatype' => 'char',
				'label' => 'State'
			),
            'source' => array(
				'querytype' => 'in',
				'datatype' => 'char',
				'label' => 'Source'
			),
			'lastsaledate' => array(
				'querytype' => 'between',
				'datatype' => 'date',
				'date-format' => 'Ymd',
				'label' => 'Last Sale Date'
			)
		);

        /**
         * Page Number
         * @var int
         */
        protected $pagenbr;

        /**
         * Function to index for
         * ii | ci | os = order search | ca = cart customer
         * @var string
         */
        protected $function;

        /**
         * Page URL
         * @var string
         */
        protected $pageurl;

        /**
         * Table Sorter - for sorting results
         * @var  TablePageSorter
         */
        protected $tablesorter;

        /**
         * HTML element to load into for AJAX
         * @var string
         */
        protected $loadinto;

        /**
         * HTML element to focus on for AJAX
         * @var string
         */
        protected $focus;

        /**
         * AJAX Data Attributes
         * @var string
         */
        protected $ajaxdata;

        /**
         * Constructs CustIndex and instantiates tablesorter
         * @param Url $url       Page URL
         * @param string   $loadinto  HTML element to load into for AJAX
         * @param string   $focus     HTML element to focus on for AJAX
         */
        public function __construct(Url $url, $loadinto, $focus) {
            $this->pageurl = new Url($url->getUrl());
            $this->loadinto = $this->focus = $loadinto;
			$this->ajaxdata = "data-loadinto='$this->loadinto' data-focus='$this->focus'";
            $this->tablesorter = new TablePageSorter($this->pageurl->query->get('orderby'));
        }

        /**
         * Sets the Page Number
         * @param int $pagenbr Page Number
         */
        public function set_pagenbr($pagenbr) {
            $this->pagenbr = $pagenbr;
        }

        /**
		 * Returns the sortby column URL
		 * @param  string $column column to sortby
		 * @return string         URL with the column sortby with the correct rule
		 */
		public function generate_sortbyURL($column) {
			$url = new Url($this->pageurl->getUrl());
			$url->query->set("orderby", "$column-".$this->tablesorter->generate_columnsortingrule($column));
			return $url->getUrl();
		}

        public function generate_loadURL() {
			$url = new Url($this->pageurl);
			$url->query->remove('filter');
			foreach (array_keys($this->filterable) as $filtercolumns) {
				$url->query->remove($filtercolumns);
			}
			return $url->getUrl();
		}

        /**
		 * Looks through the $input->get for properties that have the same name
		 * as filterable properties, then we populate $this->filter with the key and value
		 * @param  WireInput $input Use the get property to get at the $_GET[] variables
		 */
		public function generate_filter(WireInput $input) {
			$this->generate_defaultfilter($input);

			$this->userID = !empty($this->userID) ? $this->userID : DplusWire::wire('user')->loginid;

			$user = LogmUser::load($this->userID);

            if (isset($this->filters['lastsaledate'])) {
				if (empty($this->filters['lastsaledate'][0])) {
					$this->filters['lastsaledate'][0] = date('m/d/Y', strtotime($this->get_mindate('lastsaledate')));
				}

				if (empty($this->filters['lastsaledate'][1])) {
					$this->filters['lastsaledate'][1] = date('m/d/Y');
				}
			}
		}

        /**
         * Returns the number of customer index records that fit the current
         * criteria for the search based on user permissions
         * @param  string $query   Search Query
         * @param  string $loginID User Login ID, if blank, will use current user
         * @param  bool   $debug   Run in debug? If so, will return SQL Query
         * @return int             Number of customer index records that match
         */
        public function count_searchcustindex($query = '', $loginID = '', $debug = false) {
            return count_searchcustindex($query, $loginID, $this->filters, $this->filterable, $debug);
        }

        /**
         * Return the number of customer index that user has access to
         * @param  string $loginID User Login ID, if blank, will use current user
         * @param  bool   $debug   Run in debug? If so, will return SQL Query
         * @return int             Number of customer index that user has access to
         */
        public function count_distinctcustindex($loginID = '', $debug = false) {
            return count_distinctcustindex($loginID, $debug);
        }

        /**
         * Returns Customer Index records that match the Query
         * @param  string $q       Query String to match
         * @param  int    $page    Page Number to start from
         * @param  string $loginID User Login ID, if blank, will use current user
         * @param  bool   $debug   Run in debug? If so, will return SQL Query
         * @return array           Customer Index records that match the Query
         */
        public function search_custindexpaged($q, $page = 1, $loginID = '', $debug = false) {
            return search_custindexpaged($q, DplusWire::wire('session')->display, $this->pagenbr, $this->tablesorter->get_orderbystring(), $this->filters, $this->filterable, $loginID, $debug);
        }

        /**
         * Returns Distinct Customer Index Records that the user has access to
         * @param  int    $page    Page Number to start from
         * @param  string $loginID User Login ID, if blank, will use current user
         * @param  bool   $debug   Run in debug? If so, will return SQL Query
         * @return array           Distinct Customer Index Records
         */
        public function get_distinctcustindexpaged($page = 1, $loginID = '', $debug = false) {
            return get_distinctcustindexpaged(DplusWire::wire('session')->display, $this->pagenbr, $this->tablesorter->get_orderbystring(), $loginID, $debug);
        }

        /**
         * Returns the grouping description of the Customer Index based on configurations
         * NOTE customer-shipto=Customer Shipto | customer=Customer | none=No grouping
         * @return string Customer Index grouping description
         */
        public function get_configcustindexgroupby() {
            return DplusWire::wire('pages')->get('/config/customer/')->group_custindexby->title;
        }

        /**
         * Returns the states the user has customers in
         * @param  string $loginID User Login ID, if blank, will use current user
         * @param  bool   $debug   Run in debug? If so, will return SQL Query
         * @return array  array of states
         */
        public function get_statesbylogin($loginID = '', $debug = false) {
            return get_statesbylogin($loginID, $debug);
        }
    }
