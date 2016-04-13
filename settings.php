<?php

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
