<?php

include $CFG->libdir.'/formslib.php';

class Assign_Form extends moodleform{

	var $blockinstance;

	function __construct($action, &$theBlock){
		$this->blockinstance = $theBlock;
		parent::__construct($action);
	}

	function definition(){
		global $COURSE, $DB;

		// take local as default		
		if (empty($this->blockinstance->config->coursescopestartcategory)) $this->blockinstance->config->coursescopestartcategory = $COURSE->category;

		$categories = array();
		$this->blockinstance->read_category_tree($this->blockinstance->config->coursescopestartcategory, $categories, true);
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
		$mform = $this->_form;

		$mform->addElement('hidden', 'course', $COURSE->id);
		$mform->addElement('hidden', 'id', $this->blockinstance->instance->id);
		foreach($courseoptions as $cc => $cs){
			$mform->addElement('header', 'h'.$cc, format_string($cc));
			foreach($cs as $cid => $name){
				$notifytext = get_string('uncheckadvice', 'block_course_ascendants');
				$radioarray = array();
				$radioarray[] =& $mform->createElement('radio', 'c'.$cid, '', get_string('open', 'block_course_ascendants'), 1, array('onchange' => "notifyeffect('$notifytext')"));
				$radioarray[] =& $mform->createElement('radio', 'c'.$cid, '', get_string('close', 'block_course_ascendants'), 0, array('onchange' => "notifyeffect('$notifytext')"));
				$mform->addGroup($radioarray, 'radioar', format_string($name), array('&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;'), false);
			}
		}

		$mform->addElement('header', 'options', get_string('options', 'block_course_ascendants'));
		$pushnewgroupsstr = get_string('pushnewgroups', 'block_course_ascendants');
		$mform->addElement('checkbox', 'pushnewgroups', $pushnewgroupsstr);

		$this->add_action_buttons(true);
	}

	function validation($data){
	}
}