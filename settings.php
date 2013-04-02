<?php

$namebaseoptions[0] = get_string('fullname');
$namebaseoptions[1] = get_string('shortname');
$namebaseoptions[2] = get_string('idnumber');

$yesnooptions[0] = get_string('no');
$yesnooptions[1] = get_string('yes');

$settings->add(new admin_setting_configselect('block_ascendants_defaultcreatecoursegroup', get_string('createcoursegroup', 'block_course_ascendants'), '', '0', $yesnooptions));

$settings->add(new admin_setting_configselect('block_ascendants_coursegroupnamebase', get_string('coursegroupnamebase', 'block_course_ascendants'), '', 'shortname', $namebaseoptions));

$settings->add(new admin_setting_configtext('block_ascendants_coursegroupnamefilter', get_string('coursegroupnamefilter', 'block_course_ascendants'), '', ''));
