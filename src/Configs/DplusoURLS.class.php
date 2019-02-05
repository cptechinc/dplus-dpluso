<?php
	namespace Dplus\Dpluso;
	
	use ProcessWire\WireData;
	
	class DplusoURLS extends WireData {
		protected $_self;
		protected $path;
		protected $urls = array(
			'actions'  => array('_self' => 'activity'),
			'activity' => array(
				'_self'       => 'activity',
				'useractions' => 'user-actions'
			),
			'ajax' => array(
				'_self' => 'user',
				'json'  => 'json',
				'load'  => 'load'
			),
			'cart'     => array('_self' => 'cart'),
			'customer' => array(
				'_self'    => 'customers',
				'custinfo' => 'cust-info', // NOTE CHANGE $config->custinfo to this
				'contact'  => 'contact'
			),
			'confirm' => array(
				'_self'      => 'edit',
				'quote'      => 'quote', // NOTE $config->pages->confirmquote
				'order'      => 'order', // NOTE $config->pages->confirmorder
			),
            'documentation' => array('_self' => 'documentation'),
			'edit' => array(
				'_self'      => 'edit',
				'quote'      => 'quote', // NOTE $config->pages->editquote
				'order'      => 'order', // NOTE $config->pages->editorder
				'orderquote' => 'quote-to-order'
			),
			'notes'    => array('_self' => 'notes'),
			'print'    => array('_self' => 'print'),
			'products' => array(
				'_self'    => 'products',
				'iteminfo' => 'item-info', // NOTE $config->pages->iteminfo
			),
			'reports'   => array('_self' => 'reports'),
			'sys'       => array(
				'_self' => 'sys',
				'email' => 'email', // NOTE $config->pages->email
			),
			'user' => array(
				'_self'   => 'user',
				'account' => array(
					'_self' => 'account',
					'login' => 'login',
				),
				'dashboard'       => 'dashboard',   // NOTE $config->pages->dashboard
				'configs'         => 'user-config', // NOTE $config->pages->userconfigs
				'screens'         => 'user-screens',
				'tableformatters' => 'table-formatters',
				'orders'          => 'orders',
				'quotes'          => 'quotes'
			),
			'vendor'        => array(
				'_self'      => 'vendors',
				'vendorinfo' => 'vend-info',
			),
			'warehouse' => array(
				'_self'   => 'warehouse', 
				'picking' => array(
					'_self'    => 'picking',    // NOTE $config->pages->warehousepicking
					'order'    => 'pick-order', // NOTE $config->pages->salesorderpicking
					'pickpack' => 'pick-pack'   // NOTE $config->pages->salesorderpickpacking 
				),
				'binr' => array(
					'_self' => 'binr',
					'binr'  => array('_self' => 'binr'),  // NOTE $config->pages->binr
				),
				'inventory' => array(
					'_self'         => 'inventory',
					'physicalcount' => 'physical-count'
				)
			),
		);
		
		function __construct($path) {
			$this->data = new WireData();
			$this->_self = $path;
		}
		
		function __toString() {
			return $this->_self;
		}
		
		function __get($key) {
			$keys = explode('_', $key);
			$path = $this->get_pathfromarray($this->urls, $keys);
			return "{$this->_self}$path/";
		}
		
		protected function get_pathfromarray($urlarray, $keys) {
			if (is_array($urlarray[$keys[0]])) {
				$array = $urlarray[$keys[0]];
				$paths = array($urlarray[$keys[0]]['_self']);
				array_shift($keys);
				
				while (is_array($array) && !empty($keys)) {
					if (is_array($array[$keys[0]])) {
						$paths[] = array($array[$keys[0]]['_self']);
						$array = $array[$keys[0]];
						array_shift($keys);
					} else {
						$paths[] = $array[$keys[0]];
						break;
					}
				}
				return implode('/', $paths);
			} else {
				return $urlarray[$keys[0]];
			}
		}
	}
