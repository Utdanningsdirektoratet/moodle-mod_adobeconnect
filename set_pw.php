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

require('../../config.php');
require_once('classes/event/adobeconnect_set_pw_form.php');
require_once('locallib.php');

global $DB, $USER, $CFG;

$PAGE->set_context(context_system::instance());
$PAGE->set_url('/mod/adobeconnect/set_pw.php');
$PAGE->set_title(get_string('setpass', 'mod_adobeconnect'));
$PAGE->set_heading(get_string('setpass', 'mod_adobeconnect'));
$thisid = optional_param('id', 0, PARAM_INT); // course_module ID, or

require_login();

$output = $PAGE->get_renderer('core');

$acuser = $USER->username;
if (isset($CFG->adobeconnect_email_login) and !empty($CFG->adobeconnect_email_login)) {
	$acuser = $USER->email;
}

$pass = check_if_userset_pw($acuser);

$values=array();
if($pass){
	$values['currentpassword']=$pass;
}
$values['currentid']=$thisid;
$pwform = new mod_adobeconnect_form(null, $values);
if($thisid>0){
	$_SESSION['toredi']=$CFG->wwwroot.'/mod/adobeconnect/view.php?id='.$thisid;
}

if($pwform->is_cancelled()){
	if(isset($_SESSION['toredi'])&&!empty($_SESSION['toredi'])){
		$sid = $_SESSION['toredi'];
		unset($_SESSION['toredi']);
		redirect(new moodle_url($sid));
		return;
	}
	else{
		redirect(new moodle_url('/index.php'));
		return;
	}
}
else if($data = $pwform->get_data()){

	rewrite_user_password($acuser, trim($data->acpass), 1);
	if(isset($_SESSION['toredi'])&&!empty($_SESSION['toredi'])){
		$sid = $_SESSION['toredi'];
		unset($_SESSION['toredi']);
		redirect(new moodle_url($sid));
		return;
	}
	else{
		redirect(new moodle_url('/index.php'));
		return;
	}
}

if (! $cm = get_coursemodule_from_id('adobeconnect', $thisid)) {
	print_error(get_string('invalidid','mod_adobeconnect'));
}
$context = context_module::instance($thisid);

echo $output->header();

if (has_capability('mod/adobeconnect:meetingpresenter', $context) or
    has_capability('mod/adobeconnect:meetinghost', $context) || $CFG->adobeconn_expose_pass) {
	$pwform->display();
}
else{
	print_error(get_string('nocapability','mod_adobeconnect'));
}

echo $output->footer();



