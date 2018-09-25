<?php
    class EditUserActionsDisplay extends UserActionDisplay {
        /* =============================================================
 		   CLASS FUNCTIONS
 	   ============================================================ */
       /**
        * Returns a button row and input that allows the user to select the action type they'd like to create
        * @param  UserAction $action UserAction
        * @return string             HTML buttons with an input  to manipulate
        */
        public function generate_selectsubtype(UserAction $action) {
            $bootstrap = new HTMLWriter();
            $subtypes = DplusWire::wire('pages')->get("/config/actions/types/$action->actiontype/")->children();
            $content = '';
            foreach ($subtypes as $subtype) {
                if ($subtype->name == $action->actionsubtype) {
                    $content .= $bootstrap->create_element('button', "class=btn btn-primary select-button-choice btn-sm|type=button|data-value=$subtype->name", $subtype->subtypeicon . ' '. $subtype->actionsubtypelabel);
                } else {
                    $content .= $bootstrap->create_element('button', "class=btn btn-default select-button-choice btn-sm|type=button|data-value=$subtype->name", $subtype->subtypeicon . ' '. $subtype->actionsubtypelabel);
                }
            }
            $content .= $bootstrap->open('input', "type=hidden|class=select-button-value required|name=subtype|value=$action->actionsubtype");
            return $content;
        }

        /**
         * Creates a Salesperson Dropdown Select
         * @param  string $salespersonID Sales Person to default to
         * @return string                 HTML SELECT
         */
        public function generate_selectsalesperson($salespersonID) {
            $bootstrap = new HTMLWriter();
            $salespersonarray = json_decode(file_get_contents(DplusWire::wire('config')->companyfiles."json/salespersontbl.json"), true);
			$salesids = array_keys($salespersonarray['data']);
			$salespeople = array();

			foreach ($salesids as $salesID) {
				$salespeople[$salespersonarray['data'][$salesID]['splogin']] = $salespersonarray['data'][$salesID]['spname'];
			}
            return $bootstrap->select("name=assignedto|class=form-control input-sm|style=width: 200px;", $salespeople, $salespersonID);
        }

        public function generate_posteditactionurl() {
            return DplusWire::wire('config')->pages->useractions."update/";
        }
    }
