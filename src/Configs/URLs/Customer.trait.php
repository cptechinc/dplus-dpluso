<?php
    /**
	 * Functions that provide URLs to Customer Functions / Pages
	 */
	trait CustomerURLsTraits {
		public function get_customer_redirURL() {
			$url = new Url($this->paths->get_urlpath('customer'));
			$url->path->add('redir');
			return $url->getUrl();
		}

		/**
		 * Returns the URL for CI
		 * @param  string $custID   Customer ID
		 * @param  string $shiptoID Shipto ID
		 * @return string           URL PATH
		 */
		public function get_ciURL($custID, $shiptoID = '') {
			$url = new Url($this->get_customer_redirURL());
			$url->query->set('action', 'ci-customer');
			$url->query->set('custID', $custID);

			if (!empty($shiptoID)) {
				$url->query->set('shipID', $shiptoID);
			}
			return $url->getUrl();
		}

		/**
		 * Returns the URL for Customer Contact Page
		 * @param  string $custID    Customer ID
		 * @param  string $shiptoID  Shipto ID
		 * @param  string $contactID Contact ID
		 * @return string            Customer Contact Page URL
		 */
		public function get_customer_contactURL($custID, $shiptoID = '', $contactID) {
			$url = new Url($this->paths->get_urlpath('customer_contact'));
			$url->query->set('custID', $custID);

			if (!empty($shiptoID)) {
				$url->query->set('shipID', $shiptoID);
			}
			$url->query->set('contactID', $contactID);
			return $url->getUrl();
		}

		/**
		 * Returns the URL for Editing  Customer Contact Page
		 * @param  string $custID    Customer ID
		 * @param  string $shiptoID  Shipto ID
		 * @param  string $contactID Contact ID
		 * @return string            Edit Customer Contact Page URL
		 */
		public function get_customer_contact_editURL($custID, $shiptoID = '', $contactID) {
			$url = new Url($this->paths->get_urlpath('customer_contact'));
			$url->path->add('edit');
			$url->query->set('custID', $custID);

			if (!empty($shiptoID)) {
				$url->query->set('shipID', $shiptoID);
			}
			$url->query->set('contactID', $contactID);
			return $url->getUrl();
		}

		/**
		 * Returns the URL for Adding Customer Contact
		 * @param  string $custID    Customer ID
		 * @param  string $shiptoID  Shipto ID
		 * @return string            Add Customer Contact Page URL
		 */
		public function get_customer_contact_addURL($custID, $shiptoID = '') {
			$url = new Url($this->paths->get_urlpath('customer_contact'));
			$url->path->add('add');
			$url->query->set('custID', $custID);

			if (!empty($shiptoID)) {
				$url->query->set('shipID', $shiptoID);
			}
			return $url->getUrl();
		}
	}