<?php

$string['plugindist'] = 'Distribution du plugin';
$string['plugindist_desc'] = '
<p>Ce plugin est distribu� dans la communaut� Moodle pour l\'�valuation de ses fonctions centrales
correspondant � une utilisation courante du plugin. Une version "professionnelle" de ce plugin existe et est distribu�e
sous certaines conditions, afin de soutenir l\'effort de d�veloppement, am�lioration; documentation et suivi des versions.</p>
<p>Contactez un distributeur pour obtenir la version "Pro" et son support.</p>
<p><a href="http://www.mylearningfactory.com/index.php/documentation/Distributeurs?lang=fr_utf8">Distributeurs MyLF</a></p>';

require_once($CFG->dirroot.'/blocks/course_ascendants/lib.php'); // to get xx_supports_feature();
if ('pro' == block_course_ascendants_supports_feature()) {
    include($CFG->dirroot.'/blocks/course_ascendants/pro/lang/fr/pro.php');
}
