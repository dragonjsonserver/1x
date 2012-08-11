<?php
/**
 * LICENSE
 *
 * This source file is subject to the new BSD license that is bundled with this
 * package in the file LICENSE.txt. It is also available through the
 * world-wide-web at this URL: http://dragonjsonserver.de/license. If you did
 * not receive a copy of the license and are unable to obtain it through the
 * world-wide-web, please send an email to license@dragonjsonserver.de. So we
 * can send you a copy immediately.
 *
 * @copyright Copyright (c) 2012 DragonProjects (http://dragonprojects.de)
 * @license http://framework.zend.com/license/new-bsd New BSD License
 * @author Christoph Herrmann <developer@dragonjsonserver.de>
 */

/**
 * @return array
 */
return array(
    'amount' => 2,
    'perrow' => 2,
    'news' => array(
        array(
            'title' => 'Version 1.1.1 zum Download verfügbar',
            'content' =>
                  'Die Version 1.1.1 ist abgeschlossen und als Download '
                . 'verfügbar. Darin wurden zwei Fehler behoben. Zum Einen '
                . 'wird beim Einfügen von Datensätzen per "_insert", "_query" '
                . 'und "_insertupdate" immer die Last Insert ID zurückgegeben '
                . 'wenn mindestens ein Datensatz hinzugefügt wurde und zum '
                . 'Anderen ist ein Fehler im DragonJsonClient behoben der '
                . 'alle Daten der Vorbelegung in die Ausgabe des Requests '
                . 'mit angezeigt hat.',
            'timestamp' => 1344451518,
        ),
        array(
            'title' => 'Version 1.1.0 zum Download verfügbar',
            'content' =>
                  'Die Version 1.1.0 ist abgeschlossen und als Download '
                . 'verfügbar. Die größten Neuerungen sind die neuen Pakete '
                . 'DragonX_Log und DragonX_Cronjob, die Unterstützung von '
                . 'Multirequests (Bündelung mehrerer Serviceanfragen in einem '
                . 'HTTP Request) und die Verwaltung der Datenbankstruktur '
                . 'durch Installationsplugins.',
            'timestamp' => 1342988194,
        ),
        array(
            'title' => 'Version 1.0.0 zum Download verfügbar',
            'content' =>
                  'Die Version 1.0.0 ist abgeschlossen und als Download '
                . 'verfügbar. Mit im Downloadpaket der Projektvorlage '
                . 'enthalten sind alle Grundfunktionalitäten zur Erstellung '
                . 'eines Json Servers, der generische Json Client und '
                . 'optionale Pakete für die Homepage, die Datenbank und einer '
                . 'einfachen Accountverwaltung.',
            'timestamp' => 1341663737,
        ),
        array(
            'title' => 'Projektstart von DragonJsonServer',
            'content' =>
                  'Die Grundstruktur des Projektes steht, die Demo ist '
                . 'funktionsfähig, die Anmeldung bei Google Code ist '
                . 'abgeschlossen, die Domain ist in Arbeit, die Open Source '
                . 'Lizenz ist ausgewählt. Kurz: Das Projekt ist gestartet. '
                . 'Die nächsten Tage noch die letzten Arbeiten an '
                . 'einer ersten Version durchführen und der erste Download '
                . 'kann Online gestellt werden.',
            'timestamp' => 1339186768,
        ),
    )
);
