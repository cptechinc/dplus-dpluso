<?php
	/**
	 * Class for dealing with the display of arrays of UserAction
	 * Content
	 */
	class ActionsPanel extends UserActionDisplay {
		use Filterable;
		use AttributeParser;

		/**
		* Session Identifier
		* @var string
		*/
		protected $sessionID;

		/**
		* Modal to load some Ajax content into
		* @var string
		*/
		protected $modal = '#ajax-modal';

		/**
		* What Action Type to filter
		* @var string
		*/
		protected $actiontype = '';

		/**
		 * Segment of URL path to put pagination after
		 * @var string
		 */
		protected $paginateafter = 'user-actions';

		/**
		* ID of element to focus on
		* @var string
		*/
		protected $focus = '';

		/**
		* ID of element to load ajax content into
		* @var string
		*/
		protected $loadinto = '';

		/**
		* Page URL
		* @var Purl\Url
		*/
		protected $pageurl = false;

		/**
		* String of data attributes
		* @var string e.g. data-loadinto='$this->loadinto' data-focus='$this->focus'
		*/
		protected $ajaxdata;

		/**
		* Whether or not the panel div shows opened or collapse
		* @var string
		*/
		protected $collapse = 'collapse';
		// TODO $tablesorter

		/**
		* Page Number
		* @var int
		*/
		protected $pagenbr = 0;

		/**
		* Number of Actions that match
		* @var int
		*/
		protected $count = 0;

		/**
		* Array of filters that will apply to the orders
		* @var array
		*/
		protected $filters = false;

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
			)
		);

		/**
		 * Task Status and the possible values they could have
		 * @var array
		 */
		protected $taskstatuses = array(
			'completed' => array(
				'value' => 'Y',
				'label' => 'Completed'
			),
			'incomplete' => array (
				'value' => '',
				'label' => 'Incomplete'
			),
			'rescheduled' => array(
				'value' => 'R',
				'label' => 'Rescheduled'
			)
		);

		/**
		 * Panel element ID
		 * NOTE: used for AJAX
		 * @var string
		 */
		protected $panelID = 'actions-panel';

		/**
		 * Panel Body Element
		 * @var string
		 */
		protected $panelbody = 'actions';

		/**
		 * Panel Type
		 * Who or what the actions are being filtered to
		 * @var string
		 */
		protected $paneltype = 'user';

		/**
		 * What kind of view to start with
		 * day | calendar | list
		 * @var string
		 */
		protected $view = 'day';

		/**
		 * Was panel loaded through AJAX?
		 * @var bool
		 */
		protected $throughajax = false;

		/**
		 * Is panel in a modal
		 * @var bool
		 */
		protected $inmodal = false;

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
			$this->sessionID = $sessionID;
			$this->pageurl = new \Purl\Url($pageurl->getUrl());
			$this->pagenbr = Paginator::generate_pagenbr($pageurl);
			$this->throughajax = $throughajax;
			$this->panelID = !empty($panelID) ? $panelID : $this->panelID;
			$this->inmodal = $this->pageurl->query->get('modal') ? true : false;
			$this->panelID = $this->inmodal ? 'ajax-actions-panel' : $this->panelID;
			$this->loadinto = $this->focus = "#$this->panelID";
			$this->ajaxdata = "data-loadinto='$this->loadinto' data-focus='$this->focus'";
			$this->collapse = $throughajax ? '' : 'collapse';
			$this->generate_filter($input);
			$this->setup_pageurl();
			$this->count_actions();
		}

		/* =============================================================
			GETTER FUNCTIONS
		============================================================ */
		/**
		* Generates title for Panel
		* Will be overwritten by children
		* @return string
		*/
		public function generate_title() {
			return 'Your Actions';
		}

		/**
		* Returns if the panel should have the add link
		* Will be overwritten by children
		* @return bool
		*/
		public function should_haveaddlink() {
			return false;
		}

		/**
		 * Returns Page Number the List is on along with the preceding string Page
		 * @return string Page $this->pagenbr | (blank)
		 */
		public function generate_pagenumberdescription() {
			return ($this->pagenbr > 1) ? "Page $this->pagenbr" : '';
		}

		/* GENERATE URLS - URLS ARE THE HREF VALUE */
		/**
		 * Returns URL of the panel's state
		 * @return string URL
		 */
		public function generate_refreshurl() {
			$url = new \Purl\Url($this->pageurl->getUrl());
			$url->query->remove('modal');
			$url->path = DplusWire::wire('config')->pages->useractions;
			return $url->getUrl();
		}

		/**
		 * Returns URL to load panel without filtered search
		 * @return string  URL to load panel without filtered search
		 */
		public function generate_loadurl() {
			$url = new \Purl\Url($this->pageurl);
			$url->query->remove('filter');
			$url->query->remove('modal');
			foreach (array_keys($this->filterable) as $filtercolumns) {
				$url->query->remove($filtercolumns);
			}
			return $url->getUrl();
		}

		/**
		 * Returns URL to load add new action[type=$this->actiontype] form
		 * @return string URL to load add new action[type=$this->actiontype] form
		 */
		public function generate_addactionurl() {
			if (DplusWire::wire('config')->cptechcustomer == 'stempf') {
				$actiontype = ($this->actiontype == 'all') ? 'task' : $this->actiontype;
			} else {
				$actiontype = '';
			}
			$url = new \Purl\Url($this->generate_refreshurl());
			$url->path = DplusWire::wire('config')->pages->useractions."add/";
			$url->query = '';
			$url->query->set('type', $actiontype);
			return $url->getUrl();
		}

		/**
		 * Returns URL to load the current view without any filters applied
		 * @return string URL
		 */
		public function generate_clearfilterurl() {
			$url = new \Purl\Url($this->generate_refreshurl());
			$view = $url->query->get('view') ? $url->query->get('view') : $this->view;
			$url->query = '';
			$url->query->set('view', $view);
			return $url->getUrl();
		}

		/**
		 * Returns the URL to view the actions in a calendar view
		 * @return string URL to calendar view
		 */
		public function generate_calendarviewurl() {
			$url = new Purl\Url($this->generate_refreshurl());
			$url->query->set('view', 'calendar');
			if ($url->query->get('day')) {
				$monthyear = date('M-Y', strtotime($url->query->get('day')));
				$url->query->set('month', $monthyear);
				$url->query->remove('day');
			}
			$url->query->remove('completed');
			$url->query->remove('actiontype');
			$url->query->remove('duedate');
			return $url->getUrl();
		}

		/**
		 * Returns URL to go to a month by adding to $date's month
		 * @param  string  $date String formatted date
		 * @param  int     $add  Number of months to add, can be negative
		 * @return string        URL to view calendar at month that is $add months from $date
		 */
		public function generate_addmonthurl($date, $add = 1) {
			$date = $date ? new DateTime($date) : new DateTime();
			$modify = $add > 0 ? "+$add" : "$add";
			$date->modify("$modify month");
			$url = new Purl\Url($this->pageurl->getUrl());
			$url->query->set('month', $date->format('M-Y'));
			return $url->getUrl();
		}

		/**
		 * Returns the URL to view the actions in a day view
		 * @param  string $date Datetime string usually in (m/d/Y) format
		 * @return string       URL to day view
		 */
		public function generate_dayviewurl($date) {
			$date = $date ? date('m/d/Y', strtotime($date)) : date('m/d/Y');
			$url = new Purl\Url($this->generate_refreshurl());
			$url->query->set('view', 'day');
			$url->query->set('day', $date);
			$url->query->remove('month');
			return $url->getUrl();
		}

		/**
		 * Returns the URL to view the a date's scheduled tasks
		 * @param  string $date date string (usually in m/d/Y)
		 * @return string       URL
		 */
		public function generate_dayviewscheduledtasksurl($date) {
			$date = $date ? date('m/d/Y', strtotime($date)) : date('m/d/Y');
			$url = new Purl\Url($this->generate_dayviewurl($date));
			$url->query->set('filter', 'filter');
			$url->query->set('actiontype', 'task');
			$url->query->set('duedate', $date);
			$url->query->set('completed', 'Y||R');
			$url->query->remove('datecreated');
			return $url->getUrl();
		}

		/**
		 * Returns URL to view notes created that day
		 * @param  string $date date string (usually in m/d/Y)
		 * @return string       URL
		 */
		public function generate_daynotescreatedurl($date) {
			$date = $date ? date('m/d/Y', strtotime($date)) : date('m/d/Y');
			$url = new Purl\Url($this->generate_dayviewurl($date));
			$url->query->remove('duedate');
			$url->query->remove('completed');
			$url->query->set('filter', 'filter');
			$url->query->set('actiontype', 'note');
			$url->query->set('datecreated', $date);
			return $url->getUrl();
		}

		/**
		 * Returns the URL to view the panel in List View
		 * @return string  URL to load List View
		 */
		public function generate_listviewurl() {
			$url = new Purl\Url($this->generate_refreshurl());
			$url->query->remove('day');
			$url->query->remove('month');
			$url->query->set('view', 'list');
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
			$this->paginateafter = $this->paginateafter;
		}

		/**
		 * Sets the view
		 * @param string $view day | list | calendar
		 */
		public function set_view($view) {
			$this->view = !empty($view) ? $view : $this->view;
		}


		/* =============================================================
			DATABASE FUNCTIONS
		============================================================ */
		/**
		 * Returns the number of UserActions that meet the $this->filter
		 * @param  bool  $debug Return count or return SQL Query?
		 * @return int          Number of UserActions | SQL Query
		 */
		public function count_actions($debug = false) {
			return $debug ? count_actions($this->filters, $this->filterable, $debug) : $this->count = count_actions($this->filters, $this->filterable, $debug);
		}

		/**
		 * Returns an array of UserAction that meet the $this->filter
		 * @param  bool   $debug Return UserActions | return SQL Query?
		 * @return array         Array of UserAction
		 */
		public function get_actions($debug = false) {
			return get_actions($this->filters, $this->filterable, DplusWire::wire('session')->display, $this->pagenbr, $debug);
		}

		/**
		 * Returns the number of all the UserAction for that day
		 * @param  string $day   Datetime string (usually in m/d/Y)
		 * @param  bool   $debug Return count or return SQL Query?
		 * @return int           Count of All Actions for that day | SQL Query
		 */
		public function count_dayallactions($day, $debug = false) {
			return count_dayallactions($day, $this->filters, $this->filterable, $debug);
		}

		/**
		 * Returns an array of UserAction for that day
		 * @param  string $day   Datetime string (usually in m/d/Y)
		 * @param  bool   $debug Return UserActions | return SQL Query?
		 * @return array         Array of UserAction | SQL Query?
		 */
		public function get_dayallactions($day, $debug = false) {
			return get_dayallactions($day, $this->filters, $this->filterable, $debug);
		}

		/**
		 * Returns the number of all the UserAction[type=tasks] for that day
		 * @param  string $day   Datetime string (usually in m/d/Y)
		 * @param  bool   $debug Return UserActions | return SQL Query?
		 * @return int           Number of UserAction[type=task] for that day  | SQL Query?
		 */
		public function count_daytasks($day, $debug = false) {
			$filters = $this->filters;
			$filters['actiontype'] = array('task');
			return count_dayallactions($day, $filters, $this->filterable, $debug);
		}

		/**
		 * Returns UserActions [type=task] that meet the $this->filter criteria
		 * @param  string $day   Date time string usually formatted (m/d/Y)
		 * @param  bool   $debug Whether to return array of UserActions | SQL Query
		 * @return array         array of UserActions | SQL Query
		 */
		public function get_daytasks($day, $debug = false) {
			$filters = $this->filters;
			unset($filters['datecreated']);
			$filters['actiontype'] = array('task');
			return get_dayallactions($day, $filters, $this->filterable, $debug);
		}

		/**
		 * Returns UserActions [type=task] that meet the $this->filter criteria
		 * @param  string $day   Date time string usually formatted (m/d/Y)
		 * @param  bool   $debug Whether to return array of UserActions | SQL Query
		 * @return array         array of UserActions | SQL Query
		 */
		public function get_daypriorincompletetasks($day, $debug = false) {
			$filters = $this->filters;
			$filters['completed'] = array('');
			return get_daypriorincompletetasks($day, $filters, $this->filterable, $debug);
		}

		/**
		 * Returns the number UserActions [type=task] that meet the $this->filter criteria
		 * @param  string $day   Date time string usually formatted (m/d/Y)
		 * @param  bool   $debug Whether to return array of UserActions | SQL Query
		 * @return int           Number of tasks
		 */
		public function count_daypriorincompletetasks($day, $debug = false) {
			$filters = $this->filters;
			$filters['completed'] = array('');
			return count_daypriorincompletetasks($day, $filters, $this->filterable, $debug);
		}

		/**
		 * Returns of the number of UserActions[type=task] scheduled for that day
		 * @param  string  $day  Date time string usually formatted (m/d/Y)
		 * @param  bool   $debug Whether to return Number | SQL Query
		 * @return int           Number of Tasks scheduled for that day | SQL Query
		 */
		public function count_dayscheduledtasks($day, $debug = false) {
			$filters = $this->filters;
			unset($filters['completed']);
			unset($filters['datecreated']);
			$filters['duedate'] = array($day);
			$filters['actiontype'] = array('task');
			return count_dayallactions($day, $filters, $this->filterable, $debug);
		}

		/**
		 * Returns of the number of UserActions[type=task] rescheduled that day
		 * @param  string  $day  Date time string usually formatted (m/d/Y)
		 * @param  bool   $debug Whether to return Number | SQL Query
		 * @return int           Number of Tasks rescheduled that day | SQL Query
		 */
		public function count_dayrescheduledtasks($day, $debug = false) {
			$filters = $this->filters;
			$filters['actiontype'] = array('task');
			$filters['completed'] = array('R');
			$filters['dateupdated'] = array($day);
			return count_dayallactions($day, $filters, $this->filterable, $debug);
		}

		/**
		 * Returns of the number of UserActions[type=task] that were due that day, but were rescheduled
		 * @param  string  $day  Date time string usually formatted (m/d/Y)
		 * @param  bool   $debug Whether to return Number | SQL Query
		 * @return int           Number of Tasks that were due that day, but were rescheduled | SQL Query
		 */
		public function count_daytasksduebutrescheduled($day, $debug = false) {
			$filters = $this->filters;
			$filters['actiontype'] = array('task');
			$filters['completed'] = array('R');
			$filters['duedate'] = array($day);
			return count_dayallactions($day, $filters, $this->filterable, $debug);
		}

		/**
		 * Returns of the number of UserActions[type=task] completed that day
		 * @param  string  $day  Date time string usually formatted (m/d/Y)
		 * @param  bool   $debug Whether to return Number or SQL Query
		 * @return int           Number of Tasks Completed that day | SQL Query
		 */
		public function count_daycompletedtasks($day, $debug = false) {
			$filters = $this->filters;
			$filters['actiontype'] = array('task');
			$filters['completed'] = array('Y');
			$filters['datecompleted'] = array($day);
			return count_dayallactions($day, $filters, $this->filterable, $debug);
		}

		/**
		 * Returns of the number of UserActions[type=task] that were not completed
		 * @param  string  $day  Date time string usually formatted (m/d/Y)
		 * @param  bool   $debug Whether to return Number or SQL Query
		 * @return int           Number of Tasks inomplete that day | SQL Query
		 */
		public function count_dayincompletedtasks($day, $debug = false) {
			$filters = $this->filters;
			$filters['actiontype'] = array('task');
			$filters['completed'] = array('');
			return count_dayallactions($day, $filters, $this->filterable, $debug);
		}

		/**
		 * Returns the number of UserActions[type=notes] made today
		 * @param  string  $day   Date time string usually formatted (m/d/Y)
		 * @param  bool    $debug Whether to return Number or SQL Query
		 * @return int            Number of Notes made that day | SQL Query
		 */
		public function count_daynotes($day, $debug = false) {
			$filters = $this->filters;
			unset($filters['completed']);
			$filters['actiontype'] = array('note');
			$filters['datecreated'] = array($day);
			return count_dayallactions($day, $filters, $this->filterable, $debug);
		}

		/**
		 * Returns UserActions[type=note] made that day
		 * @param  string $day   Date time string usually formatted (m/d/Y)
		 * @param  bool   $debug Whether to return array of Notes or SQL Query
		 * @return array         array of Notes | SQL Query
		 */
		public function get_daynotes($day, $debug = false) {
			$filters = $this->filters;
			unset($filters['completed']);
			$filters['actiontype'] = array('note');
			$filters['datecreated'] = array($day);
			return get_dayallactions($day, $filters, $this->filterable, $debug);
		}

		/* =============================================================
			CLASS FUNCTIONS
		============================================================ */
		public function generate_filter(ProcessWire\WireInput $input) {
			$this->generate_defaultfilter($input);

			// IF NO CHOSEN TASK STATE THEN DEFAULT TO INCOMPLETES
			if (!isset($this->filters['completed'])) {
				$this->filters['completed'] = array('');
			}

			// IF ASSIGNED USERS AREN'T BEING PROVIDED, THEN DEFAULT TO CURRENT USER
			if (!isset($this->filters['assignedto'])) {
				$this->filters['assignedto'] = array(DplusWire::wire('user')->loginid);
			}


			if (isset($this->filters['datecreated'])) {
				// PUT DATE CREATED IN THE THROUGH PART OF THE FILTER

				if (empty($this->filters['datecreated'][0] && empty($this->filters['datecreated'][1]))) {
					unset($this->filters['datecreated']);
				}
			}

			if (isset($this->filters['datecompleted'])) {
				if (!empty($this->filters['datecompleted'][0]) || !empty($this->filters['datecompleted'][1])) {
					$this->filters['completed'] = array('Y');
				}

				if (empty($this->filters['datecompleted'][1]) && !empty($this->filters['datecompleted'][0])) {
					//$this->filters['datecompleted'][1] = $this->filters['datecompleted'][0];
				}

				if (empty($this->filters['datecompleted'][0]) && empty($this->filters['datecompleted'][1])) {
					unset($this->filters['datecompleted']);
				}
			}

			if (isset($this->filters['actiontype'])) {
				if (sizeof($this->filters['actiontype']) > 1) {
					$this->actiontype = 'all';
				} else {
					$this->actiontype = $this->filters['actiontype'][0];
				}
			} else {
				$this->actiontype = 'all';
			}
		}

		/* =============================================================
			CONTENT FUNCTIONS
		============================================================ */
		/* = GENERATE LINKS - LINKS ARE THE HTML MARKUP FOR LINKS */
		/**
		 * Returns HTML Link to refresh the panel
		 * @return string  HTML Link
		 */
		public function generate_refreshlink() {
			$bootstrap = new HTMLWriter();
			$href = $this->generate_refreshurl();
			$icon = $bootstrap->icon('material-icons md-18', '&#xE86A;');
			$ajaxdata = $this->generate_ajaxdataforcontento();
			$ajaxclass = $this->inmodal ? 'modal-load' : 'load-link';
			return $bootstrap->create_element('a', "href=$href|class=btn btn-info btn-xs $ajaxclass actions-refresh pull-right hidden-print|title=button|title=Refresh Actions|aria-label=Refresh Actions|$ajaxdata|data-modal=$this->modal", $icon);
		}

		/**
		 * Returns HTML Link to Add a new action
		 * @return string HTML Link
		 */
		public function generate_addlink() {
			$bootstrap = new HTMLWriter();
			$href = $this->generate_addactionurl();
			$icon = $bootstrap->icon('material-icons md-18', '&#xE146;');
			if (DplusWire::wire('config')->cptechcustomer == 'stempf') {
				$ajaxclass = $this->inmodal ? 'modal-load' : 'load-into-modal';
				return $bootstrap->create_element('a', "href=$href|class=btn btn-info btn-xs $ajaxclass pull-right hidden-print|data-modal=$this->modal|role=button|title=Add Action", $icon);
			}
			return $bootstrap->create_element('a', "href=$href|class=btn btn-info btn-xs add-action pull-right hidden-print|data-modal=$this->modal|role=button|title=Add Action", $icon);
		}

		/**
		 * Returns HTML Link to clear the filters from day search
		 * @return string  HTML Link
		 */
		public function generate_clearfilterlink() {
			$bootstrap = new HTMLWriter();
			$href = $this->generate_loadurl();
			$icon = $bootstrap->icon('fa fa-times');
			$ajaxdata = $this->generate_ajaxdataforcontento();
			return $bootstrap->create_element('a', "href=$href|class=load-link btn btn-sm btn-warning btn-block|$ajaxdata", "Clear Filter $icon");
		}

		/**
		 * Returns a HTML Link to view the Printable version of this Page
		 * @return string  HTML Link to Print Page
		 */
		public function generate_printlink() {
			$bootstrap = new HTMLWriter();
			$href = $this->generate_refreshurl();
			$icon = $bootstrap->icon('glyphicon glyphicon-print');
			return $bootstrap->create_element('a', "href=$href|class=h3|target=_blank", $icon." View Printable");
		}


		public function generate_completetasklink(UserAction $task) {
			$bootstrap = new HTMLWriter();
			$href = $this->generate_viewactionjsonurl($task);
			$icon = $bootstrap->icon('fa fa-check-circle');
			$icon .= ' <span class="sr-only">Mark as Complete</span>';
			return $bootstrap->create_element('a', "href=$href|role=button|class=btn btn-xs btn-primary complete-action|title=Mark Task as Complete", $icon);
		}

		/**
		 * Returns Bootstrap popover element with the the possible row classes and their meaning
		 * @return string HTML Link
		 */
		public function generate_legend() {
			$bootstrap = new HTMLWriter();
			$tb = new Table('class=table table-bordered table-condensed table-striped');
			$tb->tr('class=bg-warning')->td('', 'Task Overdue');
			$tb->tr('class=bg-info')->td('', 'Task Rescheduled');
			$tb->tr('class=bg-success')->td('', 'Task Completed');
			$content = str_replace('"', "'", $tb->close());
			$attr = "tabindex=0|role=button|class=btn btn-sm btn-info|data-toggle=popover|data-placement=bottom|data-trigger=focus";
			$attr .= "|data-html=true|title=Icons Definition|data-content=$content";
			return $bootstrap->create_element('a', $attr, 'Icon Definitions');
		}

		/**
		 * Returns the row class for the action
		 * based on if the action is rescheduled, overdue
		 * @param  UserAction  $action action to be looked that
		 * @return string      CSS class for the action
		 * @uses UserAction is_rescheduled() -> bg-info | is_overdue() -> bg-warning | is_completed() -> bg-success
		 */
		public function generate_rowclass(UserAction $action) {
			if ($action->actiontype == 'task') {
				if ($action->is_rescheduled()) {
					return 'bg-info';
				}
				if ($action->is_overdue()) {
					return 'bg-warning';
				}
				if ($action->is_completed()) {
					return 'bg-success';
				}
			}
			return '';
		}

		/**
		 * Returns a calendar made with Contento
		 * for the the current month, year
		 * @param  int   $month  2 digit year with zero in front if needed
		 * @param  int   $year   4 digit year
		 * @return string        HTML Table representatino of the calendar
		 * @uses Contento
		 */
		public function generate_calendar($month, $year) {
			$bootstrap = new HTMLWriter();

			$dateComponents = getdate();
			// Create array containing abbreviations of days of week.
			$weekdays = array('Sunday','Monday','Tuesday','Wednesday','Thursday','Friday','Saturday');

			// What is the first day of the month in question?
			$firstday = mktime(0, 0, 0,$month, 1, $year);

			// How many days does this month contain?
			$monthdaycount = date('t', $firstday);

			// Retrieve some information about the first day of the
			// month in question.
			$dateComponents = getdate($firstday);

			// What is the index value (0-6) of the first day of the
			// month in question.
			$weekdayindex = $dateComponents['wday'];

			// Create the table tag opener and day headers
			$tb = new Table('class=calendar table table-condensed table-bordered');

			// Create the calendar headers
			$tb->tablesection('thead');
			$tb->tr();
			foreach ($weekdays as $day) {
				$tb->th('class=header text-center|width=14.3%', $day);
			}
			$tb->closetablesection('thead');

			$tb->tablesection('tbody');
			// Create the rest of the calendar
			// Initiate the day counter, starting with the 1st.
			$currentday = 1;

			// The variable $weekdayindex is used to
			// ensure that the calendar
			// display consists of exactly 7 columns.

			if ($weekdayindex > 0) {
				$tb->td("colspan=$weekdayindex", '&nbsp;');
			}

			$month = str_pad($month, 2, "0", STR_PAD_LEFT);

			while ($currentday <= $monthdaycount) {
				// Seventh column (Saturday) reached. Start a new row.
				if ($weekdayindex == 7) {
					$weekdayindex = 0;
					$tb->tr();
				}

				$currentdaypadded = str_pad($currentday, 2, "0", STR_PAD_LEFT);

				$date = "$month/$currentdaypadded/$year";

				$content = $bootstrap->div('class=day-number text-right', $currentday);
				$class = 'day';
				$listitems = '';

				if ($date == date('m/d/Y')) {
					$class = 'day active';
				}

				if ($this->count_daynotes($date)) {
					$listitems .= $bootstrap->li('role=presentation', '&nbsp;&nbsp;Notes '.$bootstrap->span('class=badge pull-right', $this->count_daynotes($date)).'<br>');
				}

				if ($this->count_dayscheduledtasks($date)) {
					//$listitems .= $bootstrap->li('role=presentation', 'Tasks '.$bootstrap->span('class=pull-right', $this->count_dayscheduledtasks($date)).'<br>');
				}

				if ($this->count_dayincompletedtasks($date)) {
					$listitems .= $bootstrap->li('role=presentation|class=bg-warning', '&nbsp;&nbsp;Incomplete'.$bootstrap->span('class=badge bg-warning pull-right', $this->count_dayincompletedtasks($date)).'<br>');
				}

				if ($this->count_daytasksduebutrescheduled($date)) {
					$listitems .= $bootstrap->li('role=presentation|class=bg-info', '&nbsp;&nbsp;Rescheduled'.$bootstrap->span('class=badge bg-info pull-right', $this->count_daytasksduebutrescheduled($date)).'<br>');
				}

				if ($this->count_daycompletedtasks($date)) {
					$listitems .= $bootstrap->li('role=presentation|class=bg-success', '&nbsp;&nbsp;Completed'.$bootstrap->span('class=badge bg-success pull-right', $this->count_daycompletedtasks($date)).'<br>');
				}

				if ($this->count_dayrescheduledtasks($date)) {
					$listitems .= $bootstrap->li('role=presentation', '&nbsp;&nbsp;Resched on this day'.$bootstrap->span('class=badge bg-info pull-right', $this->count_dayrescheduledtasks($date)).'<br>');
				}

				$list = $bootstrap->ul('class=list-unstyled', $listitems);

				$content .= $bootstrap->div('class=day-list', $list);

				if ($this->count_dayallactions($date) || $this->count_daynotes($date) || $this->count_dayrescheduledtasks($date) || $this->count_dayscheduledtasks($date)) {
					$href = $this->generate_dayviewscheduledtasksurl($date);
					$ajaxdata = $this->generate_ajaxdataforcontento();
					$content .= $bootstrap->div('class=action-bar', $bootstrap->a("href=$href|class=btn btn-xs btn-block btn-primary load-link|$ajaxdata", 'View Actions'));
				}

				$tb->td("class=$class", $content);
				// Increment counters
				$currentday++;
				$weekdayindex++;
			}

			// Complete the row of the last week in month, if necessary
			if ($weekdayindex != 7) {
				$daysremaining = 7 - $weekdayindex;
				$tb->td("colspan=$daysremaining", '&nbsp;');
			}
			$tb->closetablesection('tbody');
			return $bootstrap->div('class=table-responsive', $tb->close());
		}
	}
