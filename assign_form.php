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
 * Block instance editing form.
 *
 * @package    block_course_ascendants
 * @category   blocks
 * @author Moodle 2.x Valery Fremaux <valery.fremaux@gmail.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
defined('MOODLE_INTERNAL') || die();

require_once($CFG->libdir.'/formslib.php');

class course_ascendants_assign_form extends moodleform {

    protected $blockinstance;

    public function __construct($url, &$theblock, $categories) {
        $this->blockinstance = $theblock;
        parent::__construct($url, array('categories' => $categories));
    }

    public function definition() {
        global $COURSE, $DB;

        // Take local as default.
        if (empty($this->blockinstance->config->coursescopestartcategory)) {
            $this->blockinstance->config->coursescopestartcategory = $COURSE->category;
        }

        $courseoptions = array();
        if ($this->_customdata['categories']) {
            foreach ($this->_customdata['categories'] as $cat) {
                if (!empty($cat->courses)) {
                    foreach ($cat->courses as $c) {
                        $courseoptions[$cat->name][$c->id] = format_string($c->fullname);
                    }
                }
            }
        }

        $mform = $this->_form;
        $mform->addelement('hidden', 'what', 'assign');
        $mform->setType('what', PARAM_TEXT);

        foreach ($courseoptions as $cc => $cs) {
            $mform->addElement('header', 'h'.$cc, format_string($cc));
            $mform->setExpanded('h'.$cc);
            uasort($cs, 'sort_by_fullname');
            foreach ($cs as $cid => $name) {

                if ($cid == $COURSE->id) {
                    continue;
                }

                $notifytext = get_string('uncheckadvice', 'block_course_ascendants');
                $radioarray = array();
                $label = get_string('opened', 'block_course_ascendants');
                $attrs = array('onchange' => "notifyeffect('$notifytext')");
                $radioarray[] =& $mform->createElement('radio', 'c'.$cid, '', $label, 1, $attrs);
                $label = get_string('closed', 'block_course_ascendants');
                $radioarray[] =& $mform->createElement('radio', 'c'.$cid, '', $label, 0, $attrs);

                if (!empty($this->blockinstance->config->completionlocked)) {
                    $label = get_string('nolock', 'block_course_ascendants');
                    $radioarray[] =& $mform->createElement('radio', 'l'.$cid, '', $label, 0, $attrs);

                    $label = get_string('courselock', 'block_course_ascendants');
                    $radioarray[] =& $mform->createElement('radio', 'l'.$cid, '', $label, 1, $attrs);

                    $label = get_string('cmlockon', 'block_course_ascendants');
                    $radioarray[] =& $mform->createElement('radio', 'l'.$cid, '', $label, 2, $attrs);

                    $mform->disabledIf('l'.$cid, 'c'.$cid, 'neq', 1);

                    $attrs = ['size' => 6];
                    $radioarray[] =& $mform->createElement('text', 'lockcm'.$cid, '', $attrs);
                    $mform->setType('lockcm'.$cid, PARAM_INT);
                    $mform->disabledIf('lockcm'.$cid, 'c'.$cid, 'neq', 1);
                }

                $padding = array('&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;');
                $mform->addGroup($radioarray, 'radioar', format_string($name), $padding, false);
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

    public function validation($data, $files = null) {
        global $DB;

        $errors = [];

        $cmidkeys = preg_grep('/lockcm\\d+/', array_keys($data));
        if (!empty($cmidkeys)) {
            foreach ($cmidkeys as $k) {
                if ($data[$k] && !$DB->record_exists('course_modules', ['id' => $data[$k]])) {
                    $errors[$k] = get_string('badcmid', 'block_course_ascendants');
                }
            }
        }

        return $errors;
    }
}

function sort_by_fullname($a, $b) {
    return strcmp($a, $b);
}