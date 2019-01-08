<?php
	namespace Dplus\Dpluso\OrderDisplays;

	use Purl\Url;
	use Dplus\ProcessWire\DplusWire;

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
		 * Returns URL load the dplus notes from
		 * @param  Order  $quote    to use Quotenbr
		 * @param  int    $linenbr  Line Number
		 * @return string           URL to load Dplus Notes
		 */
		public function generate_request_dplusnotesURL(Order $quote, $linenbr = 0) {
			$url = new Url($this->pageurl->getUrl());
			$url->path = DplusWire::wire('config')->pages->notes."redir/";
			$url->query->setData(array('action' => 'get-quote-notes', 'qnbr' => $quote->quotnbr, 'linenbr' => $linenbr));
			return $url->getUrl();
		}

		/**
		 * Sets up a common url function for getting documents request url, classes that have this trait
		 * will define generate_request_documentsURL(Order $quote)
		 * Not used as of 10/25/2017
		 * @param  Order  $quote [description]
		 * @return string		URL to the order redirect to make the get order documents request
		 */
		public function generate_request_documentsURL(Order $quote, OrderDetail $quotedetail = null) {
			return '';
		}

		/**
		 * Returns with the URL to edit the Quote
		 * @param  Order  $quote Used for Quotenbr
		 * @return string        URL to edit quote
		 */
		public function generate_editURL(Order $quote) {
			$url = $this->generate_quotesredirURL();
			$url->query->setData(array('action' => 'edit-quote', 'qnbr' => $quote->quotnbr));
			$url->query->set('quoteorigin', $this->pageurl->getURL());
			return $url->getUrl();
		}

		/**
		 * Returns URL to push quote to Order
		 * @param  Order  $quote Quotenbr
		 * @return string URL to Order Quote
		 */
		public function generate_orderquoteURL(Order $quote) {
			$url = $url = new Url($this->pageurl->getUrl());
			$url->path = DplusWire::wire('config')->pages->orderquote;
			$url->query->setData(array('qnbr' => $quote->quotnbr));
			return $url->getUrl();
		}


		/**
		 * Returns URL to view the print version
		 * @param  Order  $quote Uses Quotenbr
		 * @return string        Print Link URL
		 * @uses                 $this->generate_request_detailsURL($quote)
		 */
		public function generate_printURL(Order $quote) {
			$url = new Url($this->generate_request_detailsURL($quote));
			$url->query->set('print', 'true');
			return $url->getUrl();
		}

		/**
		 * Returns URL to view the print page version
		 * NOTE USED for PDFMaker
		 * @param  Order  $quote Uses Quotenbr
		 * @return string        Print Link URL
		 */
		public function generate_printpageURL(Order $quote) {
			$url = new Url($this->pageurl->getUrl());
			$url->path = DplusWire::wire('config')->pages->print."quote/";
			$url->query->set('qnbr', $quote->quotnbr);
			$url->query->set('view', 'pdf');
			return $url->getUrl();
		}

		/**
		 * Returns URL to send email
		 * @param  Order  $quote Uses Quotenbr
		 * @return string        Print Link URL
		 */
		public function generate_sendemailURL(Order $quote) {
			$url = new Url(DplusWire::wire('config')->pages->email."quote/");
			$url->query->set('qnbr', $quote->quotnbr);
			$url->query->set('referenceID', $this->sessionID);
			return $url->getUrl();
		}

		/**
		 * Returns URL to load linked user actions
		 * @param  Order  $quote For quotelink
		 * @return string        URL to load linked useractions
		 */
		public function generate_linkeduseractionsURL(Order $quote) {
			$url = new Url($this->pageurl->getUrl());
			$url->path = DplusWire::wire('config')->pages->actions."all/load/list/quote/";
			$url->query->setData(array('qnbr' => $quote->quotnbr));
			return $url->getUrl();
		}

		/**
		 * Returns the URL to view the detail
		 * @param  Order       $quote  For quotenbr
		 * @param  OrderDetail $detail For Linenbr
		 * @return string              View Detail URL
		 */
		public function generate_viewdetailURL(Order $quote, OrderDetail $detail) {
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
		public function generate_request_detailsURL(Order $quote) {
			$url = $this->generate_quotesredirURL();
			$url->query->setData(array('action' => 'load-quote-details', 'qnbr' => $quote->quotnbr));
			return $url->getUrl();
		}

		/**
		 * Returns the URL to view / edit the detail
		 * @param  Order       $quote  for Quote Number
		 * @param  OrderDetail $detail for detail line Number
		 * @return string              URL to edit / view detail
		 */
		public function generate_vieweditdetailURL(Order $quote, OrderDetail $detail) {
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
		 * Makes the URL to the orders redirect page,
		 * @return Url URL to REDIRECT page
		 */
		public function generate_quotesredirURL() {
			$url = new Url(DplusWire::wire('config')->pages->quotes);
			$url->path = DplusWire::wire('config')->pages->quotes."redir/";
			return $url;
		}
	}
