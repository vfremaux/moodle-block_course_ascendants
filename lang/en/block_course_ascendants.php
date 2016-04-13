<?php

$string['course_ascendants:addinstance'] = 'Can add to course';
$string['course_ascendants:configure'] = 'Can configure';

$string['ascendantsaccess'] = 'Access to ascendant course modules';
$string['arrangebydefault'] = 'Arrange by (default)';
$string['blockname'] = 'Ascendants courses';
$string['configcatstringfilter'] = 'Regex filter for filtering Categoy names';
$string['configcatstringfilter_help'] = 'A regex capable to discard some parts of a category name';
$string['configcoursegroupname'] = 'Course group name base';
$string['configcoursegroupname_help'] = 'Allows choosing the information from which the full course group name will be generated';
$string['configcreatecoursegroup'] = 'Create course group';
$string['configcreatecoursegroup_help'] = 'A full course group is a group that encompasses all registered (non hidden direct assignation) participants in the course. This course scope group may be propagated into the dependant course modules when opening them.';
$string['configshowdescription'] = 'show course description';
$string['configshowdescription_help'] = 'If enabled, the course description is shown under the course link';
$string['configstringlimit'] = 'Length limit for course names';
$string['configstringlimit_help'] = 'Sets up a max limitation for the course name. Setup to 0 for unlimited length.';
$string['configarrangeby'] = 'Order by';
$string['bycats'] = 'cateogories and course order';
$string['byplan'] = 'training plan order';
$string['coursegroupcreated'] = 'Course group creation was required and no course group detected. Creating the course group';
$string['coursegroupnamebase'] = 'Course group name';
$string['coursegroupnamefilter'] = 'Regex filter for course group name';
$string['createcoursegroup'] = 'Create course group';
$string['configcoursescopestartcategory'] = 'Start category';
$string['configcoursescopestartcategory_help'] = 'Only courses in this category subtree will be considered';
$string['fullcourse'] = 'Full course group';
$string['manageascendants'] = 'Manage course bindings';
$string['options'] = 'Options';
$string['open'] = 'Open';
$string['close'] = 'Close';
$string['pluginname'] = 'Ascendants courses';
$string['pushnewgroups'] = 'Push course group in open metacourses when missing';
$string['configshowcategories'] = 'Show categories';
$string['title'] = 'Available subcourses';
$string['uncheckadvice'] = 'Care that binding out a metacourse will probably loose student data';
$string['enrolled'] = 'visited';
$string['unenrolled'] = 'Not yet visited';
$string['completed'] = 'Completed';
$string['completedon'] = 'Completed on {$a->completed} ({$a->days} days)';


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

$string['pushnewgroups_help'] = '
    You have enabled the local course group creation in block settings. If this check is enabled, the local group representing all your local course students will
    be copied and synced into any newly open course module.
';
