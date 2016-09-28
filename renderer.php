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

/**
 * Version details
 *
 * @package    block_course_ascendants
 * @category   blocks
 * @copyright  2012 onwards Valery Fremaux (valery.fremaux@gmail.com)
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

class block_course_ascendants_renderer extends plugin_renderer_base {

    function courserow($course, $theblock, $coursecount = 0) {
        global $OUTPUT, $COURSE, $USER;

        $coursecontext = context_course::instance($COURSE->id);
        $blockcontext = context_block::instance($theblock->instance->id);

        $str = '';

        $str .= '<div class="course-ascendant-coursebox coursebox">';
        $str .= '<div class="info">';
        $str .= '<div class="name">';
        if (!empty($this->config->stringlimit)) {
            $fullname = shorten_text(format_string($course->fullname), $theblock->config->stringlimit);
        } else {
            $fullname = format_string($course->fullname);
        }
        $courseurl = new moodle_url('/course/view.php', array('id' => $course->id));
        $str .= '<a title="'.s($course->fullname).'" href="'.$courseurl.'" class="coursename">'.format_string($fullname).'</a>';
        if (@$theblock->config->arrangeby == 1) {
            if (has_capability('block/course_ascendants:configure', $coursecontext)) {
                if ($course->localorder > 0) {
                    $upurl = new moodle_url(me());
                    $upurl->params(array('what' => 'asc-down', 'downcourse' => $course->id, 'blockid' => $theblock->instance->id, 'upcourse' => null));
                    $str .= ' <a class="cmd" href="'.$upurl.'"><img src="'.$OUTPUT->pix_url('t/up').'"></a>';
                } else {
                    $str .= ' <span class="course-ascendants shadow"><img src="'.$OUTPUT->pix_url('t/up').'"></span>';
                }
                if ($course->localorder < $coursecount - 1) {
                    $downurl = new moodle_url(me());
                    $downurl->params(array('what' => 'asc-up', 'upcourse' => $course->id, 'blockid' => $theblock->instance->id, 'downcourse' => null));
                    $str .= ' <a class="cmd" href="'.$downurl.'"><img src="'.$OUTPUT->pix_url('t/down').'"></a>';
                } else {
                    $str .= ' <span class="course-ascendants shadow"><img src="'.$OUTPUT->pix_url('t/down').'"></span>';
                }
            }
        }
        $str .= '</div>';
        if (!empty($theblock->config->showdescription)) {
            $description = format_text($course->summary);
            $str .= '<div class="block-ascendants course-description">'.$description;

            if (!has_capability('block/course_ascendants:configure', $blockcontext, $USER->id, false) && $course->enablecompletion) {
    
                $completedstr = get_string('completed', 'block_course_ascendants');
                $enrolledstr = get_string('enrolled', 'block_course_ascendants');
                $unenrolledstr = get_string('unenrolled', 'block_course_ascendants');
    
                $str .= '<div class="block-ascendants-module-completion">';
                if ($course->completionenrolled) {
                    if ($course->completioncompleted) {
                        $e = new StdClass();
                        $e->completed = userdate($course->completioncompleted);
                        $e->days = ceil($course->completioncompleted - $course->completionenrolled / DAYSECS);
                        $str .= get_string('completedon', 'block_course_ascendants', $e).' <img src="'.$OUTPUT->pix_url('completed', 'block_course_ascendants').'" title="'.$completedstr.'" /> ';
                    } else {
                        $str .= ' <img src="'.$OUTPUT->pix_url('notcompleted', 'block_course_ascendants').'"  title="'.$enrolledstr.'" /> ';
                    }
                } else {
                    $str .= ' <img src="'.$OUTPUT->pix_url('notvisited', 'block_course_ascendants').'"  title="'.$unenrolledstr.'" /> ';
                }
                $str .= '</div>';
            }
            $str .= '</div>';
        }

        $str .= '</div>';
        $str .= '</div>';

        return $str;
    }
}