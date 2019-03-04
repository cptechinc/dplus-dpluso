<?php
	namespace Dplus\Dpluso\OrderDisplays;

	/**
	 * External Libraries
	 */
	use Purl\Url;

	/**
	 * Internal Libraries
	 */
	use Dplus\Base\ThrowErrorTrait;
	use Dplus\Base\MagicMethodTraits;
	use Dplus\Base\AttributeParser;
	use Dplus\Dpluso\Configs\DplusoConfigURLs;

	/**
	 * Use Statements for Model Classes which are non-namespaced
	 */
	use Order;

	/**
	 * Blueprint for Order Display classes
	 */
	abstract class OrderDisplay {
		use ThrowErrorTrait;
		use MagicMethodTraits;
		use AttributeParser;

		/**
		 * URL object that contains the Path to the page
		 * @var Url
		 */
		protected $pageurl;

		/**
		 * Session Identifier
		 * @var string
		 */
		protected $sessionID;

		/**
		 * ID of Modal to use
		 * @var string or False
		 */
		protected $modal;

		/**
		 * Base Constructor
		 * @param string  $sessionID  Session Identifier
		 * @param Url     $pageurl   URL object to get URL
		 * @param mixed   $modal     ID of modal to use or false
		 */
		public function __construct($sessionID, Url $pageurl, $modal = false) {
			$this->sessionID = $sessionID;
			$this->pageurl = new Url($pageurl->getUrl());
			$this->modal = $modal;
		}

		/* =============================================================
			OrderDisplay Interface Functions
		============================================================= */
	}
