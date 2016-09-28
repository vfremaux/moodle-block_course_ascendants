<?php

require('../../../config.php');
require_once($CFG->dirroot.'/blocks/course_ascendants/db/upgrade.php');

require_login();
require_capability('moodle/site:config', context_system::instance());

block_ascendants_feed_instances();
