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
require_once($CFG->dirroot.'/blocks/course_ascendants/lib.php');

class block_course_ascendants_edit_form extends block_edit_form {

    public function specific_definition($mform) {
        global $DB, $COURSE;

        $config = get_config('block_course_ascendants');

        $mform->addElement('header', 'configheader', get_string('blocksettings', 'block'));

        $mform->addElement('text', 'config_title', get_string('alttitle', 'block_course_ascendants'));
        $mform->setType('config_title', PARAM_MULTILANG);
        $mform->addHelpButton('config_title', 'alttitle', 'block_course_ascendants');

        $mform->addElement('advcheckbox', 'config_showcategories', get_string('showcategories', 'block_course_ascendants'));

        $label = get_string('showdescription', 'block_course_ascendants');
        $mform->addElement('advcheckbox', 'config_showdescription', $label);
        $mform->addHelpButton('config_showdescription', 'showdescription', 'block_course_ascendants');

        $catoptions = $DB->get_records_menu('course_categories', null, 'sortorder', 'id,name');
        $label = get_string('coursescopestartcategory', 'block_course_ascendants');
        $mform->addElement('select', 'config_coursescopestartcategory', $label, $catoptions);
        $mform->addHelpButton('config_coursescopestartcategory', 'coursescopestartcategory', 'block_course_ascendants');

        $arrangeopts = array('0' => get_string('bycats', 'block_course_ascendants'),
            '1' => get_string('byplan', 'block_course_ascendants'));
        $label = get_string('arrangeby', 'block_course_ascendants');
        $mform->addElement('select', 'config_arrangeby', $label, $arrangeopts);
        $mform->setDefault('configèarrangeby', @$config->defaultarrangeby);

        if (block_course_ascendants_supports_feature('display/tiles')) {
            $options = [
                'list' => get_string('list', 'block_course_ascendants'),
                'tiles' => get_string('tiles', 'block_course_ascendants') 
            ];
            $mform->addElement('select', 'config_coursedisplaymode', get_string('coursedisplaymode', 'block_course_ascendants'), $options);
            if (empty($config->defaultcoursedisplaymode)) {
                $config->defaultcoursedisplaymode = 'list';
            }
            $mform->setDefault('config_displaymode', $config->defaultcoursedisplaymode);
        }

        $label = get_string('stringlimit', 'block_course_ascendants');
        $mform->addElement('text', 'config_stringlimit', $label, array('size' => 4, 'maxlength' => 3));
        $mform->setType('config_stringlimit', PARAM_INT);
        $mform->addHelpButton('config_stringlimit', 'stringlimit', 'block_course_ascendants');

        $label = get_string('completionlocked', 'block_course_ascendants');
        $mform->addElement('advcheckbox', 'config_completionlocked', $label);
        $mform->setType('config_completionlocked', PARAM_BOOL);
        $mform->addHelpButton('config_completionlocked', 'completionlocked', 'block_course_ascendants');

        $label = get_string('catstringfilter', 'block_course_ascendants');
        $mform->addElement('text', 'config_catstringfilter', $label, array('size' => 40, 'maxlength' => 80));
        $mform->setType('config_catstringfilter', PARAM_TEXT);
        $mform->addHelpButton('config_catstringfilter', 'catstringfilter', 'block_course_ascendants');

        if (block_course_ascendants_supports_feature('group/propagate')) {

            $groupoptions[0] = get_string('no');
            $groupoptions[1] = get_string('globalcoursegroup', 'block_course_ascendants');
            $groupoptions[2] = get_string('propagateexistinggroups', 'block_course_ascendants');

            $label = get_string('createcoursegroup', 'block_course_ascendants');
            $mform->addElement('select', 'config_createcoursegroup', $label, $groupoptions);
            $mform->addHelpButton('config_createcoursegroup', 'createcoursegroup', 'block_course_ascendants');
            $mform->setDefault('config_createcoursegroup', $config->defaultcreatecoursegroup);

            $namebaseoptions[0] = get_string('fullname');
            $namebaseoptions[1] = get_string('shortname');
            $namebaseoptions[2] = get_string('idnumber');

            $label = get_string('coursegroupnamebase', 'block_course_ascendants');
            $mform->addElement('select', 'config_coursegroupnamebase', $label, $namebaseoptions);
            $mform->setDefault('config_coursegroupnamebase', $config->defaultcoursegroupnamebase);
            $mform->addHelpButton('config_coursegroupnamebase', 'coursegroupnamebase', 'block_course_ascendants');
            $mform->disabledIf('config_coursegroupnamebase', 'config_createcoursegroup', 'neq', 1);

            $label = get_string('coursegroupnamefilter', 'block_course_ascendants');
            $mform->addElement('text', 'config_coursegroupnamefilter', $label, $namebaseoptions);
            $mform->setDefault('config_coursegroupnamefilter', $config->defaultcoursegroupnamefilter);
            $mform->addHelpButton('config_coursegroupnamefilter', 'coursegroupnamefilter', 'block_course_ascendants');
            $mform->setType('config_coursegroupnamefilter', PARAM_TEXT);
            $mform->disabledIf('config_coursegroupnamefilter', 'config_createcoursegroup', 'neq', 1);
        }
    }
}
