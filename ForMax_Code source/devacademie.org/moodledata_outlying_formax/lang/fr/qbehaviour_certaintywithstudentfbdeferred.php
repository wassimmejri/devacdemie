<?php
// This file is part of Moodle - https://moodle.org/
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
// along with Moodle.  If not, see <https://www.gnu.org/licenses/>.

/**
 * Strings for component 'qbehaviour_certaintywithstudentfbdeferred', language 'fr', version '4.5'.
 *
 * @package     qbehaviour_certaintywithstudentfbdeferred
 * @category    string
 * @copyright   1999 Martin Dougiamas and contributors
 * @license     https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

$string['allegederror'] = 'Erreur présumée';
$string['allegederror_details'] = 'Erreur présumée ({$a})';
$string['allegederrorplural'] = 'Erreurs présumées';
$string['almostsure'] = 'Presque sûr';
$string['almostsure_details'] = 'vous étiez presque sûr';
$string['answer'] = 'votre réponse était {$a}';
$string['answercategorydetails'] = '{$a->certainty} et {$a->youranswer}';
$string['behavioursummary'] = 'Résumé des niveaux de certitude';
$string['correct'] = 'correcte';
$string['correctanswers'] = 'Réponses correctes';
$string['declaredignorance'] = 'Réponse au hasard';
$string['declaredignorance_details'] = 'Réponse au hasard (vous avez répondu au hasard, peu importe que votre réponse soit correcte ou incorrecte)';
$string['declaredignoranceplural'] = 'Réponses au hasard';
$string['expectedtrend'] = 'Tendance attendue';
$string['expectedtrend_help'] = 'Cette courbe décrit la forme globale que l’histogramme prendrait pour un comportement lucide. Plus l’histogramme est proche de la forme de cette courbe, plus vous étiez lucide vis-à-vis de vos réponses et votre certitude.';
$string['incorrect'] = 'incorrecte';
$string['incorrectanswers'] = 'Réponses incorrectes';
$string['ncorrectanswers'] = 'Réponses correctes : {$a}';
$string['ndeclaredignorance'] = 'Réponses au hasard : {$a}';
$string['nincorrectanswers'] = 'Réponses incorrectes : {$a}';
$string['numofanswers'] = 'Nombre de réponses';
$string['pleaseselectcertainty'] = 'Veuillez sélectionner une catégorie.';
$string['pluginname'] = 'Niveaux de certitude et feedback de l’étudiant (différé)';
$string['pluginsettings'] = 'Paramètres des comportements avec degrés de certitudes';
$string['quitesure'] = 'Plutôt sûr';
$string['quitesure_details'] = 'vous étiez plutôt sûr';
$string['quiteunsure'] = 'Peu sûr';
$string['quiteunsure_details'] = 'vous étiez peu sûr';
$string['random'] = 'Fifty-fifty ou moins';
$string['random_alt'] = 'J’ai répondu au hasard';
$string['random_details'] = 'vous avez répondu au hasard';
$string['random_open'] = 'Je pense que c’est faux';
$string['settings:answercategorization'] = 'Catégorisation des réponses';
$string['settings:answerclasses'] = 'Classes de réponses';
$string['settings:answerclassesinfo'] = 'Vous pouvez personnaliser la couleur associée à chaque classe de réponses.<br>
Vous pouvez modifier le nom affiché des classes de réponses via <a href="{$a}">les paramètres de personnalisation de la langue</a>.';
$string['settings:certaintylevela'] = 'Degré de certitude {$a}';
$string['settings:certaintylevels'] = 'Degrés de certitude';
$string['settings:certaintylevelsinfo'] = 'Vous pouvez personnaliser le nom affiché (si pertinent), le pourcentage affiché ainsi que la manière dont sera catégorisée chaque réponse selon qu’elle est vraie / fausse, pour chaque degré de certitude.<br>
Vous pouvez modifier plus finement le nom affiché des degrés de certitude via <a href="{$a}">les paramètres de personnalisation de la langue</a>.';
$string['settings:enablefbforclasses'] = 'Afficher le champ de commentaire étudiant pour les classes de réponses';
$string['settings:enablefbforclasses_help'] = 'Le champ proposant à l’étudiant d’écrire un commentaire à propos de sa réponse ne sera affiché que pour les réponses appartenant aux classifications sélectionnées.';
$string['settings:error:categoryorder'] = 'Veuillez conserver une continuité entre les catégorisations de degrés de certitude : d’abord un degré optionnel de Réponse au hasard, puis les degrés de Connaissance fragile / Erreurs présumées, et enfin les degrés de Connaissance solide / Erreurs insoupçonnées.';
$string['settings:label'] = 'Nom affiché';
$string['settings:loadpresets'] = 'Charger le préréglage d’échelle';
$string['settings:percentage'] = 'Pourcentage';
$string['settings:preset:alternative'] = 'Alternatif (linéaire)';
$string['settings:preset:default'] = 'Défaut (<em>legacy</em>)';
$string['settings:preset:recommended'] = 'Recommandé (non-linéaire)';
$string['settings:studentfeedback'] = 'Champ de commentaire étudiant';
$string['settings:useopenlabel'] = 'le nom alternatif de degré de certitude pour les questions ouvertes';
$string['settings:useopenlabel_help'] = 'Pour les questions ouvertes (i.e. ni Vrai/Faux ni Questions à choix multiple), ce nom alternatif peut être utilisé pour décrire ce degré de certitude.';
$string['settings:useopenlabela'] = 'Utiliser « {$a} » pour les questions ouvertes';
$string['settingsformerrors'] = 'Modifications non enregistrées. Des erreurs ont été trouvées dans les données envoyées. Veuillez vous référer aux messages d’erreur ci-après.';
$string['sure'] = 'Tout à fait sûr';
$string['sure_details'] = 'vous étiez tout à fait sûr';
$string['sureknowledge'] = 'Connaissance solide';
$string['sureknowledge_details'] = 'Connaissance solide ({$a})';
$string['sureknowledgeplural'] = 'Connaissances solides';
$string['unexpectederror'] = 'Erreur insoupçonnée';
$string['unexpectederror_details'] = 'Erreur insoupçonnée ({$a})';
$string['unexpectederrorplural'] = 'Erreurs insoupçonnées';
$string['unsure'] = 'Très peu sûr';
$string['unsure_details'] = 'vous étiez très peu sûr';
$string['unsureknowledge'] = 'Connaissance fragile';
$string['unsureknowledge_details'] = 'Connaissance fragile ({$a})';
$string['unsureknowledgeplural'] = 'Connaissances fragiles';
$string['whatisyourcertaintylevel'] = 'Quel est votre degré de certitude associé à votre réponse ?';
