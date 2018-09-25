<?php 
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
