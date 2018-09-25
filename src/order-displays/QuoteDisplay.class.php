<?php 
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
		 * @param Purl\Url $pageurl   URL to current page
		 * @param string   $modal     ID of modal to use for AJAX
		 * @param string   $qnbr      Quote Number
		 */
		public function __construct($sessionID, \Purl\Url $pageurl, $modal, $qnbr) {
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
		public function generate_documentsrequesturl(Order $quote, OrderDetail $quotedetail = null) {
			return $this->generate_documentsrequesturltrait($quote, $quotedetail);
		}
		
		public function generate_editlink(Order $quote) {
			$bootstrap = new HTMLWriter();
			$href = $this->generate_editurl($quote);
			$icon = $bootstrap->icon('material-icons', '&#xE150;');
			return $bootstrap->create_element('a', "href=$href|class=btn btn-block btn-warning", $icon. " Edit Quote");   
		}
		
		public function generate_loaddetailsurl(Order $quote) {
			$url = new \Purl\Url($this->generate_loaddetailsurltrait($quote));
			return $url->getUrl();
		}
		
		public function generate_detailvieweditlink(Order $quote, OrderDetail $detail) {
			$bootstrap = new HTMLWriter();
			$href = $this->generate_detailviewediturl($quote, $detail);
			$icon = $bootstrap->create_element('span', 'class=h3', $bootstrap->icon('glyphicon glyphicon-eye-open'));
			return $bootstrap->create_element('a', "href=$href|class=update-line|data-kit=$detail->kititemflag|data-itemid=$detail->itemid|data-custid=$quote->custid|aria-label=View Detail Line", $icon);	
		}
	}
