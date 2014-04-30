<?php

	include '../../config.php';
	include $CFG->dirroot.'/blocks/course_ascendants/assign_form.php';

	$courseid = required_param('course', PARAM_INT) ; // the course id

	if (!$course = $DB->get_record('course', array('id' => $courseid))){
		print_error('invalidcourseid');
	}

    $id = required_param('id', PARAM_INT); // the block instance id
    if (!$instance = $DB->get_record('block_instances', array('id' => $id))){
        print_error('Invalidblockid');
    }
    $theBlock = block_instance('course_ascendants', $instance);
	$context = context_block::instance($theBlock->instance->id);
// Security

	require_login($course);
	require_capability('block/course_ascendants:configure', $context);

/// check if course group must be created

	if (!isset($theBlock->config)) $theBlock->config = new StdClass();
	if (!isset($theBlock->config->createcoursegroup)) $theBlock->config->createcoursegroup = false;
	if (!isset($theBlock->config->coursegroupnamebase)) $theBlock->config->coursegroupnamebase = 0;
	if (!isset($theBlock->config->coursegroupnamefilter)) $theBlock->config->coursegroupnamefilter = '';

	if (empty($theBlock->config->coursegroupname)){
		switch($theBlock->config->coursegroupnamebase){
			case 0 : 
				$coursebase = $COURSE->fullname;
				break;
			case 1 : 
				$coursebase = $COURSE->shortname;
				break;
			case 2 : 
				$coursebase = $COURSE->idnumber;
				break;
		}
		if ($theBlock->config->coursegroupnamefilter){
			preg_match('/'.$theBlock->config->coursegroupnamefilter.'/', $coursebase, $matches);
			if (isset($matches[1])){
				$groupname = $matches[1];
			} else {
				$groupname = $matches[0];
			}
		} else {
			$groupname = $coursebase;
		}
	} else {
		$groupname = $theBlock->config->coursegroupname;
	}

	$coursegroup = $DB->get_record('groups', array('name' => $groupname, 'courseid' => $COURSE->id));
	if (!$coursegroup){
		if ($theBlock->config->createcoursegroup){
			// create the group and add all enrolled users in (only direct roles)
			$coursegroup->courseid = $COURSE->id;
			$coursegroup->name = $groupname;			
			$coursegroup->timecreated = time();
			$coursegroup->modified = 0;
			$coursegroup->id = $DB->insert_record('groups', $coursegroup);
			$notify = get_string('coursegroupcreated', 'block_course_ascendants');
		} 		
	}

	// If finally group exists or come to exist, sync members
	
	// TODO : integrate new difference of enrolled and assigned users... 
	
	if ($coursegroup){
		// get all users with direct assignment
		$context = context_course::instance($COURSE->id);
		if ($directassignments  = $DB->get_records_select('role_assignments', " contextid = ? " , array($context->id), 'id', 'DISTINCT userid,userid')){
			foreach($directassignments as $assign){
				// add all missing members
				if (!$DB->record_exists('groups_members', array('groupid' => $coursegroup->id, 'userid' => $assign->userid))){
					$groupmember = new StdClass;
					$groupmember->groupid = $coursegroup->id;
					$groupmember->userid = $assign->userid;
					$groupmember->timeadded = time();
					$DB->insert_record('groups_members', $groupmember);
				}
			}
		}
	}
/// get data

	$url = $CFG->wwwroot.'/blocks/course_ascendants/assign.php?course='.$courseid.'id='.$id;

	$mform = new Assign_Form($url, $theBlock);	

	if ($mform->is_cancelled()){
		redirect($CFG->wwwroot.'/course/view.php?id='.$courseid);
	}
	if ($data = $mform->get_data()){
		include 'assign.controller.php';
		redirect($CFG->wwwroot.'/course/view.php?id='.$courseid);
	}
/// Print page

	$url = $CFG->wwwroot.'/blocks/course_ascendants/assign.php';
	$PAGE->navigation->add(get_string('ascendantsaccess', 'block_course_ascendants'));
	$PAGE->set_url($url);
	$PAGE->set_title($SITE->shortname.': '.$course->fullname);
	$PAGE->set_heading($SITE->shortname.': '.$course->fullname);
	echo $OUTPUT->header();

	echo $OUTPUT->heading(get_string('manageascendants', 'block_course_ascendants'));
	if (!empty($notify)) echo $OUTPUT->notification($notify);

	if($ascendants = $theBlock->get_ascendants(0, false)){
		$ascendants = array_keys($ascendants);
		$data = new StdClass();
		foreach($ascendants as $asc){
			$key = 'c'.$asc;
			$data->$key = 1;
		}
	}

	$mform->set_data($data);
	$mform->display();
	echo $OUTPUT->footer($course);