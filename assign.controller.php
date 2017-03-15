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
 * @package    block_course_ascendants
 * @category   blocks
 * @author     Moodle 2.x Valery Fremaux <valery.fremaux@gmail.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 *
 * controller for course assignation
 */
defined('MOODLE_INTERNAL') || die();

require_once($CFG->dirroot.'/enrol/meta/locallib.php');
require_once($CFG->dirroot.'/enrol/meta/lib.php');
require_once($CFG->dirroot.'/blocks/course_ascendants/listlib.php');

if ($data) {
    $dataarr = (array)$data;
    $inputdata = preg_grep('/^c\d+/', array_keys($dataarr));
    $allmetas = array();

    // Prepare next order for insertion.
    $lastorder = list_last_order($id);
    if (is_null($lastorder)) {
        $neworder = 0;
    } else {
        $neworder = $lastorder + 1;
    }

    // We know now we got them all (radio buttons).
    if (!empty($inputdata)) {
        foreach ($inputdata as $cid) {
            $metaid = str_replace('c', '', $cid);

            $params = array('enrol' => 'meta', 'customint1' => $data->course, 'courseid' => $metaid);
            if ($enrol = $DB->get_record('enrol', $params)) {
                $enrol->status = !$dataarr[$cid];
                $DB->update_record('enrol', $enrol);
                if (!$dataarr[$cid]) {
                    // Not any more an attached module.
                    if ($DB->record_exists('block_course_ascendants', array('blockid' => $id, 'courseid' => $metaid))) {
                        list_remove($id, $metaid);
                    }
                } else {

                    $localrec = new StdClass();
                    $localrec->blockid = $id;
                    $localrec->courseid = $metaid;
                    $localrec->sortorder = $neworder;
                    if (!$DB->record_exists('block_course_ascendants', array('blockid' => $id, 'courseid' => $metaid))) {
                        $DB->insert_record('block_course_ascendants', $localrec);
                        $neworder++;
                    }
                }
            } else {
                // If must be attached, make a new meta enrol record and add it to the remote metacourse.
                if ($dataarr[$cid] == 1) {
                    $enrol = new enrol_meta_plugin();
                    $metacourse = $DB->get_record('course', array('id' => $metaid));
                    $enrol->add_instance($metacourse, array('customint1' => $data->course));

                    $localrec = new StdClass();
                    $localrec->blockid = $id;
                    $localrec->courseid = $metaid;
                    $localrec->sortorder = $neworder;
                    if (!$DB->record_exists('block_course_ascendants', array('blockid' => $id, 'courseid' => $metaid))) {
                        $DB->insert_record('block_course_ascendants', $localrec);
                        $neworder++;
                    }
                }
            }

            if ($dataarr[$cid] == 1) {
                // For further group pushing.
                $allmetas[] = $metaid;
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
                    $select = " contextid = ? AND userid = ? AND hidden = 0 ";
                    if ($DB->get_records_select('role_assignments', $select, array($context->id, $u->id))) {
                        // Just create if not registered there.
                        $params = array('groupid' => $metagroup->id, 'userid' => $u->id);
                        if (!$DB->record_exists('groups_members', $params)) {
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
