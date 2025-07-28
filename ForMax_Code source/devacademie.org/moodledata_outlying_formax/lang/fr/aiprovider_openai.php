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
 * Strings for component 'aiprovider_openai', language 'fr', version '4.5'.
 *
 * @package     aiprovider_openai
 * @category    string
 * @copyright   1999 Martin Dougiamas and contributors
 * @license     https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

$string['action:generate_image:endpoint'] = 'Point de terminaison API';
$string['action:generate_image:model'] = 'Modèle IA';
$string['action:generate_image:model_desc'] = 'Le modèle utilisé pour générer des images sur la base de prompts utilisateur.';
$string['action:generate_text:endpoint'] = 'Point de terminaison API';
$string['action:generate_text:model'] = 'Modèle IA';
$string['action:generate_text:model_desc'] = 'Le modèle utilisé pour générer la réponse textuelle.';
$string['action:generate_text:systeminstruction'] = 'Instruction système';
$string['action:generate_text:systeminstruction_desc'] = 'Cette instruction est envoyée au modèle IA avec le prompt de l’utilisateur. Il n’est pas recommandé de la modifier, sauf si c’est absolument nécessaire.';
$string['action:summarise_text:endpoint'] = 'Point de terminaison API';
$string['action:summarise_text:model'] = 'Modèle IA';
$string['action:summarise_text:model_desc'] = 'Le modèle utilisé pour résumer le texte fourni.';
$string['action:summarise_text:systeminstruction'] = 'Instruction système';
$string['action:summarise_text:systeminstruction_desc'] = 'Cette instruction est envoyée au modèle IA avec le prompt de l’utilisateur. Il n’est pas recommandé de la modifier, sauf si c’est absolument nécessaire.';
$string['apikey'] = 'Clef API OpenAI';
$string['apikey_desc'] = 'Obtenir une clef dans les <a href="https://platform.openai.com/account/api-keys" target="_blank">clefs OpenAI Platform API</a>.';
$string['enableglobalratelimit'] = 'Fixer la limite du taux de requêtes pour le site';
$string['enableglobalratelimit_desc'] = 'Limite le nombre de requêtes que tout le site peut faire au fournisseur API OpenAI toutes les heures.';
$string['enableuserratelimit'] = 'Fixer la limite du taux de requêtes pour le site par utilisateur';
$string['enableuserratelimit_desc'] = 'Limite le nombre de requêtes que chaque utilisateur peut faire au fournisseur API OpenAI toutes les heures.';
$string['globalratelimit'] = 'Nombre maximum de requêtes pour tout le site';
$string['globalratelimit_desc'] = 'Le nombre de requêtes permises par heure, pour tout le site.';
$string['orgid'] = 'ID d’organisation OpenAI';
$string['orgid_desc'] = 'Obtenir une ID d’organisation OpenAI sur votre <a href="https://platform.openai.com/account/org-settings" target="_blank">compte OpenAI Platform</a>';
$string['pluginname'] = 'Fournisseur API OpenAI';
$string['privacy:metadata'] = 'Le plugin fournisseur API OpenAI n’enregistre aucune donnée personnelle.';
$string['privacy:metadata:aiprovider_openai:externalpurpose'] = 'Cette information est envoyée à l’API OpenAI pour permettre la génération d’une réponse. Les réglages de votre compte OpenAI peuvent modifier la façon dont OpenAI enregistre ces données. Aucune donnée personnelle n’est directement envoyée à OpenAI ou enregistrée dans Moodle par ce plugin.';
$string['privacy:metadata:aiprovider_openai:model'] = 'Le modèle utilisé pour générer la réponse.';
$string['privacy:metadata:aiprovider_openai:numberimages'] = 'Lorsque des images sont générées, le nombre d’images de la réponse.';
$string['privacy:metadata:aiprovider_openai:prompttext'] = 'Le prompt saisi par l’utilisateur pour générer la réponse.';
$string['privacy:metadata:aiprovider_openai:responseformat'] = 'Lorsque des images sont générées, le format de la réponse.';
$string['userratelimit'] = 'Nombre maximum de requêtes par utilisateur';
$string['userratelimit_desc'] = 'Le nombre de requêtes permises par heure, par utilisateur.';
