<?php
	namespace Dplus\Dpluso\OrderDisplays;
	
	use Dplus\ProcessWire\DplusWire;
	
	/**
	 * Use Statements for Model Classes which are non-namespaced
	 */
	use Order, OrderDetail;
	
	class CustomerQuotePanel extends QuotePanel implements OrderPanelCustomerInterface {
		use OrderPanelCustomerTraits;
		
		protected $quotes = array();
		protected $filterable = array(
			'quotnbr' => array(
				'querytype' => 'between',
				'datatype' => 'char',
				'label' => 'Quote #'
			),
			'custid' => array(
				'querytype' => 'between',
				'datatype' => 'char',
				'label' => 'CustID'
			),
			'shiptoid' => array(
				'querytype' => 'in',
				'datatype' => 'char',
				'label' => 'ShiptoID'
			),
			'quotdate' => array(
				'querytype' => 'between',
				'datatype' => 'date',
				'label' => 'Quote Date'
			),
			'revdate' => array(
				'querytype' => 'between',
				'datatype' => 'date',
				'label' => 'Review Date'
			),
			'expdate' => array(
				'querytype' => 'between',
				'datatype' => 'date',
				'label' => 'Expire Date'
			),
			'subtotal' => array(
				'querytype' => 'between',
				'datatype' => 'numeric',
				'label' => 'Order Total'
			),
			'salesperson' => array(
				'querytype' => 'in',
				'datatype' => 'char',
				'label' => 'Sales Rep'
			)
		);
		
		/* =============================================================
			OrderPanelInterface Functions
			LINKS ARE HTML LINKS, AND URLS ARE THE URLS THAT THE HREF VALUE
		============================================================ */
		public function generate_loadurl() { 
			$url = new \Purl\Url(parent::generate_loadurl());
			$url->query->set('action', 'load-cust-quotes');
			$url->query->set('custID', $this->custID);
			if (!empty($this->shipID)) {
				$url->query->set('shipID', $this->shipID);
			}
			return $url->getUrl();
		}
		
		public function generate_loaddetailsurl(Order $quote) {
			$url = new \Purl\Url(parent::generate_loaddetailsurl($quote));
			$url->query->set('custID', $quote->custid);
			if (!empty($this->shipID)) {
				$url->query->set('shipID', $this->shipID);
			}
			return $url->getUrl();
		}
		
		public function generate_lastloadeddescription() {
			if (DplusWire::wire('session')->{'quotes-loaded-for'}) {
				if (DplusWire::wire('session')->{'quotes-loaded-for'} == $this->custID) {
					return 'Last Updated : ' . DplusWire::wire('session')->{'quotes-updated'};
				}
				return '';
			}
			return '';
		}
		
		public function generate_filter(\ProcessWire\WireInput $input) {
			parent::generate_filter($input);
			$this->filters['custid'][] = $this->custID;
			
			if (!empty($this->shipID)) {
				$this->filters['shiptoid'][] = $this->shipID;
			}
			
			if (isset($this->filters['subtotal'])) {
				if (!strlen($this->filters['subtotal'][1])) {
					$this->filters['subtotal'][1] = get_maxquotetotal($this->sessionID, $this->custID);
				}
			}
		}
		
		/* =============================================================
			OrderDisplayInterface Functions
			LINKS ARE HTML LINKS, AND URLS ARE THE URLS THAT THE HREF VALUE
		============================================================ */
		public function generate_editlink(Order $quote) {
			return $quote->can_Edit() ? parent::generate_editlink($quote) : '';
		}
		
		public function generate_documentsrequesturl(Order $quote, OrderDetail $quotedetail = null) {
			$url = new \Purl\Url(parent::generate_documentsrequesturl($quote, $quotedetail));
			$url->query->set('custID', $this->custID);
			return $url->getUrl();
		}
	}
