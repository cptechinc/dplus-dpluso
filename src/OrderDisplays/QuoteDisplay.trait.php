<?php
	namespace Dplus\Dpluso\OrderDisplays;
	
	use Purl\Url;
	use Dplus\ProcessWire\DplusWire;
	use Dplus\Content\HTMLWriter;

	/**
	 * Use Statements for Model Classes which are non-namespaced
	 */
	use Order, OrderDetail;


	/**
	 * Traits that will be shared accross QuotePanels and Quote Displays
	 */
	trait QuoteDisplayTraits {
		/* =============================================================
			OrderDisplayInterface Functions
			LINKS ARE HTML LINKS, AND URLS ARE THE URLS THAT THE HREF VALUE
		============================================================ */
		/**
		 * // TODO rename for URL()
		 * Returns URL load the dplus notes from
		 * @param  Order  $quote    to use Quotenbr
		 * @param  int    $linenbr  Line Number
		 * @return string           URL to load Dplus Notes
		 */
		public function generate_dplusnotesrequestURL(Order $quote, $linenbr) {
			$url = new Url($this->pageurl->getUrl());
			$url->path = DplusWire::wire('config')->pages->notes."redir/";
			$url->query->setData(array('action' => 'get-quote-notes', 'qnbr' => $quote->quotnbr, 'linenbr' => $linenbr));
			return $url->getUrl();
		}

		/**
		 * // TODO rename for URL()
		 * Sets up a common url function for getting documents request url, classes that have this trait
		 * will define generate_documentsrequestURL(Order $quote)
		 * Not used as of 10/25/2017
		 * @param  Order  $quote [description]
		 * @return string		URL to the order redirect to make the get order documents request
		 */
		public function generate_documentsrequestURLtrait(Order $quote, OrderDetail $quotedetail = null) {
			$url = $this->generate_quotesredirurl();
			$url->query->setData(array('action' => 'get-quote-documents', 'qnbr' => $quote->quotnbr));
			if ($quotedetail) {
				$url->query->set('itemdoc', $quotedetail->itemid);
			}
			return $url->getUrl();
		}

		/**
		 * // TODO rename for URL()
		 * Returns with the URL to edit the Quote
		 * @param  Order  $quote Used for Quotenbr
		 * @return string        URL to edit quote
		 */
		public function generate_editURL(Order $quote) {
			$url = $this->generate_quotesredirurl();
			$url->query->setData(array('action' => 'edit-quote', 'qnbr' => $quote->quotnbr));
			return $url->getUrl();
		}

		/**
		 * // FIXME Remove, and make link at presentation level
		 * Returns link to Order the quote
		 * @param  Order  $quote Used to get Quote Nbr
		 * @return string HTML link for ordering quote
		 */
		public function generate_orderquotelink(Order $quote) {
			if (!has_dpluspermission(DplusWire::wire('user')->loginid, 'eso')) {
				return false;
			}
			$bootstrap = new HTMLWriter();
			$href = $this->generate_orderquoteurl($quote);
			$icon = $bootstrap->icon('fa fa-print');
			return $bootstrap->a("href=$href|class=btn btn-sm btn-default", $icon." Send To Order");
		}

		/**
		 * // TODO rename for URL()
		 * Returns URL to push quote to Order
		 * @param  Order  $quote Quotenbr
		 * @return string URL to Order Quote
		 */
		public function generate_orderquoteurl(Order $quote) {
			$url = $url = new Url($this->pageurl->getUrl());
			$url->path = DplusWire::wire('config')->pages->orderquote;
			$url->query->setData(array('qnbr' => $quote->quotnbr));
			return $url->getUrl();
		}

		/**
		 * // FIXME Remove, and make link at presentation level
		 * Returns HTML Link to view the print version of this quote
		 * @param  Order  $quote
		 * @return string HTML link to view print version
		 * @uses          $this->generate_viewprinturl($quote);
		 */
		public function generate_viewprintlink(Order $quote) {
			$bootstrap = new HTMLWriter();
			$href = $this->generate_viewprinturl($quote);
			$icon = $bootstrap->span('class=h3', $bootstrap->icon('fa fa-print'));
			return $bootstrap->a("href=$href|target=_blank", $icon." View Printable Quote");
		}

		/**
		 * // TODO rename for URL()
		 * Returns URL to view the print version
		 * @param  Order  $quote Uses Quotenbr
		 * @return string        Print Link URL
		 * @uses                 $this->generate_loaddetailsURL($quote)
		 */
		public function generate_viewprinturl(Order $quote) {
			$url = new Url($this->generate_loaddetailsURL($quote));
			$url->query->set('print', 'true');
			return $url->getUrl();
		}

		/**
		 * // TODO rename for URL()
		 * Returns URL to view the print page version
		 * @param  Order  $quote Uses Quotenbr
		 * @return string        Print Link URL
		 */
		public function generate_viewprintpageurl(Order $quote) {
			$url = new Url($this->pageurl->getUrl());
			$url->path = DplusWire::wire('config')->pages->print."quote/";
			$url->query->set('qnbr', $quote->quotnbr);
			$url->query->set('view', 'pdf');
			return $url->getUrl();
		}

		/**
		 * // TODO rename for URL()
		 * Returns URL to send email
		 * @param  Order  $quote Uses Quotenbr
		 * @return string        Print Link URL
		 */
		public function generate_sendemailurl(Order $quote) {
			$url = new Url(DplusWire::wire('config')->pages->email."quote/");
			$url->query->set('qnbr', $quote->quotnbr);
			$url->query->set('referenceID', $this->sessionID);
			return $url->getUrl();
		}

		/**
		 * // FIXME Remove, and make link at presentation level
		 * Returns HTML link to view linked user actions link
		 * @param  Order  $quote for quotenbr
		 * @return string        HTML link for viewing linked user actions
		 * @uses                 $this->generate_viewlinkeduseractionsurl($quote);
		 */
		public function generate_viewlinkeduseractionslink(Order $quote) {
			$href = $this->generate_viewlinkeduseractionsurl($quote);
			$icon = $bootstrap->span('class=h3', $bootstrap->icon('fa fa-check-square-o'));
			return $bootstrap->a("href=$href|target=_blank", $icon." View Associated Actions");
		}

		/**
		 * // TODO rename for URL()
		 * Returns URL to load linked user actions
		 * @param  Order  $quote For quotelink
		 * @return string        URL to load linked useractions
		 */
		public function generate_viewlinkeduseractionsurl(Order $quote) {
			$url = new Url($this->pageurl->getUrl());
			$url->path = DplusWire::wire('config')->pages->actions."all/load/list/quote/";
			$url->query->setData(array('qnbr' => $quote->quotnbr));
			return $url->getUrl();
		}

		/**
		 * // FIXME Remove, and make link at presentation level
		 * Returns HTML link to load the quote detail
		 * @param  Order       $quote  For QuoteNbr
		 * @param  OrderDetail $detail Gets quote attributes
		 * @return string              HTML link to load details
		 */
		public function generate_viewdetaillink(Order $quote, OrderDetail $detail) {
			$bootstrap = new HTMLWriter();
			$href = $this->generate_viewdetailurl($quote, $detail);
			$icon = $bootstrap->icon('fa fa-info-circle');
			return $bootstrap->a("href=$href|class=h3 view-item-details detail-line-icon|data-itemid=$detail->itemid|data-kit=$detail->kititemflag|data-modal=#ajax-modal", $icon);
		}

		/**
		 * // TODO rename for URL()
		 * Returns the URL to view the detail
		 * @param  Order       $quote  For quotenbr
		 * @param  OrderDetail $detail For Linenbr
		 * @return string              View Detail URL
		 */
		public function generate_viewdetailurl(Order $quote, OrderDetail $detail) {
			$url = new Url($this->pageurl->getUrl());
			$url->path = DplusWire::wire('config')->pages->ajax."load/view-detail/quote/";
			$url->query->setData(array('qnbr' => $quote->quotnbr, 'line' => $detail->linenbr));
			return $url->getUrl();
		}

		/**
		 * Return String URL to orders redir to request order details
		 * This is here for the use of getting the Print link
		 * @param  Order  $quote Quote
		 * @return string
		 */
		public function generate_loaddetailsURLtrait(Order $quote) {
			$url = $this->generate_quotesredirurl();
			$url->query->setData(array('action' => 'load-quote-details', 'qnbr' => $quote->quotnbr));
			return $url->getUrl();
		}

		/**
		 * // TODO rename for URL()
		 * Returns the URL to view / edit the detail
		 * @param  Order       $quote  for Quote Number
		 * @param  OrderDetail $detail for detail line Number
		 * @return string              URL to edit / view detail
		 */
		public function generate_detailviewediturl(Order $quote, OrderDetail $detail) {
			$url = new Url(DplusWire::wire('config')->pages->ajaxload.'edit-detail/quote/');
			$url->query->setData(array('qnbr' => $quote->quotnbr, 'line' => $detail->linenbr));
			return $url->getUrl();
		}

		/**
		 * Returns an array of QuoteDetail from Database
		 * @param  Order  $quote for QuoteNbr
		 * @param  bool   $debug Determines if Array or SQL Query return
		 * @return array         QuoteDetail array or | SQL Query
		 */
		public function get_quotedetails(Order $quote, $debug = false) {
			return get_quotedetails($this->sessionID, $quote->quotnbr, true, $debug);
		}

		/* =============================================================
			URL Helper Functions
		============================================================ */
		/**
		 * // TODO rename for URL()
		 * Makes the URL to the orders redirect page,
		 * @return Url URL to REDIRECT page
		 */
		public function generate_quotesredirurl() {
			$url = new Url(DplusWire::wire('config')->pages->quotes);
			$url->path = DplusWire::wire('config')->pages->quotes."redir/";
			return $url;
		}
	}
