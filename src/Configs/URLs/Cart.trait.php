<?php
	namespace Dplus\Dpluso\Configs;

	/**
	 * External Libraries
	 */
	use Purl\Url;

    /**
	 * Functions that provide URLS to Cart Pages / Functions
	 */
	trait CartURLsTraits {
		/**
		 * Returns URL PATH to the Cart Redir Page
		 * @return string
		 */
		public function get_cart_redirURL() {
			$url = new Url($this->paths->get_urlpath('cart'));
			$url->path->add('redir');
			return $url->getUrl();
		}

		/**
		 * Returns the URL for setting the Cart Customer
		 * @param  string $custID    Customer ID
		 * @param  string $shiptoID  Shipto ID
		 * @return string            Set Cart Customer
		 */
		public function get_setcartcustomerURL($custID, $shiptoID = '') {
			$url = new Url($this->get_cart_redirURL());
			$url->query->set('action', 'shop-as-customer');
			$url->query->set('custID', $custID);

			if (!empty($shiptoID)) {
				$url->query->set('shipID', $shiptoID);
			}
			return $url->getUrl();
		}

		/**
		 * Returns the URL to the Edit Cart Detail Page for a Detail Line
		 * @param  int    $linenbr
		 * @return string        URL for Editing Cart Detail Line
		 */
		public function get_cart_viewedit_detailURL(int $linenbr) {
			$url = new Url($this->paths->get_urlpath('ajax_load'));
			$url->path->add('edit-detail');
			$url->path->add('cart');
			$url->query->set('line', $linenbr);
			return $url->getUrl();
		}

		/**
		 * Returns the URL to the View Cart Detail Page for a Detail Line
		 * @param  int    $linenbr
		 * @return string        URL for View Cart Detail Line
		 */
		public function get_cart_view_detailURL(int $linenbr) {
			$url = new Url($this->paths->get_urlpath('ajax_load'));
			$url->path->add('view-detail');
			$url->path->add('cart');
			$url->query->set('line', $linenbr);
			return $url->getUrl();
		}

		/**
		 * Returns the URL to the Remove detail Line Page
		 * @param  int    $linenbr
		 * @return string          URL to remove Cart Detail
		 */
		public function get_cart_remove_detailURL(int $linenbr) {
			$url = new Url($this->get_cart_redirURL());
			$url->query->set('action', 'remove-line');
			$url->query->set('line', $linenbr);
			return $url->getUrl();
		}

		/**
		 * Returns the URL to the Empty Cart
		 * @return string          URL to Empty cart
		 */
		public function get_cart_emptyURL() {
			$url = new Url($this->get_cart_redirURL());
			$url->query->set('action', 'empty-cart');
			return $url->getUrl();
		}

		/**
		 * Returns the URL to the create sales order
		 * @return string          URL to create sales order
		 */
		public function get_cart_create_salesorderURL() {
			$url = new Url($this->get_cart_redirURL());
			$url->query->set('action', 'create-sales-order');
			return $url->getUrl();
		}

		/**
		 * Returns the URL to the create quote
		 * @return string          URL to create quote
		 */
		public function get_cart_create_quoteURL() {
			$url = new Url($this->get_cart_redirURL());
			$url->query->set('action', 'create-quote');
			return $url->getUrl();
		}

		/**
		 * Returns the URL to the reorder items URL
		 * @return string          URL to reorder items URL
		 */
		public function get_cart_reorderURL() {
			$url = new Url($this->get_cart_redirURL());
			$url->query->set('action', 'reorder-items');
			return $url->getUrl();
		}
	}
