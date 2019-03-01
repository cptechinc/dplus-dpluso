<?php
    namespace Dplus\Dpluso\Configs;

    /**
	 * External Libraries
	 */
    use Purl\Url;
    
    /**
	 * Functions that provide URLs to Vendor Functions / Pages
	 */
    trait VendorURLsTrait {
        /**
         * Returns URL to Load Vendor Information Page for Vendor ID
         * @param  string $vendorID Vendor ID
         * @return string           Vendor Information Page
         */
        public function get_viURL($vendorID) {
            $url = new Url($this->paths->get_urlpath('vendor'));
            $url->path->add('redir');
            $url->query->set('action', 'vi-vendor');
            $url->query->set('vendorID', $vendorID);
			return $url->getUrl();
        }

        /**
         * Returns URL to Load Vendor Information Page for Vendor ID
         * @param  string $vendorID   Vendor ID
         * @param  string $shipfromID Vendor Ship from ID
         * @return string             Vendor Information Page
         */
        public function get_vishipfromURL($vendorID, $shipfromID) {
            $url = new Url($this->paths->get_urlpath('vendor'));
            $url->path->add('redir');
            $url->query->set('action', 'vi-shipfrom');
            $url->query->set('vendorID', $vendorID);
            $url->query->set('shipfromID', $shipfromID);
			return $url->getUrl();
        }
    }