<?php
	namespace Dplus\Dpluso\Configs;

	/**
	 * External Libraries
	 */
	use Purl\Url;

    /**
	 * Functions that provide URLS to Order Pages / Functions
	 */
	trait OrderURLsTraits {
		/**
		 * Returns the URL to the Sales Order Redirect for Requests
		 * @return string Sales Order Redirect
		 */
		public function get_salesorders_redirURL() {
			$url = new Url($this->paths->get_urlpath('orders'));
			$url->path->add('redir');
			return $url->getUrl();
		}

		/**
		 * Returns the URL to Request Sales Order Documents
		 * @return string Sales Order Redirect
		 */
		public function get_salesorders_documentsURL($ordn, $itemID = '') {
			$url = new Url($this->get_salesorders_redirURL());
			$url->query->set('action', 'get-order-documents');
			$url->query->set('ordn', $ordn);
			$url->query->set('itemdoc', $itemID);
			return $url->getUrl();
		}

		/**
		 * Returns URL to Request Edit Sales Order
		 * @param  string $ordn       Sales Order Number
		 * @param  string $originURL  URL to send to Request
		 * @return string             Reques Edit Sales Order URL
		 */
		public function get_order_editURL($ordn, $originURL) {
			$url = new Url($this->get_salesorders_redirURL());
			$url->query->set('action', 'get-order-edit');
			$url->query->set('ordn', $ordn);
			$url->query->set('orderorigin', $originURL);
			return $url->getUrl();
		}

		/**
		 * Returns URL to Request Release Sales Order
		 * @param  string $ordn       Sales Order Number
		 * @return string             Request Relase Sales Order URL
		 */
		public function get_order_releaseURL($ordn) {
			$url = new Url($this->get_salesorders_redirURL());
			$url->query->set('action', 'release-order');
			$url->query->set('ordn', $ordn);
			return $url->getUrl();
		}

		/**
		 * Returns URL to Request Order Details and View Print Page
		 * @param  string $ordn Sales Order Number
		 * @return string       Request Sales Order Details URL
		 */
		public function get_order_request_printURL($ordn) {
			$url = new Url($this->get_salesorders_redirURL());
			$url->query->set('action', 'get-order-print');
			$url->query->set('ordn', $ordn);
			return $url->getUrl();
		}

		/**
		 * Returns URL to view Sales Order Print Page
		 * NOTE USED FOR PDFMAKER
		 * @param  string $ordn        Sales Order Number
		 * @param  bool   $pdf         Is this for PDF Maker?
		 * @return string              Sales Order Print Page URL for PDFMaker
		 */
		public function get_order_printpageURL($ordn, $pdf = false) {
			$url = new Url($this->find('print_order'));
			$url->query->set('ordn', $ordn);

			if ($pdf) {
				$url->query->set('view', 'pdf');
			}
			return $url->getUrl();
		}

		/**
		 * Returns URL to Email Sales Order Page
		 * @param  string $ordn      Sales Order Number
		 * @param  string $sessionID Session Identifier
		 * @return string            Email Sales Order URL
		 */
		public function get_email_orderURL($ordn, $sessionID) {
			$url = new Url($this->find('sys_email_order'));
			$url->query->set('ordn', $ordn);
			$url->query->set('referenceID', $sessionID);
			return $url->getUrl();
		}

		/**
		 * Returns URL to UserActions Page that are linked to a Sales Order Number
		 * @param  string $ordn      Sales Order Number
		 * @return string            Sales Order Linked UserActions Page
		 */
		public function get_order_linkedactionsURL($ordn) {
			$url = new Url($this->find('activity_useractions'));
			$url->query->set('ordn', $ordn);
			return $url->getUrl();
		}

		/**
		 * Returns URL to view Detail Line
		 * @param  string $ordn      Sales Order Number
		 * @param  int    $linenbr   Line Number
		 * @return string            View Detail Line URL
		 */
		public function get_order_view_detailURL($ordn, int $linenbr) {
			$url = new Url($this->find('ajax_load_view-detail_order'));
			$url->query->set('ordn', $ordn);
			$url->query->set('line', $linenbr);
			return $url->getUrl();
		}

		/**
		 * Returns URL request Sales Order Details
		 * @param  string $ordn      Sales Order Number
		 * @return string            Sales Order Request Details URL
		 */
		public function get_order_request_detailsURL($ordn) {
			$url = new Url($this->get_salesorders_redirURL());
			$url->query->set('action', 'get-order-details');
			$url->query->set('ordn', $ordn);
			return $url->getUrl();
		}

		/**
		 * Returns URL to edit Detail Line
		 * @param  string $ordn      Sales Order Number
		 * @param  int    $linenbr   Line Number
		 * @return string            Edit Detail Line URL
		 */
		public function get_order_edit_detailURL($ordn, int $linenbr) {
			$url = new Url($this->find('ajax_load_edit-detail_order'));
			$url->query->set('ordn', $ordn);
			$url->query->set('line', $linenbr);
			return $url->getUrl();
		}

		/**
		 * Returns URL request Sales Order Tracking
		 * @param  string $ordn      Sales Order Number
		 * @return string            Sales Order Request Tracking URL
		 */
		public function get_order_request_trackingURL($ordn) {
			$url = new Url($this->get_salesorders_redirURL());
			$url->query->set('action', 'get-order-tracking');
			$url->query->set('ordn', $ordn);
			return $url->getUrl();
		}

		/**
		 * Returns URL to unlock Sales Order
		 * @param  string $ordn      Sales Order Number
		 * @return string            Unlock Sales Order URL
		 */
		public function get_order_unlockURL($ordn) {
			$url = new Url($this->get_salesorders_redirURL());
			$url->query->set('action', 'unlock-order');
			$url->query->set('ordn', $ordn);
			return $url->getUrl();
		}

		/**
		 * Returns URL to unlock Sales Order
		 * @param  string $ordn      Sales Order Number
		 * @param  int    $linenbr   Line Number
		 * @param  string $pageurl   Return Page URL
		 * @return string            Unlock Sales Order URL
		 */
		public function get_order_removedetailURL($ordn, int $linenbr, $pageurl) {
			$url = new Url($this->get_salesorders_redirURL());
			$url->query->set('action', 'remove-line-get');
			$url->query->set('ordn', $ordn);
			$url->query->set('linenbr', $linenbr);
			$url->query->set('page', $pageurl);
			return $url->getUrl();
		}

		/**
		 * Returns URL to Confirm Sales Order Page
		 * @param  string $ordn      Sales Order Number
		 * @return string            ConfirmSales Order URL
		 */
		public function get_order_confirmURL($ordn) {
			$url = new Url($this->find('confirm_order'));
			$url->query->set('ordn', $ordn);
			return $url->getUrl();
		}
	}
