<?php
// This file is part of Moodle - http://moodle.org/
//
// Moodle is free software: you can redistribute it and/or modify
// it under the terms of the GNU General Public License as published by
// the Free Software Foundation, either version 3 of the License, or
// (at your option) any later version.
//
// Moodle is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
// GNU General Public License for more details.
//
// You should have received a copy of the GNU General Public License
// along with Moodle.  If not, see <http://www.gnu.org/licenses/>.

$string['course_ascendants:addinstance'] = 'Can add to course';
$string['course_ascendants:configure'] = 'Can configure';

// Privacy
$string['privacy:metadata'] = 'The Course Ascendants block does not store any personal data about any user.';

$string['alttitle'] = 'Alternative title';
$string['alttitle_help'] = 'leave blank for default';
$string['arrangeby'] = 'Order by';
$string['arrangeby_desc'] = '';
$string['arrangebydefault'] = 'Arrange by (default)';
$string['ascendantsaccess'] = 'Access to ascendant course modules';
$string['blockname'] = 'Ascendants courses';
$string['bycats'] = 'cateogories and course order';
$string['byplan'] = 'training plan order';
$string['catstringfilter'] = 'Regex filter for filtering Category names';
$string['closed'] = 'Closed';
$string['completed'] = 'Completed';
$string['completedon'] = 'Completed on {$a->completed} ({$a->days} days)';
$string['completionlocked'] = 'Lock by completion';
$string['completionlocked_help'] = 'If this option is enabled, unstarted modules will only be available if all started modules are achieved.';
$string['configdefaultcoursegroupnamefilter'] = 'Regex filter for course group name (default)';
$string['configdefaultcoursegroupnamefilter_desc'] = 'Default value for course regex filter. Use a regexp expression with one subcapture group "()"';
$string['configdefaultarrangeby'] = 'Order by (default)';
$string['configdefaultarrangeby_desc'] = 'Default value for course arrangement order when creating new instances.';
$string['configdefaultcreatecoursegroup'] = 'Create course group (default)';
$string['configdefaultcreatecoursegroup_desc'] = 'Create course group';
$string['configdefaultcompletionlocked'] = 'Lock by completion (default)';
$string['configdefaultcompletionlocked_desc'] = 'Default value for completion lock when creating new instances.';
$string['configdefaultcoursedisplaymode'] = 'Default course display mode (default)';
$string['configdefaultcoursedisplaymode_desc'] = 'You can choose between list mode and tiles mode. This is the default values for new bloc instances.';
$string['configdefaultcoursegroupnamebase'] = 'Course group name based on';
$string['configdefaultshowcategories'] = 'Show categories (default)';
$string['configdefaultcompletionlocked_desc'] = 'Default value for displaying categories when creating new instances.';
$string['coursedisplaymode'] = 'Display mode';
$string['coursescopestartcategory'] = 'Start category';
$string['coursegroupcreated'] = 'Course group creation was required and no course group detected. Creating the course group';
$string['coursegroupnamebase'] = 'Course group name base';
$string['coursegroupnamebase_help'] = 'This pattern is used to create the group name.';
$string['coursegroupnamefilter'] = 'Regex filter for course group name';
$string['coursegroupnamefilter_help'] = 'This is applied on descendant course name to create the group name in the subrogated learning submodule.';
$string['createcoursegroup'] = 'Create course group';
$string['enrolled'] = 'visited';
$string['feedcourseids'] = 'Feed course ids: {$a->total} / {$a->done}';
$string['fullcourse'] = 'Full course group';
$string['gotocourse'] = 'Go to course';
$string['globalcoursegroup'] = 'Create full descendant course group';
$string['propagateexistinggroups'] = 'Propagate existing group memberships';
$string['list'] = 'List';
$string['manageascendants'] = 'Manage course bindings';
$string['opened'] = 'Open';
$string['options'] = 'Options';
$string['pluginname'] = 'Ascendants courses';
$string['pushnewgroups'] = 'Push course group in open metacourses when missing';
$string['showdescription'] = 'show course description';
$string['stringlimit'] = 'Length limit for course names';
$string['tiles'] = 'Tiles';
$string['title'] = 'Available subcourses';
$string['showcategories'] = 'Show categories';
$string['uncheckadvice'] = 'Care that binding out a metacourse will probably loose student data';
$string['unenrolled'] = 'Not yet visited';

$string['catstringfilter_help'] = 'A regex capable to discard some parts of a category name';

$string['coursegroupname_help'] = 'Allows choosing the information from which the full course group name will be generated';

$string['createcoursegroup_help'] = '
    A full course group is a group that encompasses all registered (non hidden direct assignation) participants in the course.
    This course scope group may be propagated into the dependant course modules when opening them.
';

$string['showdescription_help'] = 'If enabled, the course description is shown under the course link';

$string['stringlimit_help'] = 'Sets up a max limitation for the course name. Setup to 0 for unlimited length.';

$string['openclosemodules_help'] = '## Access to course ascendants
### Opening, closing parent courses

Parent courses are metacourses that inherit enrolments from this course.

When opening (assigning) an ascendant, you will link this course to the
specified metacourse, thus enroling current users and giving them access to the
metacourse content.

When closing a module (unassigning), you will unbind enrolments . Your students
will not be any more capable to enter the course, but their production
(documents, data) will remain stored within the course data. If you open again
the access to the module, your users will recover the course in the same state
they left it.';

$string['coursescopestartcategory_help'] = 'Only courses in this category subtree will be considered as assignable learning submodules';

$string['pushnewgroups_help'] = '
    You have enabled the local course group creation in block settings. If this check is enabled, the local group representing all your local course students will
    be copied and synced into any newly open course module.
';

$string['completionlocked_help'] = '
    If enabled, each course in the list is locked until the previous course has completed. This also mean that general dashboard should NOT
    expose courses available by meta-enrolment and that all modules in the course set have completion enabled and configured.
';

include(__DIR__.'/pro_additional_strings.php');