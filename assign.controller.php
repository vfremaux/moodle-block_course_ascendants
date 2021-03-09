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
namespace block_course_ascendants;

use StdClass;
use coding_exception;
use enrol_meta_plugin;
use moodle_url;
use block_course_ascendants_pro;

defined('MOODLE_INTERNAL') || die();

require_once($CFG->dirroot.'/enrol/meta/locallib.php');
require_once($CFG->dirroot.'/enrol/meta/lib.php');
require_once($CFG->dirroot.'/blocks/course_ascendants/listlib.php');
require_once($CFG->dirroot.'/group/lib.php');

class assign_controller {

    protected $blockinstance;

    public function receive($cmd, $data, $mform = false) {
        global $DB;

        if ($cmd == 'assign') {
            $this->data = $data;
            $this->data->courseid = required_param('course', PARAM_INT);
            $this->data->id = required_param('id', PARAM_INT); // blockid.
            if (!$instance = $DB->get_record('block_instances', array('id' => $this->data->id))) {
                print_error('Invalidblockid');
            }
            $this->blockinstance = block_instance('course_ascendants', $instance);
        }

        $this->received = true;
    }

    public function process($cmd) {
        global $DB, $CFG;

        if (!$this->received) {
            throw new coding_exception('Data must be received in controller before operation. this is a programming error.');
        }

        if ($cmd == 'assign') {

            // Get all activated metas.
            $dataarr = (array)$this->data;
            $metaids = preg_grep('/^c\d+/', array_keys($dataarr));
            $allmetas = [];

            // Prepare next order for insertion.
            $lastorder = list_last_order($this->data->id);
            if (is_null($lastorder)) {
                $neworder = 0;
            } else {
                $neworder = $lastorder + 1;
            }

            // We know now we got them all (radio buttons).
            if (!empty($metaids)) {
                foreach ($metaids as $cid) {
                    $metaid = str_replace('c', '', $cid);
                    $metastate = $dataarr[$cid];
                    echo "$metaid/$metastate ";

                    // inverts meaning of $metiad/courseid for enrol as we enrol in the meta course.
                    $params = array('enrol' => 'meta', 'customint1' => $this->data->courseid, 'courseid' => $metaid);
                    if ($enrol = $DB->get_record('enrol', $params)) {
                        // If meta enrolled, disable enrolment and strip course off the learning plan.
                        $enrol->status = !$dataarr[$cid];
                        $enrol->customint1 = $this->data->courseid;
                        if (block_course_ascendants_supports_feature('group/propagate')) {
                            if ($this->blockinstance->config->createcoursegroup) {
                                include_once($CFG->dirroot.'/blocks/course_ascendants/pro/lib.php');
                                $proinstance = new block_course_ascendants_pro($this->blockinstance);
                                $groupid = $proinstance->check_and_create_course_group($this->data->courseid, $metaid);
                                // Force enrol plugin to syc user into the cursus group.
                                $enrol->customint2 = $groupid;
                            }
                        }
                        $DB->update_record('enrol', $enrol);
                        if (!$dataarr[$cid]) {
                            // Not any more an attached module.
                            $params = array('blockid' => $this->data->id, 'metaid' => $metaid);
                            if ($DB->record_exists('block_course_ascendants', $params)) {
                                list_remove($this->data->id, $metaid);
                            }
                        } else {
                            $localrec = new StdClass();
                            $localrec->blockid = $this->data->id;
                            $localrec->courseid = $this->data->courseid; // the cursus id
                            $localrec->metaid = $metaid; // the learning ascendant module.
                            $localrec->sortorder = $neworder;
                            $params = array('blockid' => $this->data->id, 'metaid' => $metaid);
                            if (!$DB->record_exists('block_course_ascendants', $params)) {
                                $DB->insert_record('block_course_ascendants', $localrec);
                                $neworder++;
                            }
                        }
                    } else {
                        // If must be attached, make a new meta enrol record and add it to the remote metacourse.
                        if ($dataarr[$cid] == 1) {
                            $enrolplugin = new enrol_meta_plugin();
                            $metacourse = $DB->get_record('course', ['id' => $metaid]);
                            $params = ['customint1' => $this->data->courseid];
                            if (block_course_ascendants_supports_feature('group/propagate')) {
                                if ($this->blockinstance->config->createcoursegroup) {
                                    include_once($CFG->dirroot.'/blocks/course_ascendants/pro/lib.php');
                                    $proinstance = new block_course_ascendants_pro($this->blockinstance);
                                    $groupid = $proinstance->check_and_create_course_group($this->data->courseid, $metaid);
                                    // Force enrol plugin to syc user into the cursus group.
                                    $params['customint2'] = $groupid;
                                }
                            }
                            $enrolplugin->add_instance($metacourse, $params);

                            $localrec = new StdClass();
                            $localrec->blockid = $this->data->id;
                            $localrec->courseid = $this->data->courseid; // The cursus id
                            $localrec->metaid = $metaid; // The ascendant learning module id
                            $localrec->sortorder = $neworder;
                            if (!$DB->record_exists('block_course_ascendants', array('blockid' => $this->data->id, 'metaid' => $metaid))) {
                                $DB->insert_record('block_course_ascendants', $localrec);
                                $neworder++;
                            }
                        } else {
                            if ($DB->record_exists('block_course_ascendants', array('blockid' => $this->data->id, 'metaid' => $metaid))) {
                                list_remove($this->data->id, $metaid);
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
            if (!empty($data->pushnewgroups)) {
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
            return new moodle_url('/course/view.php', ['id' => $this->data->courseid]);
        }
    }
}
