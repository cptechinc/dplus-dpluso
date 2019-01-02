<?php
	namespace Dplus\Dpluso\OrderDisplays;
	
	use Purl\Url;
	use Dplus\ProcessWire\DplusWire;
	use Dplus\Content\HTMLWriter;

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
		public function generate_unlockurl(Order $quote) {
			$url = $this->generate_quotesredirurl();
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
		 * Returns HTML to discard Quote changes
		 * @param  Order  $quote Quote
		 * @return string        HTML to discard Quote changes
		 */
		public function generate_discardchangeslink(Order $quote) {
			$bootstrap = new HTMLWriter();
			$href = $this->generate_unlockurl($quote);
			$icon = $bootstrap->icon('fa fa-times');
			return $bootstrap->a("href=$href|class=btn btn-block btn-warning", $icon. " Discard Changes, Unlock Quote");
		}

		/**
		 * Returns HTML Link to delete detail line
		 * @param  Order       $quote  Quote
		 * @param  OrderDetail $detail QuoteDetail
		 * @return string              HTML Link to delete detail line
		 */
		public function generate_deletedetaillink(Order $quote, OrderDetail $detail) {
			$bootstrap = new HTMLWriter();
			$icon = $bootstrap->icon('fa fa-trash-o') . $bootstrap->span('class=sr-only', 'Delete Line');
			$url = $this->generate_quotesredirurl();
			$url->query->setData(array('action' => 'remove-line-get', 'qnbr' => $quote->quotnbr, 'linenbr' => $detail->linenbr, 'page' => $this->pageurl->getUrl()));
			$href = $url->getUrl();
			return $bootstrap->a("href=$href|class=btn btn-sm btn-danger|title=Delete Line", $icon);
		}

		/**
		 * Returns HTML bootstrap alert div that this Quote is will be in read only mode
		 * @return string HTML bootstrap alert div that this Quote is will be in read only mode
		 */
		public function generate_readonlyalert() {
			$bootstrap = new HTMLWriter();
			$msg = $bootstrap->b('', 'Attention!') . ' This order will open in read-only mode, you will not be able to save changes.';
			return $bootstrap->alertpanel('warning', $msg);
		}

		/**
		 * Returns HTML bootstrap alert for an error
		 * @param  Quote  $quote Quote
		 * @return string        HTML bootstrap alert for an error
		 */
		public function generate_erroralert(Quote $quote) {
			$bootstrap = new HTMLWriter();
			$msg = $bootstrap->b('', 'Error!') . $quote->errormsg;
			return $bootstrap->alertpanel('danger', $msg, false);
		}
	}
