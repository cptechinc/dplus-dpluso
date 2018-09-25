<?php
    class UserActionDisplay {
		use ThrowErrorTrait;
		use MagicMethodTraits;
		use AttributeParser;

        protected $modal = '#ajax-modal';
        protected $pageurl = false;
        protected $userID;

		/* =============================================================
			CONSTRUCTOR FUNCTIONS
		============================================================ */
        public function __construct(\Purl\Url $pageurl) {
            $this->pageurl = new \Purl\Url($pageurl->getUrl());
            $this->userID = DplusWire::wire('user')->loginid;
        }

		/* =============================================================
			CLASS FUNCTIONS
		============================================================ */
		/**
		 * Returns URL location where that action can be displayed from
		 * @param  UserAction $action UserAcion to use the Action Type and ID
		 * @return string         URL where action can be displayed from
		 */
		public function generate_viewactionurl(UserAction $action) {
			return DplusWire::wire('config')->pages->useractions."?id=".$action->id;
		}

		/**
		 * Returns URL to the Edit Action Page
		 * @param  UserAction $action UserAcion to use the Action Type and ID
		 * @return string         URL where action can be edited from
		 */
		public function generate_editactionurl(UserAction $action) {
			return DplusWire::wire('config')->pages->useractions."update/?id=".$action->id;
		}

		/**
		 * Returns URL where the User Action can mark itself as compelete
		 * @param  UserAction $action   $action UserAcion to use the Action Type and ID
		 * @param  string     $complete Y | N
		 * @return string               URL
		 */
		public function generate_completionurl(UserAction $action, $complete) {
			return DplusWire::wire('config')->pages->useractions."update/?id=".$action->id."&complete=".$complete; //true or false
		}

		/**
		 * Returns URL where the User Action can be Rescheduled
		 * @param  UserAction $action $action UserAcion to use the Action Type and ID
		 * @return string            URL
		 */
		public function generate_rescheduleurl(UserAction $action) {
			return DplusWire::wire('config')->pages->useractions."update/?id=$action->id&edit=reschedule";
		}

		/**
		 * Returns URL where the Action can be viewed in JSON format
		 * @param  UserAction $action $action UserAcion to use the Action ID
		 * @return string            URL
		 */
		public function generate_viewactionjsonurl(UserAction $action) {
			return DplusWire::wire('config')->pages->ajax."json/load-action/?id=".$action->id;
		}

		/**
		 * Takes the UserAction customerlink and shiptolink makes a Customer object and then generates
		 * the link to the customer page
		 * @param  UserAction $action Uses customerlink and shiptolink to generate Customer object
		 * @return string             URL to load the customer page
		 */
		public function generate_ciloadurl(UserAction $action) {
			$customer = Customer::load($action->customerlink, $action->shiptolink);
			return $customer->generate_customerurl();
		}

		/**
		 * Takes the UserAction customerlink makes a Customer object and then generates
		 * the link to the customer page
		 * @param  UserAction $action Uses customerlink to generate Customer object
		 * @return string             URL to load the customer page
		 */
		public function generate_customerurl(UserAction $action) {
			$customer = Customer::load($action->customerlink);
			return $customer ? $customer->generate_customerurl() : '';
		}

		/**
		 * Takes the UserAction customerlink and shiptolink makes a Customer object and then generates
		 * the link to the customer page
		 * @param  UserAction $action Uses customerlink and shiptolink to generate Customer object
		 * @return string             URL to load the customer page
		 */
		public function generate_shiptourl(UserAction $action) {
			$customer = Customer::load($action->customerlink, $action->shiptolink);
			return $customer ? $customer->generate_customerurl() : '';
		}

		/**
		 * Returns the URL to the contact page that is linked to this UserAction
		 * @param  UserAction $action customerlink, shiptolink, and contactlink to generate Contact object
		 * @return string             Contact page URL
		 */
		public function generate_contacturl(UserAction $action) {
			$contact = Contact::load($action->customerlink, $action->shiptolink, $action->contactlink);
			return $contact ? $contact->generate_contacturl() : '';
		}

		/**
		 * Returns link to click to view the UserAction
		 * @param  UserAction $action Uses it to generate the URL to view it
		 * @return string             HTML link
		 */
        public function generate_viewactionlink(UserAction $action) {
            $bootstrap = new HTMLWriter();
            $href = $this->generate_viewactionurl($action);
            $icon = $bootstrap->icon('material-icons md-18', '&#xE02F;');
            return $bootstrap->create_element('a', "href=$href|role=button|class=btn btn-xs btn-primary modal-load|data-modal=$this->modal|title=View Action", $icon);
        }

		/**
		 * Returns link to click to edit the action
		 * @param  UserAction $action Uses it to generate the URL to edit
		 * @return string             HTML link
		 */
        public function generate_editactionlink(UserAction $action) {
            $bootstrap = new HTMLWriter();
            $href = $this->generate_editactionurl($action);
            $icon = $bootstrap->icon('glyphicon glyphicon-pencil');
            $type = ucfirst($action->actiontype);
            return $bootstrap->create_element('a', "href=$href|role=button|class=btn btn-primary modal-load|data-modal=$this->modal|title=Edit Action", $icon. " Edit $type");
        }

		/**
		 * Returns link to click to mark task as complete
		 * @param  UserAction $task Uses it to generate the URL to mark as comeplete
		 * @return string           HTML link
		 */
        public function generate_completetasklink(UserAction $task) {
            $bootstrap = new HTMLWriter();
            $href = $this->generate_viewactionjsonurl($task);
            $icon = $bootstrap->icon('fa fa-check-circle');
            $icon .= ' <span class="sr-only">Mark as Complete</span>';
            return $bootstrap->create_element('a', "href=$href|role=button|class=btn btn-primary complete-action|title=Mark Task as Complete", $icon. " Complete Task");
        }

        public function generate_rescheduletasklink(UserAction $task) {
            $bootstrap = new HTMLWriter();
            $href = $this->generate_rescheduleurl($task);
            $icon = $bootstrap->icon('fa fa-calendar');
            return $bootstrap->create_element('a', "href=$href|role=button|class=btn btn-default modal-load|data-modal=$this->modal|", $icon. " Reschedule Task");
        }

        public function generate_customerpagelink(UserAction $action) {
            $bootstrap = new HTMLWriter();
            $href = $this->generate_customerurl($action);
            $icon = $bootstrap->icon('glyphicon glyphicon-share');
            return $bootstrap->create_element('a', "href=$href", $icon." Go to Customer Page");
        }

        public function generate_shiptopagelink(UserAction $action) {
            $bootstrap = new HTMLWriter();
            $href = $this->generate_customerurl($action);
            $icon = $bootstrap->icon('glyphicon glyphicon-share');
            return $bootstrap->create_element('a', "href=$href", $icon." Go to Shipto Page");
        }

        public function generate_contactpagelink(UserAction $action) {
            $bootstrap = new HTMLWriter();
            $href = $this->generate_contacturl($action);
            $icon = $bootstrap->icon('glyphicon glyphicon-share');
            return $bootstrap->create_element('a', "href=$href", $icon." Go to Contact Page");
        }
    }
