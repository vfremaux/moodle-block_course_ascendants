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
 * minimalistic edit form
 *
 * @package   block_course_ascendants
 * @copyright 2013 Valery Fremaux / valery.fremaux@gmail.com
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

require_once($CFG->libdir.'/formslib.php');

class block_course_ascendants_edit_form extends block_edit_form {

    public function specific_definition($mform) {
        global $DB, $COURSE;

        $config = get_config('block_course_ascendants');

        $mform->addElement('header', 'configheader', get_string('blocksettings', 'block'));

        $mform->addElement('checkbox', 'config_showcategories', get_string('configshowcategories', 'block_course_ascendants'));

        $label = get_string('configshowdescription', 'block_course_ascendants');
        $mform->addElement('checkbox', 'config_showdescription', $label);
        $mform->addHelpButton('config_showdescription', 'configshowdescription', 'block_course_ascendants');

        $catoptions = $DB->get_records_menu('course_categories', null, 'sortorder', 'id,name');
        $label = get_string('configcoursescopestartcategory', 'block_course_ascendants');
        $mform->addElement('select', 'config_coursescopestartcategory', $label, $catoptions);
        $mform->addHelpButton('config_coursescopestartcategory', 'configcoursescopestartcategory', 'block_course_ascendants');

        $arrangeopts = array('0' => get_string('bycats', 'block_course_ascendants'),
            '1' => get_string('byplan', 'block_course_ascendants'));
        $mform->addElement('select', 'config_arrangeby', get_string('configarrangeby', 'block_course_ascendants'), $arrangeopts);
        $mform->setDefault('configèarrangeby', @$config->arrangeby);

        $label = get_string('configstringlimit', 'block_course_ascendants');
        $mform->addElement('text', 'config_stringlimit', $label, array('size' => 4, 'maxlength' => 3));
        $mform->setType('config_stringlimit', PARAM_INT);
        $mform->addHelpButton('config_stringlimit', 'configstringlimit', 'block_course_ascendants');

        $label = get_string('configcatstringfilter', 'block_course_ascendants');
        $mform->addElement('text', 'config_catstringfilter', $label, array('size' => 40, 'maxlength' => 80));
        $mform->setType('config_catstringfilter', PARAM_TEXT);
        $mform->addHelpButton('config_catstringfilter', 'configcatstringfilter', 'block_course_ascendants');

        $label = get_string('configcreatecoursegroup', 'block_course_ascendants');
        $mform->addElement('checkbox', 'config_createcoursegroup', $label);
        $mform->addHelpButton('config_createcoursegroup', 'configcreatecoursegroup', 'block_course_ascendants');
        $mform->setDefault('config_createcoursegroup', $config->defaultcreatecoursegroup);

        $label = get_string('configcoursegroupname', 'block_course_ascendants');
        $mform->addElement('text', 'config_coursegroupname', $label, array('size' => 40));
        $mform->setType('config_coursegroupname', PARAM_TEXT);
        $mform->addHelpButton('config_coursegroupname', 'configcoursegroupname', 'block_course_ascendants');

        $filter = $config->coursegroupnamefilter;
        switch ($config->coursegroupnamebase) {
            case 'shortname':
                $namesource = $COURSE->shortname;
                break;

            case 'idnumber':
                $namesource = $COURSE->idnumber;
                break;

            default:
                $namesource = $COURSE->fullname;
                break;
        }

        if ($filter) {
            if (preg_match($filter, $namesource, $matches)) {
                $namesource = $matches[1];
            }
        }
        $mform->setDefault('config_coursegroupname', $namesource);
    }
}
