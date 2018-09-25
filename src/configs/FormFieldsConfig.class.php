<?php 
	/**
	 * Used for establishing form rules for forms
	 * like Sales Order Head or Quote Head
	 */
    class FormFieldsConfig {
		use ThrowErrorTrait;
		use MagicMethodTraits;
		
		/**
		 * Form Type
		 * sales-order|quote
		 * @var string
		 */
        protected $formtype;
		
		/**
		 * Array of fields
		 * @example "custid": {
         * 	"label": "Customer ID",
         * 	"required": false,
         * 	"datatype": "C",
         * 	"before-decimal": false,
         * 	"after-decimal": false,
         * 	"date-format": false
         * }
		 * @var array
		 */
        protected $fields = false;
		
		/**
		 * Allowed Config Types
		 * @var array
		 */
        protected $allowedtypes = array('sales-order', 'quote');
		
		/**
		 * Aliases for pages that have the a slightly different name but need the same config
		 * used to load the config without changing values
		 * @var array
		 */
		protected $aliases = array(
			'sales-orders' => 'sales-order',
			'quotes' => 'quote'
		);
		
		/**
		 * Where the Form Config files are located
		 * @var string
		 */
        public static $filedir = false;
        
        /**
         * Class Constructor
         * @param string $formtype Type of Form to load config, ** It can use an aliase
         */
        public function __construct($formtype) {
            $this->formtype = $formtype;
            $this->init($formtype);
        }
		
		/* =============================================================
    		GETTER FUNCTIONS
			MagicMethodTraits
    	============================================================ */
        
        /* =============================================================
    		CLASS FUNCTIONS
    	============================================================ */
		/**
		 * Loads the fields from the config whether from the database or the 
		 * default file
		 * @param  string $formtype type of form ex sales-order | quote
		 */
        protected function init($formtype) {
            if (!in_array($formtype, $this->allowedtypes)) {
				if (!in_array($formtype, array_keys($this->aliases))) {
					$configtype = $this->aliases[$formtype];
					if (does_customerconfigexist($configtype)) {
	                    $this->fields = json_decode(get_customerconfig($formtype), true);
	                } else {
	                    $this->load_file();
	                }
				} else {
					$this->error("$formtype is not a valid form config");
	                return false;
				}
            } else {
                if (does_customerconfigexist($formtype)) {
                    $this->fields = json_decode(get_customerconfig($formtype), true);
                } else {
                    $this->load_file();
                }
            }
        }
        
		/**
		 * Loads the config file then converts the file into an array
		 * Then it assigns $this->fields to that array
		 * @return void
		 */
        public function load_file() {
            if (file_exists(self::$filedir.$this->formtype."-form-fields.json")) {
                $this->fields = json_decode(file_get_contents(self::$filedir.$this->formtype."-form-fields.json"), true);
            } else {
                $this->error("Can't find default config for this formtype.");
            }
		}
        
		/**
		 * Returns if Checkbox for form config field should be fixed
		 * @param  string $key field to load the required value from
		 * @return string      checked | 
		 */
        public function generate_showrequired($key) {
            return $this->fields['fields'][$key]['required'] ? 'checked' : '';
        }
        
		/**
		 * Returns if input should have the required class
		 * @param  string $key field to load the required value from
		 * @return string     required | 
		 */
        public function generate_showrequiredclass($key) {
            return $this->fields['fields'][$key]['required'] ? 'required' : '';
        }
        
		/**
		 * Returns if field should have asterisk for the label
		 * @param  string $key field to load the required value from
		 * @return string      <b class="text-danger">*</b> | 
		 */
        public function generate_asterisk($key) {
            return $this->fields['fields'][$key]['required'] ? '&nbsp;<b class="text-danger">*</b>' : '';
        }
        
		/**
		 * Takes the values from the form and sets the values for the fields
		 * @param  ProcessWire\WireInput $input Object with the input values
		 * @return void        
		 */
        public function generate_configfrominput(ProcessWire\WireInput $input) {
            foreach ($this->fields['fields'] as $key => $field) {
                $this->fields['fields'][$key]['label'] = $input->post->text("$key-label");
                $this->fields['fields'][$key]['before-decimal'] = strlen($input->post->text("$key-before-decimal")) ? $input->post->text("$key-before-decimal") : false;
                $this->fields['fields'][$key]['after-decimal'] = strlen($input->post->text("$key-after-decimal")) ? $input->post->text("$key-after-decimal") : false;
                $this->fields['fields'][$key]['required'] = strlen($input->post->text("$key-required")) ? true : false;
            }
        }
		
		/**
		 * Sets the Form Config file directory
		 * @param string $dir path/to/dir
		 */
		public static function set_defaultconfigdirectory($dir) {
			self::$filedir = $dir;
		}
		
		/* =============================================================
    		CRUD FUNCTIONS
    	============================================================ */
		/**
		 * Saves / Updates to the database
		 * Checks if it already exists in database, then it saves accordingly
		 * @return string SQL QUERY STIRNG
		 * @uses
		 */
        public function save() {
            if (does_customerconfigexist($this->formtype)) {
                return update_customerconfig($this->formtype, json_encode($this->fields), $debug = false);
            } else {
                return create_customerconfig($this->formtype, json_encode($this->fields), $debug = false);
            }
        }
        
		/**
		 * Uses the save() function, then it returns an array response
		 * that can be easily used converted to JSON
		 * @return array Response array with response values and messages
		 * @uses
		 */
        public function save_andrespond() {
            $response = $this->save();
            if ($response['success']) {
                $msg = "Your form ($this->formtype) configuration has been saved";
                $json = array (
                    'response' => array (
                        'error' => false,
                        'notifytype' => 'success',
                        'action' => $response['querytype'],
                        'message' => $msg,
                        'icon' => 'glyphicon glyphicon-floppy-disk',
                    )
                );
            } else {
                $msg = "Your configuration ($this->formtype) was not able to be saved, you may have not made any discernable changes.";
                $json = array (
                    'response' => array (
                        'error' => true,
                        'notifytype' => 'danger',
                        'action' => $response['querytype'],
                        'message' => $msg,
                        'icon' => 'glyphicon glyphicon-warning-sign',
                        'sql' => $response['sql']
                    )
                );
            }
            return $json;
        }
    }
