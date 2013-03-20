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
    'perpage' => 4,
    'perrow' => 2,
    'news' => array(
        array(
            'title' => 'Version 1.13.0 zum Download verfügbar',
            'content' =>
                  'Die Version 1.13.0 ist abgeschlossen und als Download '
                . 'verfügbar.',
            'timestamp' => 1363766390,
        ),
        array(
            'title' => 'Version 2.x verfügbar',
            'content' =>
                  'Nachdem die Version 1.x ihre Praxistauglichkeit bewiesen '
                . 'hat und durch viele Erweiterungen bereichert wurde gibt es '
                . 'nun mit der Version 2.x die nächste Generation. Der Core '
                . 'des Projektes ist umgebaut auf Zend Framework 2 und steht '
                . 'über GitHub und per Composer über Packagist Als Zend '
                . 'Framework 2 Modul zur Verfügung.',
            'timestamp' => 1363215430,
        ),
        array(
            'title' => 'Version 1.12.0 zum Download verfügbar',
            'content' =>
                  'Die Version 1.12.0 ist abgeschlossen und als Download '
                . 'verfügbar. Die Version ist weniger umfangreich als die '
                . 'vorherigen Versionen. Sie bietet eine Überarbeitung der '
                . 'Unterstützung von verschachtelten Record Listen mit der '
                . 'Storage Engine des ZendDbAdapters und neue Features zur '
                . 'Filterung und Auslesen der Record Listen selbst.',
            'timestamp' => 1358288796,
        ),
        array(
            'title' => 'Version 1.11.1 zum Download verfügbar',
            'content' =>
                  'Die Version 1.11.1 ist abgeschlossen und als Download '
                . 'verfügbar. Es wurde ein Fehler behoben der auftrat man man '
                . 'eine Clientmessage in der Datenbank speichern wollte da '
                . 'die Methodensignatur der "toArray()" Methode nicht mehr '
                . 'mit der Änderung der Eigenschaftenklasse übereinstimmte.',
            'timestamp' => 1357860765,
        ),
        array(
            'title' => 'Version 1.11.0 zum Download verfügbar',
            'content' =>
                  'Die Version 1.11.0 ist abgeschlossen und als Download '
                . 'verfügbar. Es wurde eine eigene Registryklasse hinzugefügt '
                . 'welche Lazy Loading unterstützt und Werte erst geladen '
                . 'wenn auf die Keys zugegriffen wird. Des Weiteren gab es '
                . 'Performanceverbesserungen bei den Records und '
                . 'Verbesserungen bei der Storage Engine wie das automatische '
                . 'verwalten verschachtelter Transaktionen.',
            'timestamp' => 1357772880,
        ),
        array(
            'title' => 'Version 1.10.1 zum Download verfügbar',
            'content' =>
                  'Die Version 1.10.1 ist abgeschlossen und als Download '
                . 'verfügbar. Es wurde ein Fehler behoben der auftrat wenn '
                . 'ein Pre/Postdispatch Plugin oder Service eine Ausnahme '
                . 'geworfen hat die nicht von den eigenen Ausnahmeklassen '
                . 'abgeleitet sind.',
            'timestamp' => 1353521731,
        ),
        array(
            'title' => 'Version 1.10.0 zum Download verfügbar',
            'content' =>
                  'Die Version 1.10.0 ist abgeschlossen und als Download '
                . 'verfügbar. Es wurde die Ausnahmebehandlung erweitert '
                . 'sodass Ausnahmen die an den Client gesendet werden nun '
                . 'automatisch geloggt werden und der Schweregrad abhängig '
                . 'von der geworfenen Ausnahmeklasse definiert werden kann. '
                . 'Ausserdem gibt es kleinere Fehlerbehebungen und eine '
                . 'Erweiterung des JsonClients bei dem nun die '
                . 'Callbackmethoden Zugriff auf das JsonRequest Objekt haben.',
            'timestamp' => 1353358673,
        ),
        array(
            'title' => 'Version 1.9.1 zum Download verfügbar',
            'content' =>
                  'Die Version 1.9.1 ist abgeschlossen und als Download '
                . 'verfügbar. Es wurde ein Fehler behoben der auftrat wenn '
                . 'man ein Objekt einer Eigenschaftenklasse mit Arraydaten '
                . 'befüllte zu denen keine Attribute vorhanden waren.',
            'timestamp' => 1352915933,
        ),
        array(
            'title' => 'Version 1.9.0 zum Download verfügbar',
            'content' =>
                  'Die Version 1.9.0 ist abgeschlossen und als Download '
                . 'verfügbar. Es wurde eine eigene Ausnahmeklasse eingebaut '
                . 'durch welche die das Logging von Ausnahmen und Rückgabe '
                . 'zum Client verinfacht. Des Weiteren erlauben Plugins nun '
                . 'die Angabe von Abhängigkeiten die die Reihenfolge bestimmt '
                . 'in welcher diese aufgerufen werden. Bei der Storage Engine '
                . 'hat sich die Verwendung von Records mit "clone" geändert. '
                . 'Wenn man nun eine Kopie eines Records erstellt erhält man '
                . 'auch aus Datenbanksicht einen neuen Record, daher ohne ID '
                . 'der beim Speichern einen neuen Datensatz erstellt.',
            'timestamp' => 1352752407,
        ),
        array(
            'title' => 'Version 1.8.0 zum Download verfügbar',
            'content' =>
                  'Die Version 1.8.0 ist abgeschlossen und als Download '
                . 'verfügbar. In der Version wurden die Struktur für die '
                . 'Install Plugins umgebaut, sodass jedes Plugin sich nun '
                . 'in einer eigenen Transaktionen installiert/updatet und '
                . 'Fehler nicht mehr zu inkonsistenten Zuständen führen. '
                . 'diesen nicht die Datenbankstruktur gefährden. Des Weiteren '
                . 'wurde das Handling der Multirequests beim Client '
                . 'vereinfacht und auch der DragonJsonClient hat durch die '
                . 'URI Funktionalität Features dazugewonnen.',
            'timestamp' => 1352411135,
        ),
        array(
            'title' => 'Version 1.7.0 zum Download verfügbar',
            'content' =>
                  'Die Version 1.7.0 ist abgeschlossen und als Download '
                . 'verfügbar. Vor allem die neue Accountverwaltung bei der '
                . 'Accounts nun an beliebige Loginmechanismen wie E-Mail '
                . 'Adresse und Passwort Kombination verknüpft werden können '
                . 'und viele Verbesserungen an der Storage Engine zählen zu '
                . 'den Inhalten der Version.',
            'timestamp' => 1351342158,
        ),
        array(
            'title' => 'Version 1.6.1 zum Download verfügbar',
            'content' =>
                  'Die Version 1.6.1 ist abgeschlossen und als Download '
                . 'verfügbar. In der Version wurde ein Fehler in der '
				. 'Reihenfolge der Initialisierungen von Plugin und '
				. 'Repository Registry behoben wodurch es nun auch möglich '
				. 'ist Plugins in anderen Repositories zu definieren.',
            'timestamp' => 1348162516,
        ),
        array(
            'title' => 'Version 1.6.0 zum Download verfügbar',
            'content' =>
                  'Die Version 1.6.0 ist abgeschlossen und als Download '
                . 'verfügbar. In der Version hat sie die Projektstruktur '
                . 'durch die Unterstützung von Repositories geändert. Diese '
                . 'erlauben es nun die eigenen Dateien von den Dateien des '
                . 'Frameworks zu trennen und somit das Aktualisieren des '
                . 'Framework zu vereinfachen.',
            'timestamp' => 1347580549,
        ),
        array(
            'title' => 'Version 1.5.0 zum Download verfügbar',
            'content' =>
                  'Die Version 1.5.0 ist abgeschlossen und als Download '
                . 'verfügbar. In der Version wurde die Accountverwaltung '
                . 'durch ein für Homepage und API gemeinsames '
                . 'Sessionmanagement abgeschlossen und durch die Möglichkeit '
                . 'der temporären Profile abgerundet.',
            'timestamp' => 1347036792,
        ),
        array(
            'title' => 'Version 1.4.0 zum Download verfügbar',
            'content' =>
                  'Die Version 1.4.0 ist abgeschlossen und als Download '
                . 'verfügbar. Geändert hat sich vor allem das Layout der '
                . 'Homepage mit insbesondere die Startseite, das Pagination, '
                . 'die Formulare und die Hinweismeldungen. Des Weiteren sind '
                . 'nun auch die Ressourcenabfragen für Navigationselemente '
                . 'und Controlleraufrufe nutzbar sowie die "Sofort loslegen" '
                . 'Möglichkeit.',
            'timestamp' => 1346854222,
        ),
        array(
            'title' => 'Version 1.3.1 zum Download verfügbar',
            'content' =>
                  'Die Version 1.3.1 ist abgeschlossen und als Download '
                . 'verfügbar. In der Version wurde ein Fehler bei der '
                . 'automatischen Weiterleitung behoben wenn man ohne '
                . 'eingeloggten Account auf die Startseite des '
                . 'Administrationsbereiches zugreifen wollte.',
            'timestamp' => 1346720647,
        ),
        array(
            'title' => 'Version 1.3.0 zum Download verfügbar',
            'content' =>
                  'Die Version 1.3.0 ist abgeschlossen und als Download '
                . 'verfügbar. In der Version gab es einige Verbesserungen der '
                . 'vorhandenen Pakete, die Auftrennung der Homepage in einen '
                . 'öffentlichen und administrativen Bereich und die Grundlage '
                . 'für die Benutzerverwaltung durch Ressourcen und Rollen. '
                . 'Des Weiteren wurde die Accountverwaltung erweitert sodass '
                . 'nun Accounts nach der Registrierung validiert, geändert '
                . 'und gelöscht werden können.',
            'timestamp' => 1346707689,
        ),
        array(
            'title' => 'Version 1.2.6 zum Download verfügbar',
            'content' =>
                  'Die Version 1.2.6 ist abgeschlossen und als Download '
                . 'verfügbar. In der Version wurde ein Fehler beim Senden des '
                . 'HTTP Headers bei Multirequests sowie die Probleme '
                . 'der Abfrage der Clientnachrichten bei Multirequests '
                . 'behoben. Clientnachrichten werden nun nur beim letzten '
                . 'Request angefordert und der letzten Antwort bearbeitet.',
            'timestamp' => 1345752151,
        ),
        array(
            'title' => 'Version 1.2.5 zum Download verfügbar',
            'content' =>
                  'Die Version 1.2.5 ist abgeschlossen und als Download '
                . 'verfügbar. In der Version wurden Fehler beim Laden von '
                . 'Records und RecordLists aus dem ZendDbAdapter Storage '
                . 'wodurch Records nicht richtig auf NULL gesetzt wurden und '
                . 'es zu Fehlermeldungen kommen konnte. Des Weiteren war die '
                . 'Erstellung des RSS Feeds fehlerhaft und wurde korrigiert. ',
            'timestamp' => 1345584896,
        ),
        array(
            'title' => 'Version 1.2.4 zum Download verfügbar',
            'content' =>
                  'Die Version 1.2.4 ist abgeschlossen und als Download '
                . 'verfügbar. In der Version wurde vor allem ein Fehler im '
                . 'Paket Account behoben. Der Fehler trat auf, wenn bei einem '
                . 'Service keine API Dokumentation mit einer Annotation '
                . 'zur Ermittlung der Notwendigkeit eines gültigen Accounts '
                . 'vorhanden war.',
            'timestamp' => 1345467048,
        ),
        array(
            'title' => 'Version 1.2.3 zum Download verfügbar',
            'content' =>
                  'Die Version 1.2.3 ist abgeschlossen und als Download '
                . 'verfügbar. In der Version wurde in den Paketen Log und '
                . 'Cronjob SQL Fehler bei der Neuinstallation behoben.',
            'timestamp' => 1345414897,
        ),
        array(
            'title' => 'Version 1.2.2 zum Download verfügbar',
            'content' =>
                  'Die Version 1.2.2 ist abgeschlossen und als Download '
                . 'verfügbar. In der Version wurde das Diagramm zur '
                . 'Architektur auf den aktuellen Stand gebracht mit den '
                . 'beiden Paketen Storage und Clientmessage.',
            'timestamp' => 1345413861,
        ),
        array(
            'title' => 'Version 1.2.1 zum Download verfügbar',
            'content' =>
                  'Die Version 1.2.1 ist abgeschlossen und als Download '
                . 'verfügbar. Es gab zwei Fehler die behoben wurden. Zum '
                . 'Einen gab es einen SQL Fehler im Paket Log beim '
                . 'Übertragen der Daten in die neue Tabellenstruktur und '
                . 'zum Anderen war die abstrakte Klasse für die Keys der '
                . 'Clientnachrichten nicht als abstrakt definiert.',
            'timestamp' => 1345412535,
        ),
        array(
            'title' => 'Version 1.2.0 zum Download verfügbar',
            'content' =>
                  'Die Version 1.2.0 ist abgeschlossen und als Download '
                . 'verfügbar. Weitreichende Änderungen ergab vor allem der '
                . 'Umstieg auf die Storage Engine. Diese ermöglicht es wie '
                . 'ein ORM Records in die Datenbank oder andere Datenquellen '
                . 'zu speichern. Im Gegensatz zu ORMs wird dabei jedoch nicht '
                . 'auf SQL verzichtet sondern effektiv unterstützt. Ein '
                . 'weiteres neues Feature ist die Accountverwaltung für die '
                . 'Homepage mit der man sich nun Registrieren und Anmelden '
                . 'kann sowie die Möglichkeit sein Passwort zurück zu setzen. ',
            'timestamp' => 1345397367,
        ),
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
