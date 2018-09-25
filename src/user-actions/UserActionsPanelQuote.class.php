<?php
	class QuoteActionsPanel extends ActionsPanel {
		/**
		 * Panel Type
		 * Who or what the actions are being filtered to
		 * @var string
		 */
		protected $paneltype = 'quote';

		/**
		 * Quote Number
		 * @var string
		 */
		protected $qnbr;

		/**
		 * Array of filterable columns and their attributes
		 * @var array
		 */
		protected $filterable = array(
			'actiontype' => array(
				'querytype' => 'in',
				'datatype' => 'text',
				'label' => 'Action Type'
			),
			'completed' => array(
				'querytype' => 'in',
				'datatype' => 'text',
				'label' => 'Completed'
			),
			'assignedto' => array(
				'querytype' => 'in',
				'datatype' => 'text',
				'label' => 'Assigned To'
			),
			'datecreated' => array(
				'querytype' => 'between',
				'datatype' => 'mysql-date',
				'label' => 'Date Created',
				'date-format' => "m/d/Y H:i:s"
			),
			'datecompleted' => array(
				'querytype' => 'between',
				'datatype' => 'mysql-date',
				'label' => 'Date Completed',
				'date-format' => "m/d/Y H:i:s"
			),
			'dateupdated' => array(
				'querytype' => 'between',
				'datatype' => 'mysql-date',
				'label' => 'Date Updated',
				'date-format' => "m/d/Y H:i:s"
			),
			'duedate' => array(
				'querytype' => 'between',
				'datatype' => 'mysql-date',
				'label' => 'Due Date',
				'date-format' => "m/d/Y H:i:s"
			),
			'quotelink' => array(
				'querytype' => 'in',
				'datatype' => 'text',
				'label' => 'Quote Number'
			)
		);

		/**
		 * What kind of view to start with
		 * day | calendar | list
		 * @var string
		 */
		protected $view = 'list';


		/* =============================================================
			CONSTRUCTOR FUNCTIONS
		============================================================ */
		/**
		 * Constructor
		 * @param string                $sessionID   Session Identifier
		 * @param Purl\Url              $pageurl     Object that contains URL to Page
		 * @param ProcessWire\WireInput $input       Input such as the $_GET array to run generate_filter
		 * @param bool                  $throughajax If panel was loaded through ajax
		 * @param string                $panelID     Panel element ID
		 */
		public function __construct($sessionID, \Purl\Url $pageurl, ProcessWire\WireInput $input, $throughajax = false, $panelID = '') {
			parent::__construct($sessionID, $pageurl, $input, $throughajax, $panelID);
			$this->input = $input;
		}

		/* =============================================================
			GETTER FUNCTIONS
		============================================================ */
		/**
		* Generates title for Panel
		* @return string
		*/
		public function generate_title() {
			return "Quote #$this->qnbr Actions";
		}

		/**
		* Returns if the panel should have the add link
		* @return bool
		*/
		public function should_haveaddlink() {
			return true;
		}

		/**
		 * Returns URL to load add new action[type=$this->actiontype] form
		 * @return string                 URL to load add new action[type=$this->actiontype] form
		 */
		public function generate_addactionurl() {
			$url = new Purl\Url(parent::generate_addactionurl());
			$url->query->set('qnbr', $this->qnbr);
			return $url->getUrl();
		}

		public function generate_clearfilterurl() {
			$url = new Purl\Url(parent::generate_clearfilterurl());
			$url->query->set('qnbr', $this->qnbr);
			return $url->getUrl();
		}

		/* =============================================================
			SETTER FUNCTIONS
		============================================================ */
		/**
		 * Manipulates $this->pageurl path and query data as needed
		 * then sets $this->paginateafter value
		 * @return void
		 */
		public function setup_pageurl() {
			parent::setup_pageurl();
			$this->pageurl->query->set('qnbr', $this->qnbr);
		}

		/**
		 * Sets the Quote Number for the filter, and $this->pageurl
		 * @param string $qnbr    Quote Number
		 */
		public function set_qnbr($qnbr) {
			$this->qnbr = $qnbr;
			$this->generate_filter($this->input);
			$this->setup_pageurl();
		}

		/* =============================================================
			CLASS FUNCTIONS
		============================================================ */
		public function generate_filter(ProcessWire\WireInput $input) {
			parent::generate_filter($input);
			$this->filters['quotelink'] = array($this->qnbr);
		}

		/* =============================================================
			CONTENT FUNCTIONS
		============================================================ */
	}
