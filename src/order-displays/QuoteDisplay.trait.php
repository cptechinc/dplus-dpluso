<?php
	/**
	 * Traits that will be shared accross QuotePanels and Quote Displays
	 */
	trait QuoteDisplayTraits {
		/* =============================================================
			OrderDisplayInterface Functions
			LINKS ARE HTML LINKS, AND URLS ARE THE URLS THAT THE HREF VALUE
		============================================================ */
		/**
		 * Generates an HTML link for loading the dplus notes
		 * @param  Order  $quote   Quote to load Dplus notes from
		 * @param  string $linenbr Line Number to load the notes for
		 * @return string          HTML to load the dplus notes
		 */
		public function generate_loaddplusnoteslink(Order $quote, $linenbr = '0') {
			$bootstrap = new HTMLWriter();
			$href = $this->generate_dplusnotesrequesturl($quote, $linenbr);

			if ($quote->can_edit()) {
				$title = ($quote->has_notes()) ? "View and Create Quote Notes" : "Create Quote Notes";
			} else {
				$title = ($quote->has_notes()) ? "View Quote Notes" : "View Quote Notes";
			}
			$content = $bootstrap->icon('material-icons', '&#xE0B9;') . ' ' . $title;
			return $bootstrap->create_element('a', "href=$href|class=btn btn-default load-notes|title=$title|data-modal=$this->modal", $content);
		}

		/**
		 * Returns URL load the dplus notes from
		 * @param  Order  $quote    to use Quotenbr
		 * @param  int    $linenbr  Line Number
		 * @return string           URL to load Dplus Notes
		 */
		public function generate_dplusnotesrequesturl(Order $quote, $linenbr) {
			$url = new \Purl\Url($this->pageurl->getUrl());
			$url->path = DplusWire::wire('config')->pages->notes."redir/";
			$url->query->setData(array('action' => 'get-quote-notes', 'qnbr' => $quote->quotnbr, 'linenbr' => $linenbr));
			return $url->getUrl();
		}

		/**
		 * Returns HTML link to load documents from
		 * @param  Order  $quote            Quote
		 * @param  OrderDetail $quotedetail Decides if to load detail line notes
		 * @return string                   HTML link
		 */
		public function generate_loaddocumentslink(Order $quote, OrderDetail $quotedetail = null) {
			if ($quotedetail) {
				return $this->generate_loaddetaildocumentslink($quote, $quotedetail);
			} else {
				return $this->generate_loadheaderdocumentslink($quote, $quotedetail);
			}
		}

		/**
		 * Returns HTML link to load header documents from
		 * @param  Order  $quote            Quote
		 * @param  OrderDetail $quotedetail Decides if to load detail line notes
		 * @return string                   HTML link
		 */
		public function generate_loadheaderdocumentslink(Order $quote, OrderDetail $quotedetail = null) {
			$bootstrap = new HTMLWriter();
			$href = $this->generate_documentsrequesturl($quote, $quotedetail);
			$icon = $bootstrap->icon('fa fa-file-text');
			$ajaxdata = "data-loadinto=.docs|data-focus=.docs|data-click=#documents-link";

			if ($quote->has_documents()) {
				return $bootstrap->create_element('a', "href=$href|class=btn btn-primary load-sales-docs|role=button|title=Click to view Documents|$ajaxdata", $icon. ' Show Documents');
			} else {
				return $bootstrap->create_element('a', "href=#|class=btn btn-default|title=No Documents Available", $icon. ' 0 Documents Found');
			}
		}

		/**
		 * Returns HTML link to load detail documents from
		 * @param  Order  $quote            Quote
		 * @param  OrderDetail $quotedetail Decides if to load detail line notes
		 * @return string                   HTML link
		 */
		public function generate_loaddetaildocumentslink(Order $quote, OrderDetail $quotedetail = null) {
			$bootstrap = new HTMLWriter();
			$href = $this->generate_documentsrequesturl($quote, $quotedetail);
			$icon = $bootstrap->icon('fa fa-file-text');
			$ajaxdata = "data-loadinto=.docs|data-focus=.docs|data-click=#documents-link";

			if ($quotedetail->has_documents()) {
				return $bootstrap->create_element('a', "href=$href|class=h3 load-sales-docs|role=button|title=Click to view Documents|$ajaxdata", $icon);
			} else {
				return $bootstrap->create_element('a', "href=#|class=h3 text-muted|title=No Documents Available", $icon);
			}
		}


		/**
		 * Sets up a common url function for getting documents request url, classes that have this trait
		 * will define generate_documentsrequesturl(Order $quote)
		 * Not used as of 10/25/2017
		 * @param  Order  $quote [description]
		 * @return string		URL to the order redirect to make the get order documents request
		 */
		public function generate_documentsrequesturltrait(Order $quote, OrderDetail $quotedetail = null) {
			$url = $this->generate_quotesredirurl();
			$url->query->setData(array('action' => 'get-quote-documents', 'qnbr' => $quote->quotnbr));
			if ($quotedetail) {
				$url->query->set('itemdoc', $quotedetail->itemid);
			}
			return $url->getUrl();
		}

		/**
		 * Returns with the URL to edit the Quote
		 * @param  Order  $quote Used for Quotenbr
		 * @return string        URL to edit quote
		 */
		public function generate_editurl(Order $quote) {
			$url = $this->generate_quotesredirurl();
			$url->query->setData(array('action' => 'edit-quote', 'qnbr' => $quote->quotnbr));
			return $url->getUrl();
		}

		/**
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
			$icon = $bootstrap->icon('glyphicon glyphicon-print');
			return $bootstrap->create_element('a', "href=$href|class=btn btn-sm btn-default", $icon." Send To Order");
		}

		/**
		 * Returns URL to push quote to Order
		 * @param  Order  $quote Quotenbr
		 * @return string URL to Order Quote
		 */
		public function generate_orderquoteurl(Order $quote) {
			$url = $url = new \Purl\Url($this->pageurl->getUrl());
			$url->path = DplusWire::wire('config')->pages->orderquote;
			$url->query->setData(array('qnbr' => $quote->quotnbr));
			return $url->getUrl();
		}

		/**
		 * Returns HTML Link to view the print version of this quote
		 * @param  Order  $quote
		 * @return string HTML link to view print version
		 * @uses          $this->generate_viewprinturl($quote);
		 */
		public function generate_viewprintlink(Order $quote) {
			$bootstrap = new HTMLWriter();
			$href = $this->generate_viewprinturl($quote);
			$icon = $bootstrap->create_element('span','class=h3', $bootstrap->icon('glyphicon glyphicon-print'));
			return $bootstrap->create_element('a', "href=$href|target=_blank", $icon." View Printable Quote");
		}

		/**
		 * Returns URL to view the print version
		 * @param  Order  $quote Uses Quotenbr
		 * @return string        Print Link URL
		 * @uses                 $this->generate_loaddetailsurl($quote)
		 */
		public function generate_viewprinturl(Order $quote) {
			$url = new \Purl\Url($this->generate_loaddetailsurl($quote));
			$url->query->set('print', 'true');
			return $url->getUrl();
		}

		/**
		 * Returns URL to view the print page version
		 * @param  Order  $quote Uses Quotenbr
		 * @return string        Print Link URL
		 * @uses                 $this->generate_loaddetailsurl($quote)
		 */
		public function generate_viewprintpageurl(Order $quote) {
			$url = new \Purl\Url($this->pageurl->getUrl());
			$url->path = DplusWire::wire('config')->pages->print."quote/";
			$url->query->set('qnbr', $quote->quotnbr);
			$url->query->set('view', 'pdf');
			return $url->getUrl();
		}

		/**
		 * Returns URL to send email
		 * @param  Order  $quote Uses Quotenbr
		 * @return string        Print Link URL
		 * @uses                 $this->generate_loaddetailsurl($quote)
		 */
		public function generate_sendemailurl(Order $quote) {
			$url = new \Purl\Url(DplusWire::wire('config')->pages->email."quote/");
			$url->query->set('qnbr', $quote->quotnbr);
			$url->query->set('referenceID', $this->sessionID);
			return $url->getUrl();
		}

		/**
		 * Returns HTML link to view linked user actions link
		 * @param  Order  $quote for quotenbr
		 * @return string        HTML link for viewing linked user actions
		 * @uses                 $this->generate_viewlinkeduseractionsurl($quote);
		 */
		public function generate_viewlinkeduseractionslink(Order $quote) {
			$href = $this->generate_viewlinkeduseractionsurl($quote);
			$icon = $bootstrap->create_element('span','class=h3', $bootstrap->icon('glyphicon glyphicon-check'));
			return $bootstrap->create_element('a', "href=$href|target=_blank", $icon." View Associated Actions");
		}

		/**
		 * Returns URL to load linked user actions
		 * @param  Order  $quote For quotelink
		 * @return string        URL to load linked useractions
		 */
		public function generate_viewlinkeduseractionsurl(Order $quote) {
			$url = new \Purl\Url($this->pageurl->getUrl());
			$url->path = DplusWire::wire('config')->pages->actions."all/load/list/quote/";
			$url->query->setData(array('qnbr' => $quote->quotnbr));
			return $url->getUrl();
		}

		/**
		 * Returns HTML link to load the quote detail
		 * @param  Order       $quote  For QuoteNbr
		 * @param  OrderDetail $detail Gets quote attributes
		 * @return string              HTML link to load details
		 */
		public function generate_viewdetaillink(Order $quote, OrderDetail $detail) {
			$bootstrap = new HTMLWriter();
			$href = $this->generate_viewdetailurl($quote, $detail);
			$icon = $bootstrap->icon('fa fa-info-circle');
			return $bootstrap->create_element('a', "href=$href|class=h3 view-item-details detail-line-icon|data-itemid=$detail->itemid|data-kit=$detail->kititemflag|data-modal=#ajax-modal", $icon);
		}

		/**
		 * Returns the URL to view the detail
		 * @param  Order       $quote  For quotenbr
		 * @param  OrderDetail $detail For Linenbr
		 * @return string              View Detail URL
		 */
		public function generate_viewdetailurl(Order $quote, OrderDetail $detail) {
			$url = new \Purl\Url($this->pageurl->getUrl());
			$url->path = DplusWire::wire('config')->pages->ajax."load/view-detail/quote/";
			$url->query->setData(array('qnbr' => $quote->quotnbr, 'line' => $detail->linenbr));
			return $url->getUrl();
		}

		/**
		 * Return String URL to orders redir to request order details
		 * This is here for the use of getting the Print link
		 * @param  Order  $quote [description]
		 * @return string		[description]
		 */
		public function generate_loaddetailsurltrait(Order $quote) {
			$url = $this->generate_quotesredirurl();
			$url->query->setData(array('action' => 'load-quote-details', 'qnbr' => $quote->quotnbr));
			return $url->getUrl();
		}

		/**
		 * Returns the URL to view / edit the detail
		 * @param  Order       $quote  for Quote Number
		 * @param  OrderDetail $detail for detail line Number
		 * @return string              URL to edit / view detail
		 */
		public function generate_detailviewediturl(Order $quote, OrderDetail $detail) {
			$url = new \Purl\Url(DplusWire::wire('config')->pages->ajaxload.'edit-detail/quote/');
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
		 * Makes the URL to the orders redirect page,
		 * @return Purl\Url URL to REDIRECT page
		 */
		public function generate_quotesredirurl() {
			$url = new \Purl\Url(DplusWire::wire('config')->pages->quotes);
			$url->path = DplusWire::wire('config')->pages->quotes."redir/";
			return $url;
		}
	}
