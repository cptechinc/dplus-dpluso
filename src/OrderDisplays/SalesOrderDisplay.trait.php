<?php
	namespace Dplus\Dpluso\OrderDisplays;

	/**
	 * External Libraries
	 */
	use Purl\Url;

	/**
	 * Internal Libraries
	 */
	use Dplus\ProcessWire\DplusWire;
	use Dplus\Dpluso\Configs\DplusoConfigURLs;

	/**
	 * Use Statements for Model Classes which are non-namespaced
	 */
	use Order, OrderDetail;

	/**
	 * Traits that will be shared by Sales Order Displays like Displays or Panels
	 */
	trait SalesOrderDisplayTraits {

		/**
		 * Returns URL to request Dplus Notes
		 * @param  Order  $order   SalesOrder
		 * @param  int    $linenbr Line Number
		 * @return string          URL to request Dplus Notes
		 */
		public function generate_request_dplusnotesURL(Order $order, $linenbr = 0) {
			$urlconfig = DplusoConfigURLs::get_instance();
			return $urlconfig->get_request_order_dplusnotesURL($order->ordernumber, intval($linenbr));
		}

		/**
		 * Sets up a common url function for getting documents request url, classes that have this trait
		 * will define generate_documentsrequestURLtr(Order $order)
		 * @param  Order       $order        SalesOrder
		 * @param  OrderDetail $orderdetail  SalesOrderDetail
		 * @return string		             URL to the order redirect to make the get order documents request
		 */
		public function generate_request_documentsURL(Order $order, OrderDetail $orderdetail = null) {
			$urlconfig = DplusoConfigURLs::get_instance();

			if ($orderdetail) {
				return $urlconfig->get_salesorders_documentsURL($order->ordernumber, $orderdetail->itemid);
			} else {
				return $urlconfig->get_salesorders_documentsURL($order->ordernumber);
			}
		}

		/**
		 * Returns URL to Request Edit Order
		 * @param  Order  $order SalesOrder
		 * @return string        URL to edit order page
		 */
		public function generate_editURL(Order $order) {
			$urlconfig = DplusoConfigURLs::get_instance();
			return $urlconfig->get_order_editURL($order->ordernumber, $this->pageurl->getURL());
		}

		/**
		 * Returns URL to Request Release Order
		 * @param  Order  $order SalesOrder
		 * @return string        URL to edit order page
		 */
		public function generate_releaseurl(Order $order) {
			$urlconfig = DplusoConfigURLs::get_instance();
			return $urlconfig->get_order_releaseURL($order->ordernumber);
		}

		/**
		 * Returns URL to view print page for Sales Order
		 * @param  Order  $order SalesOrder
		 * @return string        URL to view print page
		 */
		public function generate_printURL(Order $order) {
			$urlconfig = DplusoConfigURLs::get_instance();
			return $urlconfig->get_order_request_printURL($order->ordernumber);
		}

		/**
		 * Returns URL to view print page for order
		 * NOTE USED for PDFMaker
		 * @param  Order  $order SalesOrder
		 * @return string        URL to view print page
		 */
		public function generate_printpageURL(Order $order) {
			$urlconfig = DplusoConfigURLs::get_instance();
			return $urlconfig->get_order_printpageURL($order->ordernumber, $pdf = true);
		}

		/**
		 * Returns URL to send email of this print page
		 * @param  Order  $order SalesOrder
		 * @return string        URL to email Order
		 */
		public function generate_sendemailURL(Order $order) {
			$urlconfig = DplusoConfigURLs::get_instance();
			return $urlconfig->get_email_orderURL($order->ordernumber, $this->sessionID);
		}

		/**
		 * Returns URL to load linked UserActions
		 * @param  Order  $order SalesOrder | Quote
		 * @return string        URL to load linked UserActions
		 */
		public function generate_linkeduseractionsURL(Order $order) {
			$urlconfig = DplusoConfigURLs::get_instance();
			return $urlconfig->get_order_linkedactionsURL($order->ordernumber);
		}

		/**
		 * Returns URL to view SalesOrderDetail
		 * @param  Order       $order  SalesOrder
		 * @param  OrderDetail $detail SalesOrderDetail
		 * @return string              URL view detail
		 */
		public function generate_viewdetailURL(Order $order, OrderDetail $detail) {
			$urlconfig = DplusoConfigURLs::get_instance();
			return $urlconfig->get_order_view_detailURL($order->ordernumber, $detail->linenbr);
		}

		/**
		 * Returns URL to load detail lines for Sales Order
		 * @param  Order  $order SalesOrder
		 * @return string        URL to load detail lines for Sales Order
		 */
		public function generate_request_detailsURL(Order $order) {
			$urlconfig = DplusoConfigURLs::get_instance();
			return $urlconfig->get_order_request_detailsURL($order->ordernumber);
		}

		/**
		 * Returns the URL to load the edit/view detail URL
		 * Checks if we are editing Sales Order to show edit functions
		 * @param  Order       $order  SalesOrder
		 * @param  OrderDetail $detail SalesOrderDetail
		 * @return string              URL to load the edit/view detail URL
		 * @uses $order->can_edit()
		 */
		public function generate_vieweditdetailURL(Order $order, OrderDetail $detail) {
			$urlconfig = DplusoConfigURLs::get_instance();
			return $urlconfig->get_order_edit_detailURL($order->ordernumber, $detail->linenbr);
		}

		/* =============================================================
			SalesOrderDisplayInterface Functions
		============================================================ */

		/**
		 * Sets up a common url function for getting d request url, classes that have this trait
		 * @param  Order  $order Sales Order
		 * @return string        URL to the order redirect to make the get order documents request
		 */
		public function generate_request_trackingURL(Order $order) {
			$urlconfig = DplusoConfigURLs::get_instance();
			return $urlconfig->get_order_request_trackingURL($order->ordernumber);
		}

		/**
		 * Returns Sales Order Details
		 * @param  Order  $order SalesOrder
		 * @param  bool   $debug Whether to execute query and return Sales Order Details
		 * @return array        SalesOrderDetails Array | SQL Query
		 */
		public function get_orderdetails(Order $order, $debug = false) {
			return get_orderdetails($this->sessionID, $order->ordernumber, true, $debug);
		}
	}
