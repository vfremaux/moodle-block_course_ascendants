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

require_once($CFG->dirroot.'/blocks/course_ascendants/assign.controller.php');

/**
 * Define all the restore steps that wll be used by the restore_page_module_block_task
 */

/**
 * Define the complete course_ascendants structure for restore
 */
class restore_course_ascendants_block_structure_step extends restore_block_instance_structure_step {

    protected function define_structure() {

        $paths = array();
        $paths[] = new restore_path_element('course_ascendants', '/block/courseascendants');

        return $paths;
    }

    /**
     * Note that this WILL NOT work when moving the course to another moodle.
     */
    public function process_course_ascendants($data) {
        global $DB;

        $data = (object) $data;

        $data->courseid = $this->task->get_courseid();
        $data->blockid = $this->task->get_blockid();

        // TODO : If lockcmid points to a course module in the source course (where the block sits), then the cmid should be remapped.
        $lockcmid = $this->get_mappingid('course_modules', $data->lockcmid);
        if ($lockcmid) {
            $data->lockcmid = $lockcmid;
        }

        // Add an instance of meta enrol in the target course using course_ascendants assign controller
        // Meta target may not exist if backup comes from another moodle.
        // TODO Detect backup is exogeneous and do not process rebinding even if some course exists witht his id.
        if ($DB->record_exists('course', ['id' => $data->metaid])) {
            $controller = new \block_course_ascendants\assign_controller();
            $cdata = new Stdclass;
            $cdata->courseid = $data->courseid;
            $cdata->id = $data->blockid;
            $key = 'c'.$data->metaid;
            $cdata->$key = 1;
            $controller->receive('delegatedassign', $cdata);
            $controller->process('delegatedassign');
        }
    }
}
