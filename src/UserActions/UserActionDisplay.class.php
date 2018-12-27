<?php
	namespace Dplus\Dpluso\UserActions;
	
	use Purl\Url;
    use Dplus\ProcessWire\DplusWire;
    use Dplus\Content\HTMLWriter;
	
	/**
	 * Use Statements for Model Classes which are non-namespaced
	 */
	use UserAction;
	use Customer;
	use Contact;
	
	/**
	 * Class for dealing with the display of a User Action
	 */
    class UserActionDisplay {
		use \Dplus\Base\ThrowErrorTrait;
		use \Dplus\Base\MagicMethodTraits;
		use \Dplus\Base\AttributeParser;
		
		/**
		 * HTML Modal ID
		 * @var string
		 */
        protected $modal = '#ajax-modal';
		
		/**
		 * Page URL
		 * @var Url
		 */
        protected $pageurl = false;
		
		/**
		 * User ID
		 * @var string
		 */
        protected $userID;

		/* =============================================================
			CONSTRUCTOR FUNCTIONS
		============================================================ */
		/**
		 * Constructor
		 * @param Url $pageurl URL Page is on
		 */
        public function __construct(Url $pageurl) {
            $this->pageurl = new Url($pageurl->getUrl());
            $this->userID = DplusWire::wire('user')->loginid;
        }

		/* =============================================================
			CLASS FUNCTIONS
		============================================================ */
		/**
		 * Returns URL location where that action can be displayed from
		 * // TODO rename for URL()
		 * @param  UserAction $action UserAcion to use the Action Type and ID
		 * @return string         URL where action can be displayed from
		 */
		public function generate_viewactionurl(UserAction $action) {
			return DplusWire::wire('config')->pages->useractions."?id=".$action->id;
		}

		/**
		 * Returns URL to the Edit Action Page
		 * // TODO rename for URL()
		 * @param  UserAction $action UserAcion to use the Action Type and ID
		 * @return string             URL where action can be edited from
		 */
		public function generate_editactionurl(UserAction $action) {
			return DplusWire::wire('config')->pages->useractions."update/?id=".$action->id;
		}

		/**
		 * Returns URL where the User Action can mark itself as compelete
		 * // TODO rename for URL()
		 * @param  UserAction $action   $action UserAcion to use the Action Type and ID
		 * @param  string     $complete Y | N
		 * @return string               URL
		 */
		public function generate_completionurl(UserAction $action, $complete) {
			return DplusWire::wire('config')->pages->useractions."update/?id=".$action->id."&complete=".$complete; //true or false
		}

		/**
		 * Returns URL where the User Action can be Rescheduled
		 * // TODO rename for URL()
		 * @param  UserAction $action $action UserAcion to use the Action Type and ID
		 * @return string            URL
		 */
		public function generate_rescheduleurl(UserAction $action) {
			return DplusWire::wire('config')->pages->useractions."update/?id=$action->id&edit=reschedule";
		}

		/**
		 * Returns URL where the Action can be viewed in JSON format
		 * // TODO rename for URL()
		 * @param  UserAction $action $action UserAcion to use the Action ID
		 * @return string            URL
		 */
		public function generate_viewactionjsonurl(UserAction $action) {
			return DplusWire::wire('config')->pages->ajax."json/load-action/?id=".$action->id;
		}

		/**
		 * Takes the UserAction customerlink and shiptolink makes a Customer object and then generates
		 * the link to the customer page
		 * // TODO rename for URL()
		 * @param  UserAction $action Uses customerlink and shiptolink to generate Customer object
		 * @return string             URL to load the customer page
		 */
		public function generate_ciloadurl(UserAction $action) {
			$customer = Customer::load($action->customerlink, $action->shiptolink);
			return $customer->generate_customerURL();
		}

		/**
		 * Takes the UserAction customerlink makes a Customer object and then generates
		 * the link to the customer page
		 * // TODO rename for URL()
		 * @param  UserAction $action Uses customerlink to generate Customer object
		 * @return string             URL to load the customer page
		 */
		public function generate_customerURL(UserAction $action) {
			$customer = Customer::load($action->customerlink);
			return $customer ? $customer->generate_customerURL() : '';
		}

		/**
		 * Takes the UserAction customerlink and shiptolink makes a Customer object and then generates
		 * the link to the customer page
		 * // TODO rename for URL()
		 * @param  UserAction $action Uses customerlink and shiptolink to generate Customer object
		 * @return string             URL to load the customer page
		 */
		public function generate_shiptourl(UserAction $action) {
			$customer = Customer::load($action->customerlink, $action->shiptolink);
			return $customer ? $customer->generate_customerURL() : '';
		}

		/**
		 * Returns the URL to the contact page that is linked to this UserAction
		 * // TODO rename for URL()
		 * @param  UserAction $action customerlink, shiptolink, and contactlink to generate Contact object
		 * @return string             Contact page URL
		 */
		public function generate_contacturl(UserAction $action) {
			$contact = Contact::load($action->customerlink, $action->shiptolink, $action->contactlink);
			return $contact ? $contact->generate_contacturl() : '';
		}

		/**
		 * Returns link to click to view the UserAction
		 * // FIXME Remove, and make link at presentation level
		 * @param  UserAction $action Uses it to generate the URL to view it
		 * @return string             HTML link
		 */
        public function generate_viewactionlink(UserAction $action) {
            $bootstrap = new HTMLWriter();
            $href = $this->generate_viewactionurl($action);
            $icon = $bootstrap->icon('material-icons md-18', '&#xE02F;');
            return $bootstrap->a("href=$href|role=button|class=btn btn-xs btn-primary modal-load|data-modal=$this->modal|title=View Action", $icon);
        }

		/**
		 * Returns link to click to edit the action
		 * // FIXME Remove, and make link at presentation level
		 * @param  UserAction $action Uses it to generate the URL to edit
		 * @return string             HTML link
		 */
        public function generate_editactionlink(UserAction $action) {
            $bootstrap = new HTMLWriter();
            $href = $this->generate_editactionurl($action);
            $icon = $bootstrap->icon('fa fa-pencil');
            $type = ucfirst($action->actiontype);
            return $bootstrap->a("href=$href|role=button|class=btn btn-primary modal-load|data-modal=$this->modal|title=Edit Action", $icon. " Edit $type");
        }

		/**
		 * Returns link to click to mark task as complete
		 * // FIXME Remove, and make link at presentation level
		 * @param  UserAction $task Uses it to generate the URL to mark as comeplete
		 * @return string           HTML link
		 */
        public function generate_completetasklink(UserAction $task) {
            $bootstrap = new HTMLWriter();
            $href = $this->generate_viewactionjsonurl($task);
            $icon = $bootstrap->icon('fa fa-check-circle');
            $icon .= ' <span class="sr-only">Mark as Complete</span>';
            return $bootstrap->a("href=$href|role=button|class=btn btn-primary complete-action|title=Mark Task as Complete", $icon. " Complete Task");
        }
		
		/**
		 * Returns link to click to reschedule task
		 * // FIXME Remove, and make link at presentation level
		 * @param  UserAction $task Uses it to generate the URL
		 * @return string           HTML link
		 */
        public function generate_rescheduletasklink(UserAction $task) {
            $bootstrap = new HTMLWriter();
            $href = $this->generate_rescheduleurl($task);
            $icon = $bootstrap->icon('fa fa-calendar');
            return $bootstrap->a("href=$href|role=button|class=btn btn-default modal-load|data-modal=$this->modal|", $icon. " Reschedule Task");
        }
		
		/**
		 * Returns link to click to customer page
		 * // FIXME Remove, and make link at presentation level
		 * @param  UserAction $action Uses it to generate the URL
		 * @return string             HTML link
		 */
        public function generate_customerpagelink(UserAction $action) {
            $bootstrap = new HTMLWriter();
            $href = $this->generate_customerURL($action);
            $icon = $bootstrap->icon('fa fa-share-square-o');
            return $bootstrap->a("href=$href", $icon." Go to Customer Page");
        }
		
		/**
		 * Returns link to click to customer shipto page
		 * // FIXME Remove, and make link at presentation level
		 * @param  UserAction $action Uses it to generate the URL
		 * @return string             HTML link
		 */
        public function generate_shiptopagelink(UserAction $action) {
            $bootstrap = new HTMLWriter();
            $href = $this->generate_customerURL($action);
            $icon = $bootstrap->icon('fa fa-share-square-o');
            return $bootstrap->a("href=$href", $icon." Go to Shipto Page");
        }
		
		/**
		 * Returns link to click to customer (shipto) contact page
		 * // FIXME Remove, and make link at presentation level
		 * @param  UserAction $action Uses it to generate the URL
		 * @return string             HTML link
		 */
        public function generate_contactpagelink(UserAction $action) {
            $bootstrap = new HTMLWriter();
            $href = $this->generate_contacturl($action);
            $icon = $bootstrap->icon('fa fa-share-square-o');
            return $bootstrap->a("href=$href", $icon." Go to Contact Page");
        }
    }
