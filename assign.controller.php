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
 * controller for course assign
 *
 * @package    block
 * @subpackage course_ascendants
 * @copyright  2012 onwards Valery Fremaux (valery.fremaux@gmail.com)
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

if (!defined('MOODLE_INTERNAL')) die ('You cannot use this script this way');

require_once($CFG->dirroot.'/enrol/meta/locallib.php');
require_once($CFG->dirroot.'/enrol/meta/lib.php');

if ($data) {
    $dataarr = (array)$data;
    $inputdata = preg_grep('/^c\d+/', array_keys($dataarr));
    // We know now we got them all (radio buttons).
    if (!empty($inputdata)) {
        foreach ($inputdata as $cid) {
            $metaid = str_replace('c', '', $cid);

            if ($enrol = $DB->get_record('enrol', array('enrol' => 'meta', 'customint1' => $data->course, 'courseid' => $metaid))) {
                $enrol->status = !$dataarr[$cid];
                $DB->update_record('enrol', $enrol);
            } else {
                // If must be attached, make a new meta enrol record and add it to the remote metacourse.
                if ($dataarr[$cid] == 1) {
                    $enrol = new enrol_meta_plugin();
                    $metacourse = $DB->get_record('course', array('id' => $metaid));
                    $enrol->add_instance($metacourse, array('customint1' => $data->course));
                }
            }

            if ($dataarr[$cid] == 1) {
                $allmetas[] = $metaid; // For further group pushing.
            }
            // Sync all users in open metacourses.
            enrol_meta_sync($metaid);
        }
    }

    // Now we sync our course group into opened metacourses.
    // If told to, push the course group whereever it is missing, based on groupname.
    if (@$data->pushnewgroups) {
        foreach ($allmetas as $m) {
            if (!$DB->record_exists('groups', array('courseid' => $m, 'name' => $coursegroup->name))) {
                $metagroup->courseid = $m;
                $metagroup->name = $groupname;
                $metagroup->timecreated = time();
                $metagroup->modified = 0;
                $metagroup->id = $DB->insert_record('groups', $metagroup);
            }

            // Now resync group anyway.
            if ($members = groups_get_members($coursegroup->id)) {
                $context = context_course::instance($m);
                foreach ($members as $u) {
                    // We need check if candidate usr to transfer has real role (was synced bymetacourse).
                    if ($DB->get_records_select('role_assignments', " contextid = $context->id AND userid = $u->id AND hidden = 0 ")) {
                        // Just create if not registered there.
                        if (!$DB->record_exists('groups_members', array('groupid' => $metagroup->id, 'userid' => $u->id))) {
                            $groupmember = new StdClass;
                            $groupmember->groupid = $metagroup->id;
                            $groupmember->userid = $u->id;
                            $groupmember->timeadded = time();
                            $DB->insert_record('groups_members', $groupmember);
                        }
                    }
                }
            }
        }
    }
}
