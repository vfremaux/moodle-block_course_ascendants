<?php
// This file keeps track of upgrades to 
// the dashboard block
//
// Sometimes, changes between versions involve
// alterations to database structures and other
// major things that may break installations.
//
// The upgrade function in this file will attempt
// to perform all the necessary actions to upgrade
// your older installtion to the current version.
//
// If there's something it cannot do itself, it
// will tell you what you need to do.
//
// The commands in here will all be database-neutral,
// using the functions defined in lib/ddllib.php

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

            if ($ascendants = $block->get_all_ascendants($block->config->coursescopestartcategory, false, $parentcontext->instanceid)) {
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