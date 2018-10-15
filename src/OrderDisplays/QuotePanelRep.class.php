<?php 	
	class RepQuotePanel extends QuotePanel {
		
		public function __construct($sessionID, \Purl\Url $pageurl, $modal, $loadinto, $ajax) {
			parent::__construct($sessionID, $pageurl, $modal, $loadinto, $ajax);
			$this->pageurl = new Purl\Url($pageurl->getUrl());
			$this->setup_pageurl();
		}
		
		public function setup_pageurl() {
			parent::setup_pageurl();
			$this->pageurl->path->add('salesrep');
			$this->paginationinsertafter = 'salesrep';
		}
		
		/* =============================================================
			QuotePanelInterface Functions
			LINKS ARE HTML LINKS, AND URLS ARE THE URLS THAT THE HREF VALUE
		============================================================ */
		public function get_quotecount($debug = false) {
			return parent::get_quotecount($debug);
		}
		
		public function get_quotes($debug = false) {
			return parent::get_quotes($debug);
		}
	}
