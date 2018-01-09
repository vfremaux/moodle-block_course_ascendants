<?php //$Id: block_course_ascendants.php,v 1.8 2012-07-18 16:09:58 vf Exp $

<<<<<<< HEAD
class block_course_ascendants extends block_list {

    function init() {
        $this->title = get_string('title', 'block_course_ascendants') ;
=======
/**
 * @package    block_course_ascendants
 * @category   blocks
 * @copyright  2012 onwards Valery Fremaux (valery.fremaux@gmail.com)
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 *
 * the block course ascendants
 */
defined('MOODLE_INTERNAL') || die();

require_once($CFG->dirroot.'/blocks/course_ascendants/listlib.php');

class block_course_ascendants extends block_base {

    public function init() {
        $this->title = get_string('title', 'block_course_ascendants');
>>>>>>> MOODLE_33_STABLE
    }

    public function has_config() {
        return true;
    }

<<<<<<< HEAD
    function applicable_formats() {
        return array('all' => true, 'mod' => false, 'tag' => false);
        // return array('site' => false, 'course' => true);
=======
    public function applicable_formats() {
        return array('course' => true, 'mod' => false, 'tag' => false, 'my' => false);
>>>>>>> MOODLE_33_STABLE
    }

    public function instance_allow_config() {
        return true;
    }

    public function instance_allow_multiple() {
        return false;
    }

<<<<<<< HEAD
    function get_content() {
        global $THEME, $CFG, $COURSE;
=======
    /**
     * Serialize and store config data
     */
    public function instance_config_save($data, $nolongerused = false) {

        if (!isset($data->showdescription)) {
            $data->showdescription = 0;
        }

        parent::instance_config_save($data, false);
    }


    public function get_content() {
        global $COURSE, $PAGE, $USER;
>>>>>>> MOODLE_33_STABLE

        if ($this->content !== NULL) {
            return $this->content;
        }
        if (!isset($this->config->showcategories)){
        	$this->config->showcategories = false;
        }
        $coursecontext = context_course::instance($COURSE->id);

<<<<<<< HEAD
        // fetch direct ascendants that are metas who point the current course as descendant
=======
        if (@$this->config->arrangeby == 1) {
            // Execute block micro controller if arranging by local plan.
            if ($what = optional_param('what', '', PARAM_TEXT)) {
                if ($what == 'asc-down') {
                    $downcourse = required_param('downcourse', PARAM_INT);
                    $blockid = required_param('blockid', PARAM_INT);
                    list_down($blockid, $downcourse);
                }
                if ($what == 'asc-up') {
                    $upcourse = required_param('upcourse', PARAM_INT);
                    $blockid = required_param('blockid', PARAM_INT);
                    list_up($blockid, $upcourse);
                }
            }
        }

        // Fetch direct ascendants that are metas who point the current course as descendant.
>>>>>>> MOODLE_33_STABLE
        $categories = array();
        $this->read_category_tree(0 + @$this->config->coursescopestartcategory, $categories);

        $this->content = new stdClass;
        $this->content->items = array();
        $this->content->icons = array();
        $this->content->footer = '';
<<<<<<< HEAD
=======
        $this->content->text = '';

        if (@$this->config->arrangeby == 0) {
            // Scan directly category results and output them in lbock space.
            if ($categories) {
                foreach ($categories as $cat) {
                    if ($this->config->showcategories && !empty($cat->courses)) {
                        $filteredprevcat = $cat->name;
                        if (!empty($this->config->catstringfilter)) {
                            $pattern = $this->config->catstringfilter;
                            preg_match("/$pattern/", $filteredprevcat, $matches);
                            $filteredprevcat = $matches[0];
                        }
                        $this->content->text .= '<div class="category"><b>'.$filteredprevcat.'</b></div>';
                    }
                    if (!empty($cat->courses)) {
                        $this->content->text .= '<div clas="courses">';
                        foreach ($cat->courses as $ascendant) {
                            $asccontext = context_course::instance($ascendant->id);
                            if (!is_enrolled($asccontext, $USER->id, '', true) &&
                                    !is_viewing($asccontext, $USER->id) &&
                                            !is_siteadmin($USER->id)) {
                                continue;
                            }
                            $this->content->text .= $renderer->courserow($ascendant, $this);
                        }
                        $this->content->text .= '</div>';
                    }
                }
            }
        } else {
            // Prescan categories, sort them by local order and finally output them.
            if ($categories) {
                $flatcourses = array();
                foreach ($categories as $cat) {
                    if (!empty($cat->courses)) {
                        foreach ($cat->courses as $cid => $ascendant) {
                            $flatcourses[$cid] = $ascendant;
                        }
                    }
                }
>>>>>>> MOODLE_33_STABLE

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
<<<<<<< HEAD
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
=======
     * Reads category tree in correct order.
     */
    public function read_category_tree($catstart, &$categories, $seeunbound = false, $seeinvisible = false) {
        global $DB;
        static $level = 0;

        if ($catstart != 0 && $level == 0) {
            $cat = $DB->get_record('course_categories', array('id' => $catstart), 'id,name,visible');
            if (!$cat->visible && !$seeinvisible) {
                return;
            }
            if ($ascendants = $this->get_ascendants($catstart, $seeunbound)) {
                $cat->courses = array();
                foreach ($ascendants as $asc) {
                    $context = context_course::instance($asc->id);
                    if ($asc->visible || has_capability('moodle/course:viewhiddencourses', $context) || $seeinvisible) {
                        $cat->courses[$asc->id] = $asc;
                    }
                }
            }
            $categories[] = $cat;
        }

        // Get in subcats.
        $select = "parent = ? ";
        $fields = 'id,name,visible';
        if ($catlevel = $DB->get_records_select('course_categories', $select, array($catstart), 'sortorder', $fields)) {
            foreach ($catlevel as $cat) {
                $catcontext = context_coursecat::instance($cat->id);
                if ((!$cat->visible &&
                        !has_capability('moodle/category:viewhiddencategories', $catcontext)) &&
                                !$seeinvisible) {
                    continue;
                }

                if ($ascendants = $this->get_ascendants($cat->id, $seeunbound)) {
                    $cat->courses = array();
                    foreach ($ascendants as $asc) {
                        $context = context_course::instance($asc->id);
                        if ($asc->visible || has_capability('moodle/course:viewhiddencourses', $context) || $seeinvisible) {
                            $cat->courses[$asc->id] = $asc;
                        }
                    }
>>>>>>> MOODLE_33_STABLE
                }
            }
<<<<<<< HEAD
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
=======
        }
    }

    /**
     * get all potential or effective ascendants
     * an ascendant course is a course having a metacourse enrolment
     * instance bound to us, either it is active or not or no enrol instance at all (but could have).
     * @param int $catid the root category where to search
     * @param bool $seeunbound
     */
    public function get_ascendants($catid, $seeunbound, $courseid = null) {
        global $COURSE, $DB, $USER;

        // Getting all meta enrols that point me.
        if (is_null($courseid)) {
            $courseid = $COURSE->id;
        }

        $catclause = ($catid) ? " AND c.category = $catid " : '';

        if ($seeunbound) {
            $params = array($this->instance->id, $USER->id, $courseid);
            $sql = "
                SELECT DISTINCT
                    c.id,
                    c.category,
                    c.shortname,
                    c.fullname,
                    c.sortorder,
                    c.summary,
                    c.visible,
                    c.enablecompletion,
                    bca.sortorder as localorder,
                    cc.timecompleted as completioncompleted,
                    ula.timeaccess as completionenrolled,
                    e.id as isbound
                FROM
                    {course} c
                LEFT JOIN
                    {block_course_ascendants} bca
                ON
                    bca.courseid = c.id AND
                    bca.blockid = ?
                LEFT JOIN
                   {course_completions} cc
                ON
                   cc.course = c.id AND
                   cc.userid = ?
                LEFT JOIN
                    {enrol} e
                ON
                    c.id = e.courseid AND
                    e.enrol = 'meta' AND
                    e.customint1 = ? AND
                    e.status = 0
                LEFT JOIN
                    {user_lastaccess} ula
                ON
                    ula.userid = cc.userid AND
                    ula.courseid = c.id
                WHERE
                    1 = 1
                    $catclause
            ";
            $ascendants = $DB->get_records_sql($sql, $params);
        } else {
            $params = array($this->instance->id, $USER->id, $courseid);

            $sql = "
                SELECT DISTINCT
                    c.id,
                    c.category,
                    c.shortname,
                    c.fullname,
                    c.sortorder,
                    c.summary,
                    c.visible,
                    c.enablecompletion,
                    bca.sortorder as localorder,
                    cc.timecompleted as completioncompleted,
                    ula.timeaccess as completionenrolled
                FROM
                    {course} c
                LEFT JOIN
                    {enrol} e
                ON
                    c.id = e.courseid AND
                    e.enrol = 'meta'
                LEFT JOIN
                    {block_course_ascendants} bca
                ON
                    bca.courseid = c.id AND
                    bca.blockid = ?
                LEFT JOIN
                   {course_completions} cc
                ON
                   cc.course = c.id AND
                   cc.userid = ?
                LEFT JOIN
                    {user_lastaccess} ula
                ON
                    ula.userid = cc.userid AND
                    ula.courseid = c.id
                WHERE
                    e.customint1 = ? AND status = 0
                    $catclause
            ";
            $ascendants = $DB->get_records_sql($sql, $params);
        }

        return $ascendants;
    }

    /**
     * Same but recursively
     */
    public function get_all_ascendants($catid, $seeunbound, $courseid = null) {
        global $DB;

        $ascendants = array();

        $ascendants = $this->get_ascendants($catid, $seeunbound, $courseid);
        $childcats = $DB->get_records('course_categories', array('parent' => $catid));
        if ($childcats) {
            foreach ($childcats as $c) {
                $ascendants = $ascendants + $this->get_all_ascendants($c->id, $seeunbound, $courseid);
            }
>>>>>>> MOODLE_33_STABLE
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
<<<<<<< HEAD
    *
    */
    function user_can_edit() {
        global $CFG, $COURSE;
=======
     *
     */
    public function user_can_edit() {
        global $COURSE;
>>>>>>> MOODLE_33_STABLE

        $context = context_course::instance($COURSE->id);
        if (has_capability('block/course_ascendants:configure', $context)){
 	       return true;
        }

		return false;
    }

<<<<<<< HEAD
	/**
	* tests if full course group exists
	*
	*/    
    function has_course_group(){
    	global $COURSE, $DB;
=======
    /**
     * tests if full course group exists
     *
     */
    public function has_course_group() {
        global $COURSE, $DB;
>>>>>>> MOODLE_33_STABLE

		return $DB->record_exists('groups', array('courseid' => $CORUSE->id, 'name' => $COURSE->shortname));
    }

<<<<<<< HEAD
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
=======
    /**
     * registeres a "full course" (all participants) group
     *
     */
    public function make_course_group() {
        global $COURSE, $DB;

        if (!$this->has_course_group()) {
            $groupobj->courseid = $COURSE->id;
            $groupobj->name = $COURSE->shortname;
            $groupobj->description = get_string('fullcourse', 'block_course_ascendants');
            $groupobj->timecreated = time();
            $groupobj->timemodified = time();
            $fullgroupid = $DB->insert_record('groups', $groupobj);
        }
    }
}

function sort_by_localorder(&$a, &$b) {
    if ($a->localorder > $b->localorder) {
        return 1;
    }
    if ($a->localorder < $b->localorder) {
        return -1;
    }
    return 0;
}
>>>>>>> MOODLE_33_STABLE
