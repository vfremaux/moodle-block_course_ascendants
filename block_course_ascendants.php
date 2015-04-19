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
 * the block master class
 *
 * @package    block
 * @subpackage course_ascendants
 * @copyright  2012 onwards Valery Fremaux (valery.fremaux@gmail.com)
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

class block_course_ascendants extends block_base {

    function init() {
        $this->title = get_string('title', 'block_course_ascendants');
    }

    function has_config() {
        return true;
    }

    function applicable_formats() {
        return array('all' => true, 'mod' => false, 'tag' => false);
    }

    function instance_allow_config() {
        return true;
    }

    function instance_allow_multiple() {
        return false;
    }

    function get_content() {
        global $THEME, $CFG, $COURSE;

        if ($this->content !== NULL) {
            return $this->content;
        }

        if (!isset($this->config)) {
            $this->config = new StdClass();
        }

        if (!isset($this->config->showcategories)) {
            $this->config->showcategories = false;
        }
        $coursecontext = context_course::instance($COURSE->id);

        // Fetch direct ascendants that are metas who point the current course as descendant.
        $categories = array();
        $this->read_category_tree(0 + @$this->config->coursescopestartcategory, $categories);

        $this->content = new stdClass;
        $this->content->footer = '';

        if ($categories) {
            foreach ($categories as $cat) {
                if ($this->config->showcategories && !empty($cat->courses)) {
                    $filteredprevcat = $cat->name;
                    if (!empty($this->config->catstringfilter)) {
                        $pattern = $this->config->catstringfilter;
                        preg_match("/$pattern/", $filteredprevcat, $matches);
                        $filteredprevcat = $matches[0];
                    }
                    $this->content->text .= '<div class="category"><b>'.$filteredprevcat.'</b></div>';
                }
                if (!empty($cat->courses)) {
                    $this->content->text .= '<div clas="courses">';
                    foreach ($cat->courses as $ascendant) {
                        $this->content->text .= '<div class="coursebox">';
                        $this->content->text .= '<div class="info">';
                        $this->content->text .= '<div class="name">';
                        if (!empty($this->config->stringlimit)) {
                            $fullname = shorten_text($ascendant->fullname, $this->config->stringlimit);
                        } else {
                            $fullname = $ascendant->fullname;
                        }
                        $courseurl = new moodle_url('/course/view.php', array('id' => $ascendant->id));
                        $this->content->text .= '<a title="'.s($ascendant->fullname).' href="'.$courseurl.'">'.format_string($fullname).'</a>';
                        $this->content->text .= '</div>';
                        $this->content->text .= '</div>';
                        $this->content->text .= '</div>';
                    }
                    $this->content->text .= '</div>';
                }
            }
        }
        if (has_capability('moodle/course:manageactivities', $coursecontext)) {
            $manageascendantsstr = get_string('manageascendants', 'block_course_ascendants');
            $assignurl = new moodle_url('/blocks/course_ascendants/assign.php', array('course' => $COURSE->id, 'id' => $this->instance->id, 'sesskey' => sesskey()));
            $this->content->footer = '<a href="'.$assignurl.'">'.$manageascendantsstr.'</a>';
        }
        return $this->content;
    }

    /**
     * reads category tree in correct order
     */
    function read_category_tree($catstart, &$categories, $seeunbound = false, $seeinvisible = false) {
        global $CFG, $COURSE, $DB;
        static $level = 0;

        if ($catstart != 0 && $level == 0) {
            $cat = $DB->get_record('course_categories', array('id' => $catstart), 'id,name,visible');
            if (!$cat->visible && !$seeinvisible) continue;
            if ($ascendants = $this->get_ascendants($catstart, $seeunbound)) {
                $cat->courses = array();
                foreach($ascendants as $asc) {
                    $context = context_course::instance($asc->id);
                    if ($asc->visible || has_capability('moodle/course:viewhiddencourses', $context) || $seeinvisible) {
                        $cat->courses[$asc->id] = $asc;
                    }
                }
            }
            $categories[] = $cat;
        }

        // get in subcats
        if ($catlevel = $DB->get_records_select('course_categories', "parent = ? ", array($catstart), 'sortorder', 'id,name,visible')) {
            foreach($catlevel as $cat) {
                $catcontext = context_coursecat::instance($cat->id);
                if ((!$cat->visible && !has_capability('moodle/category:showhiddencategories', $catcontext)) && !$seeinvisible) {
                    continue;
                }

                if ($ascendants = $this->get_ascendants($cat->id, $seeunbound)) {
                    $cat->courses = array();
                    foreach($ascendants as $asc) {
                        $context = context_course::instance($asc->id);
                        if ($asc->visible || has_capability('moodle/course:viewhiddencourses', $context) || $seeinvisible){
                            $cat->courses[$asc->id] = $asc;
                        }
                    }
                }
                $categories[] = $cat;
                $level++;
                $this->read_category_tree($cat->id, $categories, $seeunbound, $seeinvisible);
                $level--;
            }
        }
        // if ($level == 0) print_object($categories);
    }

    /**
     * get all potential or effective ascendants
     * an ascendant course is a course having a metacourse enrolment
     * instance bound to us, either it is active or not or no enrol instance at all (but could have). 
     * @param int $catid the root category where to search
     * @param bool $seeunbound
     */
    function get_ascendants($catid, $seeunbound) {
        global $COURSE, $DB;

        // Getting all meta enrols that point me.

        if ($seeunbound) {
            $invisibleclause =  '((e.customint1 <> ? ) OR (e.customint1 = ? AND status = 1) OR e.id IS NULL)';
            $params = array($COURSE->id, $COURSE->id);
        } else {
            $invisibleclause = ' e.customint1 = ? AND status = 0 ' ;
            $params = array($COURSE->id);
        }

        $catclause = ($catid) ? " c.category = $catid AND " : '' ;

        $sql = "
            SELECT DISTINCT
                c.id,
                c.category,
                c.shortname,
                c.fullname,
                c.sortorder,
                c.visible
            FROM
                {course} c
            LEFT JOIN
                {enrol} e
            ON
                c.id = e.courseid
            WHERE
                $catclause
                $invisibleclause
        ";
        return $DB->get_records_sql($sql, $params);
    }

    /**
     *
     */
    function user_can_edit() {
        global $COURSE;

        $context = context_course::instance($COURSE->id);
        if (has_capability('block/course_ascendants:configure', $context)) {
            return true;
        }

        return false;
    }

    /**
     * tests if full course group exists
     *
     */
    function has_course_group() {
        global $COURSE, $DB;

        return $DB->record_exists('groups', array('courseid' => $CORUSE->id, 'name' => $COURSE->shortname));
    }

    /**
     * registeres a "full course" (all participants) group
     *
     */
    function make_course_group(){
        global $COURSE, $DB;

        if (!$this->has_course_group()) {
            $groupobj->courseid = $COURSE->id;
            $groupobj->name = $COURSE->shortname;
            $groupobj->description = get_string('fullcourse', 'block_course_ascendants');
            $groupobj->timecreated = time();
            $groupobj->timemodified = time();
            $fullgroupid = $DB->insert_record('groups', $groupobj);
        }
    }
}
