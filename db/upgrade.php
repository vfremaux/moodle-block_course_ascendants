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

defined('MOODLE_INTERNAL') || die();

function xmldb_block_course_ascendants_upgrade($oldversion = 0) {
    global $CFG, $DB;

    $dbman = $DB->get_manager();

    $result = true;

    // Moodle 2.0 break line

    if ($oldversion < 2015101601) {

        // Convert settings to plugin scope settings.
        set_config('defaultcreatecoursegroup', $CFG->block_ascendants_defaultcreatecoursegroup, 'block_course_ascendants');
        set_config('coursegroupnamebase', $CFG->block_ascendants_coursegroupnamebase, 'block_course_ascendants');
        set_config('coursegroupnamefilter', $CFG->block_ascendants_coursegroupnamefilter, 'block_course_ascendants');
        set_config('block_ascendants_defaultcreatecoursegroup', null);
        set_config('block_ascendants_coursegroupnamebase', null);
        set_config('block_ascendants_coursegroupnamefilter', null);

        // Course Ascendants savepoint reached.
        upgrade_block_savepoint(true, 2015101601, 'course_ascendants');
    }

    if ($oldversion < 2015111414) {

        // Define table block_course_ascendants to be created.
        $table = new xmldb_table('block_course_ascendants');

        // Adding fields to table block_course_ascendants.
        $table->add_field('id', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, XMLDB_SEQUENCE, null);
        $table->add_field('blockid', XMLDB_TYPE_INTEGER, '11', null, XMLDB_NOTNULL, null, '0');
        $table->add_field('courseid', XMLDB_TYPE_INTEGER, '11', null, XMLDB_NOTNULL, null, '0');
        $table->add_field('sortorder', XMLDB_TYPE_INTEGER, '4', null, XMLDB_NOTNULL, null, '0');

        // Adding keys to table block_course_ascendants.
        $table->add_key('primary', XMLDB_KEY_PRIMARY, array('id'));

        // Adding indexes to table block_course_ascendants.
        $table->add_index('ix_unique_course_block', XMLDB_INDEX_UNIQUE, array('blockid', 'courseid'));

        // Conditionally launch create table for block_course_ascendants.
        if (!$dbman->table_exists($table)) {
            $dbman->create_table($table);
        }

        block_ascendants_feed_instances();

        // Course_ascendants savepoint reached.
        upgrade_block_savepoint(true, 2015111414, 'course_ascendants');
    }

    return $result;
}

function block_ascendants_feed_instances() {
    global $DB;

    $instances = $DB->get_records('block_instances', array('blockname' => 'course_ascendants'));
    if ($instances) {

        // Full remap all sortorders.
        $DB->delete_records('block_course_ascendants', array());

        foreach ($instances as $bi) {
            $block = block_instance('course_ascendants', $bi);
            echo '<pre>';
            mtrace('Upgrading instance '.$bi->id);

            // Parent context holds the relevant course id as instance.
            $parentcontext = context::instance_by_id($bi->parentcontextid, MUST_EXIST);

            if (empty($block->config->coursescopestartcategory)) {
                mtrace('Skipping instance : No category setup.');
                echo '</pre>';
                continue;
            }

            $catscope = $block->config->coursescopestartcategory;
            if ($ascendants = $block->get_all_ascendants($catscope, false, $parentcontext->instanceid)) {
                mtrace('   Upgrading instance ascendants... ');
                $ix = 0;
                foreach ($ascendants as $asc) {
                    mtrace('      Registering instance ascendant '.$asc->id);
                    $rec = new StdClass;
                    $rec->blockid = $block->instance->id;
                    $rec->courseid = $asc->id;
                    $rec->sortorder = $ix;
                    $DB->insert_record('block_course_ascendants', $rec);
                    $ix++;
                }
            }
            echo '</pre>';
        }
    }
}