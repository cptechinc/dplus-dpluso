<?php
	namespace Dplus\Dpluso\Configs;

	/**
	 * External Libraries
	 */
	use Purl\Url;

    /**
	 * Functions that provide URLs to Booking Functions / Pages
	 */
	trait BookingsURLsTraits {
		/**
		 * Returns the URL for Loading bookings through Ajax
		 * @return string Bookings ajax URL
		 */
		public function get_bookings_ajaxURL() {
			$url = new Url($this->paths->get_urlpath('ajax_load'));
			$url->path->add('bookings');
			return $url->getUrl();
		}

		/**
		 * Returns the Sales Orders Bookings Ajax URL
		 * @return string Sales Order bookings Ajax URL
		 */
		public function get_bookings_orders_ajaxURL() {
			$url = new Url($this->get_bookings_ajaxURL());
			$url->path->add('sales-orders');
			return $url->getUrl();
		}

		/**
		 * Returns the URL to view the date provided's bookings
		 * @param  string $date     Date to view Orders for
		 * @param  string $custID   Customer ID
		 * @param  string $shiptoID Customer Shipto ID
		 *
		 * @return string           URL to view the date's booked orders
		 */
		public function get_bookings_salesorders_bydayURL($date, $custID = '', $shiptoID = '') {
			$url = new Url($this->get_bookings_orders_ajaxURL());
			$url->query->set('date', $date);

			if (!empty($custID)) {
				$url->query->set('custID', $custID);
				if (!empty($shiptoID)) {
					$url->query->set('shipID', $shiptoID);
				}
			}

			return $url->getUrl();
		}

		/**
		 * Returns URL to view the bookingsfor a sales order on a particular date
		 * @param  string $ordn Sales Order #
		 * @param  string $date Date
		 * @param  string $custID   Customer ID
		 * @param  string $shiptoID Customer Shipto ID
		 * @return string       URL to view bookings for that order # and date
		 */
		public function get_bookings_day_salesorderURL($ordn, $date, $custID = '', $shiptoID = '') {
			$url = new Url($this->get_bookings_ajaxURL());
			$url->path->add('sales-order');
			$url->query->set('ordn', $ordn);
			$url->query->set('date', $date);
			if (!empty($custID)) {
				$url->query->set('custID', $custID);
				if (!empty($shiptoID)) {
					$url->query->set('shipID', $shiptoID);
				}
			}
			return $url->getUrl();
		}

		/**
		 * Returns URL to Bookings Page with a filter between Book Dates
		 * @param  string $start Start Date m/d/Y
		 * @param  string $end   End Date m/d/Y
		 * @return string        URL to Bookings
		 */
		public function get_bookings_filter_bookdatesURL($start, $end) {
			$url = new Url($this->get_bookings_ajaxURL());
			$url->query->set('filter', 'filter');
			$url->query->set('bookdate', "$start|$end");
			return $url->getUrl();
		}
	 }