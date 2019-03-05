<?php
	namespace Dplus\Dpluso\OrderDisplays;

	/**
	 * External Libraries
	 */
	use Purl\Url;

	/**
	 * Internal Libraries
	 */
	use Dplus\Dpluso\Configs\DplusoConfigURLs;

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
			$urlconfig = DplusoConfigURLs::get_instance();
			return $urlconfig->get_quote_unlockURL($uote->quotnbr);
		}

		/**
		 * Returns confirmation page URL
		 * @param  Order  $quote Quote
		 * @return string        URL for Quote confirmation page
		 */
		public function generate_confirmationURL(Order $quote) {
			$urlconfig = DplusoConfigURLs::get_instance();
			return $urlconfig->get_quote_confirmURL($qnbr);
		}

		/**
		 * Returns URL to delete detail line
		 * @param  Order       $quote  Quote
		 * @param  OrderDetail $detail QuoteDetail
		 * @return string              HTML Link to delete detail line
		 */
		function generate_removedetailURL(Order $quote, OrderDetail $detail) {
			$urlconfig = DplusoConfigURLs::get_instance();
			return $urlconfig->get_quote_removedetailURL($quote->quotnbr, $detail->linenbr, $this->pageurl->getUrl());
		}
	}
