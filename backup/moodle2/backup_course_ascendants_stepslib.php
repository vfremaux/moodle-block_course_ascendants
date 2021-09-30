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
 * @subpackage backup-moodle2
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
defined('MOODLE_INTERNAL') || die();

/**
 * Define all the backup steps that wll be used by the backup_page_module_block_task
 */

/**
 * Define the complete forum structure for backup, with file and id annotations
 */
class backup_course_ascendants_block_structure_step extends backup_block_structure_step {

    protected function define_structure() {
        global $DB;

        // Define each element separated.
        $courseascendants = new backup_nested_element('course_ascendants', array('id'), array(
            'courseid', 'blockid', 'metaid', 'locktype', 'lockcmid', 'sortorder'
        ));

        // Build the tree.

        // Define sources.

        $params = [
            'blockid' => $this->task->get_blockid(),
            'courseid' => $this->task->get_courseid()
        ];
        $instances = $DB->get_records('block_course_ascendants', $params);
        $courseascendants->set_source_array($instances);

        // Return the root element (course_ascendants), wrapped into standard block structure.
        return $this->prepare_block_structure($courseascendants);
    }
}
