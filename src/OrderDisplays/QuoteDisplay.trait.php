<?php
	namespace Dplus\Dpluso\OrderDisplays;

	/**
	 * Internal Libraries
	 */
	use Dplus\Dpluso\Configs\DplusoConfigURLs;

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
			$urlconfig = DplusoConfigURLs::get_instance();
			return $urlconfig->get_request_quote_dplusnotesURL($quote->quotnbr, intval($linenbr));
		}

		/**
		 * Sets up a common url function for getting documents request url, classes that have this trait
		 * will define generate_request_documentsURL(Order $quote)
		 * Not used as of 10/25/2017
		 * @param  Order  $quote Quote to derive Quote ID
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
			$urlconfig = DplusoConfigURLs::get_instance();
			return $urlconfig->get_quote_editURL($quote->quotnbr, $this->pageurl->getURL());
		}

		/**
		 * Returns URL to push quote to Order
		 * @param  Order  $quote Quotenbr
		 * @return string URL to Order Quote
		 */
		public function generate_orderquoteURL(Order $quote) {
			$urlconfig = DplusoConfigURLs::get_instance();
			return $urlconfig->get_quote_orderURL($quote->quotnbr);
		}


		/**
		 * Returns URL to view the print version
		 * @param  Order  $quote Uses Quotenbr
		 * @return string        Print Link URL
		 * @uses                 $this->generate_request_detailsURL($quote)
		 */
		public function generate_printURL(Order $quote) {
			$urlconfig = DplusoConfigURLs::get_instance();
			return $urlconfig->get_quote_request_printURL($quote->quotnbr);
		}

		/**
		 * Returns URL to view the print page version
		 * NOTE USED for PDFMaker
		 * @param  Order  $quote Uses Quotenbr
		 * @return string        Print Link URL
		 */
		public function generate_printpageURL(Order $quote) {
			$urlconfig = DplusoConfigURLs::get_instance();
			return $urlconfig->get_quote_printpageURL($quote->quotnbr, $forpdf = true);
		}

		/**
		 * Returns URL to send email
		 * @param  Order  $quote Uses Quotenbr
		 * @return string        Print Link URL
		 */
		public function generate_sendemailURL(Order $quote) {
			$urlconfig = DplusoConfigURLs::get_instance();
			return $urlconfig->get_email_quoteURL($quote->quotnbr, $this->sessionID);
		}

		/**
		 * Returns URL to load linked user actions
		 * @param  Order  $quote For quotelink
		 * @return string        URL to load linked useractions
		 */
		public function generate_linkeduseractionsURL(Order $quote) {
			$urlconfig = DplusoConfigURLs::get_instance();
			return $urlconfig->get_quote_linkedactionsURL($quote->quotnbr);
		}

		/**
		 * Returns the URL to view the detail
		 * @param  Order       $quote  For quotenbr
		 * @param  OrderDetail $detail For Linenbr
		 * @return string              View Detail URL
		 */
		public function generate_viewdetailURL(Order $quote, OrderDetail $detail) {
			$urlconfig = DplusoConfigURLs::get_instance();
			return $urlconfig->get_quote_view_detailURL($quote->quotnbr, $detail->linenbr);
		}

		/**
		 * Return String URL to orders redir to request order details
		 * This is here for the use of getting the Print link
		 * @param  Order  $quote Quote
		 * @return string
		 */
		public function generate_request_detailsURL(Order $quote) {
			$urlconfig = DplusoConfigURLs::get_instance();
			return $urlconfig->get_quote_request_detailsURL($quote->quotnbr);
		}

		/**
		 * Returns the URL to view / edit the detail
		 * @param  Order       $quote  for Quote Number
		 * @param  OrderDetail $detail for detail line Number
		 * @return string              URL to edit / view detail
		 */
		public function generate_vieweditdetailURL(Order $quote, OrderDetail $detail) {
			$urlconfig = DplusoConfigURLs::get_instance();
			return $urlconfig->get_quote_edit_detailURL($quote->quotnbr, $detail->linenbr);
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
	}
