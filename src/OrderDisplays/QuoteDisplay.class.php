<?php
	namespace Dplus\Dpluso\OrderDisplays;
	
	use Purl\Url;
	use Dplus\Content\HTMLWriter;

	/**
	 * Use Statements for Model Classes which are non-namespaced
	 */
	use Order, OrderDetail, Quote;

	class QuoteDisplay extends OrderDisplay implements OrderDisplayInterface, QuoteDisplayInterface {
		use QuoteDisplayTraits;

		/**
		 * Quote Number
		 * @var string
		 */
		protected $qnbr;

		/**
		 * Quote
		 * @var Quote
		 */
		protected $quote;

		/**
		 * Primary Constructor
		 * @param string   $sessionID Session Identifier
		 * @param Url      $pageurl   URL to current page
		 * @param string   $modal     ID of modal to use for AJAX
		 * @param string   $qnbr      Quote Number
		 */
		public function __construct($sessionID, Url $pageurl, $modal, $qnbr) {
			parent::__construct($sessionID, $pageurl, $modal);
			$this->qnbr = $qnbr;
		}

		/* =============================================================
			Class Functions
		============================================================ */
		/**
		 * Loads Quote from database
		 * @param  bool   $debug If Query is Executed
		 * @return Quote         Quote | SQL Query
		 * @uses
		 */
		public function get_quote($debug = false) {
			return get_quotehead($this->sessionID, $this->qnbr, 'Quote', false);
		}

		/* =============================================================
			OrderDisplayInterface Functions
			LINKS ARE HTML LINKS, AND URLS ARE THE URLS THAT THE HREF VALUE
		============================================================ */
		public function generate_request_documentsURL(Order $quote, OrderDetail $quotedetail = null) {
			return $this->generate_documentsrequestURLtrait($quote, $quotedetail);
		}
		
		public function generate_request_dplusnotesURL(Order $quote, $linenbr = 0) {
			return $this->generate_request_dplusnotesURLtrait($quote, $linenbr);
		}
		
		public function generate_request_detailsURL(Order $quote) {
			$url = new Url($this->generate_loaddetailsURLtrait($quote));
			return $url->getUrl();
		}
	}
