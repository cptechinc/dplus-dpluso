<?php
    /**
     * Class for dealing with the Customer Index database table
     */
    class CustomerIndex {
        use ThrowErrorTrait;
		use MagicMethodTraits;
        use AttributeParser;
        
        /**
		 * Array of filters that will apply to the orders
		 * @var array
		 */
		protected $filters = false; // Will be instance of array

		/**
		 * Array of key->array of filterable columns
		 * @var array
		 */
		protected $filterable;
        
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
         * @var TablePageSorter
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
         * @param Purl\Url $url       Page URL 
         * @param string   $loadinto  HTML element to load into for AJAX
         * @param string   $focus     HTML element to focus on for AJAX
         */
        public function __construct(Purl\Url $url, $loadinto, $focus) {
            $this->pageurl = new Purl\Url($url->getUrl());
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
		public function generate_tablesortbyurl($column) {
			$url = new \Purl\Url($this->pageurl->getUrl());
			$url->query->set("orderby", "$column-".$this->tablesorter->generate_columnsortingrule($column));
			return $url->getUrl();
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
            return count_searchcustindex($query, $loginID, $debug);
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
            return search_custindexpaged($q, DplusWire::wire('session')->display, $this->pagenbr, $this->tablesorter->get_orderbystring(), $loginID, $debug);
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
    }
