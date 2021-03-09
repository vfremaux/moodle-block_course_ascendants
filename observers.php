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
 * Event observers used for course ascendants group propagation.
 *
 * @package    block_course_ascendants
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

require_once($CFG->dirroot.'/group/lib.php');

if (!function_exists('debug_trace')) {
    function debug_trace($message, $label = '') {
        assert(1);
    }
}

/**
 * Event observer for block course ascendants.
 */
class block_course_ascendants_observer {

    /**
     * This will purge the recycling register from this course entry.
     * Note : group propagation may be activated Before en meta enrol method has propagated the 
     * enrolment. But should take no much time to fix.
     * @param object $event
     */
    public static function on_group_member_added(\core\event\group_member_added $event) {
        global $DB;

        // Am i in a course_ascendants enabled course.
        $courseid = $DB->get_field('groups', 'courseid', ['id' => $event->groupid]);
        $coursecontext = context_course::instance($courseid);
        $blockinstances = $DB->get_records('block_instances', ['blockname' => 'course_ascendants', 'parentcontextid' => $coursecontext->id]);

        if (!$blockinstances) {
            return;
        }

        $group = $DB->get_record('groups', ['groupid' => $event->groupid]);

        foreach ($blockinstances as $bi) {
            // Is the course_ascendants enabled for group propagation.
            $config = unserialize(base64_decode($bi->configdata));

            if ($config->createcoursegroup != 2) {
                continue;
            }

            // Get ascendants courses.
            $ascendants = $DB->get_records('block_course_ascendants', ['blockid' => $bi->id]);
            if (!$ascendants) {
                continue;
            }

            foreach ($ascendants as $asc) {
                // Check and copy group definitions in ascendants (create them if needed).
                if (!$oldgroup = $DB->get_record('groups', ['name' => $group->name, 'courseid' => $asc->id])) {
                    $newgroup = clone($group);
                    $newgroup->courseid = $asc->id;
                    $ascgroupid = groups_create_group($newgroup);
                } else {
                    $ascgroupid = $oldgroup->id;
                }

                // Add membership in those groups.
                if (!$oldmembership = $DB->get_record('groups_members', ['groupid' => $ascgroupid, 'userid' => $event->relateduserid])) {
                    groups_add_member($ascgroupid, $event->relateduserid, 'block_course_ascendants', $bi->id);
                }
            }
        }
    }
}
