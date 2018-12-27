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
		// TODO rename for URL()
		public function generate_documentsrequesturl(Order $quote, OrderDetail $quotedetail = null) {
			return $this->generate_documentsrequesturltrait($quote, $quotedetail);
		}
		
		// FIXME Remove, and make link at presentation level
		public function generate_editlink(Order $quote) {
			$bootstrap = new HTMLWriter();
			$href = $this->generate_editurl($quote);
			$icon = $bootstrap->icon('material-icons', '&#xE150;');
			return $bootstrap->a("href=$href|class=btn btn-block btn-warning", $icon. " Edit Quote");
		}
		
		public function generate_loaddetailsURL(Order $quote) {
			$url = new Url($this->generate_loaddetailsURLtrait($quote));
			return $url->getUrl();
		}
		
		// FIXME Remove, and make link at presentation level
		public function generate_detailvieweditlink(Order $quote, OrderDetail $detail) {
			$bootstrap = new HTMLWriter();
			$href = $this->generate_detailviewediturl($quote, $detail);
			$icon = $bootstrap->span('class=h3', $bootstrap->icon('fa fa-eye'));
			return $bootstrap->a("href=$href|class=update-line|data-kit=$detail->kititemflag|data-itemid=$detail->itemid|data-custid=$quote->custid|aria-label=View Detail Line", $icon);
		}
	}
