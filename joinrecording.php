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
 * @package    mod_adobeconnect
 * @author     Akinsaya Delamarre (adelamarre@remote-learner.net)
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @copyright  (C) 2015 Remote Learner.net Inc http://www.remote-learner.net
 * 
 * The purpose of this file is to add a log entry when the user views a
 * recording
 */

require_once(dirname(dirname(dirname(__FILE__))).'/config.php');
require_once(dirname(__FILE__).'/locallib.php');
require_once(dirname(__FILE__).'/connect_class.php');
require_once(dirname(__FILE__).'/connect_class_dom.php');

// Adobeconnect Admin settings
$settings_ac_host =  get_config('adobeconnect', 'adobeconnect_host');
$settings_ac_meethost =  get_config('adobeconnect', 'adobeconnect_meethost');
$settings_ac_port =  get_config('adobeconnect', 'adobeconnect_port');
$settings_ac_admin_login =  get_config('adobeconnect', 'adobeconnect_admin_login');
$settings_ac_admin_password =  get_config('adobeconnect', 'adobeconnect_admin_password');

$id         = required_param('id', PARAM_INT);
$groupid    = required_param('groupid', PARAM_INT);
$recscoid   = required_param('recording', PARAM_INT);

global $CFG, $USER, $DB;

// Do the usual Moodle setup
if (! $cm = get_coursemodule_from_id('adobeconnect', $id)) {
    error('Course Module ID was incorrect');
}
$cond = array('id' => $cm->course);
if (! $course = $DB->get_record('course', $cond)) {
    error('Course is misconfigured');
}

$cond = array('id' => $cm->instance);
if (! $adobeconnect = $DB->get_record('adobeconnect', $cond)) {
    error('Course module is incorrect');
}

require_login($course, true, $cm);

// ---------- //


// Get HTTPS setting
$https      = false;
$protocol   = 'http://';
if (isset($CFG->adobeconnect_https) and (!empty($CFG->adobeconnect_https))) {
    $https      = true;
    $protocol   = 'https://';
}

// Check if the user's email is the Connect Pro user's login
$usrobj = new stdClass();
$usrobj = clone($USER);
/**** START Auto-Login ****/
if (isset($CFG->adobeconnect_email_login) and
!empty($CFG->adobeconnect_email_login)) {
$usrobj->username = obfuscatedEmail($usrobj->email, $usrobj->id);
}

$usrobj->password = aconnect_create_user_password($usrobj->email);

if ( $usrobj->username == $settings_ac_admin_login ) {
  $usrobj->password = $settings_ac_admin_password;
} 

/***** END Auto-Login ************/
/*$usrobj->username = set_username($usrobj->username, $usrobj->email);

$usrcanjoin = false;
*/

$params = array('instanceid' => $cm->instance, 'groupid' => $groupid);
$sql = "SELECT meetingscoid FROM {adobeconnect_meeting_groups} amg WHERE ".
       "amg.instanceid = :instanceid AND amg.groupid = :groupid";

$meetscoid = $DB->get_record_sql($sql, $params);

// Get the Meeting recording details
$aconnect   = aconnect_login();
$recording  = array();
$fldid      = aconnect_get_folder($aconnect, 'content');
//$usrcanjoin = false;
$context    = context_module::instance(CONTEXT_MODULE, $cm->id);
$data       = aconnect_get_recordings($aconnect, $fldid, $meetscoid->meetingscoid);


/// Set page global
$url = new moodle_url('/mod/adobeconnect/view.php', array('id' => $cm->id));

$PAGE->set_url($url);
$PAGE->set_context($context);
$PAGE->set_title(format_string($adobeconnect->name));
$PAGE->set_heading($course->fullname);

if (!empty($data) && array_key_exists($recscoid, $data)) {

    $recording = $data[$recscoid];
} else {

    // If at first you don't succeed ...
    $data2 = aconnect_get_recordings($aconnect, $meetscoid->meetingscoid, $meetscoid->meetingscoid);

    if (!empty($data2) && array_key_exists($recscoid, $data2)) {
        $recording = $data2[$recscoid];
    }
}

/** deleted**/
//aconnect_logout($aconnect);

if (empty($recording) and confirm_sesskey()) {
    notify(get_string('errormeeting', 'adobeconnect'));
/** added **/
aconnect_logout($aconnect);
    die();
}

// If separate groups is enabled, check if the user is a part of the selected group
if (NOGROUPS != $cm->groupmode) {
    $usrgroups = groups_get_user_groups($cm->course, $USER
    ->id);
    $usrgroups = $usrgroups[0]; // Just want groups and not groupings

    $group_exists = false !== array_search($groupid, $usrgroups);
    $aag          = has_capability('moodle/site:accessallgroups', $context);

    if ($group_exists || $aag) {
        $usrcanjoin = true;
    }
} else {
    $usrcanjoin = true;
}


if (!$usrcanjoin) {
    notice(get_string('usergrouprequired', 'adobeconnect'), $url);
} else {
    //If a recording is private, it won't allow access to those who aren't participants
    // i.e. haven't previously joined the meeting.
    // In that case, create user if necessary and assign lowest required permission to see recording
    if (!($usrprincipal = aconnect_user_exists($aconnect, $usrobj))) {
        if (!($usrprincipal = aconnect_create_user($aconnect, $usrobj))) {
            debugging("error creating user", DEBUG_DEVELOPER);
        }
    }
    if (!aconnect_check_user_perm($aconnect, $usrprincipal, $meetscoid->meetingscoid, ADOBE_HOST) && !aconnect_check_user_perm($aconnect, $usrprincipal, $meetscoid->meetingscoid, ADOBE_PRESENTER)) {
        if (!aconnect_check_user_perm($aconnect, $usrprincipal, $meetscoid->meetingscoid, ADOBE_PARTICIPANT, true)) {
          debugging('Error assigning user adobe participant role', DEBUG_DEVELOPER);
        }
    }
}

aconnect_logout($aconnect);

// Trigger an event for viewing a recording.
$params = array(
    'relateduserid' => $USER->id,
    'courseid' => $course->id,
    'context' => context_module::instance($id),
);
$event = \mod_adobeconnect\event\adobeconnect_view_recording::create($params);
$event->trigger();

// Include the port number only if it is a port other than 80
$port = '';

if (!empty($settings_ac_port) and (80 != $settings_ac_port)) {
    $port = ':' . $settings_ac_port;
}

$aconnect = new connect_class_dom($settings_ac_host, $settings_ac_port, '', '', '', $https);

$password = $usrobj->password;
$login= $usrobj->username;

//$aconnect->request_http_header_login(1, $login);

//============ START Auto-===================>
if ( $CFG->adobeconnect_login_type == 'httpauth' ) {

$aconnect->request_http_header_login(1, $login);
} else {
$aconnect->request_user_login($login, $password);
$test=check_if_user_logged_in($aconnect);

}

//============ END Auto-Login ===================|
$adobesession = $aconnect->get_cookie();

$redirlink = $protocol.$settings_ac_meethost.$port.$meeting['url']."?session=".$aconnect->get_cookie();

if(!$test){
	echo "<script type='text/javascript'>alert('".get_string('couldnoterror','mod_adobeconnect')."');window.location='$redirlink';</script>";
	exit;
}

redirect($protocol . $settings_ac_meethost . $port . $recording['url'] . '?session=' . $aconnect->get_cookie());
