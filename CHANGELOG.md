# Fernschach-Verwaltung Changelog

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
