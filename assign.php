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

$courseid = required_param('course', PARAM_INT); // The course id.

if (!$course = $DB->get_record('course', array('id' => $courseid))) {
    print_error('invalidcourseid');
}

require_login($course);

$id = required_param('id', PARAM_INT); // The block instance id.

if (!$instance = $DB->get_record('block_instances', array('id' => $id))) {
    print_error('Invalidblockid');
}

$blockinstance = block_instance('course_ascendants', $instance);
$context = context_block::instance($blockinstance->instance->id);

$params = array('course' => $courseid, 'id' => $id);
$url = new moodle_url('/blocks/course_ascendants/assign.php', $params);
$PAGE->set_url($url);

// Security.

require_capability('block/course_ascendants:configure', $context);

// Check if course group must be created.

if (!isset($blockinstance->config)) {
    $blockinstance->config = new StdClass();
}

if (!isset($blockinstance->config->createcoursegroup)) {
    $blockinstance->config->createcoursegroup = false;
}

// Get data.

$url = new moodle_url('/blocks/course_ascendants/assign.php', ['course' => $courseid, 'id' => $id]);

$categories = [];
if (empty($blockinstance->config->coursescopestartcategory)) {
    $firstcats = $DB->get_records('course_categories', ['parent' => 0], 'sortorder', '*', 0, 1);
    $firstcat = array_shift($firstcats);
    $blockinstance->config->coursescopestartcategory = $firstcat->id;
}
$blockinstance->read_category_tree($blockinstance->config->coursescopestartcategory, $categories, true, true);

$mform = new course_ascendants_assign_form($url, $blockinstance, $categories);

if ($mform->is_cancelled()) {
    redirect(new moodle_url('/course/view.php', array('id' => $courseid)));
}

$action = optional_param('what', false, PARAM_TEXT);

if ($action) {
    // Get all returned data in a stub.
    $data = $mform->get_data();
    include($CFG->dirroot.'/blocks/course_ascendants/assign.controller.php');
    $controller = new \block_course_ascendants\assign_controller();
    $controller->receive($action, $data);
    if ($redirecturl = $controller->process($action)) {
        redirect($redirecturl);
    }
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

$formdata = new StdClass;
if (!empty($categories)) {
    foreach ($categories as $cat) {
        if (!empty($cat->courses)) {
            foreach ($cat->courses as $cid => $course) {
                $key = 'c'.$cid;
                $lockkey = 'l'.$cid;
                $cmlockkey = 'lockcm'.$cid;
                if ($course->isbound) {
                    $formdata->$key = 1;
                }
                $formdata->$lockkey = $course->locktype;
                $formdata->$cmlockkey = ($course->lockcmid) ? $course->lockcmid : '';
            }
        }
    }
}

$mform->set_data($formdata);
$mform->display();

echo $OUTPUT->footer();