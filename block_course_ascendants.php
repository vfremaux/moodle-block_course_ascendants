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
 * @copyright  2012 onwards Valery Fremaux (valery.fremaux@gmail.com)
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 *
 * the block course ascendants
 */
defined('MOODLE_INTERNAL') || die();

require_once($CFG->dirroot.'/blocks/course_ascendants/listlib.php');

class block_course_ascendants extends block_base {

    public function init() {
        $this->title = get_string('title', 'block_course_ascendants');
    }

    public function has_config() {
        return true;
    }

    public function applicable_formats() {
        return array('course' => true, 'mod' => false, 'tag' => false, 'my' => false);
    }

    public function instance_allow_config() {
        return true;
    }

    public function instance_allow_multiple() {
        return false;
    }

    /**
     * Serialize and store config data
     */
    public function instance_config_save($data, $nolongerused = false) {

        if (!isset($data->showdescription)) {
            $data->showdescription = 0;
        }

        parent::instance_config_save($data, false);
    }


    public function get_content() {
        global $COURSE, $PAGE, $USER;

        if ($this->content !== null) {
            return $this->content;
        }

        if (!isset($this->config)) {
            $this->config = new StdClass();
        }

        if (!isset($this->config->showcategories)) {
            $this->config->showcategories = false;
        }
        $coursecontext = context_course::instance($COURSE->id);

        if (@$this->config->arrangeby == 1) {
            // Execute block micro controller if arranging by local plan.
            if ($what = optional_param('what', '', PARAM_TEXT)) {
                if ($what == 'asc-down') {
                    $downcourse = required_param('downcourse', PARAM_INT);
                    $blockid = required_param('blockid', PARAM_INT);
                    list_down($blockid, $downcourse);
                }
                if ($what == 'asc-up') {
                    $upcourse = required_param('upcourse', PARAM_INT);
                    $blockid = required_param('blockid', PARAM_INT);
                    list_up($blockid, $upcourse);
                }
            }
        }

        // Fetch direct ascendants that are metas who point the current course as descendant.
        $categories = array();
        $this->read_category_tree(0 + @$this->config->coursescopestartcategory, $categories);

        $renderer = $PAGE->get_renderer('block_course_ascendants');

        $this->content = new stdClass;
        $this->content->footer = '';
        $this->content->text = '';

        if (@$this->config->arrangeby == 0) {
            // Scan directly category results and output them in lbock space.
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
                            $asccontext = context_course::instance($ascendant->id);
                            if (!is_enrolled($asccontext, $USER->id, '', true) &&
                                    !is_viewing($asccontext, $USER->id) &&
                                            !is_siteadmin($USER->id)) {
                                continue;
                            }
                            $this->content->text .= $renderer->courserow($ascendant, $this);
                        }
                        $this->content->text .= '</div>';
                    }
                }
            }
        } else {
            // Prescan categories, sort them by local order and finally output them.
            if ($categories) {
                $flatcourses = array();
                foreach ($categories as $cat) {
                    if (!empty($cat->courses)) {
                        foreach ($cat->courses as $cid => $ascendant) {
                            $flatcourses[$cid] = $ascendant;
                        }
                    }
                }

                uasort($flatcourses, 'sort_by_localorder');

                $this->content->text .= '<div class="courses">';
                if (!empty($flatcourses)) {
                    foreach ($flatcourses as $c) {
                        $this->content->text .= $renderer->courserow($c, $this, count($flatcourses));
                    }
                }
                $this->content->text .= '</div>';

            }
        }

        if (has_capability('moodle/course:manageactivities', $coursecontext)) {
            $manageascendantsstr = get_string('manageascendants', 'block_course_ascendants');
            $params = array('course' => $COURSE->id, 'id' => $this->instance->id, 'sesskey' => sesskey());
            $linkurl = new moodle_url('/blocks/course_ascendants/assign.php', $params);
            $this->content->footer = '<a href="'.$linkurl.'">'.$manageascendantsstr.'</a>';
        }
        return $this->content;
    }

    /**
     * Reads category tree in correct order.
     */
    public function read_category_tree($catstart, &$categories, $seeunbound = false, $seeinvisible = false) {
        global $DB;
        static $level = 0;

        if ($catstart != 0 && $level == 0) {
            $cat = $DB->get_record('course_categories', array('id' => $catstart), 'id,name,visible');
            if (!$cat->visible && !$seeinvisible) {
                return;
            }
            if ($ascendants = $this->get_ascendants($catstart, $seeunbound)) {
                $cat->courses = array();
                foreach ($ascendants as $asc) {
                    $context = context_course::instance($asc->id);
                    if ($asc->visible || has_capability('moodle/course:viewhiddencourses', $context) || $seeinvisible) {
                        $cat->courses[$asc->id] = $asc;
                    }
                }
            }
            $categories[] = $cat;
        }

        // Get in subcats.
        $select = "parent = ? ";
        $fields = 'id,name,visible';
        if ($catlevel = $DB->get_records_select('course_categories', $select, array($catstart), 'sortorder', $fields)) {
            foreach ($catlevel as $cat) {
                $catcontext = context_coursecat::instance($cat->id);
                if ((!$cat->visible &&
                        !has_capability('moodle/category:viewhiddencategories', $catcontext)) &&
                                !$seeinvisible) {
                    continue;
                }

                if ($ascendants = $this->get_ascendants($cat->id, $seeunbound)) {
                    $cat->courses = array();
                    foreach ($ascendants as $asc) {
                        $context = context_course::instance($asc->id);
                        if ($asc->visible || has_capability('moodle/course:viewhiddencourses', $context) || $seeinvisible) {
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
    }

    /**
     * get all potential or effective ascendants
     * an ascendant course is a course having a metacourse enrolment
     * instance bound to us, either it is active or not or no enrol instance at all (but could have).
     * @param int $catid the root category where to search
     * @param bool $seeunbound
     */
    public function get_ascendants($catid, $seeunbound, $courseid = null) {
        global $COURSE, $DB, $USER;

        // Getting all meta enrols that point me.
        if (is_null($courseid)) {
            $courseid = $COURSE->id;
        }

        if ($seeunbound) {
            $invisibleclause = '((e.customint1 <> ? ) OR (e.customint1 = ? AND status = 1) OR e.id IS NULL)';
            $params = array($this->instance->id, $USER->id, $courseid, $courseid);
        } else {
            $invisibleclause = ' e.customint1 = ? AND status = 0 ';
            $params = array($this->instance->id, $USER->id, $courseid);
        }

        $catclause = ($catid) ? " c.category = $catid AND " : '';

        $sql = "
            SELECT DISTINCT
                c.id,
                c.category,
                c.shortname,
                c.fullname,
                c.sortorder,
                c.summary,
                c.visible,
                c.enablecompletion,
                bca.sortorder as localorder,
                cc.timecompleted as completioncompleted,
                cc.timeenrolled as completionenrolled
            FROM
                {course} c
            LEFT JOIN
                {enrol} e
            ON
                c.id = e.courseid AND
                e.enrol = 'meta'
            LEFT JOIN
                {block_course_ascendants} bca
            ON
                bca.courseid = c.id AND
                bca.blockid = ?
            LEFT JOIN
               {course_completions} cc
            ON
               cc.course = c.id AND
               cc.userid = ?
            WHERE
                $catclause
                $invisibleclause
        ";
        return $DB->get_records_sql($sql, $params);
    }

    /**
     * Same but recursively
     */
    public function get_all_ascendants($catid, $seeunbound, $courseid = null) {
        global $DB;

        $ascendants = array();

        $ascendants = $this->get_ascendants($catid, $seeunbound, $courseid);
        $childcats = $DB->get_records('course_categories', array('parent' => $catid));
        if ($childcats) {
            foreach ($childcats as $c) {
                $ascendants = $ascendants + $this->get_all_ascendants($c->id, $seeunbound, $courseid);
            }
        }

        return $ascendants;
    }


    /**
     *
     */
    public function user_can_edit() {
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
    public function has_course_group() {
        global $COURSE, $DB;

        return $DB->record_exists('groups', array('courseid' => $COURSE->id, 'name' => $COURSE->shortname));
    }

    /**
     * registeres a "full course" (all participants) group
     *
     */
    public function make_course_group() {
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

function sort_by_localorder(&$a, &$b) {
    if ($a->localorder > $b->localorder) {
        return 1;
    }
    if ($a->localorder < $b->localorder) {
        return -1;
    }
    return 0;
}