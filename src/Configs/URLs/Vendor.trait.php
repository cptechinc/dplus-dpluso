<?php
    namespace Dplus\Dpluso\Configs;

    /**
	 * External Libraries
	 */
    use Purl\Url;
    
    trait VendorURLsTrait {
        public function get_viURL($vendorID) {
            $url = new Url($this->paths->get_urlpath('vendor'));
            $url->path->add('redir');
            $url->query->set('action', 'vi-vendor');
            $url->query->set('vendorID', $vendorID);
			return $url->getUrl();
        }
        public function get_vishipfromURL($vendorID, $shipfromID) {
            $url = new Url($this->paths->get_urlpath('vendor'));
            $url->path->add('redir');
            $url->query->set('action', 'vi-shipfrom');
            $url->query->set('vendorID', $vendorID);
            $url->query->set('shipfromID', $shipfromID);
			return $url->getUrl();
        }
    }