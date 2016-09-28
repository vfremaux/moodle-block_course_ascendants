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
 * Block instance editing form.
 *
 * @package    block_course_ascendants
 * @category   blocks
 * @author Moodle 2.x Valery Fremaux <valery.fremaux@gmail.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
require_once($CFG->libdir.'/formslib.php');

class Assign_Form extends moodleform {

    var $blockinstance;

    function __construct($action, &$theBlock) {
        $this->blockinstance = $theBlock;
        parent::__construct($action);
    }

    function definition() {
        global $COURSE, $DB;

        // Take local as default.
        if (empty($this->blockinstance->config->coursescopestartcategory)) {
            $this->blockinstance->config->coursescopestartcategory = $COURSE->category;
        }

        $categories = array();
        $this->blockinstance->read_category_tree($this->blockinstance->config->coursescopestartcategory, $categories, true);
        $courseoptions = array();
        if ($categories) {
            foreach ($categories as $cat) {
                if (!empty($cat->courses)) {
                    foreach ($cat->courses as $c) {
                        $courseoptions[$cat->name][$c->id] = $c->fullname;
                    }
                }
            }
        }
        $mform = $this->_form;

        $mform->addElement('hidden', 'course', $COURSE->id);
        $mform->setType('course', PARAM_INT);

        $mform->addElement('hidden', 'id', $this->blockinstance->instance->id);
        $mform->setType('id', PARAM_INT);

        foreach ($courseoptions as $cc => $cs) {
            $mform->addElement('header', 'h'.$cc, format_string($cc));
            $mform->setExpanded('h'.$cc);
            uasort($cs, 'sort_by_fullname');
            foreach ($cs as $cid => $name) {
                $notifytext = get_string('uncheckadvice', 'block_course_ascendants');
                $radioarray = array();
                $radioarray[] =& $mform->createElement('radio', 'c'.$cid, '', get_string('open', 'block_course_ascendants'), 1, array('onchange' => "notifyeffect('$notifytext')"));
                $radioarray[] =& $mform->createElement('radio', 'c'.$cid, '', get_string('close', 'block_course_ascendants'), 0, array('onchange' => "notifyeffect('$notifytext')"));
                $mform->addGroup($radioarray, 'radioar', format_string($name), array('&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;'), false);
            }
        }

        if (!empty($this->blockinstance->createcoursegroup)) {
            $mform->addElement('header', 'options', get_string('options', 'block_course_ascendants'));
            $pushnewgroupsstr = get_string('pushnewgroups', 'block_course_ascendants');
            $mform->addElement('checkbox', 'pushnewgroups', $pushnewgroupsstr);
            $mform->addHelpButton('pushnewgroups', 'pushnewgroups', 'block_ascendant');
            $mform->setDefault('pushnewgroups', 1);
        }

        $this->add_action_buttons(true);
    }

    function validation($data, $files = null) {
    }
}

function sort_by_fullname($a, $b) {
    return strcmp($a, $b);
}