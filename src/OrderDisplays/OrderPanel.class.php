<?php
	namespace Dplus\Dpluso\OrderDisplays;
	
	use Dplus\Base\QueryBuilder;
	use Dplus\Content\TablePageSorter;
	use Dplus\Content\HTMLWriter;
	
	/**
	 * Use Statements for Model Classes which are non-namespaced
	 */
	use Order;
	
	/**
	 * Blueprint class for dealing with lists of orders and their display
	 */
	abstract class OrderPanel extends OrderDisplay {
		use \Dplus\Base\AttributeParser;

		/**
		 * ID of HTML element to focus on after ajax load
		 * @var string e.g. #orderpanel
		 */
		public $focus;

		/**
		 * ID of HTML element to load into after ajax load
		 * @var string e.g. #orderpanel
		 */
		public $loadinto;

		/**
		 * String of data attributes
		 * @var string e.g. data-loadinto='$this->loadinto' data-focus='$this->focus'
		 */
		public $ajaxdata;

		/**
		 * Segment of URL to place the pagination segment
		 * @var string
		 */
		public $paginationinsertafter;

		/**
		 * Boolean to decide if this has been loaded through ajax
		 * @var bool
		 */
		public $throughajax;

		/**
		 * Whether or not the panel div shows opened or collapse
		 * @var string
		 */
		public $collapse = 'collapse';

		/**
		 * Object to sort the columns
		 * @var \Dplus\Content\TablePageSorter
		 */
		public $tablesorter; // Will be instatnce of TablePageSorter

		/**
		 * Page Number
		 * @var int
		 */
		public $pagenbr;

		/**
		 * Which Order Number is the active Order
		 * @var string
		 */
		public $activeID = false;

		/**
		 * Number of Orders
		 * @var int
		 */
		public $count;

		/**
		 * Array of filters that will apply to the orders
		 * @var array
		 */
		public $filters = false; // Will be instance of array

		/**
		 * Array of key->array of filterable columns
		 * @var array
		 */
		public $filterable;

		/**
		 * Panel Type
		 * @var string
		 */
		public $paneltype;

		/**
		 * Constructor
		 * @param string  $sessionID  Session Identifier
		 * @param \Purl\Url $pageurl   Page URL Object
		 * @param string  $modal      ID of Modal Element
		 * @param string  $loadinto   ID of element to AJAX Load into
		 * @param bool  $ajax         Use Ajax
		 * @uses
		 */
		public function __construct($sessionID, \Purl\Url $pageurl, $modal, $loadinto, $ajax) {
			parent::__construct($sessionID, $pageurl, $modal);
			$this->loadinto = $this->focus = $loadinto;
			$this->ajaxdata = "data-loadinto='$this->loadinto' data-focus='$this->focus'";
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
		 * @uses parent::setup_pageurl()
		 */
		abstract public function setup_pageurl();

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
			return $bootstrap->create_element('a', $attr, '<b>?</b>');
		}

		/* =============================================================
			OrderPanelInterface Functions
		============================================================ */
		/**
		 * Returns HTML link for clearing the search
		 * @return string HTML Link
		 */
		public function generate_clearsearchlink() {
			$bootstrap = new HTMLWriter();
			$href = $this->generate_loadurl();
			$icon = $bootstrap->icon('fa fa-search-minus');
			$ajaxdata = $this->generate_ajaxdataforcontento();
			return $bootstrap->create_element('a', "href=$href|class=generate-load-link btn btn-warning btn-block|$ajaxdata", "Clear Search $icon");
		}

		/**
		 * Returns HTML link for clearing the sort
		 * @return [HTML Link
		 */
		public function generate_clearsortlink() {
			$bootstrap = new HTMLWriter();
			$href = $this->generate_clearsorturl();
			$ajaxdata = $this->generate_ajaxdataforcontento();
			return $bootstrap->create_element('a', "href=$href|class=btn btn-warning btn-sm load-link|$ajaxdata", '(Clear Sort)');
		}

		/**
		 * Returns URL with the sort parameters removed
		 * @return string URL to load
		 */
		public function generate_clearsorturl() {
			$url = new \Purl\Url($this->pageurl->getUrl());
			$url->query->remove("orderby");
			return $url->getUrl();
		}

		/**
		 * Returns HTML link to load the Orders/Quotes
		 * @return string HTML link
		 */
		public function generate_loadlink() {
			$bootstrap = new HTMLWriter();
			$href = $this->generate_loadurl();
			$ajaxdata = $this->generate_ajaxdataforcontento();
			return $bootstrap->create_element('a', "href=$href|class=generate-load-link|$ajaxdata", "Load Orders");
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
		 * Looks through the $input->get for properties that have the same name
		 * as filterable properties, then we populate $this->filter with the key and value
		 * @param  \ProcessWire\WireInput $input Use the get property to get at the $_GET[] variables
		 */
		public function generate_filter(\ProcessWire\WireInput $input) {
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
		}


		/**
		 * Looks through the $input->get for properties that have the same name
		 * as filterable properties, then we populate $this->filter with the key and value
		 * @param  \ProcessWire\WireInput $input Use the get property to get at the $_GET[] variables
		 */
		public function generate_defaultfilter(\ProcessWire\WireInput $input) {
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
		 * Returns the orders description e.g. sales order
		 * @return string Panel Type Description
		 */
		public function generate_paneltypedescription() {
			return ucwords(str_replace('-', ' ', $this->paneltype.'s'));
		}
	}
