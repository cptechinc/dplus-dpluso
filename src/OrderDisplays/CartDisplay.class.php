<?php
	namespace Dplus\Dpluso\OrderDisplays;

	use Purl\Url;
	use Dplus\ProcessWire\DplusWire;
	use Dplus\Dpluso\Configs\DplusoConfigURLs;

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
		 * Returns URL for dplus notes for that Line #
		 * @param  Order  $cart    CartQuote
		 * @param  int    $linenbr Line #
		 * @return string          URL to load Dplus Notes
		 * @uses
		 */
		public function generate_request_dplusnotesURL(Order $cart, $linenbr) {
			$urlconfig = DplusoConfigURLs::get_instance();
			return $urlconfig->get_request_cart_dplusnotesURL($linenbr);
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
			$urlconfig = DplusoConfigURLs::get_instance();
			return $urlconfig->get_cart_viewedit_detailURL($detail->linenbr);
		}

		/**
		 * Returns URL to remove detail
		 * @param  Order       $cart   CartQuote
		 * @param  OrderDetail $detail CartDetail
		 * @return string              URL to load edit detail
		 * @uses
		 */
		public function generate_removedetailURL(Order $cart, OrderDetail $detail) {
			$urlconfig = DplusoConfigURLs::get_instance();
			return $urlconfig->get_cart_remove_detailURL($detail->linenbr);
		}
	}
