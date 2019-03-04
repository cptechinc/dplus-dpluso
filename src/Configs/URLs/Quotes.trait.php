<?php
	namespace Dplus\Dpluso\Configs;

	/**
	 * External Libraries
	 */
	use Purl\Url;
	
    /**
	 * Functions that provide URLS to Order Pages / Functions
	 */
	trait QuoteURLsTraits {
		/**
		 * Returns the URL to the Sales Order Redirect for Requests
		 * @return string Sales Order Redirect
		 */
		public function get_quotes_redirURL() {
			$url = new Url($this->paths->get_urlpath('quotes'));
			$url->path->add('redir');
			return $url->getUrl();
		}
	}