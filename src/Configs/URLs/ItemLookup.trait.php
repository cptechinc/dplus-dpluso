<?php
    namespace Dplus\Dpluso\Configs;

	/**
	 * External Libraries
	 */
    use Purl\Url;
    
    trait ItemLookupURLsTraits {
        //////////////////////////
        // Base
        //////////////////////////
        /**
         * Returns URL Path to the Item Search Results Location
         * @return string Item Search Results URL
         */
		function get_itemlookup_base_resultsURL() {
			$url = new Url($this->paths->get_urlpath('ajax_load'));
			$url->path->add('products');
			$url->path->add('item-search-results');
			return $url->getUrl();
        }
        
         /**
         * Returns URL Path to the Non Stock form Location
         * @return string Non Stock Form URL
         */
        function get_itemlookup_base_nonstockformURL() {
			$url = new Url($this->paths->get_urlpath('ajax_load'));
			$url->path->add('products');
			$url->path->add('non-stock');
			return $url->getUrl();
        }
        
         /**
         * Returns URL Path to the Add Multiple form Location
         * @return string Add Multiple Form URL
         */
        function get_itemlookup_base_addmultipleURL() {
			$url = new Url($this->paths->get_urlpath('ajax_load'));
			$url->path->add('products');
			$url->path->add('add-detail');
			return $url->getUrl();
        }
        
        //////////////////////////
        // Cart
        //////////////////////////

        /**
         * Returns URL Path to the Item Search Results Location for the Cart
         * @param  string $custID   Customer ID
         * @param  string $shiptoID Customer Shipto ID
         * @return string           Cart Item Search Results
         */
		function get_itemlookup_cart_resultsURL($custID, $shiptoID = '') {
			$url = new Url($this->get_itemlookup_base_resultsURL());
			$url->path->add('cart');
			$url->query->set('custID', $custID)->set('shipID', $shiptoID);
			return $url->getUrl();
		}
		
        /**
         * Returns URL Path to the Non Stock form Location for the Cart
         * @param  string $custID   Customer ID
         * @param  string $shiptoID Customer Shipto ID
         * @return string           Cart non Stock Form
         */
		function get_itemlookup_cart_nonstockformURL($custID, $shiptoID = '') {
			$url = new Url($this->get_itemlookup_base_nonstockformURL());
			$url->path->add('cart');
			$url->query->set('custID', $custID)->set('shipID', $shiptoID);
			return $url->getUrl();
		}

        /**
         * Returns URL Path to the Add Multiple form Location for the Cart
         * @param  string $custID   Customer ID
         * @param  string $shiptoID Customer Shipto ID
         * @return string           Cart Add Multiple Form
         */
		function get_itemlookup_cart_addmultipleURL($custID, $shiptoID = '') {
			$url = new Url($this->get_itemlookup_base_addmultipleURL());
			$url->query->set('custID', $custID)->set('shipID', $shiptoID);
			return $url->getUrl();
		}

        //////////////////////////
        // Sales Order
        //////////////////////////
        /**
         * Returns URL Path to the Item Search Results Location for the Sales Order
         * @param  string $custID   Customer ID
         * @param  string $shiptoID Customer Shipto ID
         * @param  string $ordn     Sales Order Number
         * @return string           Sales Order Item Search Results URL
         */
		function get_itemlookup_order_resultsURL($custID, $shiptoID = '', $ordn) {
			$url = new Url($this->get_itemlookup_base_resultsURL());
			$url->path->add('order');
			$url->query->set('custID', $custID)->set('shipID', $shiptoID)->set('ordn', $ordn);
			return $url->getUrl();
		}

        /**
         * Returns URL Path to the Non Stock form Location for the Sales Order
         * @param  string $custID   Customer ID
         * @param  string $shiptoID Customer Shipto ID
         * @param  string $ordn     Sales Order Number
         * @return string           Sales Order Non Stock Form URL
         */
		function get_itemlookup_order_nonstockformURL($custID, $shiptoID = '', $ordn) {
			$url = new Url($this->get_itemlookup_base_nonstockformURL());
			$url->path->add('order');
			$url->query->set('custID', $custID)->set('shipID', $shiptoID)->set('ordn', $ordn);
			return $url->getUrl();
		}

        /**
         * Returns URL Path to the Add Multiple form Location for the Sales Order
         * @param  string $custID   Customer ID
         * @param  string $shiptoID Customer Shipto ID
         * @param  string $ordn     Sales Order Number
         * @return string           Sales Order Add Multiple form URL
         */
		function get_itemlookup_order_addmultipleURL($custID, $shiptoID = '', $ordn) {
			$url = new Url($this->get_itemlookup_base_addmultipleURL());
			$url->path->add('order');
			$url->query->set('custID', $custID)->set('shipID', $shiptoID)->set('ordn', $ordn);
			return $url->getUrl();
		}

        /**
         * Returns URL Path to the Item Search Results Location for the Quote
         * @param  string $custID   Customer ID
         * @param  string $shiptoID Customer Shipto ID
         * @param  string $qnbr     Quote Number
         * @return string           Quote Item Search Results URL
         */
		function get_itemlookup_quote_resultsURL($custID, $shiptoID = '', $qnbr) {
			$url = new Url($this->get_itemlookup_base_resultsURL());
			$url->path->add('quote');
			$url->query->set('custID', $custID)->set('shipID', $shiptoID)->set('qnbr', $qnbr);
			return $url->getUrl();
		}

        /**
         * Returns URL Path to the Non Stock Form Location for the Quote
         * @param  string $custID   Customer ID
         * @param  string $shiptoID Customer Shipto ID
         * @param  string $qnbr     Quote Number
         * @return string           Quote Non Stock Form URL
         */
		function get_itemlookup_quote_nonstockformURL($custID, $shiptoID = '', $qnbr) {
			$url = new Url($this->get_itemlookup_base_nonstockformURL());
			$url->path->add('quote');
			$url->query->set('custID', $custID)->set('shipID', $shiptoID)->set('qnbr', $qnbr);
			return $url->getUrl();
        }
        
        /**
         * Returns URL Path to the Add Multiple Form Location for the Quote
         * @param  string $custID   Customer ID
         * @param  string $shiptoID Customer Shipto ID
         * @param  string $qnbr     Quote Number
         * @return string           Quote Add Multiple Form URL
         */
		function get_itemlookup_quote_addmultipleURL($custID, $shiptoID = '', $qnbr) {
			$url = new Url($this->get_itemlookup_base_addmultipleURL());
			$url->path->add('quote');
			$url->query->set('custID', $custID)->set('shipID', $shiptoID)->set('qnbr', $qnbr);
			return $url->getUrl();
		}
	}