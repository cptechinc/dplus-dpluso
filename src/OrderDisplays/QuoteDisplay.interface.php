<?php 
	namespace Dplus\Dpluso\OrderDisplays;
	
	/**
	 * Use Statements for Model Classes which are non-namespaced
	 */
	use Order;
	
	/**
	 * Functions QuoteDisplay classes have to implement
	 */
    interface QuoteDisplayInterface {
		/**
		 * Returns Quote Details
		 * @param  Order  $quote Quote $quote
		 * @param  bool   $debug Whether or not to execute Query
		 * @return array         array of QuoteDetail | SQL Query
		 * @uses
		 */
        public function get_quotedetails(Order $quote, $debug = false);
    }
