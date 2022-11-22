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
    function debug_trace($msg, $tracelevel = 0, $label = '', $backtracelevel = 1) {
        assert(1);
    }
}

/**
 * Event observer for block course ascendants.
 */
class block_course_ascendants_observer {

    /**
     * Note : group propagation may be activated Before en meta enrol method has propagated the 
     * enrolment. But should take no much time to fix.
     * @param object $event
     */
    public static function on_group_member_added(\core\event\group_member_added $event) {
        global $DB;

        // Am i in a course_ascendants enabled course.
        $courseid = $DB->get_field('groups', 'courseid', ['id' => $event->objectid]);
        $coursecontext = context_course::instance($courseid);
        $blockinstances = $DB->get_records('block_instances', ['blockname' => 'course_ascendants', 'parentcontextid' => $coursecontext->id]);

        if (!$blockinstances) {
            return;
        }

        $group = $DB->get_record('groups', ['groupid' => $event->objectid]);

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
                if (!$oldgroup = $DB->get_record('groups', ['name' => $group->name, 'courseid' => $asc->metaid])) {
                    $newgroup = clone($group);
                    unset($newgroup->id);
                    $newgroup->courseid = $asc->metaid;
                    $newgroup->enrolmentkey = '';
                    debug_trace("Creating ascendant group {$newgroup->name} ", TRACE_DEBUG_FINE);
                    debug_trace($newgroup, TRACE_DEBUG);
                    $ascgroupid = groups_create_group($newgroup);
                } else {
                    debug_trace("Using old ascendant group {$oldgroup->name} ", TRACE_DEBUG_FINE);
                    $ascgroupid = $oldgroup->id;
                }

                // Add membership in those groups.
                if (!$oldmembership = $DB->get_record('groups_members', ['groupid' => $ascgroupid, 'userid' => $event->relateduserid])) {
                    debug_trace("Registering {$event->relateduserid} as member {$ascgroupid} ", TRACE_DEBUG_FINE);
                    groups_add_member($ascgroupid, $event->relateduserid, 'block_course_ascendants', $bi->id);
                }
            }
        }
    }

    public static function on_group_member_removed(\core\event\group_member_removed $event) {
        global $DB;

        debug_trace("Group memnber removed received ", TRACE_DEBUG_FINE);
        // Am i in a course_ascendants enabled course.
        $coursecontext = context_course::instance($event->courseid);
        $blockinstances = $DB->get_records('block_instances', ['blockname' => 'course_ascendants', 'parentcontextid' => $coursecontext->id]);

        if (!$blockinstances) {
            debug_trace("No course ascendants in context ", TRACE_DEBUG_FINE);
            return;
        }

        debug_trace("Has course ascendants in context ", TRACE_DEBUG_FINE);
        $group = $DB->get_record('groups', ['id' => $event->objectid]);

        foreach ($blockinstances as $bi) {
            // Is the course_ascendants enabled for group propagation.
            $config = unserialize(base64_decode($bi->configdata));

            if (empty($config->createcoursegroup) && $config->createcoursegroup != 2) {
                debug_trace("Course ascendants {$bi->id} not configured for propagation ", TRACE_DEBUG_FINE);
                continue;
            }

            // Get ascendants courses.
            $ascendants = $DB->get_records('block_course_ascendants', ['blockid' => $bi->id]);
            if (!$ascendants) {
                debug_trace("Course ascendants with no moduled ", TRACE_DEBUG_FINE);
                continue;
            }

            debug_trace("Processing ascendants on {$bi->id}  ", TRACE_DEBUG_FINE);
            foreach ($ascendants as $asc) {
                // Check and copy group definitions in ascendants (create them if needed).
                if ($oldgroup = $DB->get_record('groups', ['name' => $group->name, 'courseid' => $asc->metaid])) {
                    // Remove membership in those groups.
                    debug_trace("Unregistering {$event->relateduserid} from group {$oldgroup->id} ", TRACE_DEBUG_FINE);
                    groups_remove_member($oldgroup->id, $event->relateduserid);
                }
            }
        }
    }

    public static function on_course_delete(\core\event\course_deleted $event) {
        global $DB;

        // Just remove course_ascendants block data. Enrol methods are processed by core observers.
        // When a course is removed that is a meta submodule, its enrol methods will be destroyed and
        // course_ascendants master courses should be unlinked.
        $DB->delete_records('block_course_ascendants', ['metaid' => $event->objectid]);

        // Just remove course_ascendants block data. Enrol methods are processed by core observers.
        // When a master course is deleted, then all meta submodules will be stripped off. 
        $DB->delete_records('block_course_ascendants', ['courseid' => $event->objectid]);
    }
}
