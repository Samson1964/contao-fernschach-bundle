# Fernschach-Verwaltung Changelog

## Version 0.6.3 (2022-10-28)

* Add: tl_settings.fernschach_resetUpdate editierbar gemacht, um Einfluß auf die Neuberechnung der Kontostände aller Spieler zu haben
* Fix: Buchungsliste -> Bei Berechnung des Saldos für jede Buchung wurde der globale Reset nicht berücksichtigt -> Fehler in Helper.php:95

## Version 0.6.2 (2022-10-28)

* Change: Zeitstempel-Steuerung für Aktualisierung der globalen Reset-Buchungen eingebaut
* Add: Spezialfilter in Auflistung tl_fernschach_spieler für fehlende ICCF-Nummer
* Add: Buchungsimport anhand ICCF-Mitgliedsnummer
* Fix: Spezialfilter "Alle Mitglieder" funktionierte nicht -> fehlendes break bei case
* Add: Spezialfilter in Auflistung tl_fernschach_spieler für fehlende E-Mail-Adresse(n)

## Version 0.6.1 (2022-10-19)

* Fix: Export "ORDER BY name,vorname ASC': SQLSTATE[42S22]: Column not found: 1054 Unknown column 'name' in 'order clause'"

## Version 0.6.0 (2022-10-19)

* Change: tl_fernschach_spieler.status -> Bisher Textfeld, jetzt Select-Liste
* Change: tl_fernschach_spieler -> Bereich Verein dem Bereich Mitgliedschaften zugeordnet
* Change: tl_fernschach_spieler.streichung -> Bessere Beschreibung
* Add: tl_fernschach_spieler_konto.sortierung -> Bis zu zweistelligen Wert angeben, um Buchungen bei gleichem Datum wie gewünscht zu sortieren
* Change: Saldo-Ausgaben/Berechnungen in tl_fernschach_spieler_konto um Feld sortierung erweitert
* Change: tl_fernschach_spieler.memberships.status -> Beschreibung von "Status" auf "Bemerkung" geändert
* Add: Ausgabe des Saldos in Auflistung tl_fernschach_spieler
* Add: Spezialfilter in Auflistung tl_fernschach_spieler -> Nur Mitglieder (Nicht archiviert, Status Mitglied, Veröffentlicht und mind. eine gültige Mitgliedschaft)
* Change: Aktualisierung der globalen Reset-Buchungen ausgelagert in Helper-Klasse
* Add: Export nach Excel für die angezeigten Spieler
* Add: Abhängigkeit phpoffice/phpspreadsheet in composer.json
* Add: Spezialfilter in Auflistung tl_fernschach_spieler für fehlendes Geburtsdatum
 
## Version 0.5.5 (2022-09-16)

* Fix: Buchungsimport -> 'INSERT INTO tl_fernschach_turniere (`tstamp`, `titel`, `published`) VALUES (1663096448, 'MS-317', '')': SQLSTATE[42S22]: Column not found: 1054 Unknown column 'titel' in 'field list' -> Richtig ist title
* Change: Reset-Buchungen in Auflistung tl_fernschach_spieler_konto mit Grundlinie statt Icon gekennzeichnet

## Version 0.5.4 (2022-09-09)

* Add: tl_fernschach_spieler.archived -> Archiv-Checkbox für Markierung von archivierten Spielern. In der Übersicht werden archivierte Spieler in grauer Schrift dargestellt.
* Add: tl_fernschach_spieler_konto.resetRecord -> Checkbox für Markierung der Buchung als Reset-Datensatz (Verwaltung Reset-Datensatz über tl_settings)
* Add: tl_settings für die Einstellungen der Fernschach-Verwaltung
* Add: tl_settings.fernschach_resetActive -> Aktiviert/Deaktiviert die globale Resetbuchung
* Add: tl_settings.fernschach_resetDate -> Setzt das Datum für die globale Resetbuchung
* Add: tl_settings.fernschach_resetSaldo -> Setzt den Saldo für die globale Resetbuchung
* Add: Callback in tl_fernschach_spieler_konto für die Statusprüfung der globalen Resetbuchung

## Version 0.5.3 (2022-07-14)

* Add: Markierungs-Icon in der Buchungsübersicht (als Toogler mit Haste) -> Hintergrundfarbe wird vom Toggler nicht gewechselt
* Change: Saldo-Reset-Icon vor den Betrag gesetzt
* Add: tl_fernschach_spieler_konto.importDate -> nichtbearbeitbares Feld mit dem Importzeitpunkt als Unixzeitstempel (wird gesetzt beim Import)
* Change: Saldo wird in der Buchungsübersicht jetzt bei allen Buchungen angezeigt

## Version 0.5.2 (2022-07-13)

* Add: tl_fernschach_spieler_konto.saldoReset -> Setzt den Saldo auf 0 zurück und rechnet den neuen Saldo mit dem Buchungsbetrag weiter
* Add: tl_fernschach_spieler_konto.markierung -> Markiert die Buchung mit anderer Farbe in der Buchungsliste
* Fix: Saldoberechnung falsch, Sortierung nach Buchungsdatum fehlte
* Add: Import Buchungen -> Feld kategorie (Feld art gefixt)
* Add: Import Buchungen -> Feld resetSaldo (reset in CSV-Datei)
* Add: Import Buchungen -> Feld markieren
* Change: Buchungen ohne Typ (Soll/Haben) werden mit gelber Schrift dargestellt

## Version 0.5.1 (2022-07-12)

* Add: tl_fernschach_spieler.fertig zur Markierung ob der Datensatz fertig bearbeitet wurde
* Add: haste_ajax_operation in tl_fernschach_spieler für Toggler der Spalte fertig
* Add: tl_fernschach_konto Filter für kategorie verbessert
* Fix: Fehler in Filter-Abfrage bei Saldo-Ermittlung
* Add: Die Verknüpfung einer Buchung zu einer Meldung kann manuell geändert werden.

## Version 0.5.0 (2022-07-11)

* Change: tl_fernschach_turniere_meldungen -> Umbau des Meldeformulars
* Add: tl_fernschach_spieler_konto.meldungId -> Enthält die ID des Datensatzes in tl_fernschach_turniere_meldungen
* Add: Funktion in tl_fernschach_turniere_meldungen zum Aktualisieren von tl_fernschach_spieler_konto 
* Add: tl_fernschach_spieler_konto.kategorie für Unterscheidung Beitrag oder Guthaben
* Change: tl_fernschach_spieler_konto.art -> Beitrag und Guthaben in kategorie ausgelagert

## Version 0.4.1 (2022-06-28)

* Add: Spieler-Modul - 2. Adresse
* Change: Zu durchsuchende Felder minimiert
* Change: Zu filternde Felder minimiert
* Change: Zu sortierende Felder minimiert

## Version 0.4.0 (2022-06-28)

* Ausbau tl_fernschach_spieler_konto  
* Fix: Sortierung Mitgliedsnummer falsch
* Fix: Buchungsimport

## Version 0.3.6 (2022-05-23)

* Change: Inhaltselement Zusagen soll diese erst nach dem Start des Turniers anzeigen
* Fix: Zusagen (BdF + Veranstalter) werden im FE nicht angezeigt
* Change: Template ce_fernschach_zusagen.html5: Statt Zusagen heißt es jetzt Bestätigungen

## Version 0.3.5 (2022-05-19)

* Add: tl_fernschach_spieler.titelinfo für den nachfolgenden Import
* Change: ImportSpieler neue Felder titelhalter und iccftitel -> wird bei tl_fernschach_spieler.titelinfo hinzugefügt

## Version 0.3.4 (2022-05-17)

* Add: tl_fernschach_spieler zusätzliche Felder anhand Stammdaten-Datenbank angelegt -> anrede, klassenberechtigung, telefax1, telefax2, streichung, briefanrede, gastNummer, servertesterNummer, fremdspielerNummer, zuzug, adresszusatz, verein, status
* Change: ImportSpieler mit neuen Feldern versehen und Überschreibfunktionen angepaßt

## Version 0.3.3 (2022-05-13)

* Change: Bewerbungen in Turnier-Einstellungen nur anzeigen, wenn Bewerbungen erlaubt sind
* Fix: In Einstellungen von Turnieren mit Bewerbungen fehlen die Namen
* Fix: Im Formular für die Bewerbungen sollte Vor- und Nachname automatisch ausgefüllt werden, wenn leer
* Add: Importfunktion für Turniere wieder eingebaut

## Version 0.3.2 (2022-05-12)

* Change: tl_fernschach_spieler -> Bearbeitungslink für Bewerbungen deaktiviert
* Add: Informationen zu den Bewerbungen im Baum von tl_fernschach_turniere
* Fix: Im Navigationspfad des Turnierbaumes fehlte ein Icon
* Add: tl_fernschach_turniere_bewerbungen.stateOrganizer für Aktivierung der Veranstalterzusage
* Change: Inhaltselement Zusagen angepaßt wegen der neuen Baumstruktur
* Change: Inhaltselement Zusagen -> Veranstalterzusage erforderlich

## Version 0.3.1 (2022-05-11)

* Add: tl_fernschach_turniere_meldungen.meldungDatum -> time() als default
* Change: Zuordnung Meldungen zu den Turniergruppen optimiert

## Version 0.3.0 (2022-05-10)

* Change: ce_fernschach_zusagen h3 statt h2 als Überschrift
* Add: tl_fernschach_turnierkategorien für die Verwaltung der Turnierkategorien
* Add: tl_fernschach_turnierhauptklassen für die Verwaltung der Turnierhauptklassen
* Change: tl_fernschach_turniere auf Baumstruktur umgebaut, die alten Felder kommen in den Typ tournament
* Add: tl_fernschach_turniere_meldungen und tl_fernschach_turniere_spieler
* Add: tl_fernschach_turniere_bewerbungen

## Version 0.2.2 (2022-04-22)

* Fix: Inhaltselement Zusagen - Spieler wurden nicht angezeigt im Frontend

## Version 0.2.1 (2022-04-22)

* Change: Icons in tl_fernschach_turniere verkleinert von 16 auf 12px
* Fix: tl_module Meldeformular Turnieranmeldung
* Add: tl_fernschach_turniere.turnierleiterUserId für die Zuordnung eines Turnierleiters/Turniers zu einem Backend-Benutzer
* Add: Meldedatum des Turniers in Meldeformular ausgeben
* Change: Auswahl der Turniere im Meldeformular verbessert
* Add: tl_fernschach_turniere.onlineAnmeldung - Checkbox, ob das Turnier im Online-Meldeformular angezeigt werden soll
* Add: tl_fernschach_turniere.spielerMax - Maximale Anzahl von Spielern festlegen
* Add: tl_fernschach_turniere.art - Turnierart (Klassenturnier, Thematurnier usw.)
* Add: tl_fernschach_turniere.artInfo - Freies Feld für die Turnierart
* Add: Im Turnier die Anzahl der Bewerbungen ausgeben
* Add: Nutzung Notification-Center
* Add: Inhaltselement Zusagen
* Add: tl_fernschach_turniere.applicationText für Zusagen-Ansicht im Frontend

## Version 0.2.0 (2022-04-15)

* Turnierimport fertiggestellt (classes/ImportTurniere)
* Buchungsimport fertiggestellt (classes/ImportBuchungen)

## Version 0.1.1 (2022-04-14)

* Add: tl_member.fernschach_memberId für Zuordnung eines BdF-Spielers/Mitglieds zu einem Frontend-Mitglied
* Add: Turniertyp Einladungsturnier in tl_fernschach_turniere inkl. Filtermöglichkeit
* Change: tl_fernschach_turniere - Ausgabe der Datensätze beim Status mit Icons statt Texten
* Fix: Titel werden nicht angezeigt vom Modul TitelNormenLast

## Version 0.1.0 (2022-04-13)

* Add: Backend-Module Spieler, Turniere, Meldungen
* Add: tl_fernschach_spieler, tl_fernschach_mitgliedschaften
* Add: tl_fernschach_turniere, tl_fernschach_meldungen
* Add: Abhängigkeit codefog/contao-haste
* Add: Frontend-Modul Meldeformular.php
* Change: Ausbau tl_fernschach_mitgliedschaften
* Add: Zuordnung der Meldungen zu Spielern in tl_fernschach_spieler, ggfs. Neuanlegen des Spielers
* Change: Anpassung tl_fernschach_spieler anhand von tl_mitgliederverwaltung
* Delete: tl_fernschach_mitgliedschaften -> kommt in tl_fernschach_spieler mit rein
* Kompletteinbau des contao-mitgliederverwaltung-bundle

## Version 0.0.1 (2022-02-24)

* Initiale Version für Contao 4
