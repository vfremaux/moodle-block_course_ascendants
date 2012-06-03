<?php

if ($data){
	$dataarr = (array)$data;
	$inputdata = preg_grep('/^c\d+/', array_keys($dataarr));
	
	delete_records('course_meta', 'child_course', $courseid);
	
	if (!empty($inputdata)){
		foreach($inputdata as $cid){
			$metaid = str_replace('c', '', $cid);
			$newmeta->child_course = $courseid;
			$newmeta->parent_course = $metaid;
			insert_record('course_meta', $newmeta);
		}
	}
	redirect($CFG->wwwroot."/course/view.php?id={$courseid}");
}


?>