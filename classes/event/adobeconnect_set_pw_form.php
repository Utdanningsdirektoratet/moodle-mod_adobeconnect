<?php
// This file is part of Moodle - http://moodle.org/
//
// Moodle is free software: you can redistribute it and/or modify
// it under the terms of the GNU General Public License as published by
// the Free Software Foundation, either version 3 of the License, or
// (at your option) any later version.
//
// Moodle is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
// GNU General Public License for more details.
//
// You should have received a copy of the GNU General Public License
// along with Moodle.  If not, see <http://www.gnu.org/licenses/>.

/**
 * @package    mod_adobeconnect_maintained
 * @author     Carsten Nielsen
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @copyright  (C) 2022 Utdanningsdirektoratet https://udir.no
 */

require_once("$CFG->libdir/formslib.php");
class mod_adobeconnect_form extends moodleform{
	function definition() {
		global $CFG;

		$mform = $this->_form;
		$mform->addElement('html', "<div class='onlyforac'>".get_string('onlyforexplain','mod_adobeconnect')." </div>");
		$mform->addElement('passwordunmask', 'acpass', get_string('myacpass', 'mod_adobeconnect'), array('class' => 'aconsetpw'));

		if(isset($this->_customdata['currentpassword'])){
			$mform->setDefault('acpass', $this->_customdata['currentpassword']);
		}

		$mform->addElement('html',"<div class='acaction'>");
		$this->add_action_buttons();
		$mform->addElement('html',"</div>");
		$conid=$this->_customdata['currentid'];

		$mform->addElement('html', "<a href='reset_pw.php?id=$conid'><div>".get_string('respass','mod_adobeconnect')." </div></a>");
	}
}
class mod_ac_reset_form extends moodleform{
	function definition(){
		global $CFG;
		$mform = $this->_form;
		$mform->addElement('html', "<div class='resetstring'>".get_string('resetwarning','mod_adobeconnect')."</div>");
		$mform->addElement('html', "<div class='onlyforac'>".get_string('resetdont','mod_adobeconnect')."</div>");

		$mform->addElement('html',"<div class='acaction'>");
		$this->add_action_buttons(true, get_string('reset'));
		$mform->addElement('html',"</div>");
	}
}
