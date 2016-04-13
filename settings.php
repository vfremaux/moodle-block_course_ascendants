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

$namebaseoptions[0] = get_string('fullname');
$namebaseoptions[1] = get_string('shortname');
$namebaseoptions[2] = get_string('idnumber');

$yesnooptions[0] = get_string('no');
$yesnooptions[1] = get_string('yes');

$settings->add(new admin_setting_configselect('block_course_ascendants/defaultcreatecoursegroup', get_string('createcoursegroup', 'block_course_ascendants'), '', '0', $yesnooptions));

$settings->add(new admin_setting_configselect('block_course_ascendants/coursegroupnamebase', get_string('coursegroupnamebase', 'block_course_ascendants'), '', 'shortname', $namebaseoptions));

$settings->add(new admin_setting_configtext('block_course_ascendants/coursegroupnamefilter', get_string('coursegroupnamefilter', 'block_course_ascendants'), '', ''));

$arrangeopts = array('0' => get_string('bycats', 'block_course_ascendants'),
    '1' => get_string('byplan', 'block_course_ascendants'));
$settings->add(new admin_setting_configselect('block_course_ascendants/arrangeby', get_string('arrangebydefault', 'block_course_ascendants'), '', 0, $arrangeopts));
