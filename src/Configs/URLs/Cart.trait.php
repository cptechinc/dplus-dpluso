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
		 * Returns the URL for setting the Cart Customer
		 * @param  string $custID    Customer ID
		 * @param  string $shiptoID  Shipto ID
		 * @return string            Set Cart Customer
		 */
		public function get_setcartcustomerURL($custID, $shiptoID = '') {
			$url = new Url($this->paths->get_urlpath('cart'));
			$url->path->add('redir');
			$url->query->set('action', 'shop-as-customer');
			$url->query->set('custID', $custID);

			if (!empty($shiptoID)) {
				$url->query->set('shipID', $shiptoID);
			}
			return $url->getUrl();
		}
	}