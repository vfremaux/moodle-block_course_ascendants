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
$string['course_ascendants:configure'] = 'Configurer';

// Privacy
$string['privacy:metadata'] = 'Le bloc Cours Ascendants ne détient directement aucune donnée relative aux utilisateurs.';

$string['alttitle'] = 'Titre alternatif';
$string['alttitle_help'] = 'Laisser vide pour le titre par défaut';
$string['arrangeby'] = 'Arranger par';
$string['ascendantsaccess'] = 'Accès aux modules liés';
$string['badcmid'] = 'Id de module d\'activité invalide';
$string['blockname'] = 'Cours ascendants';
$string['bycats'] = 'Catégories';
$string['byplan'] = 'Ordre du plan de formation';
$string['catstringfilter'] = 'Filtre sur les catégories';
$string['catstringfilter_help'] = 'Une expression régulière permettant de ne garder qu\'une portion du nom de catégorie';
$string['closed'] = 'Retiré';
$string['cmlockon'] = 'Verrou d\'activité : ';
$string['completed'] = 'Terminé';
$string['completedon'] = 'Terminé le {$a->completed} (en {$a->days} jours)';
$string['completionlocked'] = 'Verrouiler la liste sur l\'achèvement de cours';
$string['completionlocked_help'] = 'Si cette option est active, les modules non commencés ne sont pas accessibles tant que tous les modules commencés sont achevés';
$string['configdefaultarrangeby'] = 'Arranger par (défaut)';
$string['configdefaultarrangeby_desc'] = 'Valeur par défaut de l\'ordre de rangement des cours pour les nouvelles instances.';
$string['configdefaultcoursedisplaymode'] = 'Mode d\'affichage des cours (défaut)';
$string['configdefaultcoursedisplaymode_desc'] = 'Vous pouvez choisir entre le mode "liste" ou le mode "cartes". Ceci est la valeur par défaut pour les nouvelles instances de blocs.';
$string['configdefaultcoursegroupnamebase'] = 'Base du nom pour le groupe de cursus (défaut)';
$string['configdefaultcoursegroupnamefilter'] = 'Filtre d\'extraction du nom de groupe (défaut)';
$string['configdefaultcoursegroupnamefilter_desc'] = 'Extrait le nom de groupe de la base du nom. Une expression régulière disposant d\'un sous-motif de capture "()".';
$string['configdefaultcreatecoursegroup'] = 'Creation du groupe de cursus (default)';
$string['configdefaultcreatecoursegroup_desc'] = 'Valeur par défaut de l\'option de création du groupe de cursus. Le groupe de cursus rassemble tous les étudiants inscrits dans les modules provenant d\'un cursus partieulier';
$string['showcategories'] = 'Voir les catégories ';
$string['stringlimit'] = 'Limite de longueur des noms ';
$string['configtitle'] = 'Titre du bloc (laisser vide pour le titre par défaut).';
$string['coursedisplaymode'] = 'Mode d\'affichage';
$string['coursegroupcreated'] = 'La création automatique de groupe de cours est active et aucun groupe n\'était détecté. Le groupe de cours est créé.';
$string['coursegroupnamebase'] = 'Base du nom pour groupe de cours';
$string['coursegroupnamebase_help'] = 'Ce paramètre permet de sélectionner la donnée de base à partir de laquelle le nom du groupe de cours est créé';
$string['coursegroupnamefilter'] = 'Filtre (regexp) pour nom de cours';
$string['courselock'] = 'Achèvement du cours';
$string['coursescopestartcategory'] = 'Catégorie de choix des cours ';
$string['courseboxheight'] = 'Hauteur de boite de courd';
$string['courseboxheight_desc'] = 'Concerne uniquement le mode d\'affichage par boîte. Peut être surchagé par la valeur du plugin local_my s\'il est installé';
$string['createcoursegroup'] = 'Créer automatiquement le groupe de cours local';
$string['createcoursegroup_help'] = 'Un groupe de cours local est un groupe qui prend toutes les personnes présentes du cours. Ce groupe s\'il existe, peut être transféré et synchronisé dans les modules de cours liés pour le cursus, au moment de leur l\'ouverture.';
$string['enrolled'] = 'En cours';
$string['feedcourseids'] = 'Calcul des ids de cours : {$a->total} / {$a->done}';
$string['fullcourse'] = 'Groupe complet';
$string['gotocourse'] = 'Aller au cours';
$string['globalcoursegroup'] = 'Créer le groupe de cours dans les modules de formation';
$string['propagateexistinggroups'] = 'Propager les groupes existants';
$string['list'] = 'Liste';
$string['manageascendants'] = 'Ouvrir / fermer des modules';
$string['nolock'] = 'Pas de verrouillage';
$string['opened'] = 'Ajouté';
$string['options'] = 'Options';
$string['pluginname'] = 'Cours ascendants';
$string['pushnewgroups'] = 'Pousser les nouveaux groupes lors de l\'ouverture';
$string['showcategories'] = 'Voir les catégories ';
$string['showdescription'] = 'Afficher la description de cours';
$string['showdescription_help'] = 'Si cette option est active, la description de cours sera affichée en dessous du lien vers le cours';
$string['stringlimit'] = 'Limite de longueur des noms ';
$string['stringlimit_help'] = 'Fixe une limite maximale pour la longueur des labels de liens. Laisser à 0 pour aucune limite.';
$string['tiles'] = 'Cartes';
$string['title'] = 'Mon parcours de formation';
$string['uncheckadvice'] = 'Attention, refermer un module peut conduire à une perte des productions de certains étudiants';
$string['unenrolled'] = 'pas encore commencé';


$string['completionlocked_help'] = '
    Si cette fonction est activée, les liens des cours ne seront libérés que si le module précédent de la liste a été achevé. Ceci suppose aussi que :

    Les tableaux de bord ou listes de cours ne permettent pas aux utilisateurs d\'atteindre directement le cours en cas d\'inscription par métacours,
    et que l\'achèvement de cours soit configuré dans tous les modules.
';

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

$string['coursescopestartcategory_help'] = 'Les cours de cette catégorie et ses descendantes pourront être assignés comme modules d\'apprentissage';

include(__DIR__.'/pro_additional_strings.php');
