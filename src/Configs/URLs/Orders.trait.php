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
	}
