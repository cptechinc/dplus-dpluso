<?php
	namespace Dplus\Dpluso\OrderDisplays;
	
	use Purl\Url;
	use Dplus\ProcessWire\DplusWire;
	use Dplus\Content\HTMLWriter;

	/**
	 * Use Statements for Model Classes which are non-namespaced
	 */
	use CartQuote, CartDetail;
	use Order, OrderDetail;
	use Qnote;


	/**
	 * Class that handles aspects of the display of the Carthead
	 */
	class CartDisplay extends OrderDisplay {

		/**
		 * Carthead, from carthed
		 * @var CartQuote
		 */
		protected $cart;

		/* =============================================================
			Class Functions
		============================================================ */
		/**
		 * Loads the CartQuote from carthed table
		 * @param  bool       $debug Whether to return CartQuote or SQL Query
		 * @return CartQuote  or SQL Query
		 */
		public function get_cartquote($debug = false) {
			return $this->cart = CartQuote::load($this->sessionID, $debug);
		}

		/**
		 * // TODO rename for URL()
		 * Returns URL for dplus notes for that Line #
		 * @param  Order  $cart    CartQuote
		 * @param  int    $linenbr Line #
		 * @return string          URL to load Dplus Notes
		 * @uses
		 */
		public function generate_request_dplusnotesURL(Order $cart, $linenbr) {
			$url = new Url($this->pageurl->getUrl());
			$url->path = DplusWire::wire('config')->pages->notes."redir/";
			$url->query->setData(array('action' => 'get-cart-notes', 'linenbr' => $linenbr));
			return $url->getUrl();
		}

		/**
		 * Is not implemented yet
		 * @param  Order $cart   CartQuote
		 * @param  mixed $detail CartDetail
		 * @return void          Isn't implemented yet
		 */
		public function generate_request_documentsURL(Order $cart, OrderDetail $detail = null) {
			// TODO
		}

		/**
		 * Returns URL to load edit detail
		 * @param  Order       $cart   CartQuote
		 * @param  OrderDetail $detail CartDetail
		 * @return string              URL to load edit detail
		 */
		public function generate_vieweditdetailURL(Order $cart, OrderDetail $detail) {
			$url = new Url($this->pageurl->getUrl());
			$url->path = DplusWire::wire('config')->pages->ajax."load/edit-detail/cart/";
			$url->query->setData(array('line' => $detail->linenbr));
			return $url->getUrl();
		}

		/**
		 * // TODO rename for URL()
		 * Returns URL to remove detail
		 * @param  Order       $cart   CartQuote
		 * @param  OrderDetail $detail CartDetail
		 * @return string              URL to load edit detail
		 * @uses
		 */
		public function generate_detaildeleteurl(Order $cart, OrderDetail $detail) {
			$url = new Url($this->pageurl->getUrl());
			$url->path = DplusWire::wire('config')->pages->cart."redir/";
			$url->query->setData(array('action' => 'remove-line', 'line' => $detail->linenbr));
			return $url->getUrl();
		}
	}
