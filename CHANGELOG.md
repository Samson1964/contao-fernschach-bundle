# Fernschach-Verwaltung Changelog

## Version 2.2.3 (2026-08-10)

* Fix: Das Mannschaftsmeldeformular gab für zwei ganz verschiedene Sachlagen denselben Satz aus — „Zurzeit steht kein Mannschaftsturnier zur Meldung offen. Möglicherweise haben Sie für alle offenen Turniere bereits gemeldet." Wer bereits gemeldet hatte, bekam damit die Auskunft, es sei nichts offen, obwohl es das war. Beide Fälle sind jetzt getrennt: Steht wirklich nichts offen, bleibt der kurze Hinweis. Hat der Mannschaftsleiter für die offenen Turniere schon gemeldet, werden diese namentlich mit dem Datum seiner Meldung aufgeführt, dazu der Hinweis auf den Turnierdirektor
* Change: Der zweite Fall wird als Auskunft dargestellt (`fs-hinweis--info`) und nicht mehr als Fehler — der Benutzer hat nichts falsch gemacht

## Version 2.2.2 (2026-08-10)

* Change: Das Turnierfeld `maxMeldungen` heißt im Backend jetzt **Meldungen je Spieler/Mannschaftsleiter**. Die alte Beschriftung „Meldungen je Spieler" war beim Mannschaftsturnier irreführend: Gezählt werden dort nicht die Spieler der Aufstellung, sondern die Meldungen des Mannschaftsleiters — der Wert legt also fest, wie viele Mannschaften er für dieses Turnier melden darf. Der Hilfetext sagt das jetzt ausdrücklich

## Version 2.2.1 (2026-08-10)

* Change: Die Hinweisfelder des Meldeformulars (Fehler und Bestätigung) haben statt des dicken farbigen Balkens an der linken Kante einen dünnen Rahmen ringsum. Die Farbe von Hintergrund und Schrift unterscheidet die beiden Fälle weiterhin

## Version 2.2.0 (2026-08-10)

Das Meldeformular für Mannschaften ist neu gebaut. Bisher entstand es über die Formularklasse des Helper-Bundles und übernahm Gestaltung und Skripte vom jeweiligen Theme — es sah auf jeder Website anders aus, und die Spieler waren aus vier Auswahllisten mit sämtlichen Mitgliedern zu suchen.

* Add: Eigenes Aussehen über `fernschach_formular.css`; die Farben und Abstände stehen als CSS-Variablen unter `.fernschach-formular` und lassen sich im Theme überschreiben
* Add: Die Zahl der Bretteingaben richtet sich nach dem Feld **Bretter** des gewählten Turniers — vier Spieler ergeben Brett 1 bis 4, sechs Spieler Brett 1 bis 6. Beim Wechsel des Turniers bleiben bereits eingetragene Spieler erhalten
* Add: Autovervollständigung der Spielerauswahl ab dem zweiten Zeichen über die neue Route `/fernschach/spieler-suche`. Gesucht wird in Nachname, Vorname, BdF-Mitgliedsnummer und ICCF-ID; mehrere Wörter müssen alle zutreffen. Die Vorschlagsliste ist auf 15 Einträge begrenzt und mit den Pfeiltasten bedienbar
* Add: Die Suchroute gibt nur angemeldeten Frontend-Mitgliedern Auskunft und antwortet sonst mit HTTP 403
* Add: Prüfung vor dem Abschicken auf gefüllte Pflichtfelder und doppelt aufgestellte Spieler, im Browser wie auf dem Server. Fehlerhafte Felder werden markiert, die Eingaben bleiben erhalten
* Add: Bestätigungsseite nach dem Abschicken mit sämtlichen Angaben — Turnier, Nenngeld, Verein, Mannschaft, Mannschaftsführer, vollständige Aufstellung und Bemerkungen
* Change: Nach dem Speichern wird umgeleitet (`send=1`), statt die Seite neu aufzubauen; ein Aktualisieren kann die Meldung nicht mehr wiederholen
* Change: Alle vom Benutzer stammenden Werte laufen in der Vorlage durch `StringUtil::specialchars()`
* Fix: `Helper::getSpieler()` hatte hinter der Bedingung des Zwischenspeichers ein Semikolon. Der Speicher war damit wirkungslos und die gesamte Spielertabelle wurde bei jedem Aufruf neu gelesen

Außerdem in dieser Fassung, als Ergebnis einer statischen Prüfung des gesamten Bundles mit PHPStan:

* Change: PHP 7 wird nicht mehr unterstützt, `composer.json` verlangt jetzt `^8.1`
* Add: `phpstan.neon.dist` liegt dem Bundle bei (Stufe 4, PHP 8.3). Die Klassen mit magischem `__get()` — `Model`, `Template`, `DataContainer`, `Database\Result` und andere — sind dort als `universalObjectCratesClasses` eingetragen, sonst meldet jede Contao-Eigenschaft einen Fehler
* Fix: Mehrere DCA-Klassen benutzten Eigenschaften, die nirgends deklariert waren; unter PHP 8.2 wären sie als „dynamische Eigenschaft" verwarnt worden
* Change: Sämtliche Funktionen und Methoden haben einen deutschen Kommentarblock mit Zweck, Parametern, Rückgabe und den Fallstricken; die alten englischen Rumpfblöcke sind ersetzt

## Version 2.1.1 (2026-08-10)

Ergebnis einer vollständigen Prüfung des Bundles mit PHP 8.3 (Syntax) und PHPStan gegen Contao 4.13.58 und Contao 5.7.7. Gefunden wurden mehrere Stellen, die unter Contao 5 zur Laufzeit abgestürzt wären, aber von den bisherigen Tests nicht berührt wurden, weil sie Daten voraussetzen.

* Fix: Der Bewerbungszähler in der Turnierliste benutzte `Controller::getImage()` und `Controller::generateImage()` — beide gibt es in Contao 5 nicht mehr. Sobald ein Turnier Bewerbungen hatte, war die Turnierliste dort nicht mehr aufrufbar. Ersetzt durch `Image::getHtml()`
* Fix: Die Brotkrumennavigation der Turniere und der Konten benutzte die in Contao 5 entfallene Konstante `TL_FILES_URL`. Der Fehler trat auf, sobald ein Knoten geöffnet wurde
* Fix: Das E-Mail-Sendeformular benutzte die in Contao 5 entfallene Konstante `TL_SCRIPT`
* Fix: `$this->Database` in tl_fernschach_turniere und tl_fernschach_turniere_spieler sowie `$this->Environment` in tl_fernschach_turniere und tl_fernschach_konten waren nie über `import()` geladen. In Contao 5 liefert der Zugriff null, der nächste Methodenaufruf brach ab
* Fix: `Helper::getMeldungen()` wurde statisch aufgerufen, war aber nicht als `static` deklariert — unter PHP 8 ein Fatal Error. Betroffen war die Teilnehmerliste eines Turniers. Zusätzlich stand hinter der if-Bedingung des Zwischenspeichers ein Semikolon, wodurch bei jedem Aufruf erneut abgefragt wurde
* Fix: `Helper::checkResetbuchungen()` fragte mit der Variablen `$id` ab, die es in der Methode nicht gibt — die Prüfung der globalen Resetbuchungen lief damit stets ins Leere
* Fix: Der Spezialfilter im Serienmail-Verteiler war wirkungslos: Eine Zuweisungskette überschrieb den gelesenen Wert mit einem festen Feld, sodass keine der Bedingungen zutreffen konnte und der Verteiler immer alle Datensätze enthielt
* Fix: Der Turnierimport suchte nach `$set['titel']` statt `$set['title']` und fand deshalb nie ein vorhandenes Turnier — jeder Import legte Doppelgänger an, statt zu ergänzen
* Fix: Der ICCF-Import brach ab, wenn die hochgeladene Datei keine CSV-Datei war
* Fix: `tl_settings` enthielt den Schlüssel `mandatory` zweimal
* Fix: Statistik-Laufzeitmessung rechnete gegen eine undefinierte Variable
* Fix: Der Parameter `differenz` der Turnierstatistik wird jetzt in eine Zahl gewandelt, bevor damit gerechnet wird
* Change: Der überflüssige zweite Parameter am Konstruktor von `Contao\File` ist entfallen; es gibt ihn seit Contao 4.0 nicht mehr
* Change: Der ungenutzte Dienst `request_stack` ist aus dem ICCF-Import-Controller entfernt
* Change: Fehlerhafte `@param`- und `@return`-Angaben an mehreren Methoden richtiggestellt

## Version 2.1.0 (2026-08-09)

**Achtung beim Update:** Das neue Turnierfeld „Meldungen je Spieler" wird bei allen vorhandenen Turnieren auf **1** gesetzt. Ab sofort kann sich also jeder Spieler nur noch einmal je Turnier melden. Wo das nicht gewünscht ist, muss der Wert im Turnier auf 0 (unbegrenzt) oder auf die gewünschte Zahl gesetzt werden.

* Add: tl_fernschach_turniere.maxMeldungen -> Feld "Meldungen je Spieler" begrenzt, wie oft sich derselbe Spieler für ein Turnier anmelden bzw. bewerben darf (Voreinstellung 1, 0 = unbegrenzt). Anmeldungen und Bewerbungen werden getrennt gezählt
* Add: Meldeformular Spieler-Turnieranmeldung -> Turniere, für die der Spieler die zulässige Zahl an Meldungen erreicht hat, werden nicht mehr angeboten
* Add: Meldeformular Spieler-Turnieranmeldung -> Bestätigungsseite nach dem Absenden mit den letzten Meldungen des Spielers. Bisher wurde nur die Seite neu geladen und der Absender sah wieder das leere Formular — laut Leistungsreferat die eigentliche Ursache der Mehrfachbewerbungen (Rekord: neun Bewerbungen desselben Mitglieds für ein Turnier)
* Add: Meldeformular Mannschaftsanmeldung -> ebenfalls Bestätigungsseite und Begrenzung; als Nachweis dient dort die Nenngeld-Sollbuchung des Mannschaftsführers, weil eine Mannschaftsmeldung keinen Datensatz in den Anmeldungen anlegt
* Add: Excel-Export der Spieler -> neue Spalte BA "Interner Bereich" (Ja / Ja (gesperrt) / Nein) zeigt, ob dem Spieler ein Frontend-Mitgliedskonto zugeordnet ist
* Add: Classes\Helper::zaehleMeldungen(), meldungErlaubt() und getInternerBereich()
* Fix: Backend-Modul Turniere -> die Turnierart "tournament" wurde nach dem Öffnen eines Datensatzes unterhalb eines Turniers als unbekannt angezeigt. Ursache: die Auswahlliste hat die nicht erlaubten Arten mit unset() aus $GLOBALS['TL_LANG'] entfernt, womit sie für den Rest der Anfrage fehlten. Es wird jetzt auf einer Kopie gearbeitet
* Fix: Backend-Modul Turniere -> unter einem Turnier ließ sich ein weiteres Turnier anlegen, sobald der Elterndatensatz nicht ermittelt werden konnte. Die Ermittlung läuft jetzt über die Datenbank statt über das in Contao 5 verfallene activeRecord, und ein save_callback weist unzulässige Kombinationen ab. Bereits vorhandene Datensätze mit unzulässiger Art bleiben bearbeitbar und erzeugen nur einen Hinweis
* Change: Nach dem Absenden der Meldeformulare wird umgeleitet statt neu geladen. Erst dadurch fragt der Browser beim Aktualisieren nicht mehr nach dem erneuten Absenden der Formulardaten
* Change: Meldeformular Spieler-Turnieranmeldung -> das Turnier wird beim Speichern nur noch einmal geladen statt dreimal
* Add: Unit-Tests für die Begrenzung der Meldungen

## Version 2.0.0 (2026-08-02)

Portierung auf Contao 4.13 **und** Contao 5 sowie auf PHP 8. An der Datenbank ändert sich nichts.

### Contao 5

* Change: composer.json -> `contao/core-bundle: ^4.13 || ^5.0`, `php: ^7.4 || ^8.0`; `contao/newsletter-bundle` und `menatwork/contao-multicolumnwizard-bundle` ausdrücklich aufgenommen, `phpoffice/phpspreadsheet` auf `^1.29 || ^2.0 || ^3.0` gehoben
* Change: Rund 800 Verweise auf die globalen Contao-Klassenaliasse (`\Database`, `\Input`, `\System`, `\Image`, `\Backend` u. a.) durch importierte Klassen aus dem Namensraum `Contao\` ersetzt — in Contao 5 gibt es die Aliasse nicht mehr
* Change: `'dataContainer' => 'Table'` in allen 17 DCA-Dateien durch `DC_Table::class` ersetzt
* Change: Neue Klasse `Classes\Scope` als Ersatz für das, was in Contao 5 entfallen ist: `TL_MODE`, `REQUEST_TOKEN`, `Contao\Session`, `Controller::replaceInsertTags()`, `Controller::createNewVersion()`, `Controller::createInitialVersion()`, `System::log()` und `log_message()`
* Fix: `TL_MODE` in den sechs Frontend-Modulen und in der config.php durch eine Abfrage des Dienstes `contao.routing.scope_matcher` ersetzt
* Fix: `$this->Session` und `$dc->Session` (Klasse `Contao\Session`) durch den Sitzungsbeutel `contao_backend` ersetzt — in Contao 5 lieferte der Zugriff null und führte zum Absturz
* Fix: `$this->import('BackendUser')` u. a. auf `::class` umgestellt; mit Zeichenketten wirft `System::import()` in Contao 5 eine Ausnahme
* Fix: `ampersand()`, `specialchars()` und `array_insert()` durch `StringUtil`- bzw. `ArrayUtil`-Methoden ersetzt
* Fix: `log_message()` an 25 aktiven Stellen durch `Scope::logToFile()` ersetzt; die Importe hätten unter Contao 5 sonst mit „undefined function" abgebrochen
* Fix: `TL_GENERAL`/`TL_ERROR` durch die Konstanten aus `ContaoContext` ersetzt
* Fix: Die Fallunterscheidungen über die Konstante `VERSION` (Contao 3 gegen Contao 4) entfernt
* Fix: `Environment::getInstance()->request` durch `Environment::get('request')` ersetzt
* Fix: `tl_member.locked` gibt es in Contao 5 nicht mehr — die Mitgliederprüfung und die Wartung fragen die Spalte jetzt nur noch ab, wenn es sie gibt
* Fix: `\BackendModule` in Dokumentation, ZeigeTurniere und ZeigeTeilnehmer auf `Contao\BackendModule` umgestellt
* Fix: Fehlende `use`-Anweisungen in 13 DCA- und Klassendateien ergänzt (u. a. `Backend` in tl_settings.php, `DataContainer` in tl_fernschach_mitgliederstatistik.php)

### Haste entfällt

* Change: `codefog/contao-haste` ist keine Abhängigkeit mehr — Haste 4 lässt sich unter Contao 5 nicht installieren
* Change: Die 22 `haste_ajax_operation`-Schnellschalter in den DCA-Dateien laufen jetzt über das Contao-eigene `act=toggle`; die geschalteten Felder haben dafür `'toggle' => true` erhalten
* Add: Symbole `sepa_on_.png` und `fertig_.png` für den ausgeschalteten Zustand der Schnellschalter (Contao erwartet den Unterstrich vor der Dateiendung)
* Add: Neue Klasse `Classes\Tokens` als Ersatz für `\Haste\Util\StringUtil::recursiveReplaceTokensAndTags()`; sie benutzt die Contao-Dienste `contao.string.simple_token_parser` und `contao.insert_tag.parser`
* Fix: Ein nie benutztes `\Haste\Form\Form` im Meldeformular Spieler-Turnieranmeldung entfernt

### ICCF-Import

* Change: Die direkt aufrufbare Datei `Resources/public/Import_ICCF_Rating.php` ist entfallen; an ihre Stelle tritt der Controller `Controller\IccfImportController` unter der Route `/contao/fernschach/iccf-import`
* Change: `import_iccf.js` ruft die neue Route auf, meldet Fehler in der Fortschrittsanzeige und bricht ab, statt endlos weiterzufragen, wenn der Zähler stehenbleibt
* Fix: Zeilen ohne die erwarteten acht Spalten werden übersprungen, statt eine unvollständige Wertung anzulegen
* Fix: Der Sitzungszugriff läuft über die Anfrage; den Container-Dienst `session` gibt es seit Symfony 6 nicht mehr

### PHP 8

* Fix: 19 `unserialize()`-Aufrufe durch `StringUtil::deserialize()` ersetzt — `unserialize(null)` ist seit PHP 8.1 verfallen
* Fix: 67 Lesezugriffe auf `$GLOBALS['TL_CONFIG'][…]` durch `Config::get()` ersetzt; nicht gesetzte Schlüssel sind unter PHP 8 eine Warnung
* Fix: `ResetUtil` legte die Eigenschaft `resets` dynamisch an (deklariert war `Resets`) — unter PHP 8.2 eine Verfallswarnung
* Fix: `array|null` in der Signatur der Beitrittsformularprüfung durch `?array` ersetzt, damit die Datei auch unter PHP 7.4 lädt
* Fix: `strtotime(null)` in den beiden Titelnormen-Modulen abgesichert
* Fix: Die Meldeformulare brechen mit einem Hinweis ab, wenn dem Benutzerkonto kein Spielerdatensatz zugeordnet ist — vorher gab es für jedes Feld eine Warnung
* Fix: ZeigeTurniere und ZeigeTeilnehmer setzen ihre Template-Variablen auch dann, wenn keine ID übergeben wurde
* Fix: Undefinierte Variable `$titel` in der Mitgliederstatistik
* Fix: `catch(Exception $e)` in `Helper::getAlter()` fing wegen des fehlenden Namensraum-Präfixes nie etwas

### Weitere Korrekturen

* Fix: `Resources/contao/languages/de/tl_fernschach_konten_buchungen.php` war eine Kopie der DCA-Datei — dadurch wurde beim Laden der Sprachdatei die Klasse `tl_fernschach_konten_buchungen` ein zweites Mal deklariert und sämtliche Beschriftungen der Buchungstabelle fehlten
* Fix: Die fünf Cronjobs waren über die Annotation `@CronJob` angemeldet, die Contao nur bei Diensten mit `autoconfigure` auswertet — sie liefen deshalb nie. Sie stehen jetzt mit dem Tag `contao.cronjob` in der services.yaml
* Fix: Der Konstruktor der Cron-Klassen nahm das Framework entgegen, ohne es zuzuweisen; `Streichung` hatte gar keinen. Die Aufträge initialisieren das Framework jetzt selbst
* Fix: `MoveBuchungen` prüft den als Parameter übergebenen Tabellennamen, statt ihn ungeprüft in die Abfrage zu setzen
* Change: Der Benachrichtigungstyp fürs Notification Center und das zugehörige Auswahlfeld `nc_notification` sind entfallen — die Methode, die sie benutzt hätte, wurde nie aufgerufen und hätte auf nicht vorhandene Klassen zugegriffen
* Change: `services.yml` heißt jetzt `services.yaml`; der `_instanceof`-Block mit `ContainerAwareInterface` ist entfallen (in Symfony 7 gibt es die Schnittstelle nicht mehr)
* Change: Sechs Dateien von ISO-8859-1 nach UTF-8 umgestellt, Zeilenenden vereinheitlicht

### Dokumentation und Tests

* Add: Ausführliche README mit Installation, Einstellungen, allen Backend- und Frontend-Modulen, Rechten, Cronjobs, Import-/Exportformaten und Umstiegshinweisen
* Add: Unit-Tests für `ContaoFernschachBundle`, `Classes\Scope` und `Classes\Tokens` samt `phpunit.xml.dist`
* Add: Deutsche Kommentarblöcke an allen im Zuge der Portierung angefassten Methoden

## Version 1.9.6 (2026-07-29)

* Fix: Warning: Undefined array key "deleteConfirm", "initAccounts_confirm", "moveBeitragConfirm", "moveHauptConfirm" u. a. bei contao:migrate -> Lesezugriffe auf $GLOBALS['TL_LANG'] in den DCA-Dateien mit `?? null` bzw. `?? array()` abgesichert, da der DcaLoader die Sprachdateien noch nicht geladen hat
* Change: Beschreibung, Keywords und Homepage in der composer.json ergänzt, damit Packagist das Paket verständlich darstellt und über die Suche auffindbar macht

## Version 1.9.5 (2026-05-29)

* Fix: Streichung-Cron aktiviert fälschlicherweise isDeletion -> Codeblock im Cron Streichung auskommentiert
* Fix: Verstorben-Checkbox wird bei der Mitgliedschaftsprüfung ignoriert -> Helper::checkMembership geändert
* Change: Verstorben aktiviert, aber kein Mitgliedschaftsende vorhanden -> Helper::checkMembership schreibt Hinweis in das System-Log
* Change: Streichung aktiviert (mit Datum), aber kein Mitgliedschaftsende vorhanden -> Helper::checkMembership schreibt Hinweis in das System-Log

## Version 1.9.4 (2026-05-24)

* Fix: Spieler - Mitgliedschaft geprüft -> Datum wird in einem unzulässigen Format gespeichert
* Change: tl_fernschach_spieler.membercheck_date Hilfetext und Parameter verbessert
* Change: tl_fernschach_spieler.birthday -> Datepicker aktiviert
* Change: tl_fernschach_spieler.deathday -> Datepicker aktiviert
* Change: tl_fernschach_spieler.streichung -> Datepicker aktiviert

## Version 1.9.3 (2026-04-12)

* Change: EventListener ProcessFormDataListener umbenannt in Beitrittsformularpuefung
* Add: Turnierstatistik -> Turniere nach Turniertyp (national, international, ET, MT)
* Add: Turnierstatistik -> Meldungen nach Turniertyp (national, international, ET, MT)
* Add: Turnierstatistik -> Bewerbungen nach Turniertyp (national, international, ET, MT)
* Add: Turnierstatistik -> Anzahl Meldungen von Anzahl BdF-Mitgliedern für alle Turniertypen
* Add: Turnierstatistik -> Anzahl Bewerbungen von Anzahl BdF-Mitgliedern für alle Turniertypen

## Version 1.9.2 (2026-04-10)

* Add: Spezialfilter für Vorhandensein von E-Mails -> "E-Mails vorhanden"

## Version 1.9.1 (2026-04-10)

* Add: tl_fernschach_spieler_mailtemplates um Vorlagen für die E-Mail-Funktion zu verwalten
* Add: tl_fernschach_spieler_mails.template -> Auswahlfeld für eine Mailvorlage
* Add: Vorschaufunktion in tl_fernschach_spieler_mails bei der Bearbeitung der E-Mail
* Add: Helper::getPreview -> generiert eine Vorschau für E-Mails
* Change: tl_fernschach_spieler_mails.content kein Pflichtfeld mehr
* Add: Eigene Tokens in der Mailfunktion

## Version 1.9.0 (2026-04-08)

* Add: Klasse Turnierstatistik -> Statistik-Seite bei den Turnieren (Wunsch Kracht)
* Fix: Thematurniere erscheinen z.B. im Anmeldeformular nicht untereinander. Es sollte nicht nur alphabetisch, sondern auch nach Turnierart sortiert werden. Hinweis von Jörg Kracht. -> Syntaxfehler bei der Datenbankabfrage
* Add: Spezialfilter für die Ausgabe der internationalen und nationalen Titelträger

## Version 1.8.3 (2026-04-07)

* Fix: backend.css für Mailvorschau ergänzt (aus Lizenz-Bundle)
* Add: Token- und Inserttag-Ersetzung in Mailer::getPreview hinzugefügt

## Version 1.8.2 (2026-04-07)

* Fix: tl_fernschach_spieler_mails -> Icon email_senden.png nicht zu sehen -> hat auf anderes Bundle verwiesen

## Version 1.8.1 (2026-04-07)

* Add: Headerdaten in Klasse Mailer ergänzt (To, From, Reply-To, Cc, Subject, Date)
* Add: tl_fernschach_spieler_mails zeigt das Brief-Icon andersfarbig, wenn Mails vorhanden sind
* Change: tl_fernschach_spieler_mails.signatur_text kein Pflichtfeld mehr um Signatur löschen zu können
* Change: In Mailer die Speicherung von Bcc in sent_text ergänzt

## Version 1.8.0 (2026-04-06)

* Delete: Filter "Aktiver Mitgliedschaftszeitraum", da dies durch den Bot Mitgliedschaftscheck abgedeckt wird
* Add: tl_fernschach_spieler_mails für die Verwaltung und den Versand von E-Mails an einen Spieler
* Add: Klasse Mailer für den Versand der in tl_fernschach_spieler_mails gespeicherten E-Mails

## Version 1.7.3 (2026-04-06)

* Fix: Mitgliedsprüfung um die BdF-Nummer erweitert -> Helper::checkMembership -> Mitglieder sind nur Spieler mit Nummern von 1 bis 89999

## Version 1.7.2 (2026-04-05)

* Fix: Modul Titelträger: Feld Jahr wird nicht ausgeben -> substr mit falschen Parametern

## Version 1.7.1 (2026-04-05)

* Change: Ausgabe von Titelträgern um einige Felder ergänzt -> Ort, Verstorben-Status, Jahr der Verleihung

## Version 1.7.0 (2026-04-05)

* Add: tl_fernschach_spieler.qualifikationen -> Qualifikationsbescheinigungen ausstellen/eintragen (Wunsch Kracht)
* Add: Qualifikationen im Frontend im Meldeformular Spieler anzeigen (Wunsch Kracht)
* Add: Nationale Titel in tl_fernschach_spieler_titel hinzugefügt
* Add: tl_module.fernschachverwaltung_titel um eine Liste von Titelträgern auszugeben

## Version 1.6.3 (2026-02-23)

* Fix: tl_fernschach_spieler.archived -> Archivieren-Checkbox darf nicht aktiviert sein

## Version 1.6.2 (2026-02-20)

* Fix: Cron Mitgliedschaftscheck speichert true/false nicht richtig ab -> war aber okay. Problem: das Feld member muß den Inputtyp checkbox haben, um im Filter mit ja/nein angezeigt zu werden.

## Version 1.6.1 (2026-02-14)

* Add: tl_fernschach_spieler.membercheck mit zwei Optionen für Datum und Info -> Checkbox für geprüfte Mitgliedschaften
* Add: ICCF-ID bei Mannschaftsmeldungen
* Add: tl_fernschach_spieler.member -> Interne Checkbox für den Bot, um BdF-Mitglied ja/nein zu speichern
* Add: Cron Mitgliedschaftscheck erstellt, um den Status des Feldes tl_fernschach_spieler.member zu prüfen und ggfs. zu korrigieren

## Version 1.6.0 (2026-02-11)

* Add: Spezialfilter um doppelte E-Mail-Adressen zu finden (filter_mail_multiple) -> "E-Mail-Adresse 1 mehrfach verwendet"
* Add: Index tl_fernschach_spieler.email1 und tl_fernschach_spieler.email2
* Change: sorting tl_fernschach_spieler.email1 und tl_fernschach_spieler.email2 auf true
* Add: Einstellungen, Optionen für Turnieranmeldungen (noch nicht implementiert, aber anklickbar)

## Version 1.5.16 (2026-02-08)

* Fix: Turnieranmeldung Mannschaft (Turnierauswahl) jetzt mit SEPA-Prüfung

## Version 1.5.15 (2026-02-03)

* Change: Helper.getBeitragssaldo -> Beitragsamnestie Februar entfernt
* Add: Turnieranmeldung Mannschaft jetzt mit SEPA-Prüfung

## Version 1.5.14 (2026-02-02)

* Change: Helper.getBeitragssaldo -> Beitragsamnestie von Januar auf Februar erweitert

## Version 1.5.13 (2026-01-12)

* Fix: Bei Turnieranmeldung Einzel werden Mannschaftsturniere angezeigt
* Change: Funktion Helper::getBeitragssaldo gibt den aktuellen Beitragssaldo zurück. Ist der aktuelle Monat Januar wird der Saldo vom 31.12. zurückgegeben (Schonfrist Beitragszahlung)

## Version 1.5.12 (2026-01-10)

* Change: Meldeformular_Mannschaft -> ML wird direkt aus Datensatz des FE-Mitglieds geladen
* Change: tl_fernschach_turniere mit Turnierart Mannschaftsturnier und Feld Anzahl der Bretter (Standard = 4)
* Change: Modul ICCF-Rating Hinweis auf Dev-Version entfernt
* Add: Nenngeld-Buchung wird beim meldenden Spieler erzeugt

## Version 1.5.11 (2026-01-06)

* Change: fernschachverwaltung_meldeformular_player wieder zurückgesetzt auf fernschachverwaltung_meldeformular -> Probleme mit FE-Modul-Wechsel

## Version 1.5.10 (2026-01-06)

* Ausbau des ICCF-Imports via Ajax
* Verbesserung der Rating-DCA
* Immer wieder 500er bei Aufruf von bundles/contaofernschach/Import_ICCF_Rating.php?zeile=0

## Version 1.5.9 (2026-01-04)

* Add: Modul Meldeformular_Mannschaft
* Change: Modul Meldeformular -> Meldeformular_Spieler
* Add: Einstellungen für Kontaktdaten Turnierdirektor

## Version 1.5.8 (2025-12-26)

* Change: Cron\Streichung trägt jetzt fehlendes Mitgliedschaftsende in Mitgliedschaften nach, wenn to-Feld leer ist
* Add: tl_fernschach_iccf_ratinglists -> Für den Import von ICCF-Wertungslisten
* Add: tl_fernschach_iccf_players -> Für die Speicherung der ICCF-Spielerstammdaten
* Add: tl_fernschach_iccf_ratings -> Für die Speicherung der ICCF-Wertungszahlen aller Spieler 
Problem: Der Import bricht ab -> Möglichkeiten mit Ajax und Routen anschauen

## Version 1.5.7 (2025-12-24)

* Add: Cron\Streichung mit Funktionen ausgebaut -> Prüft, ob tl_fernschach_spieler.isDeletion richtig auf true/false gesetzt ist - und sucht das Streichdatum im Mitgliedschaftsende
* Change: tl_fernschach_spieler.streichung ist jetzt ein Pflichtfeld
* Fix: Rechenfehler in Helper::getSaldo (Ursache evtl.: Fehler bei Floats in PHP entstehen oft durch die begrenzte Genauigkeit des binären Systems, was zu Rundungsfehlern führt, besonders bei Operationen mit Zahlen wie 0.1 oder 0.7, die nicht exakt darstellbar sind) -> plus und minus durch bcadd und bcsub ersetzt

## Version 1.5.6 (2025-12-23)

* Add: tl_fernschach_spieler.isDeletion -> Checkbox für Aktivierung von Streichungen
* Change: tl_fernschach_spieler.streichung -> in Subpalette geschoben
* Add: Helper::checkMembership um Streichungen erweitert, Parameter 1 auf ganzen Spielerdatensatz geändert
* Add: Cron\Streichung (täglich) -> Überprüft die korrekte Setzung der Mitgliedschaftsstreichung
* Fix: Meldeformular::getTournaments -> Umwandlung $saldo = (string)$saldo -> Es gibt ein Problem mit der Saldoberechnung: 4 wird übergeben, bei Umwandlung in int wird 3 draus, bei Umwandlung in String bleibt es 4!

## Version 1.5.5 (2025-12-14)

* Change: Mitgliederpruefung::setGroups leicht geändert -> es gibt Probleme, das die Gruppen im Backend richtig erkannt werden. Problem ist wahrscheinlich, das ein String statt eines Integer übergeben (die ID der Gruppe) werden muß. (Ticket: Backend - Mitglieder - Gruppe BdF-Mitglied: Es werden viel zu wenig Spieler angezeigt)
* Delete: tl_settings.fernschach_maintenance wurde als Dummy angelegt und wird nicht gebraucht
* Delete: tl_settings.fernschach_resetUpdate -> Wartungseinstellung wird nicht mehr benötigt, da Cronjob
* Delete: tl_settings.fernschach_resetUpdate_time -> Wartungseinstellung wird nicht mehr benötigt, da Cronjob
* Delete: tl_settings.fernschach_membershipUpdate -> Wartungseinstellung wird nicht mehr benötigt, da Cronjob
* Delete: tl_settings.fernschach_membershipUpdate_time -> Wartungseinstellung wird nicht mehr benötigt, da Cronjob
* Delete: tl_settings.fernschach_maintenanceUpdate -> Wartungseinstellung wird nicht mehr benötigt, da Cronjob
* Delete: tl_settings.fernschach_maintenanceUpdate_time -> Wartungseinstellung wird nicht mehr benötigt, da Cronjob
* Delete: tl_settings.fernschach_intervall_memberbridgeCheck -> Wartungseinstellung wird nicht mehr benötigt, da Cronjob
* Delete: tl_settings.fernschach_intervall_membershipsCheck -> Wartungseinstellung wird nicht mehr benötigt, da Cronjob
* Add: tl_fernschach_spieler Hilfetext bei BdF-Mitgliedsnummern ergänzt (Ticket: Aufbau der BdF-Mitgliedsnummern)

## Version 1.5.4 (2025-12-13)

* Add: tl_fernschach_spieler.streichung -> filter auf true
* Change: Mitgliederpruefung::setGroups nochmal prüfen, da zuviel Output im Backend angezeigt wird
* Change: tl_fernschach_spieler.spielberechtigungen.datum -> default auf ''

## Version 1.5.3 (2025-12-12)

* Add: Klasse Cron\Mitgliedschaftsende -> Täglicher Cronjob, der Spieler archiviert, wenn deren Austrittsende erreicht ist. Protokollierung im System-Log. (Ticket: Mitgliedschaftsende des Spielers erreicht? Dann automatisch archivieren.)
* Change: Dokumentation Turnieranmeldungen
* Add: Dokumentation Wartung
* Fix: Warning: Undefined array key "tl_fernschach_spieler" in /src/Classes/Newsletter.php (line 94) 
* Fix: 1062 Duplicate entry '...@t-online.de' for key 'pid_email' -> beim Exportieren der Serienmail-Empfänger

## Version 1.5.2 (2025-12-11)

* Fix: Klasse Cron\Nenngeld repariert -> unnötiger Session-Aufruf erzeugte Fehler; Cron auf stündlich geändert
* Change: Temporäre Dateien der Cronjobs verschoben nach system/tmp
* Fix: Klasse Cron\Mitgliederpruefung -> Aufrufe \Versions auskommentiert wegen ErrorException: Warning: Attempt to read property "server" on null in /kunden/107305_14053/webseiten/schachbund/dsbweb-entwicklung.2023/vendor/contao/core-bundle/src/Resources/contao/classes/Versions.php
* Change: Klasse Cron\Mitgliederpruefung -> Cron auf stündlich geändert
* Delete: tl_fernschach_spieler onload_callback -> Aufruf getMaintenance deaktiviert, da das über Contao-Cron läuft

## Version 1.5.1 (2025-12-10)

* Change: Abhängigkeit menatwork/contao-multicolumnwizard-bundle von 3.6.11 auf *

## Version 1.5.0 (2025-12-10)

* Add: tl_fernschach_spieler.info_beitritt -> Speichert Daten aus dem Beitrittsformular
* Add: tl_settings.fernschach_beitrittsformular -> Auswahl eines Beitrittsformulars
* Add: Formular-Hook für das Speichern des Beitrittsformulars in der Spieler-Tabelle (Ticket: Bei der Beitrittserklärung soll gleich ein Spielerdatensatz angelegt werden)
* Add: tl_fernschach_turniere.spielerGeschlecht -> Geschlecht für das Turnier festlegen
* Add: tl_fernschach_turniere.spielerAlterMin -> Mindestalter für das Turnier festlegen
* Add: tl_fernschach_turniere.spielerAlterMax -> Maximalalter für das Turnier festlegen (Ticket: Bei Turniererstellung eine Möglichkeit schaffen das besondere spezifische Einstellungen möglich sind wie: Alter Geschlecht)
* Add: Meldeformular-Klasse -> Geschlechtsbeschränkung, Mindest- und Maximalalter eingebaut
* Add: Klasse Cron\Nenngeld erstellt -> verbunden über Service contao.cron
* Delete: Nenngeldprüfung aus Classes\Maintenance entfernt
* Delete: Klasse FernschachBot -> wird durch Cron ersetzt
* ToDo: Cron\Mitgliederpruefung komplett neu programmieren
* ToDo: Classes\Maintenance entfernen aus onload

## Version 1.4.2 (2025-11-26)

* Fix: Attempted to call an undefined method named "cspUnsafeInlineStyle" of class "MenAtWork\MultiColumnWizardBundle\Contao\Widgets\MultiColumnWizard" in vendor/menatwork/contao-multicolumnwizard-bundle/src/Contao/Widgets/MultiColumnWizard.php (line 1261) -> MCW nicht mehr kompatibel mit Contao 4.13, siehe https://github.com/menatwork/contao-multicolumnwizard-bundle/issues/50 -> MCW auf Version 3.6.11 festgepinnt

## Version 1.4.1 (2025-11-18)

* Add: Checkboxen checkBeitrag, contribution_paid und beitrag2026 wiederhergestellt

## Version 1.4.0 (2025-11-17)

* Add: Klasse PLZ für die Postleitzahlen
* Add: tl_fernschach_spieler.bundesland/bundesland2 -> Wird ein Datensatz gespeichert, erfolgt automatisch eine Zuordnung des Bundeslandes, wenn kein Bundesland ausgewählt wurde
* Add: Cron-Klasse FernschachBot (noch ohne Funktion) -> soll Maintenance später ersetzen
* Delete: tl_fernschach_spieler.status -> siehe Deck-Ticket
* Add: tl_fernschach_spieler.beitrag2026 -> Checkbox für den Beitrag 2026 (aber noch nicht in der Palette)
* Change: tl_fernschach_spieler.checkBeitrag/contribution_paid in der Palette deaktiviert, da dafür die Spalte beitragsschulden ausreicht
* Add: Spezialfilter keine Nenngeldbuchung die letzten 6, 12 oder 24 Monate

## Version 1.3.3 (2025-09-01)

* Fix: Meldeformular zeigt keine Turniere mit Meldeschluß des aktuellen Tages -> in Datenbank steht 00:00:00 als Uhrzeit in registrationDate, geprüft wurde aber mit der aktuellen Uhrzeit
* Fix: dca/tl_member.php hat ein UTF-8-BOM 

## Version 1.3.2 (2025-07-19)

* Fix: Wartungsfunktion ordnet Mitglied erst zu und löscht danach wieder
* Fix: Wartungsfunktion -> verschiedene Fehlerbereiningungen, z.B. wurde leerer tl_member.username auch geprüft

## Version 1.3.1 (2025-07-18)

* Change: Spezialfilter "Nur Mitglieder" auf "Aktiver Mitgliedschaftszeitraum" geändert
* Add: Spieler-Export -> Gönner und Ehrenmitglieder/-präsidenten werden mit ausgegeben
* Add: tl_fernschach_spieler.spielberechtigungen -> Upload von Dateien mit Angabe eines Datums als MCW realisiert
* Add: tl_module.fernschachverwaltung_radio -> In Turnieranmeldung alternativ die Turnierauswahl mit Radio-Buttons statt Select-Liste ausgeben
* Add: Nenngeld-Klasse um negative Nenngeldkonten zu finden
* Add: tl_user neues Recht "Info: Nenngeldkonten negativ anzeigen" -> im Wartungsmodus werden die negativen Nenngeld-Konten ermittelt und als Info angezeigt

## Version 1.3.0 (2025-07-02)

* Add: tl_module.fernschachverwaltung_bewerbung -> Checkbox um aus dem Formular Bewerbungen möglich zu machen
* Add: Meldeformular um Speichern in tl_fernschach_turniere_bewerbungen ausgebaut
* Add: tl_fernschach_turniere_bewerbungen.infoQualifikation und bemerkungen -> Bemerkungen des Spielers aus Meldeformular speichern

## Version 1.2.1 (2025-06-29)

* Add: tl_module.fernschachverwaltung_tournamentText -> Text "Hiermit melde ich mich zu folgendem Fernschachturnier an:" selbst anpassen

## Version 1.2.0 (2025-06-27)

* Add: frontend.css -> CSS für Turniermeldeformular hinzugefügt
* Add: tl_module.fernschachverwaltung_tournamentRoot -> Festlegen welche Turnierkategorie als Root verwendet wird

## Version 1.1.5 (2025-06-12)

* Fix: Maintenance-Klasse hängt sich auf
* Add: Wartungszeitpunkte und Intervalle in tl_settings überarbeitet
* Add: tl_member.fernschach_memberbridgeTime -> Speichert den Timestamp der letzten Prüfung der Zuordnung BdF-Mitglied
* Add: tl_fernschach_spieler.memberbridgeTime -> Speichert den Timestamp der letzten Prüfung der Zuordnung BdF-Mitglied

## Version 1.1.4 (2025-06-11)

* Change: Maintenance-Klasse überarbeitet -> tstamp-Aktualisierung fehlte
* Add: tl_module.fernschachverwaltung_hauptkonto -> Checkbox Hauptkonto ausblenden, wenn Saldo gleich 0
* Change: getTurnierleiter verschoben von Meldeformular-Klasse in Turnier-Klasse
* Add: tl_fernschach_turniere_meldungen.php -> Funktion InfoTurnierleiter bei Löschung einer Anmeldung

## Version 1.1.3 (2025-06-08)

* Fix: Funktion getTurnierleiter wiederhergestellt, da Auslagerung in Klasse Turnier auf Fehler 500 aufläuft

## Version 1.1.2 (2025-06-08)

* Add: tl_fernschach_spieler.contribution_paid (Beitrag 2025 bezahlt) -> wiederhergestellt, falls noch benötigt
* Add: tl_fernschach_spieler.checkBeitrag (Beitrag = 2024 bezahlt) -> wiederhergestellt, falls noch benötigt

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
* Delete: tl_fernschach_spieler.checkBeitrag (Beitrag = 2024 bezahlt)
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
* Change: tl_fernschach_spieler.checkBeitrag -> Übersetzung "Beitrag bezahlt" geändert auf "Beitrag = 2024 bezahlt"

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
