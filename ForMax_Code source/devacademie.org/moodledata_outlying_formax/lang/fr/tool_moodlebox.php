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
 * Strings for component 'tool_moodlebox', language 'fr', version '4.5'.
 *
 * @package     tool_moodlebox
 * @category    string
 * @copyright   1999 Martin Dougiamas and contributors
 * @license     https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

$string['changepassworderror'] = 'Le mot de passe de la MoodleBox n’a pas été modifié. Les mots de passe saisis ne concordent pas.';
$string['changepasswordmessage'] = 'Le mot de passe principal de la MoodleBox (compte Unix) et du serveur de base de données ont été modifiés.<br /><br />Attention ! Le mot de passe de l’utilisateur par défaut du Moodle <b>n’a pas été modifié</b>. Pour le modifier, veuillez passer par la page des préférences de cet utilisateur.';
$string['changewifisettings'] = 'Changer les réglages Wi-Fi';
$string['configuration'] = 'Options MoodleBox';
$string['cpufrequency'] = 'Fréquence du processeur';
$string['cpuload'] = 'Charge du processeur';
$string['cputemperature'] = 'Température du processeur';
$string['dashboard'] = 'Tableau de bord MoodleBox';
$string['datetime'] = 'Date et heure';
$string['datetime_help'] = 'Si la MoodleBox n’est pas connectée à Internet, elle ne sera pas à l’heure. Il est possible de la mettre à l’heure manuellement au moyen de ce réglage.';
$string['datetimemessage'] = 'L’horloge de la MoodleBox a été mise à l’heure. Pour une précision encore meilleure, il est recommandé de brancher la MoodleBox à un réseau connecté à Internet, au moyen d’un câble ethernet.';
$string['datetimeset'] = 'Changer la date et l’heure';
$string['datetimesetmessage'] = 'L’horloge de la MoodleBox n’est pas à l’heure. Il est vivement recommandé de régler la date et l’heure à leurs valeurs actuelles.';
$string['datetimesetting'] = 'Date et heure';
$string['defaultgateway'] = 'Passerelle par défaut';
$string['dhcpclientinfo'] = 'Adresse IP, nom et adresse MAC du client';
$string['dhcpclientnumber'] = 'nombre de clients';
$string['dhcpclients'] = 'Clients DHCP';
$string['documentation'] = 'Documentation MoodleBox';
$string['documentation_desc'] = '<p>Pour toute question au sujet de la MoodleBox, consulter la <a href="https://moodlebox.net/fr/help/" title="Documentation MoodleBox" target="_blank">documentation MoodleBox</a>.</p>';
$string['dynamicipaddress'] = 'Adresse IP (dynamique)';
$string['ethernetdisconnected'] = 'Ethernet déconnecté';
$string['forum'] = 'Forum d’assistance MoodleBox';
$string['forum_desc'] = '<p>Si vous ne trouvez pas de réponse à votre question dans la <a href="https://moodlebox.net/fr/help/" title="Documentation MoodleBox" target="_blank">documentation MoodleBox</a>, recherchez dans le <a href="https://discuss.moodlebox.net/" title="Forum MoodleBox" target="_blank">forum d’assistance MoodleBox</a> pour voir si votre question a déjà été abordée. Si ce n’est pas le cas, n’hésitez pas à ouvrir une nouvelle discussion.</p>';
$string['hardwareinfo'] = 'Information sur le matériel';
$string['hidden'] = 'Caché';
$string['hiddendhcpname'] = '--caché--';
$string['ihavedonated'] = 'J’ai fait un don ! 🎉';
$string['ihavedonated_desc'] = 'Cochez cette case si <a href="https://moodlebox.net/fr/donate/" title="Faire un don" target="_blank">vous avez fait un don</a> au projet MoodleBox.<br />Ce réglage n’a absolument aucun effet. Il vous permet simplement de marquer votre fierté d’avoir contribué au <a href="https://moodlebox.net/fr/" title="Site web MoodleBox" target="_blank">projet MoodleBox</a>. Mille mercis !';
$string['infofileerror'] = 'Information non disponible';
$string['infoheading'] = 'Information sur la MoodleBox';
$string['information'] = 'Information';
$string['interfacename'] = 'Nom d’interface';
$string['kernelversion'] = 'Version du noyau';
$string['missingconfigurationerror'] = 'Cette section n’est pas disponible, car l’installation du plugin n’est pas complète, et le réglage ne peut pas être traité par la MoodleBox. Veuillez consulter la <a href="https://github.com/moodlebox/moodle-tool_moodlebox/blob/master/README.md" target="_blank">documentation d’installation</a> afin de corriger cette erreur.';
$string['moodlebox:viewbuttonsinfooter'] = 'Voir les boutons de redémarrage et d’arrêt dans le pied de page';
$string['moodleboxsysteminfo'] = 'Informations MoodleBox';
$string['moodleboxsysteminfo_help'] = 'Le panneau d’informations MoodleBox affiche plusieurs données importantes sur la MoodleBox. Ces informations comprennent :

* des données essentielles au fonctionnement de votre MoodleBox, telles que l’espace disque restant sur la carte SD et la charge, température et fréquence du processeur ;
* les réglages actuels du réseau Wi-Fi fourni par la MoodleBox ;
* le nombre, l’adresse IP et le nom de tous les appareils connectés à la MoodleBox ;
* le modèle et le système d’exploitation de la Raspberry Pi ;
* la version de la MoodleBox et du plugin MoodleBox.';
$string['networkinterface'] = 'Interface de réseau câblé';
$string['parameter'] = 'Paramètre';
$string['passwordprotected'] = 'Protégé par mot de passe';
$string['passwordsetting'] = 'Mot de passe MoodleBox';
$string['passwordsetting_help'] = 'Le mot de passe principal de MoodleBox peut être changé ici. __Il est vivement déconseillé de conserver le mot de passe défini par défaut__. Vous __devez__ absolument le changer comme mesure de sécurité minimale.';
$string['pijuicebatterychargelevel'] = 'Niveau de charge de la batterie PiJuice';
$string['pijuicebatterystatus'] = 'Statut de la batterie PiJuice';
$string['pijuicebatterytemp'] = 'Température de la batterie PiJuice';
$string['pijuiceinfo'] = 'Information du PiJuice';
$string['pijuiceisfault'] = 'Défaut du PiJuice';
$string['pijuicestatuserror'] = 'Statut du PiJuice';
$string['pluginname'] = 'MoodleBox';
$string['pluginversion'] = 'Version du plugin MoodleBox';
$string['privacy:metadata'] = 'Le plugin MoodleBox affiche certaines informations de la Raspberry Pi et permet quelques modifications de configuration, mais ne touche ni n’enregistre aucune donnée personnelle.';
$string['projectinfo'] = '<p>Le <a href="https://moodlebox.net/fr/" title="Site web MoodleBox" target="_blank">projet MoodleBox</a> est un projet libre et gratuit développé bénévolement et sans but lucratif par <a href="https://blog.martignoni.net/a-propos/" title="Nicolas Martignoni" target="_blank">Nicolas Martignoni</a> sur son temps libre.</p><p>Nous vous remercions d’utiliser MoodleBox. Vous pouvez manifester votre satisfaction et soutenir ce projet <a href="https://moodlebox.net/fr/donate/" title="Faire un don" target="_blank">en faisant un don</a> ❤. Votre don contribuera au financement du matériel nécessaire au développement de la MoodleBox et à l’hébergement de sa documentation.</p>';
$string['raspberryhardware'] = 'Modèle Raspberry Pi';
$string['resizepartition'] = 'Redimensionner la partition de la carte SD';
$string['resizepartition_help'] = 'Utiliser ce bouton pour redimensionner la partition de la carte SD.';
$string['resizepartitionmessage'] = 'La partition de la carte SD a été redimensionnée à sa taille maximale. La MoodleBox redémarre maintenant. Elle sera à nouveau accessible dans quelques instants.';
$string['resizepartitionsetting'] = 'Redimensionnement de la partition de la carte SD';
$string['restart'] = 'Redémarrer la MoodleBox';
$string['restartmessage'] = 'La MoodleBox redémarre. Elle sera à nouveau accessible dans quelques instants.';
$string['restartstop'] = 'Redémarrage et arrêt';
$string['restartstop_help'] = 'Utiliser ces boutons pour redémarrer ou éteindre la MoodleBox. Il est fortement recommandé de ne pas débrancher l’alimentation pour éteindre la MoodleBox.';
$string['revision'] = 'No de révision du modèle';
$string['revisioncode'] = 'Code de révision du modèle';
$string['rpi1'] = 'Raspberry Pi 1';
$string['rpi2'] = 'Raspberry Pi 2B';
$string['rpi3aplus'] = 'Raspberry Pi 3A+';
$string['rpi3b'] = 'Raspberry Pi 3B';
$string['rpi3bplus'] = 'Raspberry Pi 3B+';
$string['rpi400'] = 'Raspberry Pi 400';
$string['rpi4eightgb'] = 'Raspberry Pi 4B (8 Go RAM)';
$string['rpi4fourgb'] = 'Raspberry Pi 4B (4 Go RAM)';
$string['rpi4onegb'] = 'Raspberry Pi 4B (1 Go RAM)';
$string['rpi4twogb'] = 'Raspberry Pi 4B (2 Go RAM)';
$string['rpi5eightgb'] = 'Raspberry Pi 5 (8 Go RAM)';
$string['rpi5fourgb'] = 'Raspberry Pi 5 (4 Go RAM)';
$string['rpiosversion'] = 'Version de Raspberry Pi OS';
$string['rpizero2w'] = 'Raspberry Pi Zero 2 W';
$string['rpizerow'] = 'Raspberry Pi Zero W';
$string['sdcardavailablespace'] = 'Espace libre sur la carte SD';
$string['showbuttonsinfooter'] = 'Afficher les boutons d’arrêt et de redémarrage dans le pied de page';
$string['showbuttonsinfooter_desc'] = 'Si ce réglage est activé, les boutons de redémarrage et d’arrêt de la MoodleBox sont affichés dans le pied de toutes les pages du site quand on est connecté comme administrateur ou gestionnaire.';
$string['showdatetimebuttonsinfooter'] = 'Afficher le réglage du temps dans le pied de page';
$string['showdatetimebuttonsinfooter_desc'] = 'Si ce réglage est activé, le réglage de la date et l’heure est affiché dans le pied de toutes les pages du site, lorsqu’on est connecté comme administrateur ou gestionnaire.';
$string['showrestartshutdownbuttonsinfooter'] = 'Afficher les boutons d’arrêt et de redémarrage dans le pied de page';
$string['showrestartshutdownbuttonsinfooter_desc'] = 'Si ce réglage est activé, les boutons d’arrêt et de redémarrage sont affichés dans le pied de toutes les pages du site, lorsqu’on est connecté comme administrateur ou gestionnaire.';
$string['shutdown'] = 'Arrêter la MoodleBox';
$string['shutdownmessage'] = 'La MoodleBox s’arrête. Veuillez attendre quelques secondes avant de retirer l’alimentation.';
$string['softwareversions'] = 'Versions logicielles';
$string['staticipaddress'] = 'Adresse IP (statique)';
$string['staticipaddress_help'] = 'Cette adresse est l’adresse IP statique du point d’accès Wi-Fi de la MoodleBox. Elle définit aussi la plage DHCP des adresses fournies aux clients Wi-Fi. L’adresse saisie doit être une adresse IP <strong>privée</strong> valide, par exemple « 10.10.1.1 », « 172.23.222.1 » or « 192.168.222.1 ». Son dernier segment sera automatiquement modifié en 1.';
$string['staticipaddressinvalid'] = 'Cette adresse IP n’est pa valide. L’adresse saisie doit être une adresse IP <strong>privée</strong> valide, par exemple « 10.10.1.1 », « 172.23.222.1 » or « 192.168.222.1 ».';
$string['systeminfo'] = 'Informations système';
$string['undervoltagedetected'] = '<p><b>Attention : sous-tension détectée !</b> L’alimentation de la MoodleBox est insuffisante, ce qui peut causer divers problèmes, par exemple une limitation du nombre de clients Wi-Fi ou même un arrêt inopiné de l’appareil.</p><p>Il est vivement recommandé de <b>changer d’alimentation</b>, en privilégiant l\'<a href="https://www.raspberrypi.com/products/micro-usb-power-supply/" target="_blank">alimentation Raspberry Pi 12.5W Micro USB</a> pour les modèles Raspberry Pi 3A+, 3B, 3B+ et Zero 2 W, ou l\'<a href="https://www.raspberrypi.com/products/type-c-power-supply/" target="_blank">alimentation Raspberry Pi 15W USB-C</a> pour la Raspberry Pi 4B, ou l\'<a href="https://www.raspberrypi.com/products/27w-power-supply/" target="_blank">alimentation Raspberry Pi 27W USB-C</a> pour Raspberry Pi 5.</p>';
$string['undervoltageoccurred'] = '<p>Une situation de sous-tension s’est produite depuis le dernier démarrage de la MoodleBox. Cela pourrait indiquer que l’alimentation électrique de la MoodleBox est insuffisante, ce qui peut causer divers problèmes, par exemple une limitation du nombre de clients Wi-Fi ou même un arrêt inopiné de l’appareil.</p>Il est vivement recommandé de <b>changer d’alimentation</b>, en privilégiant l\'<a href="https://www.raspberrypi.com/products/micro-usb-power-supply/" target="_blank">alimentation Raspberry Pi 12.5W Micro USB</a> pour les modèles Raspberry Pi 3A+, 3B, 3B+ et Zero 2 W, ou l\'<a href="https://www.raspberrypi.com/products/type-c-power-supply/" target="_blank">alimentation Raspberry Pi 15W USB-C</a> pour la Raspberry Pi 4B, ou l\'<a href="https://www.raspberrypi.com/products/27w-power-supply/" target="_blank">alimentation Raspberry Pi 27W USB-C</a> pour Raspberry Pi 5..</p>';
$string['unknowndhcpname'] = '--inconnu--';
$string['unknownmodel'] = 'Modèle de Raspberry Pi inconnu ou non pris en charge';
$string['unsupportedhardware'] = 'Matériel serveur non compatible détecté ! Ce plugin ne fonctionne que sur Raspberry Pi';
$string['uptime'] = 'Durée de fonctionnement du système';
$string['version'] = 'Version de la MoodleBox';
$string['visible'] = 'Visible';
$string['wifichannel'] = 'Canal Wi-Fi';
$string['wifichannel_help'] = 'Il n’est pas nécessaire de changer le canal de diffusion Wi-Fi, sauf en cas de mauvaises performances dues à des interférences.';
$string['wificountry'] = 'Pays de régulation Wi-Fi';
$string['wificountry_help'] = 'Pour des raisons juridiques, il est recommandé de sélectionner votre pays comme pays de régulation Wi-Fi.';
$string['wifipassword'] = 'Mot de passe Wi-Fi';
$string['wifipassword_help'] = 'Si vous avez choisi un réseau Wi-Fi protégé par mot de passe, pour éviter que des intrus utilisent le réseau Wi-Fi de la MoodleBox, il est recommandé de modifier son mot de passe par défaut. Le mot de passe du réseau Wi-Fi doit comporter entre 8 et 63 caractères ASCII imprimables (lettres minuscules et majuscules sans diacritiques, chiffres, ponctuation et quelques autres symboles).';
$string['wifipasswordinvalid'] = 'Le mot de passe du réseau Wi-Fi n’est pas valide. Il doit comporter entre 8 et 63 caractères ASCII imprimables (lettres minuscules et majuscules sans diacritiques, chiffres, ponctuation et quelques autres symboles).';
$string['wifipasswordon'] = 'Protection réseau Wi-Fi';
$string['wifipasswordon_help'] = 'Si ce réglage est activé, les utilisateurs doivent saisir un mot de passe pour se connecter au réseau Wi-Fi de la MoodleBox.';
$string['wifisettings'] = 'Réglages Wi-Fi';
$string['wifisettingserror'] = 'Les réglages du réseau Wi-Fi n’ont pas été modifiés. Certains réglages ne sont pas valides.';
$string['wifisettingsmessage'] = 'Les réglages Wi-Fi ont été modifiés. N’oubliez pas de communiquer à vos étudiants le nom du réseau Wi-Fi et le mot de passe.';
$string['wifissid'] = 'Nom du réseau Wi-Fi';
$string['wifissid_help'] = 'Le nom du réseau Wi-Fi (SSID) de la MoodleBox. Il doit s’agir d’une chaîne de 1 octet au minimum et de 32 octets au maximum. N’oubliez pas que certains caractères, comme les émojis, utilisent plus d’un octet.';
$string['wifissidhidden'] = 'Réseau Wi-Fi caché';
$string['wifissidhiddenstate'] = 'Visibilité du SSID Wi-Fi';
$string['wifissidhiddenstate_help'] = 'Si ce réglage est activé, le SSID Wi-Fi sera caché pour les utilisateurs, qui ne sauront pas qu’il y a une MoodleBox aux environs. Ceci diminuera considérablement la convivialité de l’appareil, mais améliorera très légèrement sa sécurité.';
$string['wifissidinvalid'] = 'Le nom du réseau Wi-Fi (SSID) indiqué n’est pas valide. Il doit s’agir d’une chaîne de 1 octet au minimum et de 32 octets au maximum.';
