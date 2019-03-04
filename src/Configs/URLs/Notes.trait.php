<?php
	namespace Dplus\Dpluso\Configs;
	
	/**
	 * External Libraries
	 */
	use Purl\Url;
	
    /**
	 * Functions that provide URLs to Notes Functions / Pages
	 */
	trait NoteURLsTrait {
		/**
		 * Returns URL PATH to the Notes Redir Page
		 * @return string
		 */
		public function get_notes_redirURL() {
			$url = new Url($this->paths->get_urlpath('notes'));
			$url->path->add('redir');
			return $url->getUrl();
		}
			

		/**
		 * Returns URL to request the Qnotes for a Cart Detail Line
		 * @param  int    $linenbr
		 * @return string          URL for Requesting Qnotes for a Cart Detail Line
		 */
		public function get_request_cart_dplusnotesURL(int $linenbr) {
			$url = new Url($this->get_notes_redirURL());
			$url->query->set('action', 'get-cart-notes');
			$url->query->set('linenbr', $linenbr);
			return $url->getUrl();
		}
	}