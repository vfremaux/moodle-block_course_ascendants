<?php //$Id: block_course_ascendants.php,v 1.8 2012-07-18 16:09:58 vf Exp $

class block_course_ascendants extends block_list {

    function init() {
        $this->title = get_string('title', 'block_course_ascendants') ;
    }

    function has_config() {
        return true;
    }

    function applicable_formats() {
        return array('all' => true, 'mod' => false, 'tag' => false);
        // return array('site' => false, 'course' => true);
    }

    function instance_allow_config() {
        return true;
    }

    function instance_allow_multiple() {
        return false;
    }

    function get_content() {
        global $THEME, $CFG, $COURSE;

        if ($this->content !== NULL) {
            return $this->content;
        }
        if (!isset($this->config->showcategories)){
        	$this->config->showcategories = false;
        }
        $coursecontext = context_course::instance($COURSE->id);

        // fetch direct ascendants that are metas who point the current course as descendant
        $categories = array();
        $this->read_category_tree(0 + @$this->config->coursescopestartcategory, $categories);

        $this->content = new stdClass;
        $this->content->items = array();
        $this->content->icons = array();
        $this->content->footer = '';

		if ($categories){
	        foreach ($categories as $cat) {
	    		if ($this->config->showcategories && !empty($cat->courses)){
	    			$filteredprevcat = $cat->name;
	    			if (!empty($this->config->catstringfilter)){
	    				$pattern = $this->config->catstringfilter;
	    				preg_match("/$pattern/", $filteredprevcat, $matches);
	    				$filteredprevcat = $matches[0];
	    			}
	    			$this->content->icons[] = '';
	    			$this->content->items[] = "<b>$filteredprevcat</b>";
	    		}
				if (!empty($cat->courses)){
		            foreach ($cat->courses as $ascendant) {
	                    $icon  = '';
	                    $this->content->icons[] = $icon;
	                    if (!empty($this->config->stringlimit)){
		                    $fullname = shorten_text($ascendant->fullname, $this->config->stringlimit);
		                } else {
		                    $fullname = $ascendant->fullname;
		                }
	                    $this->content->items[] = "<a title=\"" .s($ascendant->fullname).
	                        "\" href=\"{$CFG->wwwroot}/course/view.php?id={$ascendant->id}\">{$fullname}</a>";
		            }
		        }
	        }
	    }
        if (has_capability('moodle/course:manageactivities', $coursecontext)){
        	$manageascendantsstr = get_string('manageascendants', 'block_course_ascendants');
        	$this->content->footer = "<a href=\"{$CFG->wwwroot}/blocks/course_ascendants/assign.php?course={$COURSE->id}&amp;id={$this->instance->id}&amp;sesskey=".sesskey()."\">$manageascendantsstr</a>";
        }
        return $this->content;
    }
    /**
    * reads category tree in correct order
    */
    function read_category_tree($catstart, &$categories, $seeunbound = false, $seeinvisible = false){
    	global $CFG, $COURSE, $DB;
    	static $level = 0;

    	if ($catstart != 0 && $level == 0){
    		$cat = $DB->get_record('course_categories', array('id' => $catstart), 'id,name');
	        if ($ascendants = $this->get_ascendants($catstart, $seeunbound)){
	        	$cat->courses = array();
	        	foreach($ascendants as $asc){
	                $context = context_course::instance($asc->id);
	                if ($asc->visible || has_capability('moodle/course:viewhiddencourses', $context) || $seeinvisible){
	                	$cat->courses[$asc->id] = $asc;
	                }
                }
            }
			$categories[] = $cat;
    	}

		// get in subcats    	
    	if ($catlevel = $DB->get_records_select('course_categories', "parent = ? ", array($catstart), 'sortorder', 'id,name,visible')){
    		foreach($catlevel as $cat){
    			$catcontext = context_coursecat::instance($cat->id);
    			
		        if ($ascendants = $this->get_ascendants($cat->id, $seeunbound)){
		        	$cat->courses = array();
		        	foreach($ascendants as $asc){
		                $context = context_course::instance($asc->id);
		                $cat->courses[$asc->id] = $asc;
	                }
	            }
    			$categories[] = $cat;
    			$level++;
    			$this->read_category_tree($cat->id, $categories, $seeunbound, $seeinvisible);
    			$level--;
    		}
    	}
    	// if ($level == 0) print_object($categories);
    }

    /**
    *
    */
    function user_can_addto($page) {
        global $CFG, $COURSE;
        
        return true;
        
        $context = context_course::instance($COURSE->id);
        if (has_capability('block/course_ascendants:addinstance', $context)){
        	return true;
        }
        return false;
    }

	/**
	* get all potential or effective ascendants
	* an ascendant course is a course having a metacourse enrolment
	* instance bound to us, either it is active or not. 
	*/
	function get_ascendants($catid, $seeunbound){
		global $COURSE, $DB;
		
		// getting all meta enrols that point me
		
		$invisibleclause =  ($seeunbound) ? '' : ' AND status = 0 ' ;
		
		$catclause = ($catid) ? " c.category = $catid AND " : '' ;
		
		$sql = "
			SELECT
				c.id,
				c.category,
				c.shortname,
				c.fullname,
				c.sortorder,
				c.visible
			FROM
			    {course} c,
			    {enrol} e
			WHERE
				c.id = e.courseid AND
				$catclause				
				e.customint1 = ?
				$invisibleclause
		";
		return $DB->get_records_sql($sql, array($COURSE->id));						
	}

    /**
    *
    */
    function user_can_edit() {
        global $CFG, $COURSE;

        $context = context_course::instance($COURSE->id);
        if (has_capability('block/course_ascendants:configure', $context)){
 	       return true;
        }

		return false;
    }

	/**
	* tests if full course group exists
	*
	*/    
    function has_course_group(){
    	global $COURSE, $DB;

		return $DB->record_exists('groups', array('courseid' => $CORUSE->id, 'name' => $COURSE->shortname));
    }

	/**
	* registeres a "full course" (all participants) group
	*
	*/    
    function make_course_group(){
    	global $COURSE;

		if (!$this->has_course_group()){
			$groupobj->courseid = $COURSE->id;
			$groupobj->name = $COURSE->shortname;
			$groupobj->description = get_string('fullcourse', 'block_course_ascendants');
			$groupobj->timecreated = time();
			$groupobj->timemodified = time();
			$fullgroupid = $DB->insert_record('groups', $groupobj);
		}
    }
}

?>
