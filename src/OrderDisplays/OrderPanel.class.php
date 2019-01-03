<?php
	namespace Dplus\Dpluso\OrderDisplays;

	use Purl\Url;
	use ProcessWire\WireInput;
	use Dplus\ProcessWire\DplusWire;
	use Dplus\Base\QueryBuilder;
	use Dplus\Content\TablePageSorter;
	use Dplus\Content\HTMLWriter;
	use Dplus\Content\Paginator;
	
	/**
	 * Use Statements for Model Classes which are non-namespaced
	 */
	use Order;
	use LogmUser;
	
	/**
	 * Blueprint class for dealing with lists of orders and their display
	 */
	abstract class OrderPanel extends OrderDisplay {
		use \Dplus\Base\MagicMethodTraits;
		use \Dplus\Base\ThrowErrorTrait;
		use \Dplus\Base\AttributeParser;
		use \Dplus\Base\Filterable;
		
		/**
		 * User ID for the panel is getting orders for
		 * // NOTE If User ID belongs to a Sales Rep it will autopopulate the filter for salesperson
		 * @var string
		 */
		protected $userID;

		/**
		 * ID of HTML element to focus on after ajax load
		 * @var string e.g. #orderpanel
		 */
		protected $focus;

		/**
		 * ID of HTML element to load into after ajax load
		 * @var string e.g. #orderpanel
		 */
		protected $loadinto;

		/**
		 * String of data attributes
		 * @var string e.g. data-loadinto='$this->loadinto' data-focus='$this->focus'
		 */
		protected $ajaxdata;

		/**
		 * Segment of URL to place the pagination segment
		 * @var string
		 */
		protected $paginationinsertafter;

		/**
		 * Boolean to decide if this has been loaded through ajax
		 * @var bool
		 */
		protected $throughajax;

		/**
		 * Whether or not the panel div shows opened or collapse
		 * @var string
		 */
		protected $collapse = 'collapse';

		/**
		 * Object to sort the columns
		 * @var \Dplus\Content\TablePageSorter
		 */
		protected $tablesorter; // Will be instatnce of TablePageSorter

		/**
		 * Page Number
		 * @var int
		 */
		protected $pagenbr;

		/**
		 * Which Order Number is the active Order
		 * @var string
		 */
		protected $activeID = false;

		/**
		 * Number of Orders
		 * @var int
		 */
		protected $count;

		/**
		 * Array of filters that will apply to the orders
		 * @var array
		 */
		protected $filters = array(
			'salesperson' => array(
				'querytype' => 'in',
				'datatype' => 'char',
				'label' => 'Sales Rep'
			)
		);

		/**
		 * Array of key->array of filterable columns
		 * @var array
		 */
		protected $filterable;

		/**
		 * Panel Type
		 * @var string
		 */
		protected $paneltype;

		/**
		 * Constructor
		 * @param string  $sessionID  Session Identifier
		 * @param Url     $pageurl   Page URL Object
		 * @param string  $modal      ID of Modal Element
		 * @param string  $loadinto   ID of element to AJAX Load into
		 * @param bool    $ajax         Use Ajax
		 * @uses
		 */
		public function __construct($sessionID, Url $pageurl, $modal, $loadinto, $ajax) {
			parent::__construct($sessionID, $pageurl, $modal);
			$this->loadinto = $this->focus = $loadinto;
			if (!empty($this->loadinto)) {
				$this->ajaxdata = "data-loadinto='$this->loadinto' data-focus='$this->focus'";
			} else {
				$this->ajaxdata = '';
			}
			
			$this->tablesorter = new TablePageSorter($this->pageurl->query->get('orderby'));

			if ($ajax) {
				$this->collapse = '';
			} else {
				$this->collapse = 'collapse';
			}
		}

		/**
		 * Setup the Page URL then add the necessary components in the path and querystring
		 * @return void
		 * @uses parent::setup_pageURL()
		 */
		abstract public function setup_pageURL();
		
		/**
		 * Sets the User Property for the Order Panel
		 * @param string $userID User Login ID
		 */
		public function set_user($userID) {
			$this->userID = $userID;
		}

		/* =============================================================
			Class Functions
		============================================================ */
		/**
		 * Returns the description of the page
		 * @return string Page Number Page
		 */
		public function generate_pagenumberdescription() {
			return ($this->pagenbr > 1) ? "Page $this->pagenbr" : '';
		}

		/**
		 * Returns HTML for a popover that has the shipto address
		 * @param  Order  $order Gets the Address info from $order
		 * @return string        HTML for bootstrap popover
		 */
		public function generate_shiptopopover(Order $order) {
			$bootstrap = new HTMLWriter();
			$address = $order->shipto_address1.'<br>';
			$address .= (!empty($order->shipto_address2)) ? $order->shipto_address2."<br>" : '';
			$address .= $order->shipto_city.", ". $order->shipto_state.' ' . $order->shipto_zip;
			$attr = "tabindex=0|role=button|class=btn btn-default bordered btn-sm|data-toggle=popover";
			$attr .= "|data-placement=top|data-trigger=focus|data-html=true|title=Ship-To Address|data-content=$address";
			return $bootstrap->a($attr, '<b>?</b>');
		}

		/* =============================================================
			OrderPanelInterface Functions
		============================================================ */
		/**
		 * Returns URL with the sort parameters removed
		 * @return string URL to load
		 */
		public function generate_clearsortURL() {
			$url = new Url($this->pageurl->getUrl());
			$url->query->remove("orderby");
			return $url->getUrl();
		}
		
		/**
		 * Returns URL for searching
		 * // NOTE It removes pagination to display the first page of results
		 * @return string URL to load
		 */
		public function generate_searchURL() {
			$url = new Url($this->pageurl->getUrl());
			$url = Paginator::paginate_purl($url, 1, $this->paginationinsertafter);
			return $url->getUrl();
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


		/**
		 * Looks through the $input->get for properties that have the same name
		 * as filterable properties, then we populate $this->filter with the key and value
		 * @param  WireInput $input Use the get property to get at the $_GET[] variables
		 */
		public function generate_filter(WireInput $input) {
			$this->generate_defaultfilter($input);
			$this->userID = !empty($this->userID) ? $this->userID : DplusWire::wire('user')->loginid;
			
			$user = LogmUser::load($this->userID);
			
			if ($user->is_salesrep()) {
				$this->filters['salesperson'][] = $user->roleid;
			}
		}

		/**
		 * Returns a descrption of the filters being applied to the orderpanel
		 * @return string Description of the filters
		 */
		public function generate_filterdescription() {
			$user = LogmUser::load($this->userID);
			$filters = $this->filters;

			if ($user->is_salesrep()) {
				unset($filters['salesperson']);
			}
			
			if (empty($filters)) {
				return '';
			} else {
				$desc = 'Searching '.$this->generate_paneltypedescription().' with';
				foreach ($filters as $filter => $value) {
					$desc .= " " . QueryBuilder::generate_filterdescription($filter, $value, $this->filterable);
				}
				return $desc;
			}
		}

		/**
		 * Returns the orders description e.g. sales order
		 * @return string Panel Type Description
		 */
		public function generate_paneltypedescription() {
			return ucwords(str_replace('-', ' ', $this->paneltype.'s'));
		}
	}
