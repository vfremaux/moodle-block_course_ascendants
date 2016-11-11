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
 */
defined('MOODLE_INTERNAL') || die();

$namebaseoptions[0] = get_string('fullname');
$namebaseoptions[1] = get_string('shortname');
$namebaseoptions[2] = get_string('idnumber');

$yesnooptions[0] = get_string('no');
$yesnooptions[1] = get_string('yes');

$key = 'block_course_ascendants/defaultcreatecoursegroup';
$label = get_string('createcoursegroup', 'block_course_ascendants');
$desc = '';
$settings->add(new admin_setting_configselect($key, $label, $desc, '0', $yesnooptions));

$key = 'block_course_ascendants/coursegroupnamebase';
$label = get_string('coursegroupnamebase', 'block_course_ascendants');
$desc = '';
$settings->add(new admin_setting_configselect($key, $label, $desc, 'shortname', $namebaseoptions));

$key = 'block_course_ascendants/coursegroupnamefilter';
$label = get_string('coursegroupnamefilter', 'block_course_ascendants');
$desc = '';
$settings->add(new admin_setting_configtext($key, $label, $desc, ''));

$arrangeopts = array('0' => get_string('bycats', 'block_course_ascendants'),
                     '1' => get_string('byplan', 'block_course_ascendants'));
$key = 'block_course_ascendants/arrangeby';
$label = get_string('arrangebydefault', 'block_course_ascendants');
$desc = '';
$settings->add(new admin_setting_configselect($key, $label, $desc, 0, $arrangeopts));
