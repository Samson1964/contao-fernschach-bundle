# Fernschach-Verwaltung Changelog

## Version 1.1.3 (2025-06-08)

* Fix: Funktion getTurnierleiter wiederhergestellt, da Auslagerung in Klasse Turnier auf Fehler 500 aufläuft

## Version 1.1.2 (2025-06-08)

* Add: tl_fernschach_spieler.contribution_paid (Beitrag 2025 bezahlt) -> wiederhergestellt, falls noch benötigt
* Add: tl_fernschach_spieler.checkBeitrag (Beitrag ≤ 2024 bezahlt) -> wiederhergestellt, falls noch benötigt

## Version 1.1.1 (2025-06-08)

* Fix: tl_fernschach_spieler.beitragsschulden nicht in der Palette

## Version 1.1.0 (2025-06-08)

* Fix: Bei Turnieranmeldung im Backend landet die Buchung im Hauptkonto -> in tl_fernschach_turniere_meldungen auf tl_fernschach_spieler_konto_nenngeld korrigiert
* Add: tl_fernschach_turniere_meldungen -> onload_callback für Rechteprüfung (Hintergrund: Anmeldungen löschen nur bestimmten Personen erlauben) -> Neue Anmeldung funktioniert noch nicht richtig
* Fix: Warning: Undefined array key 0 beim Zugriff auf Benutzerrechte erweitern
* Add: Turniermeldung als Bcc an Admin (in Betaphase)
* Add: Turnieranmeldung: In der Anmeldemail soll die ICCF-Nummer mit drinstehen.
* Delete: Turnieranmeldung: Text "Es fehlen SEPA-Mandate..." entfernen -> auskommentiert in Meldeformular.php
* Fix: Maximale Spielerzahl bei Turnieranmeldungen wird nicht berücksichtigt
* Add: Klassenberechtigung bei Turnieranmeldungen berücksichtigen
* Add: Wenn Hauptkonto 0, dann nicht im Profil/Turnieranmeldung im Frontend anzeigen -> leider funktioniert Profil nicht
* Change: Funktion getTurnierleiter verschoben von tl_fernschach_turniere_meldungen nach Classes\Turnier
* Delete: Funktion Helper.updateMitgliedschaften, da nicht mehr verwendet
* Add: Meldung der Wartungsfunktion ausgeben
* Fix: Maintenance Wartungsfunktion arbeitet mit falschen Intervallen
* Delete: tl_fernschach_spieler.contribution_paid (Beitrag 2025 bezahlt)
* Delete: tl_fernschach_spieler.checkBeitrag (Beitrag ≤ 2024 bezahlt)
* Add: tl_fernschach_spieler.beitragsschulden -> Spieler als Schuldner markieren

## Version 1.0.2 (2025-05-06)

* Change: Bei Turnieranmeldung wird im Buchungsdatensatz der Turniertitel im Verwendungszweck hinzugefügt.
* Fix: ondelete_callback in tl_fernschach_turniere_meldungen -> Beim Löschen einer Meldung wurde im falschen Buchungskonto gelöscht
* Add: Ausgabe der letzten 5 Turnieranmeldungen im Meldeformular

## Version 1.0.1 (2025-05-06)

* Fix: Attempted to call function "error_log" from the global namespace. (nur im Live-Web) -> log_message auskommentiert
* Fix: tl_fernschach_turniere_meldungen.memberId ist unique -> entfernt, da hier nichts eindeutig sein darf
* Add: tl_fernschach_turniere_meldungen.player mit Unterfeld playerIn -> Als Teilnehmer eines Turniers festgelegt und playerIn ist die Turnier-ID
* Change: tl_fernschach_turniere_meldungen.player/playerIn -> Funktionsfähigkeit eingebaut inkl. Mehrere überschreiben/bearbeiten
* Fix: tl_fernschach_turniere_meldungen -> Sortierreihenfolge falsch, nicht tstamp DESC sondern meldungDatum DESC ist richtig
* Add: Klasse ZeigeTeilnehmer für die Anzeige der Teilnehmer eines Turniers

## Version 1.0.0 (2025-05-04)

* Add: tl_fernschach_turniere.turnierleiterEmail -> filter von false auf true
* Fix: Invalid CSRF token. Please reload the page and try again. (beim Abschicken einer Turnieranmeldung) -> REQUEST_TOKEN im Formular vergessen
* Fix: Beim Bearbeiten einer Buchung wird im Kopf ein falsches Konto angezeigt, z.B. Nenngeldkonto von x statt Nenngeldkonto von y -> Template-Hook korrigiert
* Change: Meldeformular SEPA- und Kontenprüfung geändert
* Change: Dokumentation TURNIERANMELDUNGEN.md
* Add: Meldeformular -> Bearbeitungslink für das gemeldete Turnier in der Mail an die TL anzeigen
* Change: Turniermodul Alpha-Version entfernt

## Version 0.21.11 (2025-05-03)

* Add: tl_user.fernschach_turnierzugriff analog tl_user_group -> aber beide Felder in der Palette deaktiviert, weil das Widget noch programmiert werden muß
* Fix: Saldozugriff auch bei Exporten beachten
* Add: tl_fernschach_turniere.archived -> Archiviert-Checkbox um nach Archivstatus filtern zu können
* Fix: Warning: Undefined array key "initAccounts_confirm" in src/Resources/contao/dca/tl_fernschach_konten.php (line 72) 
* Fix: tl_fernschach_turniere panelLayout korrigiert
* Fix: Warning: Undefined variable $objForms in src/Modules/Meldeformular.php (line 468) 
* Change: Klasse Meldeformular umgebaut von Haste/Form auf meine eigene Form-Klasse
* Add: tl_fernschach_turniere.formview und .formtitle -> nur bei Ordnern: optional Anzeige des Ordners als optgroup in der Turnieranmeldung
* Add: Turnieranmeldung nur möglich, wenn alle SEPA-Mandate vorliegen

## Version 0.21.10 (2025-03-23)

* Fix: Fehler beim Zugriff auf Haste/Form: Attempted to load class "Form" from namespace "Codefog\HasteBundle\Form" -> \Haste\Form\Form statt \Codefog\HasteBundle\Form\Form
* Fix: Der Saldo wird nicht korrekt exportiert -> Das Datum beim Spieler-Export wird ignoriert, was natürlich ein Fehler ist. -> Es wurde immer das aktuelle Datum plus 10 Jahre gesetzt, ein Überbleibsel der alten Funktion

## Version 0.21.9 (2025-01-20)

* Add: Klasse MoveBuchung für das Verschieben einzelner Buchungen
* Add: Operationen in den drei Buchungskonten für das Verschieben einzelner Buchungen

## Version 0.21.8 (2025-01-08)

* Change: ImportBuchungen.php log-Message auskommentiert
* Change: ImportBuchungen.php Option für Kontoart eingebaut
* Fix: Warning: Undefined array key "id" in src/Classes/ImportBuchungen.php (line 171) 
* Fix: Warning: Undefined array key "id" in src/Classes/ImportBuchungen.php (line 209) 
* Fix: Warning: Undefined array key "memberInternationalId" in src/Classes/ImportBuchungen.php (line 178) 
* Fix: Warning: Undefined array key 10 in src/Classes/ImportBuchungen.php (line 97) 
* Fix: An exception occurred while executing a query: SQLSTATE[22001]: String data, right truncated: 1406 Data too long for column 'typ' at row 1 beim Import von Buchungen

## Version 0.21.7 (2025-01-04)

* Change: Newsletter.php auskommentiert -> Tokens werden nicht ersetzt, und zwar auch die von Contao!

## Version 0.21.6 (2025-01-04)

* Change: log_message deaktiviert in Newsletter.php

## Version 0.21.5 (2025-01-02)

* Fix: Attempted to call function "error_log" from the global namespace. Did you mean to call "\safe\error_log"? -> Export.php log_message deaktiviert

## Version 0.21.4 (2024-12-27)

* Fix: Abhängigkeit codefog/contao-haste von * auf <5 geändert, da in Haste 5 die Namesapces geändert wurden. Haste 5 ist erst ab PHP 8.1 möglich.

## Version 0.21.3 (2024-12-26)

* Fix: Attempted to load class "Form" from namespace "Haste\Form". Did you forget a "use" statement for e.g. "Symfony\Component\DomCrawler\Form", "Schachbulle\ContaoHelperBundle\Classes\Form", "Schachbulle\ContaoFideidBundle\Modules\Form" or "Codefog\HasteBundle\Form\Form"? in src/Modules/Meldeformular.php (line 92) 
* Change: Turniermeldeformular überarbeitet, damit die Buchung auf das Nenngeldkonto geht
* Add: Ausbau tl_fernschach_konten mit Klasse Init, um einen Standardkontorahmen zu erstellen.
* Fix: Warning: Undefined array key "type" in src/Resources/contao/dca/tl_fernschach_konten.php (line 451) 
* Fix: Warning: Undefined array key "pasteafter" in src/Resources/contao/dca/tl_fernschach_konten.php (line 459) 

## Version 0.21.2 (2024-12-23)

* Add: tl_fernschach_spieler.contribution_paid -> Feld für Beitrag 2025 bezahlt
* Change: tl_fernschach_spieler.checkBeitrag -> Übersetzung "Beitrag bezahlt" geändert auf "Beitrag ≤ 2024 bezahlt"

## Version 0.21.1 (2024-12-01)

* Fix: Warning: Undefined variable $arr in Hooks/Newsletter.php (line 31) 
* Fix: Warning: Undefined array key "im" in src/Modules/TitelNormenLast.php (line 191) -> Wert in TL_Lang ergänzt, aber "im" darf es eigentlich nicht geben
* Change: Nahschachtitel in tl_fernschach_spieler_titel entfernt, da diese wohl für Verwirrung sorgen

## Version 0.21.0 (2024-10-06)

* Change: Klasse VerschiebeBuchungen fertiggestellt
* Fix: Undefined constant PhpOffice\PhpSpreadsheet\Style\NumberFormat::FORMAT_CURRENCY_EUR_SIMPLE in Classes/Export.php (line 133) => \PhpOffice\PhpSpreadsheet\Style\NumberFormat::FORMAT_CURRENCY_EUR_SIMPLE ersetzt durch '#,##0.00_-" €"'
* Add: Kenncode-Generierung an Datum binden (Beim Excel-Export wird ein Formular vorgeschaltet, was das Datum abfragt, zu dem der Kenncode generiert werden soll.)

## Version 0.20.8 (2024-10-03)

* Add: Benutzerrechte für Setzen des SEPA-Status an Bearbeitung der Felder tl_fernschach_spieler.sepaBeitrag und tl_fernschach_spieler.sepaNenngeld gekoppelt

## Version 0.20.7 (2024-07-25)

* Fix: Aufruf Helper::getSaldo hat sich geändert -> _nenngeld statt nenngeld und _beitrag statt beitrag

## Version 0.20.6 (2024-07-25)

* Fix: Anzeige Kontocheck KBN falsch
* Fix: Anzeige der Konten Nenngeld und Beitrag im Frontend falsch
* Add: Automator-Klasse für Cronjobs, erst einmal ohne Funktion

## Version 0.20.5 (2024-07-15)

* Fix: Attempted to call an undefined method named "checkKonto" of class "Schachbulle\ContaoFernschachBundle\Classes\Helper" in Modules/Kontoauszug.php (line 72) 

## Version 0.20.4 (2024-07-11)

* Add: Models Nenngeldkonto und Beitragskonto
* Fix: Globale Resetbuchungen in Beitrags- und Nenngeldkonto werden nicht ausgewertet
* Fix: Anzeige von "Konto geprüft" nicht korrekt bei Beitrags- und Guthaben-Konto -> Globale Resetbuchungen wurden versehentlich berücksichtigt, deshalb zusätzliche Abfrage nach resetRecord eingefügt
* Change: Helper::checkKonto -> Funktion ausgelagert in eigene Klasse Resetbuchung_2023

## Version 0.20.3 (2024-07-08)

* Change: Model HauptkontoModel zu Hauptkonto
* Add: Model Spieler für den Zugriff auf tl_fernschach_spieler

## Version 0.20.2 (2024-07-07)

* Add: Klasse ResetUtil für Kontoresets -> Optimierung des Codes, weil diverse alte und neue Funktionen herumgeistern. Suffix Util, weil Reset ein reserviertes Wort in PHP ist
* Change: tl_settings.fernschach_resetRecords.datum -> date statt datim, weil nur das Datum benötigt wird
* Add: Model HauptkontoModel für den Zugriff auf tl_fernschach_spieler_konto
ACHTUNG: Das Model funktioniert nicht

## Version 0.20.1 (2024-07-04)

* Fix: tl_fernschach_spieler_konto* -> sorting fields korrigiert
* Fix: The autoloader expected class "Schachbulle\ContaoFernschachBundle\Classes\MoveBuchungen" to be defined in file "schachbulle/contao-fernschach-bundle/src/Classes/MoveBuchungen.php". The file was found but the class was not in it, the class name or namespace probably has a typ -> Klasse MoveBuchungen hatte den Namen VerschiebeBuchungen
* Fix: Falsche Sortierung der Buchungen im Beitragskonto
* Add: Link globale Operation verschiebeBuchungen im Nenngeld- und Beitragskonto

## Version 0.20.0 (2024-06-27)

* Fix: Formularelemente im FE-Modul Kontoauszug sind nicht editierbar
* Change: tl_settings.fernschach_resetActive -> Bisher kann nur ein Reset gesetzt werden. Jetzt sind bis zu 9 Resets anlegbar (Nummer 1 bis 9) und für welche Konten diese gelten.
* Add: Neuer Spezialfilter in tl_fernschach_spieler -> Beitragskonto im Minus
* Fix: Sortierungsfeld in Buchungen wird als Datum angezeigt
* Change: Buchungen -> Kategorie sortierbar gemacht. Dafür Sortierbarkeit von Änderungsdatum entfernt.
* Change: Klasse VerschiebeBuchungen in MoveBuchungen umbenannt, damit es keine Kollission gibt
* Add: Klasse VerschiebeBuchungen für globale Verschiebung von Buchungen (Demomodus)
* Add: Klasse DCAParser für eigene Backend-Formulare

## Version 0.19.0 (2024-06-11)

* Change: Ausgabe von Geldbeträgen und Salden im Kontoauszug ohne Umbrüche
* Change: Icons zu den Buchungskonten ausgetauscht -> jetzt mit Anzeige der Buchungszahl und ausgegraut, wenn keine Buchungen vorhanden sind
* Add: tl_settings.fernschach_hinweis_kontoauszug -> Hinweistext im Kontoauszug, wenn Benutzer kein BdF-Mitglied ist (war vorher hardcodiert)
* Add: tl_settings.*_time -> Der Rhythmus der 3 Wartungsfunktionen läßt sich jetzt im Backend festlegen (war vorher hardcodiert)
* Add: tl_fernschach_spieler_konto Anzeige der Kategorie (Guthaben, Beitrag, Nenngeld) in der Auflistung
* Change: tl_fernschach_spieler_konto Auflistung umgebaut von mode 4 auf 2, um Spalten zu haben.
* Add: Hook parseBackendTemplate für Modifizierung der Überschrift und des Headers im Backend
* Fix: Warning: Undefined array key "fernschach_resetSaldo" in Classes/Helper.php (line 439) -> isset($GLOBALS['TL_CONFIG']['fernschach_resetActive']) führt zu einem true, was falsch wäre -> Standardvariable definiert
* Change: tl_fernschach_spieler_konto_beitrag Auflistung umgebaut von mode 4 auf 2, um Spalten zu haben.
* Change: tl_fernschach_spieler_konto_nenngeld Auflistung umgebaut von mode 4 auf 2, um Spalten zu haben.
* Add: Verschiebefunktion von Buchungen
* Add: tl_module.fernschachverwaltung_konten -> Auswahl der Konten (Haupt, Beitrag, Nenngeld), die angezeigt werden sollen im Kontoauszug
* Fix: Warning: Undefined variable $kontoauszug in Modules/Kontoauszug.php (line 142) 
* Fix: Warning: Undefined variable $saldo in Modules/Kontoauszug.php (line 141) 
* Fix: Warning: Undefined variable $buchungen in Modules/Kontoauszug.php (line 143) 
* Fix: Warning: Undefined variable $objPlayer in Modules/Kontoauszug.php (line 145) 
* Fix: Warning: Undefined variable $html in Modules/Kontoauszug.php (line 173) 
* Fix: Warning: Undefined variable $fehler in Modules/Kontoauszug.php (line 148) 

## Version 0.18.8 (2024-05-23)

* Fix: print_r in Helper-Klasse entfernt

## Version 0.18.7 (2024-05-22)

* Fix: Saldo wird bei Buchungen in der Zukunft nicht geändert -> Warning: Undefined array key 168689 in dca/tl_fernschach_spieler_konto.php (line 503) -> hat die Ursache nicht in nichtberücksichtigten Buchungen bei der Saldenberechnung

## Version 0.18.6 (2024-04-17)

* Add: Haste-Toggler in tl_fernschach_turniere_spieler.php

## Version 0.18.5 (2024-04-13)

* Fix: Haste/Form nicht gefunden in Meldeformular-Klasse
* Add: Hilfsklasse Titel -> für den Zugriff auf tl_fernschach_spieler_titel
* Fix: TitelNormen gab keine Titel mehr aus nach der Umstellung auf die tl_fernschach_spieler_titel

## Version 0.18.4 (2024-04-04)

* Fix: TitelNormenLast gab keine Titel mehr aus nach der Umstellung auf die tl_fernschach_spieler_titel
* Add: tl_fernschach_spieler.patron -> Checkbox um einen Gönner zu markieren (im Bereich Mitgliedschaften)
* Add: Spieler-Export nach Excel mit Angabe eines Saldodatums
* Add: Helper::getSaldo mit 3. Parameter, um ein Datum übergeben zu können
* Fix: Warning: Undefined array key 93 bei Zugriff auf Turniere - tl_fernschach_turniere.php (line 1099) 
* Fix: Warning: Undefined variable $temp in tl_fernschach_turniere.php (line 1107) 
* Fix: Warning: Attempt to read property "extension" on null in tl_fernschach_spieler.php (line 2162) 
* Fix: Bewerbungen/Anmeldungen zu Turnieren werden unter Spieler falsch angezeigt (Hinweis: Wunderlich) -> falsche Variable verwendet
* Fix: Fehlende Übersetzungen tl_fernschach_spieler

## Version 0.18.3 (2024-03-08)

* Change: Filter in tl_fernschach_spieler "Nicht Mitglied nach 31.12.JJJJ" von -9 auf -16 Jahre erhöht

## Version 0.18.2 (2024-01-27)

* Change: Drei Filter mit Namen "Geprüft" -> geändert auf "Geprüft K", "Geprüft B", "Geprüft N" (Konto, Beitrag, Nenngeld)

## Version 0.18.1 (2024-01-20)

* Add: Filter in tl_fernschach_spieler -> "Mitglieder neu in 2024", der Beitritte in 2024 wiedergibt (egal ob Neu- oder Wiedereintritt)
* Add: Helper-Funktion isMember zur Ermittlung einer Mitgliedschaft an einem bestimmten Datum oder in einem bestimmten Jahr

## Version 0.18.0 (2024-01-17)

* Add: tl_fernschach_spieler_konto_beitrag -> enthält die Beitragsgelder aus ehemaliger tl_fernschach_spieler_konto (kategorie = b)
* Add: tl_fernschach_spieler_konto_nenngeld -> enthält die Nenngelder/Guthaben aus ehemaliger tl_fernschach_spieler_konto (kategorie = s/g)
* Change: Helper-Funktion getSaldo umgebaut auf 2. Parameter für die Kontotabelle
* Change: Übersetzungen tl_fernschach_spieler verkürzt/überarbeitet
* Fix: Using $this when not in object context beim Aufruf der checkKonto-Funktion der Helper-Klasse -> $this->createNewVersion ersetzen nicht möglich, da "Non-static method Contao\Controller::createNewVersion() cannot be called statically" -> Zeile auskommentiert
* Change: tl_fernschach_turniere -> Nenngeld-Feld verfeinert miit true/false 
* Add: Klasse Turnier für Nenngeldabfrage u.a.
* Add: tl_fernschach_turniere -> bei Typ Ordner kann ein Nenngeld angegeben werden
* Fix: Warning: Undefined array key "breadcrumb" bei Auswahl einer Ordnerstruktur bei Turnieren
* Add: tl_fernschach_turniere.nenngeldView für die Anzeige des Nenngeldes aus übergeordnetem Turnier
 
## Version 0.17.3 (2024-01-06)

* Fix: Column not found: 1054 Unknown column 'resetSaldo' in 'field list' -> bei Buchungsimport mit Feld reset -> Feld heißt saldoReset

## Version 0.17.2 (2024-01-02)

* Delete: tl_fernschach_spieler -> Titelfelder entfernt (GM, IM usw.)
* Add: tl_fernschach_spieler -> neue Spezialfilter als Ergänzung zu "Mitgliedsende TT.MM.JJJJ": "Nicht Mitglied nach TT.MM.JJJJ"
* Add: Export mit dem neuen Spezialfilter ergänzt

## Version 0.17.1 (2023-12-29)

* Fix: Warning: Undefined array key "" in dca/tl_fernschach_spieler_konto.php (line 521) -> Sprachvariable mit @ als Prefix, um Warnung zu unterdrücken
* Add: Turniere -> Anmeldungen -> Spielername mit Popup Bearbeitung Spieler verlinkt
* Add: tl_fernschach_spieler_titel -> um die Titel aus dem normalen Spielerdatensatz auszulagern

## Version 0.17.0 (2023-12-28)

* Change: tl_fernschach_spieler.fertig war noch nicht komplett deaktiviert
* Change: Turniermeldeformular umgebaut auf (nur) BdF-Mitglieder
* Add: Turniere -> Anmeldungen -> SEPA-Status Nenngeld anzeigen
* Add: Nenngeld-Anzeige bei den Anmeldungen
* Add: Backend-Modul (versteckt in Navigation) in tl_fernschach_turniere_meldungen, um die Anmeldungen und Bewerbungen eines Spielers anzuzeigen

## Version 0.16.5 (2023-12-20)

* Change: Übersetzung SEPA-Mandate wegen Filter geändert
* Change: tl_fernschach_spieler.fertig in Palette und Übersicht ausgeblendet, da nicht mehr benötigt
* Change: tl_fernschach_spieler.sepaBeitragDatei und sepaNenngeldDatei -> mandatory von true auf false, da störend für Bearbeiter
* Add: Toggler für SEPA-Mandate in tl_fernschach_spieler (statt Anzeige fertig)
* Change: Template mod_kontoauszug -> SEPA nicht gewünscht statt nicht vorhanden

## Version 0.16.4 (2023-11-30)

* Fix: Im Excel-Export sind bei gesetztem Filter "Nur Mitglieder" + "Veröffentlicht" auch 2 Archivierte drin -> In Backendliste war bei "Alle Mitglieder" archived auf '' gesetzt
* Add: Imaginärer Bot in Maintenance-Klasse
* Check: Prüfung ob bei UPDATE-Befehlen überall createNewVersion aufgerufen wird

## Version 0.16.3 (2023-11-20)

* Change: Meldeformular ausgearbeitet
* Fix: Spezialfilter in Spielerliste wurde nicht korrekt angezeigt
* Fix: Helper::Mitgliedschaft nicht als static deklariert
* Fix: Spezialfilter wurde im Export nicht berücksichtigt
* Fix: Leichte Designkorrektur im Excel-Export

## Version 0.16.2 (2023-10-30)

* Fix: Template mod_kontoauszug -> HTML-Fehler bei SEPA-Hinweis

## Version 0.16.1 (2023-10-29)

* Add: Formular Turnieranmeldung mit Option das Formular an ein angemeldetes Mitglied zu knüpfen.
* Fix: Korrekturen wegen PHP 8
* Change: Meldeformular gekürzt auf ein Turnier
* Fix: Meldeverfahren
* Add: tl_fernschach_spieler.turnierAnmeldungenBewerbungen -> Anzeige der Turnieranmeldungen und -bewerbungen in der Spielerbearbeitung
* Add: tl_fernschach_turniere.turnierleiterInfo -> Checkbox das der Turnierleiter per E-Mail zu informieren ist
* Add: tl_settings.fernschach_emailAdresse und tl_settings.fernschach_emailVon -> Globaler Absender für Systemmails
* Add: E-Mail-Funktion bei Turnieranmeldung
* Add: Ausgabe von Status der SEPA-Mandate im Kontoauszug

## Version 0.16.0 (2023-09-07)

* Anpassungen für PHP 8
* Add: tl.settings.fernschach_newsletter -> Auswahl eines Newsletter-Archivs für die Serienmailfunktion
* Add: tl_newsletter -> Hinweis im Newsletter-Archiv plus zusätzliches feld mit der ID des Spielers in der Fernschach-Verwaltung
* Add: Klasse Newsletter zum Übertragen der E-Mail-Adressen der aktuellen Spielerliste in den Newsletter-Verteiler
* Add: Platzhalter in der Serienmail
* Add: Backend-Modul Dokumentation
* Add: Mitgliederstatistik -> optional published-Feld berücksichtigen

## Version 0.15.2 (2023-09-03)

* Anpassungen für PHP 8
* tl_fernschach_mitgliederstatistik: Toogle-Funktion auf Haste-Toggler umgebaut
* Fix: Invalid UTF8-String beim Zugriff auf Spieler-Modul -> Fehler in den SEPA-Downloads -> Nach mehreren Tests hat es plötzlich funktioniert

## Version 0.15.1 (2023-09-02)

* Anpassungen wegen PHP 8

## Version 0.15.0 (2023-09-02)

* Abhängigkeit PHP 8 hinzugefügt

## Version 0.14.4 (2023-09-02)

* tl_fernschach_spieler: Auf Funktion log_message deaktiviert. Führt im Debug-Modus zu "Warning: error_log() has been disabled for security reasons"

## Version 0.14.3 (2023-09-01)

* tl_fernschach_spieler: Aufruf pdftojpg-Funktion deaktiviert wegen "not authorized"-Fehler

## Version 0.14.2 (2023-07-22)

* Add: Felder für Upload SEPA-Mandate Nenngelder und Beiträge
* Add: tl_fernschach_spieler.checkBeitrag für Beitrag bezahlt
* Change: tl_fernschach_turniere.published -> Toggle-Funktion von Haste eingebaut
* Change: Überarbeitung Meldeformular für Turniere

## Version 0.14.1 (2023-07-11)

* Änderung Versionsnummer, da GitHub immer noch die ungültige 0.14.0 ausliefert

## Version 0.13.4 (2023-07-11)

* Change: Funktion Export::getCode -> aktuelle Zeit zum Hashwert hinzugefügt

## Version 0.13.3 (2023-07-03)

* Add: tl_fernschach_spieler.downloads -> Dateien für den Spieler hochladen
* Change: tl_fernschach_spieler.published -> deutsche Übersetzung geändert von "Aktiv" auf "Veröffentlicht"
* Fix: Helper::getSaldo -> published = true hinzugefügt - unveröffentlichte Buchungen dürfen nicht berücksichtigt werden
* Add: Ausgabe des Saldos im Spieler-Export

## Version 0.13.2 (2023-06-23)

* Fix: tl_fernschach_spieler.accountChecked wurde nicht korrekt mit Benutzerrechten abgefragt

## Version 0.13.1 (2023-06-23)

* Fix: Fehler bei Berechtigungen tl_fernschach_turniere.published und tl_fernschach_turniere.fertig

## Version 0.13.0 (2023-06-23)

* Change: Dokumentation Turnieranmeldungen und Turnierarten
* Fix: tl_fernschach_turniere -> Sprachänderungen
* Add: tl_fernschach_turniere -> Felder für Anzeige der Meldestände
* Change: tl_fernschach_turniere -> Anordnung der Felder in der tournament-Palette optimiert
* Fix: tl_fernschach_spieler -> Rechte bei toggle und show korrigiert
* Add: Auflistung Spieler -> Ausblenden von Feldern ohne Berechtigung
* Fix: Infobox bei tl_fernschach_spieler verbessert
* Add: Rechtesystem für die Felderanzeige in Infobox tl_fernschach_spieler

## Version 0.12.4 (2023-05-05)

* Change: tl_fernschach_turniere -> Sprachänderung: Ordner statt Kategorie
* Fix: Kontoauszug wird im Frontend nicht angezeigt, obwohl Reset-Buchung nach 01.04.2023 vorhanden ist -> Helper::checkKonto falsche Variable übergeben: $row['id'] statt $objPlayer->id

## Version 0.12.3 (2023-04-27)

* Fix: Maintenance-Klasse -> Überprüfung der FE-Mitglieder anhand tstamp war mit falschem Vergleichsoperator

## Version 0.12.2 (2023-04-26)

* Fix: $this->createNewVersion in Helper Zeile 281 führt zu "Using $this when not in object context" -> Zeile auskommentiert

## Version 0.12.1 (2023-04-26)

* Add: Ausbau Kontenverwaltung
* Fix: Buchungen werden im Frontend angezeigt, auch wenn keine Resetbuchung erfolgt ist. -> Arbeitsweise FE-Modul geändert: Kontostand UND Kontoauszug werden nur angezeigt, wenn Resetbuchung ab 01.04.2023 vorhanden sind.
* Fix: Darstellungsfehler Turnier-Anmeldungen (HTML-Container-Problem) -> öffnendes DIV wurde ignoriert
* Add: tl_fernschach_spieler.accountChecked (Konto geprüft) -> Eigenes Feld angelegt um danach filtern zu können. Die Helper-Funktion checkKonto korrigiert das Feld entsprechend.
* Change: Im Spielerdatensatz alle verbundenen Frontend-Konten anzeigen.
* Add: Wartungsklasse Maintenance mit Abfrage Zeitstempel tl_member

## Version 0.12.0 (2023-04-16)

* Add: tl_fernschach_konten -> Doppelte Buchführung (in der Entwicklung)
* Change: Kontoauszug.php -> Ausgabe des Kontostandes im Modul jetzt steuerbar

## Version 0.11.0 (2023-04-13)

* Add: FE-Modul für Anzeige von Buchungen im Mitgliederprofil

## Version 0.10.2 (2023-04-13)

* Fix: ContentElements/Zusagen.php -> Startdatum in der Zukunft statt in der Vergangenheit
* Change: Bessere Hilfetexte beim Inhaltselement Zusagen und beim Turniertyp
* Add: Zugriffsrechte Turniere versucht einzubauen (steht erst ganz am Anfang)
* Fix: Verstorben (und andere Felder) bei Spielern wird nicht bei Rechten angezeigt -> exclude muß true sein beim Feld
* Fix: Einige Buchungsfelder werden nicht bei Rechten angezeigt -> exclude muß true sein beim Feld
* Change: tl_fernschach_turniere_bewerbungen -> Toggle-Funktion von Haste eingebaut
* Fix: tl_fernschach_turniere_bewerbungen -> Automatisches Ausfüllen Vor- und Nachname korrigiert
* Change: tl_fernschach_turniere_meldungen -> Toggle-Funktion von Haste eingebaut
* Fix: tl_fernschach_turniere_meldungen -> Automatisches Ausfüllen Vor- und Nachname, Auflistung
* Add: Spieler markieren, wenn Buchungen abgearbeitet sind (Resetbuchung ab 01.04.2023 enthalten) -> Ja/Nein-Icon wird angezeigt

## Version 0.10.1 (2023-03-22)

* Fix: Maintenance -> bei übereinstimmender Zuordnung wurden die Mitgliedsgruppen nicht mehr geprüft

## Version 0.10.0 (2023-03-22)

* Fix: tl_fernschach_spieler -> published in SQL Spezialfilter entfernt (published wird schon durch normalen Filter gewährleistet)
* Add: tl_fernschach_spieler -> Spezialfilter Mitgliedsende 31.12. auf bis zu 9 Jahre rückwärts erweitert
* Fix: Excel-Export Spieler -> Letzte Änderung 01.01.1970, dann kein Datum ausgeben
* Fix: tl_fernschach_spieler -> Spezialfilter Mitgliedsende hat archived berücksichtigt, obwohl bereits normaler Filter
* Fix: Excel-Export Spieler -> 1. Zeile Veröffentlicht, Fertig nicht in Fettschrift
* Change: Ausbau der Wartungsfunktion zum Abgleich BdF-Mitglieder ./. Frontend-Mitglieder

## Version 0.9.3 (2023-03-11)

* Fix: UTF8-Erkennung beim Buchungsimport verbessert

## Version 0.9.2 (2023-03-11)

* Fix: An exception occurred while executing 'INSERT INTO tl_fernschach_spieler (`tstamp`, `memberId`, `memberInternationalId`, `nachname`, `vorname`, `published`) VALUES (1678530716, '22031', NULL, 'Fritsche', '?', '')': SQLSTATE[23000]: Integrity constraint violation: 1048 Column 'memberInternationalId' cannot be null
* Add: Bei Buchungsimport werden neue Spieler in das System-Log geschrieben

## Version 0.9.1 (2023-02-26)

* Fix: Attempted to call an undefined method named "toggle" of class "Contao\DC_Table". (bei Aufruf des Togglers bei den Spielern/Buchungen)

## Version 0.9.0 (2023-02-22)

* Change: tl_fernschach_spieler -> Toggle-Funktion durch Haste ersetzt
* Add: tl_user und tl_user_group
* Add: Zugriffsrechte-Prüfung tl_fernschach_spieler
* Change: tl_fernschach_spieler_konto -> Toggle-Funktion durch Haste ersetzt
* Add: Zugriffsrechte-Prüfung tl_fernschach_spieler_konto
* Fix: Buchungen -> Wenn Markieren + Saldo-Reset aktiv sind, wird die Linie Saldo-Reset nicht angezeigt

## Version 0.8.1 (2023-02-17)

* Change: Ausgabe Mitgliederstatistik -> Letztes Stichtagdatum aus Formularversand wird, wenn vorhanden, verwendet
* Change: Dateiname der Mitgliederstatistik angepaßt auf Stichtag
* Change: Ausgabe Mitgliederstatistik verbessert
* Add: Mitgliederstatistik -> Logeintrag bei fehlendem Geschlecht
* Add: Mitgliederstatistik -> Logeintrag bei fehlendem Geburtstag

## Version 0.8.0 (2023-02-16)

* Add: Wartungsfunktion für Zuordnung tl_member <> tl_fernschach_spieler
* Add: Statistikmodul für Mitgliederstatistik

## Version 0.7.0 (2023-01-16)

* Add: Verbindung tl_fernschach_spieler zu tl_member hergestellt
* Add: tl_settings -> Benutzergruppen-Zuordnung für Verbindung zu tl_member
* Change: tl_fernschach_spieler.status -> Mitgliedsstatus in Status umbenannt und mitgliedschaftsbezogene Einträge entfernt (1 = Mitglied, 2 = Ausgetreten, 3 = Verstorben); Palette geändert
* Fix: Spezialfilter "Nur Mitglieder" -> berücksichtigt (fehlerhaft) auch beendete Mitgliedschaften
* Add: Spezialfilter bei den Spielern: Austritte zum Jahresende (Vorjahr, akt. Jahr, Folgejahr)
* Add: Buchung Kategorie: + Startgeld (tl_fernschach_spieler_konto.kategorie_options)
* Add: Buchung Art: + Guthaben (tl_fernschach_spieler_konto.art_options)
* Add: Spezialfilter bei den Spielern: Nichtmitglieder (Umkehrung von Mitgliedern)
* Disabled: Onload-Callback updateMitgliedschaften deaktiviert, da tl_fernschach_spieler.status nicht mehr für Mitgliedschaft genutzt wird

## Version 0.6.5 (2022-11-25)

* Add: Codenummer im Excel-Export der Spieler -> generiert aus Datensatz-ID, Geburtstag und BdF-Mitgliedsnummer -> Hash-Wert gekürzt auf 8 Stellen

## Version 0.6.4 (2022-11-17)

* Fix: tl_fernschach_spieler.memberships (Mitgliedschaften-Array) ist maßgebend, aber Feld status ist oft falsch ("Mitglied" oder "Ausgetreten" paßt nicht zur Mitgliedschaft) -> Prüfung erfolgt über onload_callback Helper::updateMitgliedschaften
* Add: tl_settings.fernschach_membershipUpdate editierbar gemacht, um Einfluß auf die Prüfung des Mitgliedsstatus aller Spieler zu haben
* Change: Im Spieler-Export wurde die Spalte Mitgliedschaften durch die Spalten Mitgliedschaft Beginn und Ende ersetzt. Eine Ausgabe erfolgt ebenfalls.
 
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
