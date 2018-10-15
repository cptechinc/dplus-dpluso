<?php
	use Dplus\ProcessWire\DplusWire as DplusWire;
	
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
		 * Returns the link for loading cart detail notes
		 * @param  int     $linenbr Line #
		 * @return string           HTML link
		 */
		public function generate_loaddplusnoteslinkdetail($linenbr) {
			$bootstrap = new Dplus\Content\HTMLWriter();
			$href = $this->generate_dplusnotesrequesturl($this->cart, $linenbr);
			$detail = CartDetail::load($this->sessionID, $linenbr);
			$title = ($detail->has_notes()) ? "View and Create Quote Notes" : "Create Quote Notes";
			$addclass = ($detail->has_notes()) ? '' : 'text-muted';
			$content = $bootstrap->icon('material-icons md-36', '&#xE0B9;');
			$link = $bootstrap->create_element('a', "href=$href|class=load-notes $addclass|title=$title|data-modal=$this->modal", $content);
			return $link;
		}

		/**
		 * Returns the link for loading cart header notes
		 * @param  int    $linenbr Line #
		 * @return string          HTML link
		 */
		public function generate_loaddplusnoteslinkheader($linenbr = '0') {
			$bootstrap = new Dplus\Content\HTMLWriter();
			$href = $this->generate_dplusnotesrequesturl($this->cart, $linenbr);
			$has_notes = has_dplusnote($this->sessionID, $this->sessionID, '0', Qnote::get_qnotetype('cart')) == 'Y' ? true : false;
			$title = ($has_notes) ? "View and Create Quote Notes" : "Create Quote Notes";
			$addclass = ($has_notes) ? '' : 'text-muted';
			$content = $bootstrap->icon('material-icons md-36', '&#xE0B9;');
			$link = $bootstrap->create_element('a', "href=$href|class=load-notes $addclass|title=$title|data-modal=$this->modal", $content);
			return $link;
		}

		/**
		 * Generates dplus link depending on the Line #
		 * @param  Order  $cart    CartQuote
		 * @param  string $linenbr Line #
		 * @return string          HTML Link
		 * @uses
		 */
		public function generate_loaddplusnoteslink(Order $cart, $linenbr = '0') {
			return intval($linenbr) ? $this->generate_loaddplusnoteslinkdetail($linenbr) : $this->generate_loaddplusnoteslinkheader($linenbr);
		}

		/**
		 * Returns URL for dplus notes for that Line #
		 * @param  Order  $cart    CartQuote
		 * @param  int    $linenbr Line #
		 * @return string          URL to load Dplus Notes
		 * @uses
		 */
		public function generate_dplusnotesrequesturl(Order $cart, $linenbr) {
			$url = new \Purl\Url($this->pageurl->getUrl());
			$url->path = DplusWire::wire('config')->pages->notes."redir/";
			$url->query->setData(array('action' => 'get-cart-notes', 'linenbr' => $linenbr));
			return $url->getUrl();
		}

		/**
		 * Is not implemented yet
		 * @param  Order       $cart   CartQuote
		 * @param  OrderDetail $detail CartDetail
		 * @return void        Isn't implemented yet
		 */
		public function generate_loaddocumentslink(Order $cart, OrderDetail $detail = null) {
			// TODO
		}

		/**
		 * Is not implemented yet
		 * @param  Order       $cart   CartQuote
		 * @param  mixed $detail CartDetail
		 * @return void        Isn't implemented yet
		 */
		public function generate_documentsrequesturl(Order $cart, OrderDetail $detail = null) {
			// TODO
		}

		/**
		 * Returns HTML link to edit line
		 * @param  Order       $cart   CartQuote
		 * @param  OrderDetail $detail CartDetail
		 * @return string              HTML Link
		 */
		public function generate_detailvieweditlink(Order $cart, OrderDetail $detail) {
			$bootstrap = new Dplus\Content\HTMLWriter();
			$href = $this->generate_detailviewediturl($cart, $detail);
			$icon = $bootstrap->create_element('button', 'class=btn btn-sm btn-warning detail-line-icon', $bootstrap->icon('glyphicon glyphicon-pencil'));
			return $bootstrap->create_element('a', "href=$href|class=update-line|data-kit=$detail->kititemflag|data-itemid=$detail->itemid|data-custid=$cart->custid|aria-label=View Detail Line", $icon);
		}

		/**
		 * Returns URL to load edit detail
		 * @param  Order       $cart   CartQuote
		 * @param  OrderDetail $detail CartDetail
		 * @return string              URL to load edit detail
		 */
		public function generate_detailviewediturl(Order $cart, OrderDetail $detail) {
			$url = new \Purl\Url($this->pageurl->getUrl());
			$url->path = DplusWire::wire('config')->pages->ajax."load/edit-detail/cart/";
			$url->query->setData(array('line' => $detail->linenbr));
			return $url->getUrl();
		}

		/**
		 * Returns URL to remove detail
		 * @param  Order       $cart   CartQuote
		 * @param  OrderDetail $detail CartDetail
		 * @return string              URL to load edit detail
		 * @uses
		 */
		public function generate_detaildeleteurl(Order $cart, OrderDetail $detail) {
			$url = new \Purl\Url($this->pageurl->getUrl());
			$url->path = Dpluswire::wire('config')->pages->cart."redir/";
			$url->query->setData(array('action' => 'remove-line', 'line' => $detail->linenbr));
			return $url->getUrl();
		}
	}
