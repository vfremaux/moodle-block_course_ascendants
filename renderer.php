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
 * Version details
 *
 * @package    block_course_ascendants
 * @category   blocks
 * @copyright  2012 onwards Valery Fremaux (valery.fremaux@gmail.com)
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
defined('MOODLE_INTERNAL') || die();

class block_course_ascendants_renderer extends plugin_renderer_base {

    /**
     * Renders a complete course row.
     * @param object $course
     * @param object $theblock the block instance
     * @param int $mincourse
     * @param int $maxcourse
     */
    public function courserow($course, $theblock, $mincourse, $maxcourse) {
        $template = $this->export_for_template($course, $theblock, $mincourse, $maxcourse);
        return $this->output->render_from_template('block_course_ascendants/courserow', $template);
    }

    public function export_for_template($course, $theblock, $mincourse, $maxcourse) {
        global $COURSE, $USER;

        $coursecontext = context_course::instance($COURSE->id);
        $blockcontext = context_block::instance($theblock->instance->id);
        $currentlockstate = $theblock->islocked();

        $template = new StdClass;
        $template->hiddenclass = (empty($course->visible)) ? 'shadow' : '';

        if (!empty($this->config->stringlimit)) {
            $template->fullname = shorten_text(format_string($course->fullname), $theblock->config->stringlimit);
        } else {
            $template->fullname = format_string($course->fullname);
        }
        $template->shortname = $course->shortname;
        $template->courseurl = new moodle_url('/course/view.php', array('id' => $course->id));
        if (@$theblock->config->arrangeby == 1) {
            $template->arrangeinternalorder = true;
            if (has_capability('block/course_ascendants:configure', $coursecontext)) {
                $template->uppix = $this->output->pix_icon('t/up', get_string('up'));
                if ($course->localorder > $mincourse) {
                    $template->canup = true;
                    $template->upurl = new moodle_url(me());
                    $template->upurl->params(array('what' => 'asc-down',
                                         'downcourse' => $course->id,
                                         'blockid' => $theblock->instance->id,
                                         'upcourse' => null));
                }

                $template->downpix = $this->output->pix_icon('t/down', get_string('down'));
                if ($course->localorder <= $maxcourse - 1) {
                    $template->candown = true;
                    $template->downurl = new moodle_url(me());
                    $params = array('what' => 'asc-up',
                                    'upcourse' => $course->id,
                                    'blockid' => $theblock->instance->id,
                                    'downcourse' => null);
                    $template->downurl->params($params);
                }
            }
        }

        if (!empty($theblock->config->showdescription)) {
            $template->showdescription = true;
            $template->description = format_text($course->summary);
        } else {
            $template->showdescclass = 'description-not-visible';
        }

        if (!has_capability('block/course_ascendants:configure', $blockcontext, $USER->id, false) &&
                $course->enablecompletion) {

            $template->hascompletion = true;

            $completedstr = get_string('completed', 'block_course_ascendants');
            $enrolledstr = get_string('enrolled', 'block_course_ascendants');
            $unenrolledstr = get_string('unenrolled', 'block_course_ascendants');

            if ($course->completionenrolled) {
                if ($course->completioncompleted) {
                    $e = new StdClass();
                    $e->completed = userdate($course->completioncompleted);
                    $e->days = ceil(($course->completioncompleted - $course->completionenrolled) / DAYSECS);
                    $template->completedonstr = get_string('completedon', 'block_course_ascendants', $e);
                    $template->completionicon = $this->output->pix_icon('completed', $completedstr, 'block_course_ascendants');
                } else {
                    $template->completionicon = $this->output->pix_icon('notcompleted', $enrolledstr, 'block_course_ascendants');
                    if ($theblock->can_lock($course)) {
                        // check some special completion rules such as course_modules bound completion.
                        $theblock->lock();
                    }
                }
            } else {
                $template->completionicon = $this->output->pix_icon('notvisited', $unenrolledstr, 'block_course_ascendants');
                if ($theblock->can_lock($course)) {
                    $theblock->lock();
                }
            }
        }

        // If lock not marked then is a link.
        if (!empty($theblock->config->completionlocked)) {
            $template->islink = !$currentlockstate;
        } else {
            $template->islink = true;
        }

        $template->canedit = false;
        $targetcoursecontext = context_course::instance($course->id);
        if (has_capability('moodle/course:manageactivities', $targetcoursecontext)) {
            $template->canedit = true;

            // Add lock indicators.
            $this->add_lock_indicators($template, $course);
        }

        return $template;
    }

    protected function add_lock_indicators(&$template, $course) {
        switch ($course->locktype) {
            case 1: {
                $template->lockindicator = $this->output->pix_icon('t/lock', get_string('courselock', 'block_course_ascendants'), 'moodle').' '.get_string('course');
                break;
            }
            case 2: {
                $template->lockindicator = $this->output->pix_icon('t/lock', get_string('cmlockon', 'block_course_ascendants'), 'moodle').' '.$course->lockcmid;
                break;
            }
        }
    }
}