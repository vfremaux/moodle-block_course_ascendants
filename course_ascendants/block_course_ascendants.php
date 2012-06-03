<?php //$Id: block_course_ascendants.php,v 1.7 2012-03-01 19:16:49 vf Exp $

class block_course_ascendants extends block_list {
    function init() {
        $this->title = get_string('title', 'block_course_ascendants') ;
        $this->version = 2012022000;
    }

    function has_config() {
        return false;
    }

    function applicable_formats() {
        return array('site' => false, 'course' => true);
    }

    function instance_allow_config() {
        return true;
    }

    function get_content() {
        global $THEME, $CFG, $COURSE;

        if ($this->content !== NULL) {
            return $this->content;
        }
        
        if (!isset($this->config->showcategories)){
        	$this->config->showcategories = false;
        }
        
        $coursecontext = get_context_instance(CONTEXT_COURSE, $COURSE->id);

        // fetch direct ascendants that are metas who point the current course as descendant
        
        $categories = array();
        $this->read_category_tree(0 + $this->config->coursescopestartcategory, $categories);

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
    function read_category_tree($catstart, &$categories, $seeunbound = false, $seevisible = false){
    	global $CFG, $COURSE;
    	static $level = 0;
    	
    	if ($catstart != 0 && $level == 0){
    		$cat = get_record('course_categories', 'id', $catstart, '', '', '', '', 'id,name,visible');
    		if ($seeunbound){
		        $sql = "
		             SELECT DISTINCT 
		                c.id,
		                c.category,
		                c.shortname,
		                c.fullname,
		                c.sortorder,
		                c.visible
		             FROM 
		                 {$CFG->prefix}course c
		             WHERE
		                c.category = {$catstart} AND
		                c.metacourse = 1
		             ORDER BY
		                 c.sortorder
		        ";
    		} else {
		        $sql = "
		             SELECT DISTINCT 
		                c.id,
		                c.category,
		                c.shortname,
		                c.fullname,
		                c.sortorder,
		                c.visible
		             FROM 
		                 {$CFG->prefix}course c,
		                 {$CFG->prefix}course_meta mc
		             WHERE
		                c.id = mc.parent_course AND
		                c.category = {$catstart} AND
		                mc.child_course = {$COURSE->id}
		             ORDER BY
		                 c.sortorder
		        ";
		    }
	        if ($ascendants = get_records_sql($sql)){
	        	$cat->courses = array();
	        	foreach($ascendants as $asc){
	                $context = get_context_instance(CONTEXT_COURSE, $asc->id);
	                if ($asc->visible || has_capability('moodle/course:viewhiddencourses', $context) || $seevisible){
	                	$cat->courses[$asc->id] = $asc;
	                }
                }
            }
			$categories[] = $cat;
    	}
    	
    	if ($catlevel = get_records_select('course_categories', "parent = $catstart ", 'sortorder', 'id,name,visible')){
    		foreach($catlevel as $cat){
    			$catcontext = get_context_instance(CONTEXT_COURSECAT, $cat->id);
    			if ((!$cat->visible && !has_capability('moodle/category:showhiddencategories', $catcontext)) && !$seevisible){
    				continue;
    			}

    			if ($seeunbound){
			        $sql = "
			             SELECT DISTINCT 
			                c.id,
			                c.category,
			                c.shortname,
			                c.fullname,
			                c.sortorder,
			                c.visible
			             FROM 
			                 {$CFG->prefix}course c
			             WHERE
			                c.category = {$cat->id} AND
			                c.metacourse = 1
			             ORDER BY
			                 c.sortorder
			        ";		
			    } else {
			        $sql = "
			             SELECT DISTINCT 
			                c.id,
			                c.category,
			                c.shortname,
			                c.fullname,
			                c.sortorder,
			                c.visible
			             FROM 
			                 {$CFG->prefix}course c,
			                 {$CFG->prefix}course_meta mc
			             WHERE
			                c.id = mc.parent_course AND
			                c.category = {$cat->id} AND
			                mc.child_course = {$COURSE->id}
			             ORDER BY
			                 c.sortorder
						";
			    }
		        if ($ascendants = get_records_sql($sql)){
		        	$cat->courses = array();
		        	foreach($ascendants as $asc){
		                $context = get_context_instance(CONTEXT_COURSE, $asc->id);
		                if ($asc->visible || has_capability('moodle/course:viewhiddencourses', $context) || $seevisible){
		                	$cat->courses[$asc->id] = $asc;
		                }
	                }
	            }
    			$categories[] = $cat;
    			$level++;
    			$this->read_category_tree($cat->id, $categories, $seeunbound, $seevisible);
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

        $context = get_context_instance(CONTEXT_COURSE, $COURSE->id);
        if (has_capability('block/course_ascendants:canaddto', $context)){
        	return true;
        }
        return false;
    }

    /**
    *
    */
    function user_can_edit() {
        global $CFG, $COURSE;

        $context = get_context_instance(CONTEXT_COURSE, $COURSE->id);
        
        if (has_capability('block/course_ascendants:configure', $context)){
 	       return true;
        }

		return false;
    }
}

?>
