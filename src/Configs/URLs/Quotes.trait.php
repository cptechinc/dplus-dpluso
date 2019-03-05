<?php
	namespace Dplus\Dpluso\Configs;

	/**
	 * External Libraries
	 */
	use Purl\Url;

	/**
	 * Functions that provide URLS to Order Pages / Functions
	 */
	trait QuoteURLsTraits {
		/**
		 * Returns the URL to the Sales Order Redirect for Requests
		 * @return string Sales Order Redirect
		 */
		public function get_quotes_redirURL() {
			$url = new Url($this->paths->get_urlpath('quotes'));
			$url->path->add('redir');
			return $url->getUrl();
		}

		/**
		 * Returns URL to Request Quote Details
		 * @param  string $qnbr Quote Number
		 * @return string       Request Quote Details URL
		 */
		public function get_quote_request_detailsURL($qnbr) {
			$url = new Url($this->get_quotes_redirURL());
			$url->query->set('action', 'load-quote-details');
			$url->query->set('qnbr', $qnbr);
			return $url->getUrl();
		}

		/**
		 * Returns URL to Request Quote Details and View Print Page
		 * @param  string $qnbr Quote Number
		 * @return string       Request Quote Details URL
		 */
		public function get_quote_request_printURL($qnbr) {
			$url = new Url($this->get_quote_request_detailsURL($qnbr));
			$url->query->set('print', 'true');
			return $url->getUrl();
		}

		/**
		 * Returns URL to view Quote Page
		 * NOTE USED FOR PDFMAKER
		 * @param  string $qnbr        Quote Number
		 * @param  bool   $pdf         Is this for PDF Maker?
		 * @return string              Quote Print Page URL for PDFMaker
		 */
		public function get_quote_printpageURL($qnbr, $pdf = false) {
			$url = new Url($this->find('print_quote'));
			$url->query->set('qnbr', $qnbr);
			if ($pdf) {
				$url->query->set('view', 'pdf');
			}
			return $url->getUrl();
		}

		/**
		 * Returns URL to Request Edit Quote
		 * @param  string $qnbr       Quote Number
		 * @param  string $originURL  URL to send to Request
		 * @return string             Reques Edit Quote URL
		 */
		public function get_quote_editURL($qnbr, $originURL) {
			$url = new Url($this->get_quotes_redirURL());
			$url->query->set('action', 'edit-quote');
			$url->query->set('qnbr', $qnbr);
			$url->query->set('quoteorigin', $originURL);
			return $url->getUrl();
		}

		/**
		 * Returns URL to Send Quote to Order Page
		 * @param  string $qnbr Quote ID
		 * @return string       Send Quote to Order URL
		 */
		public function get_quote_orderURL($qnbr) {
			$url = new Url($this->find('edit_orderquote'));
			$url->query->set('qnbr', $qnbr);
			return $url->getUrl();
		}

		/**
		 * Returns URL to Email quote Page
		 * @param  string $qnbr      Quote ID
		 * @param  string $sessionID Session Identifier
		 * @return string            Email Quote URL
		 */
		public function get_email_quoteURL($qnbr, $sessionID) {
			$url = new Url($this->find('sys_email_quote'));
			$url->query->set('qnbr', $qnbr);
			$url->query->set('referenceID', $sessionID);
			return $url->getUrl();
		}

		/**
		 * Returns URL to UserActions Page that are linked to a Quote Number
		 * @param  string $qnbr      Quote ID
		 * @return string            Quote Linked UserActions Page
		 */
		public function get_quote_linkedactionsURL($qnbr) {
			$url = new Url($this->find('activity_useractions'));
			$url->query->set('qnbr', $qnbr);
			return $url->getUrl();
		}

		/**
		 * Returns URL to view Detail Line
		 * @param  string $qnbr      Quote ID
		 * @param  int    $linenbr   Line Number
		 * @return string            View Detail Line URL
		 */
		public function get_quote_view_detailURL($qnbr, int $linenbr) {
			$url = new Url($this->find('ajax_load'));
			$url->path->add('view-detail')->add('quote');
			$url->query->set('qnbr', $qnbr);
			$url->query->set('line', $linenbr);
			return $url->getUrl();
		}

		/**
		 * Returns URL to view Detail Line
		 * @param  string $qnbr      Quote ID
		 * @param  int    $linenbr   Line Number
		 * @return string            View Detail Line URL
		 */
		public function get_quote_edit_detailURL($qnbr, int $linenbr) {
			$url = new Url($this->find('ajax_load'));
			$url->path->add('edit-detail')->add('quote');
			$url->query->set('qnbr', $qnbr);
			$url->query->set('line', $linenbr);
			return $url->getUrl();
		}

		/**
		 * Returns URL to Unlock Quote
		 * @param  string $qnbr Quote ID
		 * @return string       Unlock Quote URL
		 */
		public function get_quote_unlockURL($qnbr) {
			$url = new Url($this->get_quotes_redirURL());
			$url->query->set('action', 'unlock-quote');
			$url->query->set('qnbr', $qnbr);
			return $url->getUrl();
		}

		/**
		 * Returns URL for Quote Confirmation Page
		 * @param  string $qnbr      Quote ID
		 * @return string            Quote confirmation Page
		 */
		public function get_quote_confirmURL($qnbr) {
			$url = new Url($this->find('confirm_quote'));
			$url->query->set('qnbr', $qnbr);
			return $url->getUrl();
		}

		/**
		 * Returns URL to remove Detail Line from Quote
		 * @param  string $qnbr          Quote ID
		 * @param  int    $linenbr       Detail Line Number
		 * @param  string $returnpageURL URL to Return to after Request
		 * @return string                Remove Quote Detail Line URL
		 */
		public function get_quote_removedetailURL($qnbr, $linenbr, $returnpageURL) {
			$url = new Url($this->get_quotes_redirURL());
			$url->query->set('action', 'remove-line-get');
			$url->query->set('qnbr', $qnbr);
			$url->query->set('linenbr', $linenbr);
			$url->query->set('page', $returnpageURL);
			return $url->getUrl();
		}

		/**
		 * Returns URL to request Quotes from Dplus
		 * @return string Request Quotes URL
		 */
		public function get_quotes_loadURL() {
			$url = new Url($this->get_quotes_redirURL());
			$url->query->set('action', 'load-quotes');
			return $url->getUrl();
		}

		/**
		 * Returns URL to edit Newly Created Quote
		 * @return string Edit new Quote URL
		 */
		public function get_new_editquoteURL() {
			$url = new Url($this->get_quotes_redirURL());
			$url->query->set('action', 'edit-new-quote');
			return $url->getUrl();
		}

		/**
		 * Returns URL to the Edit Quote Page
		 * @param  string $qnbr Quote ID
		 * @return string       Edit Quote Page URL
		 */
		public function get_edit_quotepageURL($qnbr) {
			$url = new Url($this->find('edit_quote'));
			$url->query->set('qnbr', $qnbr);
			return $url->getUrl();
		}
	}
