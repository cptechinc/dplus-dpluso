<?php
	namespace Dplus\Dpluso\OrderDisplays;
	
	use Purl\Url;
	use Dplus\ProcessWire\DplusWire;

	/**
	 * Use Statements for Model Classes which are non-namespaced
	 */
	use Order, OrderDetail, Quote;

	class EditQuoteDisplay extends QuoteDisplay {
		use QuoteDisplayTraits;
		
		/**
		 * Primary Constructor
		 * @param string   $sessionID Session Identifier
		 * @param Url      $pageurl   URL to Page
		 * @param string   $modal     Modal to use for AJAX
		 * @param string   $qnbr      Quote #
		 */
		public function __construct($sessionID, Url $pageurl, $modal, $qnbr) {
			parent::__construct($sessionID, $pageurl, $modal, $qnbr);
		}

		/* =============================================================
			Class Functions
		============================================================ */

		/**
		 * Returns URL to unlock Quote
		 * @param  Order  $quote Quote
		 * @return string        URL to unlock Quote
		 */
		public function generate_unlockURL(Order $quote) {
			$url = $this->generate_quotesredirURL();
			$url->query->set('action', 'unlock-quote');
			$url->query->set('qnbr', $quote->quotnbr);
			return $url->getUrl();
		}

		/**
		 * Returns confirmation page URL
		 * @param  Order  $quote Quote
		 * @return string        URL for Quote confirmation page
		 */
		public function generate_confirmationURL(Order $quote) {
			$url = new Url(DplusWire::wire('config')->pages->confirmquote);
			$url->query->set('qnbr', $quote->quotnbr);
			return $url->getUrl();
		}
		
		/**
		 * Returns URL to delete detail line
		 * @param  Order       $quote  Quote
		 * @param  OrderDetail $detail QuoteDetail
		 * @return string              HTML Link to delete detail line
		 */
		function generate_removedetailURL(Order $quote, OrderDetail $detail) {
			$url = $this->generate_quotesredirURL();
			$url->query->setData(array('action' => 'remove-line-get', 'qnbr' => $quote->quotnbr, 'linenbr' => $detail->linenbr, 'page' => $this->pageurl->getUrl()));
			return $url->getUrl();
		}
	}
