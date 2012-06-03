<?php

	include '../../config.php';
	
	$courseid = required_param('course', PARAM_INT) ; // the course id

	if (!$course = get_record('course', 'id', $courseid)){
		error("Bad course ID");
	}

    $id = required_param('id', PARAM_INT);
    $pinned = optional_param('pinned', 0, PARAM_INT);
    $blocktable = ($pinned) ? 'block_pinned' : 'block_instance' ;
    if (!$instance = get_record($blocktable, 'id', $id)){
        error('Invalid block');
    }
    $theBlock = block_instance('course_ascendants', $instance);
	$context = get_context_instance(CONTEXT_BLOCK, $theBlock->instance->id);

// Security

	require_login($course);
	
// get data

	if ($data = data_submitted()){
		include 'assign.controller.php';
	}
	
	$categories = array();
	$theBlock->read_category_tree($theBlock->config->coursescopestartcategory, $categories, true);

	$courseoptions = array();
	if ($categories){
		foreach($categories as $cat){
			if (!empty($cat->courses)){
				foreach($cat->courses as $c){
					$courseoptions[$cat->name][$c->id] = $c->fullname;
				}
			}
		}
	}
	
	if($descendants = get_records_menu('course_meta', 'child_course', $courseid, 'id,parent_course')){
		$descendants = array_values($descendants);
	} else {
		$descendants = array();
	}
		
/// Print page

	$navlinks[] = array('name' => get_string('ascendantsaccess', 'block_course_ascendants'), 'url' => '', 'type' => 'title'); 

	print_header($SITE->shortname.': '.$course->fullname, $SITE->shortname.': '.$course->fullname, build_navigation($navlinks));

	print_heading(get_string('manageascendants', 'block_course_ascendants'));
	
	echo '<form name="assign" method="POST" >';
	echo '<center>';
	echo '<table width="90%">';
	foreach($courseoptions as $cc => $cs){
		echo "<tr><td colspan=\"2\"><b>$cc</b></td></tr>";
		foreach($cs as $cid => $name){
			$checked = (in_array($cid, $descendants)) ? 'checked="checked"' : '' ;
			echo "<tr><td width=\"20%\"><input type=\"checkbox\" $checked name=\"c{$cid}\" value=\"1\" /><td width=\"80%\">$name</td></tr>";
		}
	}
	echo '</table>';
	$updatestr = get_string('update');
	echo "<input type=\"submit\" name=\"go_button\" value=\"$updatestr\" />";
	echo '<br/></center>';
	echo '</form>';
	
	print_footer($course);