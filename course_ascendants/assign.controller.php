<?php

if (!defined('MOODLE_INTERNAL')) die ('You cannot use this script this way');

require_once $CFG->dirroot.'/enrol/meta/locallib.php';

if ($data){
	$dataarr = (array)$data;
	$inputdata = preg_grep('/^c\d+/', array_keys($dataarr));
	// we know now we got them all (radio buttons)
	if (!empty($inputdata)){
		foreach($inputdata as $cid){
			$metaid = str_replace('c', '', $cid);
			$DB->set_field('enrol', 'status', !$dataarr[$cid], 'enrol', array('customint1' => $courseid, 'courseid' => $metaid));
			if ($dataarr[$cid] == 1){
				$allmetas[] = $metaid; // for further group pushing
			}
			// sync all users in open metacourses
            enrol_meta_sync($metaid);
		}
	}
	// Now we sync our course group into opened metacourses
	// if told to, push the course group whereever it is missing, based on groupname
	if (@$data->pushnewgroups){
		foreach($allmetas as $m){
			if (!$DB->record_exists('groups', array('courseid' => $m, 'name' => $coursegroup->name))){
				$metagroup->courseid = $m;
				$metagroup->name = $groupname;			
				$metagroup->timecreated = time();
				$metagroup->modified = 0;
				$metagroup->id = $DB->insert_record('groups', $metagroup);				
			}	
			// now resync group anyway		
			if ($members = groups_get_members($coursegroup->id)){
				$context = context_course::instance($m);
				foreach($members as $u){
					// we need check if candidate usr to transfer has real role (was synced bymetacourse)
					if ($DB->get_records_select('role_assignments', " contextid = $context->id AND userid = $u->id AND hidden = 0 ")){
						// just create if not registered there
						if (!$DB->record_exists('groups_members', array('groupid' => $metagroup->id, 'userid' => $u->id))){
							$groupmember = new StdClass;
							$groupmember->groupid = $metagroup->id;
							$groupmember->userid = $u->id;
							$groupmember->timeadded = time();
							$DB->insert_record('groups_members', $groupmember);
						}					
					}
				}
			}
		}
	}
}

?>