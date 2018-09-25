<?php
	/**
	 * Class for dealing with list of quotes
	 */
	class QuotePanel extends OrderPanel implements OrderDisplayInterface, QuoteDisplayInterface, OrderPanelInterface, QuotePanelInterface {
		use QuoteDisplayTraits;
		/**
		 * Array of Quotes
		 * @var array
		 */
		public $quotes = array();
		public $paneltype = 'quote';
		public $filterable = array(
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
			)
		);

		public function __construct($sessionID, \Purl\Url $pageurl, $modal, $loadinto, $ajax) {
			parent::__construct($sessionID, $pageurl, $modal, $loadinto, $ajax);
			$this->pageurl = $this->pageurl = new Purl\Url($pageurl->getUrl());
			$this->setup_pageurl();
		}

		public function setup_pageurl() {
			$this->pageurl->path = DplusWire::wire('config')->pages->ajax."load/quotes/";
			$this->pageurl->query->remove('display');
			$this->pageurl->query->remove('ajax');
			$this->pageurl->paginationinsertafter = 'quotes';
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
			$count = count_userquotes($this->sessionID, $this->filters, $this->filterable, $debug);
			return $debug ? $count : $this->count = $count;
		}

		/**
		 * Returns the Quotes into the property $quotes
		 * @param  bool   $debug Whether to run query to return quotes
		 * @return array         Quotes | SQL Query
		 * @uses
		 */
		public function get_quotes($debug = false) {
			$useclass = true;
			if ($this->tablesorter->orderby) {
				if ($this->tablesorter->orderby == 'quotdate') {
					$quotes = get_userquotesquotedate($this->sessionID, DplusWire::wire('session')->display, $this->pagenbr, $this->tablesorter->sortrule, $this->filters, $this->filterable, $useclass, $debug);
				} elseif ($this->tablesorter->orderby == 'revdate') {
					$quotes = get_userquotesrevdate($this->sessionID, DplusWire::wire('session')->display, $this->pagenbr, $this->tablesorter->sortrule, $this->filters, $this->filterable, $useclass, $debug);
				} elseif ($this->tablesorter->orderby == 'expdate') {
					$quotes = get_userquotesexpdate($this->sessionID, DplusWire::wire('session')->display, $this->pagenbr, $this->tablesorter->sortrule, $this->filters, $this->filterable, $useclass, $debug);
				} else {
					$quotes = get_userquotesorderby($this->sessionID, DplusWire::wire('session')->display, $this->pagenbr, $this->tablesorter->sortrule, $this->tablesorter->orderby, $this->filters, $this->filterable, $useclass, $debug);
				}
			} else {
				$this->tablesorter->sortrule = 'DESC';
				$quotes = get_userquotesquotedate($this->sessionID, DplusWire::wire('session')->display, $this->pagenbr, $this->tablesorter->sortrule, $this->filters, $this->filterable, $useclass, $debug);
				//$quotes = get_userquotes($this->sessionID, DplusWire::wire('session')->display, $this->pagenbr, $this->filters, $this->filterable, $useclass, $debug);
			}
			return $debug ? $quotes: $this->quotes = $quotes;
		}

		/* =============================================================
			OrderPanelInterface Functions
			LINKS ARE HTML LINKS, AND URLS ARE THE URLS THAT THE HREF VALUE
		============================================================ */
		public function generate_loadlink() {
			$bootstrap = new HTMLWriter();
			$href = $this->generate_loadurl();
			$ajaxdata = $this->generate_ajaxdataforcontento();
			return $bootstrap->create_element('a', "href=$href|class=generate-load-link|$ajaxdata", "Load Quotes");
		}

		public function generate_refreshlink() {
			$bootstrap = new HTMLWriter();
			$href = $this->generate_loadurl();
			$icon = $bootstrap->icon('fa fa-refresh');
			$ajaxdata = $this->generate_ajaxdataforcontento();
			return $bootstrap->create_element('a', "href=$href|class=generate-load-link|$ajaxdata", "$icon Refresh Quotes");
		}

		public function generate_expandorcollapselink(Order $quote) {
			$bootstrap = new HTMLWriter();

			if ($this->activeID == $quote->quotnbr) {
				$href = $this->generate_closedetailsurl();
				$ajaxdata = $this->generate_ajaxdataforcontento();
				$addclass = 'load-link';
				$icon = '-';
			} else {
				$href = $this->generate_loaddetailsurl($quote);
				$ajaxdata = "data-loadinto=$this->loadinto|data-focus=#$quote->quotnbr";
				$addclass = 'generate-load-link';
				$icon = '+';
			}
			return $bootstrap->create_element('a', "href=$href|class=btn btn-sm btn-primary $addclass|$ajaxdata", $icon);
		}

		public function generate_closedetailsurl() {
			$url = new \Purl\Url($this->pageurl->getUrl());
			$url->query->setData(array('qnbr' => false, 'show' => false));
			return $url->getUrl();
		}

		public function generate_rowclass(Order $quote) {
			return ($this->activeID == $quote->quotnbr) ? 'selected' : '';
		}

		public function generate_shiptopopover(Order $quote) {
			$bootstrap = new HTMLWriter();
			$address = $quote->shipaddress.'<br>';
			$address .= (!empty($quote->shipaddress2)) ? $quote->shipaddress2."<br>" : '';
			$address .= $quote->shipcity.", ". $quote->shipstate.' ' . $quote->shipzip;
			$attr = "tabindex=0|role=button|class=btn btn-default bordered btn-sm|data-toggle=popover";
			$attr .= "|data-placement=top|data-trigger=focus|data-html=true|title=Ship-To Address|data-content=$address";
			return $bootstrap->create_element('a', $attr, '<b>?</b>');
		}

		public function generate_iconlegend() {
			$bootstrap = new HTMLWriter();
			$content = $bootstrap->create_element('i', 'class=glyphicon glyphicon-shopping-cart|title=Re-order Icon', '') . ' = Re-order <br>';
			$content .= $bootstrap->create_element('i', "class=material-icons|title=Documents Icon", '&#xE873;') . '&nbsp; = Documents <br>';
			$content .= $bootstrap->create_element('i', 'class=glyphicon glyphicon-plane hover|title=Tracking Icon', '') . ' = Tracking <br>';
			$content .= $bootstrap->create_element('i', 'class=material-icons|title=Notes Icon', '&#xE0B9;') . ' = Notes <br>';
			$content .= $bootstrap->create_element('i', 'class=glyphicon glyphicon-pencil|title=Edit Order Icon', '') . ' = Edit Order <br>';
			$content = str_replace('"', "'", $content);
			$attr = "tabindex=0|role=button|class=btn btn-sm btn-info|data-toggle=popover|data-placement=bottom|data-trigger=focus";
			$attr .= "|data-html=true|title=Icons Definition|data-content=$content";
			return $bootstrap->create_element('a', $attr, 'Icon Definitions');
		}

		public function generate_loadurl() {
			$url = new \Purl\Url($this->pageurl->getUrl());
			$url->path = DplusWire::wire('config')->pages->quotes.'redir/';
			$url->query->setData(array('action' => 'load-quotes'));
			return $url->getUrl();
		}

		public function generate_loaddetailsurl(Order $quote) {
			$url = new \Purl\Url($this->generate_loaddetailsurltrait($quote));
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

		/* =============================================================
			OrderDisplayInterface Functions
			LINKS ARE HTML LINKS, AND URLS ARE THE URLS THAT THE HREF VALUE
		============================================================ */
		public function generate_loaddplusnoteslink(Order $quote, $linenbr = '0') {
			$bootstrap = new HTMLWriter();
			$href = $this->generate_dplusnotesrequesturl($quote, $linenbr);

			if ($quote->can_edit()) {
				$title = ($quote->has_notes()) ? "View and Create Quote Notes" : "Create Quote Notes";
				$addclass = ($quote->has_notes()) ? '' : 'text-muted';
			} else {
				$title = ($quote->has_notes()) ? "View Quote Notes" : "View Quote Notes";
				$addclass = ($quote->has_notes()) ? '' : 'text-muted';
			}
			$content = $bootstrap->icon('material-icons md-36', '&#xE0B9;');
			$link = $bootstrap->create_element('a', "href=$href|class=load-notes $addclass|title=$title|data-modal=$this->modal", $content);
			return $link;
		}

		public function generate_documentsrequesturl(Order $quote, OrderDetail $quotedetail = null) {
			$url = new \Purl\Url($this->generate_documentsrequesturltrait($quote, $quotedetail));
			$url->query->set('page', $this->pagenbr);
			$url->query->set('orderby', $this->tablesorter->orderbystring);
			return $url->getUrl();
		}

		public function generate_viewlinkeduseractionslink(Order $quote) {
			$bootstrap = new HTMLWriter();
			$href = $this->generate_viewlinkeduseractionsurl($quote);
			$icon = $bootstrap->create_element('span','class=h3', $bootstrap->icon('glyphicon glyphicon-check'));
			return $bootstrap->create_element('a', "href=$href|class=load-into-modal|data-modal=$this->modal", $icon." View Associated Actions");
		}

		public function generate_editlink(Order $quote) {
			$bootstrap = new HTMLWriter();

			if (DplusWire::wire('user')->hasquotelocked) {
				if ($quote->quotnbr == DplusWire::wire('user')->lockedqnbr) {
					$icon = $bootstrap->icon('glyphicon glyphicon-wrench');
					$title = "Continue editing this Quote";
				} else {
					$icon = $bootstrap->icon('material-icons md-36', '&#xE897;');
					$title = "Open Quote in Read Only Mode";
				}
			} else {
				$icon = $bootstrap->icon('glyphicon glyphicon-pencil');
				$title = "Edit Quote";
			}

			$href = $this->generate_editurl($quote);
			return $bootstrap->create_element('a', "href=$href|class=edit-order h3|title=$title", $icon);
		}

		public function generate_loaddocumentslink(Order $quote, OrderDetail $quotedetail = null) {
			$bootstrap = new HTMLWriter();
			$href = $this->generate_documentsrequesturl($quote, $quotedetail);
			$icon = $bootstrap->icon('material-icons md-36', '&#xE873;');
			$ajaxdata = $this->generate_ajaxdataforcontento();

			if ($quote->has_documents()) {
				return $bootstrap->create_element('a', "href=$href|class=generate-load-link|title=Click to view Documents|$ajaxdata", $icon);
			} else {
				return $bootstrap->create_element('a', "href=#|class=text-muted|title=No Documents Available", $icon);
			}
		}

		public function generate_detailvieweditlink(Order $quote, OrderDetail $detail) {
			$bootstrap = new HTMLWriter();
			$href = $this->generate_detailviewediturl($quote, $detail);
			return $bootstrap->create_element('a', "href=$href|class=update-line|data-kit=$detail->kititemflag|data-itemid=$detail->itemid|data-custid=$quote->custid|aria-label=View Detail Line", $detail->itemid);
		}

		public function generate_lastloadeddescription() {
			if (DplusWire::wire('session')->{'quotes-loaded-for'}) {
				if (DplusWire::wire('session')->{'quotes-loaded-for'} == DplusWire::wire('user')->loginid) {
					return 'Last Updated : ' . DplusWire::wire('session')->{'quotes-updated'};
				}
			}
			return '';
		}

		public function generate_filter(ProcessWire\WireInput $input) {
			$stringerbell = new StringerBell();
			parent::generate_filter($input);

			if (isset($this->filters['quotdate'])) {
				if (empty($this->filters['quotdate'][0])) {
					$this->filters['quotdate'][0] = date('m/d/Y', strtotime(get_minquotedate($this->sessionID, 'quotdate')));
				}

				if (empty($this->filters['quotdate'][1])) {
					$this->filters['quotdate'][1] = date('m/d/Y');
				}
			}

			if (isset($this->filters['revdate'])) {
				if (empty($this->filters['revdate'][0])) {
					$this->filters['revdate'][0] = date('m/d/Y', strtotime(get_minquotedate($this->sessionID, 'revdate')));
				}

				if (empty($this->filters['revdate'][1])) {
					$this->filters['revdate'][1] = date('m/d/Y');
				}
			}

			if (isset($this->filters['expdate'])) {
				if (empty($this->filters['expdate'][0])) {
					$this->filters['expdate'][0] = date('m/d/Y', strtotime(get_minquotedate($this->sessionID, 'expdate')));
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
