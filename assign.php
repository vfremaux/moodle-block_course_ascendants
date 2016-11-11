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
 * Block instance editing form.
 *
 * @package    block_course_ascendants
 * @category   blocks
 * @author Moodle 2.x Valery Fremaux <valery.fremaux@gmail.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 *
 * Assign submodules to current course GUI
 */

require('../../config.php');
require_once($CFG->dirroot.'/blocks/course_ascendants/assign_form.php');

$courseid = required_param('course', PARAM_INT) ; // The course id.

if (!$course = $DB->get_record('course', array('id' => $courseid))) {
    print_error('invalidcourseid');
}

$id = required_param('id', PARAM_INT); // The block instance id.

if (!$instance = $DB->get_record('block_instances', array('id' => $id))) {
    print_error('Invalidblockid');
}

$blockinstance = block_instance('course_ascendants', $instance);
$context = context_block::instance($blockinstance->instance->id);

// Security.

require_login($course);
require_capability('block/course_ascendants:configure', $context);

// Check if course group must be created.

if (!isset($blockinstance->config)) {
    $blockinstance->config = new StdClass();
}
if (!isset($blockinstance->config->createcoursegroup)) {
    $blockinstance->config->createcoursegroup = false;
}
if (!isset($blockinstance->config->coursegroupnamebase)) {
    $blockinstance->config->coursegroupnamebase = 0;
}
if (!isset($blockinstance->config->coursegroupnamefilter)) {
    $blockinstance->config->coursegroupnamefilter = '';
}

if (empty($blockinstance->config->coursegroupname)) {
    switch($blockinstance->config->coursegroupnamebase) {
        case 0 :
            $coursebase = $COURSE->fullname;
            break;

        case 1:
            $coursebase = $COURSE->shortname;
            break;

        case 2:
            $coursebase = $COURSE->idnumber;
            break;
    }

    if ($blockinstance->config->coursegroupnamefilter) {
        preg_match('/'.$blockinstance->config->coursegroupnamefilter.'/', $coursebase, $matches);
        if (isset($matches[1])) {
            $groupname = $matches[1];
        } else {
            $groupname = $matches[0];
        }
    } else {
        $groupname = $coursebase;
    }
} else {
    $groupname = $blockinstance->config->coursegroupname;
}

$coursegroup = $DB->get_record('groups', array('name' => $groupname, 'courseid' => $COURSE->id));
if (!$coursegroup) {
    if ($blockinstance->config->createcoursegroup) {
        // Create the group and add all enrolled users in (only direct roles).
        $coursegroup->courseid = $COURSE->id;
        $coursegroup->name = $groupname;
        $coursegroup->timecreated = time();
        $coursegroup->modified = 0;
        $coursegroup->id = $DB->insert_record('groups', $coursegroup);
        $notify = get_string('coursegroupcreated', 'block_course_ascendants');
    }
}

// If finally group exists or come to exist, sync members.

// TODO : integrate new difference of enrolled and assigned users...

if ($coursegroup) {
    // Get all users with direct assignment.
    $context = context_course::instance($COURSE->id);
    $select = " contextid = ? ";
    $fields = 'DISTINCT userid,userid';
    if ($directassignments  = $DB->get_records_select('role_assignments', $select, array($context->id), 'id', $fields)) {
        foreach ($directassignments as $assign) {
            // Add all missing members.
            if (!$DB->record_exists('groups_members', array('groupid' => $coursegroup->id, 'userid' => $assign->userid))) {
                $groupmember = new StdClass;
                $groupmember->groupid = $coursegroup->id;
                $groupmember->userid = $assign->userid;
                $groupmember->timeadded = time();
                $DB->insert_record('groups_members', $groupmember);
            }
        }
    }
}

// Get data.

$url = new moodle_url('/blocks/course_ascendants/assign.php', array('course' => $courseid, 'id' => $id));

$mform = new Assign_Form($url, $blockinstance);

if ($mform->is_cancelled()) {
    redirect(new moodle_url('/course/view.php', array('id' => $courseid)));
}

if ($data = $mform->get_data()) {
    include($CFG->dirroot.'/blocks/course_ascendants/assign.controller.php');
    redirect(new moodle_url('/course/view.php', array('id' => $courseid)));
}

// Print page.

$PAGE->navigation->add(get_string('ascendantsaccess', 'block_course_ascendants'));
$PAGE->set_url($url);
$PAGE->set_title($SITE->shortname.': '.$course->fullname);
$PAGE->set_heading($SITE->shortname.': '.$course->fullname);

echo $OUTPUT->header();

echo $OUTPUT->heading(get_string('manageascendants', 'block_course_ascendants'));

if (!empty($notify)) {
    echo $OUTPUT->notification($notify);
}

if ($ascendants = $blockinstance->get_ascendants(0, false)) {
    $ascendants = array_keys($ascendants);
    $data = new StdClass();
    foreach ($ascendants as $asc) {
        $key = 'c'.$asc;
        $data->$key = 1;
    }
}

$mform->set_data($data);
$mform->display();

echo $OUTPUT->footer($course);