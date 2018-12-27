<?php
	namespace Dplus\Dpluso\OrderDisplays;

	use Purl\Url;
	use ProcessWire\WireInput;
	use Dplus\ProcessWire\DplusWire;
	use Dplus\Content\HTMLWriter;

	/**
	 * Use Statements for Model Classes which are non-namespaced
	 */
	use Order, OrderDetail;

	/**
	 * Class for dealing with list of quotes
	 */
	class QuotePanel extends OrderPanel implements OrderDisplayInterface, QuoteDisplayInterface, OrderPanelInterface, QuotePanelInterface {
		use QuoteDisplayTraits;

		/**
		 * Array of Quotes
		 * @var array
		 */
		protected $quotes = array();
		protected $paneltype = 'quote';
		protected $filterable = array(
			'quotnbr' => array(
				'querytype' => 'between',
				'datatype' => 'char',
				'label' => 'Quote #'
			),
			'custid' => array(
				'querytype' => 'between',
				'datatype' => 'char',
				'label' => 'CustID'
			),
			'subtotal' => array(
				'querytype' => 'between',
				'datatype' => 'numeric',
				'label' => 'Quote Total'
			),
			'quotdate' => array(
				'querytype' => 'between',
				'datatype' => 'date',
				'label' => 'Quote Date'
			),
			'revdate' => array(
				'querytype' => 'between',
				'datatype' => 'date',
				'label' => 'Review Date'
			),
			'expdate' => array(
				'querytype' => 'between',
				'datatype' => 'date',
				'label' => 'Expire Date'
			),
			'salesperson' => array(
				'querytype' => 'in',
				'datatype' => 'char',
				'label' => 'Sales Rep'
			)
		);

		public function __construct($sessionID, Url $pageurl, $modal, $loadinto, $ajax) {
			parent::__construct($sessionID, $pageurl, $modal, $loadinto, $ajax);
			$this->pageurl = $this->pageurl = new Url($pageurl->getUrl());
			$this->setup_pageURL();
		}
		
		public function setup_pageURL() {
			$this->pageurl->path = DplusWire::wire('config')->pages->ajax."load/quotes/";
			$this->pageurl->query->remove('display');
			$this->pageurl->query->remove('ajax');
			$this->paginationinsertafter = 'quotes';
		}

		/* =============================================================
			SalesOrderPanelInterface Functions
			LINKS ARE HTML LINKS, AND URLS ARE THE URLS THAT THE HREF VALUE
		============================================================ */
		/**
		 * Returns the number of quote that will be found with the filters applied
		 * @param  bool   $debug Whether or Not the Count will be returned
		 * @return int           Number of Quotes | SQL Query
		 */
		public function get_quotecount($debug = false) {
			$count = count_quotes($this->sessionID, $this->filters, $this->filterable, $debug);
			return $debug ? $count : $this->count = $count;
		}

		/**
		 * Returns Min Date for $datetype
		 * @param  string $datetype Date Column
		 * @param  bool   $debug    Run in debug? If so, return SQL Query
		 * @return string           Min Date
		 */
		public function get_mindate($datetype = 'quotdate', $debug = false) {
			return get_minquotedate($this->sessionID, $custID = '', $shiptoID = '', $datetype, $this->filters, $this->filterable, $debug);
		}

		/**
		 * Returns Max Quote Total
		 * @param  bool   $debug  Run in debug? If so, return SQL Query
		 * @return float          Max Quote Total
		 */
		public function get_maxquotetotal($debug = false) {
			return get_maxquotetotal($this->sessionID, $custID = '', $shiptoID = '', $this->filters, $this->filterable, $debug);
		}

		/**
		 * Returns Min Quote Total
		 * @param  bool   $debug  Run in debug? If so, return SQL Query
		 * @return float          Miin Quote Total
		 */
		public function get_minquotetotal($debug = false) {
			return get_minquotetotal($this->sessionID, $custID = '', $shiptoID = '', $this->filters, $this->filterable, $debug);
		}

		/**
		 * Returns the Quotes into the property $quotes
		 * @param  bool   $debug Run in debug? If so, return SQL Query
		 * @return array         Quotes
		 * @uses
		 */
		public function get_quotes($debug = false) {
			$useclass = true;
			if ($this->tablesorter->orderby) {
				if ($this->tablesorter->orderby == 'quotdate') {
					$quotes = get_quotes_orderby_quotedate($this->sessionID, DplusWire::wire('session')->display, $this->pagenbr, $this->tablesorter->sortrule, $this->filters, $this->filterable, $useclass, $debug);
				} elseif ($this->tablesorter->orderby == 'revdate') {
					$quotes = get_quotes_orderby_revdate($this->sessionID, DplusWire::wire('session')->display, $this->pagenbr, $this->tablesorter->sortrule, $this->filters, $this->filterable, $useclass, $debug);
				} elseif ($this->tablesorter->orderby == 'expdate') {
					$quotes = get_quotes_orderby_expdate($this->sessionID, DplusWire::wire('session')->display, $this->pagenbr, $this->tablesorter->sortrule, $this->filters, $this->filterable, $useclass, $debug);
				} else {
					$quotes = get_quotes_orderby($this->sessionID, DplusWire::wire('session')->display, $this->pagenbr, $this->tablesorter->sortrule, $this->tablesorter->orderby, $this->filters, $this->filterable, $useclass, $debug);
				}
			} else {
				$this->tablesorter->sortrule = 'DESC';
				$quotes = get_quotes_orderby_quotedate($this->sessionID, DplusWire::wire('session')->display, $this->pagenbr, $this->tablesorter->sortrule, $this->filters, $this->filterable, $useclass, $debug);
			}
			return $debug ? $quotes: $this->quotes = $quotes;
		}

		/* =============================================================
			OrderPanelInterface Functions
			LINKS ARE HTML LINKS, AND URLS ARE THE URLS THAT THE HREF VALUE
		============================================================ */
		public function generate_closedetailsURL() {
			$url = new Url($this->pageurl->getUrl());
			$url->query->setData(array('qnbr' => false, 'show' => false));
			return $url->getUrl();
		}

		public function generate_shiptopopover(Order $quote) {
			$bootstrap = new HTMLWriter();
			$address = $quote->shipaddress.'<br>';
			$address .= (!empty($quote->shipaddress2)) ? $quote->shipaddress2."<br>" : '';
			$address .= $quote->shipcity.", ". $quote->shipstate.' ' . $quote->shipzip;
			$attr = "tabindex=0|role=button|class=btn btn-default bordered btn-sm|data-toggle=popover";
			$attr .= "|data-placement=top|data-trigger=focus|data-html=true|title=Ship-To Address|data-content=$address";
			return $bootstrap->a($attr, '<b>?</b>');
		}

		public function generate_iconlegend() {
			$bootstrap = new HTMLWriter();
			$content  = $bootstrap->i('class=fa fa-shopping-cart|title=Re-order Icon', '') . ' = Re-order <br>';
			$content .= $bootstrap->i("class=material-icons|title=Documents Icon", '&#xE873;') . '&nbsp; = Documents <br>';
			$content .= $bootstrap->i('class=fa fa-plane hover|title=Tracking Icon', '') . ' = Tracking <br>';
			$content .= $bootstrap->i('class=material-icons|title=Notes Icon', '&#xE0B9;') . ' = Notes <br>';
			$content .= $bootstrap->i('class=fa fa-pencil|title=Edit Order Icon', '') . ' = Edit Order <br>';
			$content = str_replace('"', "'", $content);
			$attr  = "tabindex=0|role=button|class=btn btn-sm btn-info|data-toggle=popover|data-placement=bottom|data-trigger=focus";
			$attr .= "|data-html=true|title=Icons Definition|data-content=$content";
			return $bootstrap->a($attr, 'Icon Definitions');
		}
		
		// TODO rename for URL()
		public function generate_loadurl() {
			$url = new Url($this->pageurl->getUrl());
			$url->path = DplusWire::wire('config')->pages->quotes.'redir/';
			$url->query->setData(array('action' => 'load-quotes'));
			return $url->getUrl();
		}
		
		
		public function generate_loaddetailsURL(Order $quote) {
			$url = new Url($this->generate_loaddetailsURLtrait($quote));
			$url->query->set('page', $this->pagenbr);
			$url->query->set('orderby', $this->tablesorter->orderbystring);

			if (!empty($this->filters)) {
				$url->query->set('filter', 'filter');
				foreach ($this->filters as $filter => $value) {
					$url->query->set($filter, implode('|', $value));
				}
			}
			return $url->getUrl();
		}

		public function generate_documentsrequesturl(Order $quote, OrderDetail $quotedetail = null) {
			$url = new Url($this->generate_documentsrequesturltrait($quote, $quotedetail));
			$url->query->set('page', $this->pagenbr);
			$url->query->set('orderby', $this->tablesorter->orderbystring);
			return $url->getUrl();
		}

		public function generate_viewlinkeduseractionslink(Order $quote) {
			$bootstrap = new HTMLWriter();
			$href = $this->generate_viewlinkeduseractionsurl($quote);
			$icon = $bootstrap->span('class=h3', $bootstrap->icon('fa fa-check-square-o'));
			return $bootstrap->a("href=$href|class=load-into-modal|data-modal=$this->modal", $icon." View Associated Actions");
		}

		public function generate_editlink(Order $quote) {
			$bootstrap = new HTMLWriter();

			if (DplusWire::wire('user')->hasquotelocked) {
				if ($quote->quotnbr == DplusWire::wire('user')->lockedqnbr) {
					$icon = $bootstrap->icon('fa fa-wrench');
					$title = "Continue editing this Quote";
				} else {
					$icon = $bootstrap->icon('material-icons md-36', '&#xE897;');
					$title = "Open Quote in Read Only Mode";
				}
			} else {
				$icon = $bootstrap->icon('fa fa-pencil');
				$title = "Edit Quote";
			}

			$href = $this->generate_editurl($quote);
			return $bootstrap->a("href=$href|class=edit-order h3|title=$title", $icon);
		}

		public function generate_loaddocumentslink(Order $quote, OrderDetail $quotedetail = null) {
			$bootstrap = new HTMLWriter();
			$href = $this->generate_documentsrequesturl($quote, $quotedetail);
			$icon = $bootstrap->icon('material-icons md-36', '&#xE873;');
			$ajaxdata = $this->generate_ajaxdataforcontento();

			if ($quote->has_documents()) {
				return $bootstrap->a("href=$href|class=generate-load-link|title=Click to view Documents|$ajaxdata", $icon);
			} else {
				return $bootstrap->a("href=#|class=text-muted|title=No Documents Available", $icon);
			}
		}

		public function generate_detailvieweditlink(Order $quote, OrderDetail $detail) {
			$bootstrap = new HTMLWriter();
			$href = $this->generate_detailviewediturl($quote, $detail);
			return $bootstrap->a("href=$href|class=update-line|data-kit=$detail->kititemflag|data-itemid=$detail->itemid|data-custid=$quote->custid|aria-label=View Detail Line", $detail->itemid);
		}

		public function generate_lastloadeddescription() {
			if (DplusWire::wire('session')->{'quotes-loaded-for'}) {
				if (DplusWire::wire('session')->{'quotes-loaded-for'} == DplusWire::wire('user')->loginid) {
					return 'Last Updated : ' . DplusWire::wire('session')->{'quotes-updated'};
				}
			}
			return '';
		}

		public function generate_filter(WireInput $input) {
			parent::generate_filter($input);

			if (isset($this->filters['quotdate'])) {
				if (empty($this->filters['quotdate'][0])) {
					$this->filters['quotdate'][0] = date('m/d/Y', strtotime($this->get_mindate('quotdate')));
				}

				if (empty($this->filters['quotdate'][1])) {
					$this->filters['quotdate'][1] = date('m/d/Y');
				}
			}

			if (isset($this->filters['revdate'])) {
				if (empty($this->filters['revdate'][0])) {
					$this->filters['revdate'][0] = date('m/d/Y', strtotime($this->get_mindate('revdate')));
				}

				if (empty($this->filters['revdate'][1])) {
					$this->filters['revdate'][1] = date('m/d/Y');
				}
			}

			if (isset($this->filters['expdate'])) {
				if (empty($this->filters['expdate'][0])) {
					$this->filters['expdate'][0] = date('m/d/Y', strtotime($this->get_mindate('expdate')));
				}

				if (empty($this->filters['expdate'][1])) {
					$this->filters['expdate'][1] = date('m/d/Y');
				}
			}

			if (isset($this->filters['subtotal'])) {

				if (!strlen($this->filters['subtotal'][0])) {
					$this->filters['subtotal'][0] = '0.00';
				}

				for ($i = 0; $i < (sizeof($this->filters['subtotal']) + 1); $i++) {
					if (isset($this->filters['subtotal'][$i])) {
						if (strlen($this->filters['subtotal'][$i])) {
							$this->filters['subtotal'][$i] = number_format($this->filters['subtotal'][$i], 2, '.', '');
						}
					}
				}
			}
		}
	}
