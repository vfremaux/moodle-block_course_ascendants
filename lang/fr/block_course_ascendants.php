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

$string['course_ascendants:addinstance'] = 'Ajouter un bloc Cours Ascendants';
$string['course_ascendants:configure'] = 'Peut configurer';

$string['ascendantsaccess'] = 'Accès aux modules liés';
$string['arrangebydefault'] = 'Arranger par (défaut)';
$string['blockname'] = 'Cours ascendants';
$string['createcoursegroup'] = 'Créer le groupe de cours';
$string['configcatstringfilter'] = 'Filtre sur les catégories';
$string['configcatstringfilter_help'] = 'Une expression régulière permettant de ne garder qu\'une portion du nom de catégorie';
$string['configcoursegroupname'] = 'Base du nom pour groupe de cours';
$string['configcoursegroupname_help'] = 'Ce paramètre permet de sélectionner la donnée de base à partir de laquelle le nom du groupe de cours est créé';
$string['configcreatecoursegroup_help'] = 'Un groupe de cours local est un groupe qui prend toutes les personnes présentes du cours. Ce groupe s\'il existe, peut être transféré et synchronisé dans les modules de cours liés pour le cursus, au moment de leur l\'ouverture.';
$string['configshowdescription'] = 'Afficher la description de cours';
$string['configshowdescription_help'] = 'Si cette option est active, la description de cours sera affichée en dessous du lien vers le cours';
$string['configstringlimit_help'] = 'Fixe une limite maximale pour la longueur des labels de liens. Laisser à 0 pour aucune limite.';
$string['configarrangeby'] = 'Arranger par';
$string['bycats'] = 'catégories';
$string['byplan'] = 'ordre du plan de formation';
$string['coursegroupcreated'] = 'La création automatique de groupe de cours est active et aucun groupe n\'était détecté. Le groupe de cours est créé.';
$string['coursegroupname'] = 'Nom du groupe de cours';
$string['coursegroupnamefilter'] = 'Filtre (regexp) pour nom de cours';
$string['configcoursescopestartcategory'] = 'Catégorie de choix des cours ';
$string['configcreatecoursegroup'] = 'Créer automatiquement le groupe de cours local';
$string['fullcourse'] = 'Groupe complet';
$string['manageascendants'] = 'Ouvrir/fermer des modules';
$string['options'] = 'Options';
$string['open'] = 'Ajouter';
$string['close'] = 'Retirer';
$string['pluginname'] = 'Cours ascendants';
$string['pushnewgroups'] = 'Pousser les nouveaux groupes lors de l\'ouverture';
$string['configshowcategories'] = 'Voir les catégories ';
$string['configstringlimit'] = 'Limite de longueur des noms ';
$string['title'] = 'Mon plan de formation';
$string['uncheckadvice'] = 'Attention, refermer un module peut conduire à une perte des productions de certains étudiants';
$string['enrolled'] = 'En cours';
$string['unenrolled'] = 'pas encore commencé';
$string['completed'] = 'Terminé';
$string['completedon'] = 'Terminé le {$a->completed} (en {$a->days} jours)';

$string['opencoursemodules_help'] = '
    <h3>Ouvrir / fermer des modules</h3>
    
    <p>En ouvrant des modules, vous accrochez des méta-cours à ce cours. Vos 
        apprenants deviennent automatiquement apprenants enregistrés du module
    de formation sans aucune autre action.</p>
    <p>en formant un module, vous déliez le module de formation de ce cours. Les
        apprenants ne pourront plus s\'y rendre, mais leurs productions (document, données)
        restent préservées. Si vous ouvrez à nouveau ce module, les apprenants qui y ont 
    eu une activité retrouveront leur compte dans le même état.</p>
';

$string['pushnewgroups_help'] = '
    Vous avez activé la synchronisation du groupe de cours local. si vous laissez cete case cochée, un groupe correspondant au
    groupe de cours local sera créé et synchronisé dans tous les nouveaux modules ouverts.
';