# Fernschach-Verwaltung

Contao-Erweiterung für den Fernschachbetrieb eines Verbandes: Mitglieder- und
Spielerverwaltung, Turniere mit Online-Anmeldung und -Bewerbung, drei getrennte
Beitrags- und Nenngeldkonten je Spieler, Titel und Titelnormen, Serienmails
sowie der Import der ICCF-Wertungslisten.

Entwickelt für und im Einsatz beim [Deutschen Fernschachbund (BdF)](https://www.bdf-fernschachbund.de/).

---

## Inhalt

* [Voraussetzungen](#voraussetzungen)
* [Installation](#installation)
* [Ersteinrichtung](#ersteinrichtung)
* [Einstellungen](#einstellungen)
* [Backend-Module](#backend-module)
* [Frontend-Module](#frontend-module)
* [Inhaltselement](#inhaltselement)
* [Rechteverwaltung](#rechteverwaltung)
* [Cronjobs](#cronjobs)
* [Import und Export](#import-und-export)
* [Serienmails](#serienmails)
* [Beitrittsformular](#beitrittsformular)
* [Datenbanktabellen](#datenbanktabellen)
* [Umstieg von 1.9.x auf 2.0.0](#umstieg-von-19x-auf-200)
* [Entwicklung](#entwicklung)
* [Weiterführende Dokumentation](#weiterführende-dokumentation)
* [Lizenz](#lizenz)

---

## Voraussetzungen

| Baustein | Version |
| --- | --- |
| PHP | 7.4 oder 8.x |
| Contao | 4.13 LTS oder 5.x |

Mitinstalliert werden:

* `schachbulle/contao-helper-bundle` — gemeinsame Hilfsfunktionen und der
  Formularbaukasten der Meldeformulare
* `menatwork/contao-multicolumnwizard-bundle` — mehrspaltige Eingabefelder
  (Mitgliedschaften, Normen, Reset-Buchungen)
* `contao/newsletter-bundle` — Grundlage der Serienmail-Funktion
* `phpoffice/phpspreadsheet` — Excel-Ausgabe von Spieler- und Statistikdaten

## Installation

Über den **Contao Manager**: nach `schachbulle/contao-fernschach-bundle` suchen
und installieren.

Auf der Kommandozeile:

```bash
composer require schachbulle/contao-fernschach-bundle
```

Anschließend die Datenbank aktualisieren — im Contao Manager unter
„System-Wartung → Datenbank aktualisieren“ oder auf der Kommandozeile:

```bash
vendor/bin/contao-console contao:migrate
```

## Ersteinrichtung

1. **Datenbank aktualisieren** (siehe oben). Dabei entstehen 17 eigene Tabellen
   sowie zusätzliche Felder in `tl_member`, `tl_user`, `tl_user_group`,
   `tl_module`, `tl_content` und `tl_newsletter_recipients`.
2. **Einstellungen pflegen** unter *System → Einstellungen → Fernschach-Verwaltung*.
   Ohne Absenderangaben verschickt die Erweiterung keine brauchbaren E-Mails.
3. **Rechte vergeben** in den Benutzergruppen (siehe [Rechteverwaltung](#rechteverwaltung)).
   Ohne Rechte sind die Schaltflächen in den Listen ausgegraut.
4. **Frontend-Module anlegen**, sofern Turnieranmeldung oder Kontoauszug im
   Frontend angeboten werden sollen.

## Einstellungen

Alle Einstellungen stehen unter *System → Einstellungen* in der Legende
**Fernschach-Verwaltung**.

| Einstellung | Bedeutung |
| --- | --- |
| Beitrittsformular | Contao-Formular, dessen Absendungen automatisch einen Spielerdatensatz anlegen (siehe [Beitrittsformular](#beitrittsformular)) |
| Globalen Reset-Buchungsdatensatz aktivieren | Schaltet die verbandsweiten Saldo-Resets ein. Ist die Option aus, werden vorhandene Reset-Buchungen beim Aufruf der Buchungen eines Spielers gelöscht |
| Reset-Buchungsdatensätze | Beliebig viele Resets mit Nummer, Datum, Saldo und den betroffenen Konten (Haupt-, Beitrags-, Nenngeldkonto). Sie gelten für **alle** Spieler |
| Standard-Mitgliedergruppe | Frontend-Mitgliedergruppe für Konten ohne BdF-Mitgliedschaft. Leer lassen, wenn nicht gewünscht |
| BdF-Mitgliedergruppe | Frontend-Mitgliedergruppe für Konten mit BdF-Mitgliedschaft. Der Cronjob *Mitgliederprüfung* trägt sie automatisch ein und aus |
| Serienmail-Verteiler | Newsletter-Archiv, dessen Empfängerliste für Serienmails benutzt wird |
| E-Mail-Absender / E-Mail-Adresse | Absender aller automatisch verschickten E-Mails |
| Name / E-Mail-Adresse Turnierdirektor | Empfänger der Mannschaftsmeldungen |
| Hinweis Kontoauszug | Text, der im Frontend statt des Kontoauszugs erscheint, wenn der angemeldete Benutzer kein BdF-Mitglied ist |
| Turnieranmeldung | Prüfoptionen für Anmeldungen — **noch nicht implementiert** |

## Backend-Module

Alle Module liegen im eigenen Backend-Bereich **Fernschach-Verwaltung**, der an
erster Stelle der Navigation eingefügt wird.

### Spieler

Die zentrale Personendatenbank: Stammdaten, zwei Anschriften, Kontaktdaten,
BdF- und ICCF-Mitgliedsnummer, Mitgliedschaftszeiträume, Spielberechtigungen,
Qualifikationen, Ehrungen, Titelnormen und SEPA-Mandate.

Untertabellen zu jedem Spieler:

* **Hauptkonto**, **Beitragskonto**, **Nenngeldkonto** — drei getrennte
  Buchungskonten mit Soll/Haben, Kategorie, Verwendungszweck und laufendem Saldo
* **Titel** — verliehene Titel mit Datum
* **E-Mails** — an den Spieler verschickte Nachrichten samt Versandstatus
* **E-Mail-Vorlagen** — wiederverwendbare Textbausteine für den Mailversand

Zusätzliche Schaltflächen in der Kopfzeile:

| Schaltfläche | Wirkung |
| --- | --- |
| Excel-Export | Ausgewählte Spieler als Excel-Datei ausgeben |
| Spieler importieren | Spieler aus einer CSV-Datei anlegen oder ergänzen |
| Buchungen importieren | Kontobuchungen aus einer CSV-Datei einlesen |
| Buchungen verschieben | Buchungen zwischen den drei Konten aller Spieler sortieren |
| Serienmail-Empfänger setzen | Empfängerliste des gewählten Newsletter-Archivs aus den Spielerdaten aufbauen |

### Turniere

Turnierkategorien und Turniere in einer Baumstruktur. Je Turnier: Kennziffer,
Klassenzuordnung, Melde- und Startdatum, Nenngeld, Teilnehmerhöchstzahl,
Meldungen je Spieler/Mannschaftsleiter, Beschränkungen nach Geschlecht und Alter, Turnierleitung
sowie die Schalter für Online-Anmeldung und Bewerbung.

Der Baum ist bewusst flach: Unter einer **Kategorie** stehen weitere Kategorien
und Turniere, unter einem **Turnier** nur noch **Turniergruppen**, unter einer
Gruppe nichts mehr. Die Auswahlliste bietet nur die an der jeweiligen Stelle
erlaubten Arten an; wird ein Datensatz durch Kopieren oder Verschieben an eine
unzulässige Stelle gebracht, weist das Speichern die Art zurück.

Untertabellen: **Meldungen**, **Bewerbungen** und die **Teilnehmerliste**.

Die Schaltfläche **Turnierstatistik** wertet Turniere, Meldungen und
Bewerbungen nach Tag, Monat und Jahr sowie nach Turniertyp aus.

#### Löschen von Meldungen und Bewerbungen

Eine Anmeldung hinterlässt eine Nenngeld-Sollbuchung auf dem Konto des Spielers.
Wird die Anmeldung gelöscht, verschwindet die Buchung mit — sonst bliebe eine
Forderung für ein Turnier stehen, zu dem niemand mehr gemeldet ist. Die
Sicherheitsabfrage vor dem Löschen weist darauf hin; über den Papierkorb lässt
sich eine Buchung **nicht** zurückholen, sie steht aber mit Betrag und
Verwendungszweck im Systemprotokoll.

Eindeutig ist die Zuordnung nur, wenn die Buchung in `meldungId` die Nummer der
Meldung trägt. Buchungen ohne diese Verknüpfung — die der Mannschaftsmeldungen
und alle älteren Datensätze — werden **nicht** von selbst gelöscht: Meldet ein
Mannschaftsleiter zwei Mannschaften zum selben Turnier, gehören beide Buchungen
zu verschiedenen Meldungen. Stattdessen erscheint nach dem Löschen ein Hinweis,
der die nach Spieler und Turnier passenden Buchungen benennt und sie auf
Wunsch entfernt.

### Konten

Freier Kontenrahmen mit Buchungen — als **Entwicklungsversion** gekennzeichnet
und noch nicht für den Wirkbetrieb gedacht. Die Schaltfläche *Standardkonten
anlegen* erzeugt einen einfachen Kontenrahmen, solange noch keine Konten
vorhanden sind.

### ICCF-Rating

Wertungslisten, ICCF-Spieler und deren Wertungszahlen. Eine Liste wird als
CSV-Datei hochgeladen und anschließend blockweise eingelesen; eine
Fortschrittsanzeige hält den Vorgang nach (siehe
[Import und Export](#import-und-export)).

### Mitgliederstatistik

Altersstrukturen definieren und die Mitgliederzahlen zu einem Stichtag nach
Altersgruppe und Geschlecht als Excel-Datei ausgeben.

### Dokumentation

Zeigt die Kurzdokumentation der Erweiterung im Backend an.

## Frontend-Module

| Modul | Zweck |
| --- | --- |
| Meldeformular Spieler-Turnieranmeldung | Anmeldung des angemeldeten Mitglieds zu einem Turnier |
| Meldeformular Mannschaftsanmeldung | Meldung einer kompletten Mannschaft durch den Mannschaftsführer |
| Liste der Titelträger | Alle Spieler mit einem bestimmten Titel |
| Titel und Normen ausgeben | Titel und Normen eines wählbaren Zeitraums |
| Glückwunschliste Titel und Normen | Wie oben, zusätzlich auf eine Höchstzahl begrenzt |
| Kontoauszug BdF-Mitglied | Buchungen und Kontostand des angemeldeten Mitglieds |

### Meldeformular Spieler-Turnieranmeldung

| Einstellung | Bedeutung |
| --- | --- |
| Formular an Mitglied binden | Nur verifizierte BdF-Mitglieder dürfen das Formular sehen |
| Turnierkategorie | Oberste Kategorie, aus der Turniere angeboten werden. Ohne Auswahl gelten alle Kategorien |
| Einleitungstext | Text über der Turnierauswahl |
| Bewerbungsformular | Speichert die Eingabe als Bewerbung statt als Anmeldung |
| Radio-Buttons bei Turnierauswahl | Turnierauswahl als Radio-Buttons statt als Auswahlliste |

Angeboten werden nur Turniere, die veröffentlicht sind, deren übergeordnete
Kategorien ebenfalls veröffentlicht sind, deren Meldeschluss noch nicht
verstrichen ist und deren Beschränkungen (Klasse, Geschlecht, Alter,
Teilnehmerhöchstzahl) zum Spieler passen. Ohne SEPA-Mandat entscheidet der
Kontostand darüber, ob eine Anmeldung möglich ist.

Nach dem Absenden entstehen die Meldung, die Nenngeld-Sollbuchung und je eine
E-Mail an die Turnierleitung und an den Spieler. Anschließend erscheint eine
**Bestätigungsseite** mit den letzten Meldungen des Spielers; ein erneutes
Absenden durch Aktualisieren der Seite ist damit ausgeschlossen.

### Meldeformular Mannschaftsanmeldung

| Einstellung | Bedeutung |
| --- | --- |
| Formular an Mitglied binden | Nur verifizierte BdF-Mitglieder dürfen das Formular sehen |

Der angemeldete Benutzer ist zugleich der Mannschaftsführer; seine Daten stehen
über dem Formular und werden nicht abgefragt. Angeboten werden veröffentlichte
Turniere vom Typ **Mannschaftsturnier** mit aktiver Online-Anmeldung, deren
Meldeschluss noch nicht verstrichen ist.

**Zahl der Bretter.** Wie viele Spieler zu melden sind, steht am Turnier im Feld
**Bretter**. Sobald ein Turnier gewählt ist, entstehen genau so viele
Eingabefelder — vier Bretter bei einem Vierermannschaftsturnier, sechs bei einem
Sechsermannschaftsturnier. Ohne Angabe am Turnier bleibt es bei vier. Wechselt
der Benutzer das Turnier, bleiben bereits eingetragene Spieler erhalten, soweit
das neue Turnier genügend Bretter hat.

**Spielerauswahl.** Ab dem zweiten Zeichen schlägt das Formular passende
Mitglieder vor; gesucht wird gleichzeitig in Nachname, Vorname,
BdF-Mitgliedsnummer und ICCF-ID. Mehrere durch Leerzeichen getrennte Wörter
müssen alle zutreffen, sodass sich mit `Muster Anna` gezielt eine Person finden
lässt. Die Liste ist auf 15 Einträge begrenzt und lässt sich mit den Pfeiltasten
bedienen. Vorgeschlagen werden nur Spieler, die zum heutigen Tag Mitglied sind
und deren Beitragskonto ausgeglichen ist (bzw. die ein SEPA-Mandat für den
Beitrag hinterlegt haben).

Die Vorschläge liefert die Route `/fernschach/spieler-suche`. Sie gibt nur
angemeldeten Frontend-Mitgliedern Auskunft und antwortet sonst mit HTTP 403 —
die Mitgliederliste ist kein öffentliches Verzeichnis.

**Prüfung.** Vor dem Abschicken wird geprüft, ob Turnier, Vereinsname,
Mannschaftsbezeichnung und alle Bretter ausgefüllt sind und ob ein Spieler
doppelt aufgestellt wurde. Dieselbe Prüfung läuft anschließend noch einmal auf
dem Server, ergänzt um die Frage, ob die gemeldeten Spieler überhaupt
meldefähige BdF-Mitglieder sind. Fehlerhafte Felder werden rot markiert; die
bereits gemachten Eingaben bleiben erhalten.

**Mehrere Mannschaften.** Ein Mannschaftsleiter darf für dasselbe Turnier so viele
Mannschaften melden, wie er möchte. Ein Turnier verschwindet nach einer Meldung
also nicht aus der Auswahl; das Feld *Meldungen je Spieler* wirkt hier nicht.

**Beitrag.** Melden darf nur, wer den Beitrag geregelt hat: SEPA-Vereinbarung für
den Beitrag oder ein Beitragskonto, das nicht im Minus steht. Das gilt für den
Mannschaftsleiter — sonst ist das Formular gesperrt — **und für jeden
aufgestellten Spieler**; ein Brett mit einem Spieler im Minus wird abgewiesen und
die Meldung nicht gespeichert.

**Nenngeld.** Das Nenngeld der Mannschaft geht zulasten des Mannschaftsleiters.
Angeboten wird ein Turnier deshalb nur, wenn er es aufbringen kann — entweder mit
einer **SEPA-Vereinbarung für das Nenngeld** (dann wird abgebucht, der Kontostand
ist gleichgültig) oder mit einem **Guthaben auf dem Nenngeldkonto, das das
Nenngeld deckt**. Da das Nenngeld je Turnier verschieden ist, wird das für jedes
Turnier einzeln entschieden; verglichen wird auf den Cent genau.

Fehlt beides, nennt das Formular die betroffenen Turniere mit ihrem Nenngeld und
den aktuellen Kontostand, statt einfach nichts anzubieten. Unmittelbar vor der
Buchung wird noch einmal geprüft — zwischen Aufbau und Absenden des Formulars
kann sich der Kontostand geändert haben.

**Was gespeichert wird.** Die Mannschaft landet in
`tl_fernschach_turniere_mannschaften`, ihre Aufstellung Brett für Brett in
`tl_fernschach_turniere_mannschaften_spieler`. Der Mannschaftsleiter bekommt die
Nenngeld-Sollbuchung, jeder aufgestellte Spieler einen Nenngeld-Datensatz über
**0 €** — der belastet nichts und hält nur fest, dass er zu diesem Turnier
gemeldet ist. Im Backend führt die Schaltfläche **Mannschaften bearbeiten** am
Turnier dorthin.

**Bestätigung.** Nach dem Speichern leitet das Modul auf sich selbst um (`send=1`)
und zeigt eine Bestätigungsseite, auf der Turnier, Nenngeld, Verein, Mannschaft,
Mannschaftsführer, die vollständige Aufstellung und die Bemerkungen noch einmal
aufgeführt sind. Das Aktualisieren der Seite kann die Meldung damit nicht
wiederholen. Parallel gehen zwei E-Mails heraus: an den Mannschaftsführer und an
den Turnierdirektor aus den Einstellungen.

**Gestaltung.** Seit Version 2.2.0 bringt das Formular sein eigenes Aussehen und
Verhalten mit (`fernschach_formular.css` und `mannschaftsmeldung.js` unter
`bundles/contaofernschach/`); vom Theme wird nichts mehr übernommen. Die Farben
und Abstände stehen als CSS-Variablen unter `.fernschach-formular` und lassen
sich im Theme überschreiben, ohne die Regeln selbst anzufassen:

```css
.fernschach-formular {
	--fs-farbe: #a0122b;
	--fs-radius: 0;
}
```

Ohne JavaScript zeigt das Formular keine Bretteingaben. Der Server weist eine
solche Meldung ab, sodass keine unvollständige Aufstellung entstehen kann.

### Mehrfachmeldungen

Am Turnier steht im Feld **Meldungen je Spieler**, wie oft sich derselbe Spieler
für dieses Turnier melden darf. Die Voreinstellung ist **1**, der Wert 0 hebt die
Begrenzung auf. Anmeldungen und Bewerbungen werden getrennt gezählt.

Ist die Zahl erreicht, taucht das Turnier im Meldeformular gar nicht mehr auf.
Zusätzlich prüft das Speichern noch einmal — sonst ließe sich die Sperre über den
Zurück-Knopf oder einen zweiten Browsertab umgehen. Eine abgewiesene Meldung wird
im Systemprotokoll vermerkt.

**Für Mannschaftsturniere gilt das Feld nicht.** Ein Mannschaftsleiter darf
beliebig viele Mannschaften seines Vereins melden („Musterstadt I",
„Musterstadt II", …); ein bereits gemeldetes Turnier bleibt in der Auswahl
stehen. Da es dort keine Sperre gibt, wird jede Mannschaftsmeldung im
Systemprotokoll vermerkt — nur daran lässt sich eine versehentliche
Doppelmeldung nachträglich erkennen.

### Kontoauszug BdF-Mitglied

| Einstellung | Bedeutung |
| --- | --- |
| Buchungen Minimum / Maximum | Wie viele Buchungen mindestens und höchstens erscheinen (0 = alle) |
| Ab Datum | Frühestes Buchungsdatum, das angezeigt werden darf |
| Kontostand anzeigen | Blendet den Saldo ein |
| Resetbuchung Pflicht | Zeigt Kontostand und Auszug nur, wenn es eine Resetbuchung ab dem 01.04.2023 gibt |
| Konten auswählen | Welche der drei Konten erscheinen — Reihenfolge per Drag & Drop |
| Hauptkonto ausblenden | Blendet das Hauptkonto aus, wenn dessen Saldo 0 ist |

### Titel und Normen

Beide Normen-Module haben eine Auswahl **Zeitraum** (letzter Monat bis
unbegrenzt); die Glückwunschliste zusätzlich eine **maximale Anzahl**.
Das Modul *Liste der Titelträger* erwartet die Auswahl eines Titels.

## Inhaltselement

**Zusagen zu Einladungsturnieren** (`fernschachverwaltung_zusagen`) gibt die
Teilnehmer aus, die einem Einladungsturnier zugesagt haben. Ohne Auswahl eines
Turniers erscheinen alle aktiven, noch nicht gestarteten Einladungsturniere.

## Rechteverwaltung

Die Erweiterung meldet zwei eigene Rechtegruppen an, die in Benutzern und
Benutzergruppen gepflegt werden.

**Spieler-Rechte** (`fernschach_spieler`): Anlegen, Importieren, Exportieren,
Mehrfachbearbeitung, Bearbeiten, Kopieren, Löschen, Buchungen anzeigen,
Veröffentlichen-Status setzen, Infobox anzeigen, Fertig-Status setzen.

**Buchungen-Rechte** (`fernschach_konto`): Importieren, Anlegen, Bearbeiten,
Löschen, Kopieren, Veröffentlichen-Status setzen, Infobox anzeigen,
Fertig-Status setzen, Mehrfachbearbeitung.

Zusätzlich gibt es die Rechte **Meldungen** (Anlegen, Löschen) und das Feld
**Signatur**, das im Serienmailversand unter den Text gesetzt wird.

## Cronjobs

Alle Aufträge hängen am Contao-Cron und laufen ohne weitere Einrichtung, sobald
der Contao-Cron eingerichtet ist.

| Auftrag | Takt | Aufgabe |
| --- | --- | --- |
| Mitgliedschaftsprüfung | stündlich | Setzt am Spieler das Feld „Mitglied“ passend zu seinen Mitgliedschaftszeiträumen |
| Mitgliederprüfung | stündlich | Gleicht Frontend-Mitglieder mit Spielerdatensätzen ab und pflegt die BdF-Mitgliedergruppe |
| Nenngeldprüfung | stündlich | Sucht Nenngeldkonten mit negativem Saldo |
| Mitgliedschaftsende | täglich | Beendet zum Vortag ausgelaufene Mitgliedschaften |
| Streichung | täglich | Hält Streichungsdatum und Mitgliedschaftsende widerspruchsfrei |

Die Intervalle einiger Prüfungen lassen sich über die Voreinstellungen
`fernschach_intervall_memberbridgeCheck` und `fernschach_intervall_membershipsCheck`
steuern.

## Import und Export

### Spieler importieren

CSV-Datei mit Kopfzeile, Felder durch **senkrechte Striche** (`|`) getrennt. Die
Namen in der Kopfzeile bestimmen die Zuordnung, unter anderem `mitgliednr`,
`mitgliednr_int`, `nachname`, `vorname`, `geburtstag`, `strasse`, `plz`, `ort`,
`email1`. Bereits vorhandene Spieler werden anhand der Mitgliedsnummer ergänzt
statt doppelt angelegt. Der Vorgang wird nach `var/logs/fernschach-verwaltung.log`
mitgeschrieben.

### Buchungen importieren

CSV-Datei mit Kopfzeile, Felder durch **Semikolon** getrennt. Buchungen werden
dem Spieler über seine Mitgliedsnummer zugeordnet; unbekannte Mitgliedsnummern
legen einen neuen Spieler an. Anschließend empfiehlt sich *Buchungen
verschieben*, damit Beitrags- und Nenngeldbuchungen in den richtigen Konten
landen.

### Spieler exportieren

Excel-Datei der in der Liste gefilterten Spieler. Auf Wunsch enthält sie einen
aus Geburtsdatum und Mitgliedsnummer gebildeten Kenncode zu einem Stichtag.

Die letzte Spalte **Interner Bereich** zeigt, ob dem Spieler ein
Frontend-Mitgliedskonto zugeordnet ist: *Ja*, *Ja (gesperrt)* bei einem
deaktivierten Konto oder *Nein*. Ohne Konto kann sich der Spieler nicht anmelden
und weder Turniermeldungen abgeben noch seinen Kontoauszug einsehen.

### ICCF-Wertungsliste importieren

Im Modul *ICCF-Rating* eine Wertungsliste anlegen, deren Zeile aufrufen und
*CSV importieren* wählen. Erwartet wird eine semikolongetrennte Datei mit den
Spalten

```text
ICCF-ID;Land;Titel;Name (Nachname, Vorname);Partien;Wertung;Abweichung;Kennzeichen
```

Die Datei wird nach `system/tmp` hochgeladen; anschließend liest die Route
`/contao/fernschach/iccf-import` sie in Blöcken zu 500 Zeilen ein und die
Fortschrittsanzeige zeigt den Stand. Alle bisherigen Wertungen der Liste werden
vorher auf „nicht veröffentlicht“ gesetzt, sodass nach dem Import nur der Inhalt
der neuen Datei aktiv ist. Bekannte Spieler werden nur aktualisiert, wenn die
Liste jünger ist als ihr letzter Stand; jede Änderung wird im internen Feld des
Spielers protokolliert.

## Serienmails

E-Mails an Spieler entstehen in der Untertabelle **E-Mails** eines Spielers oder
über den Serienmailversand. In Betreff, Text und Signatur dürfen Platzhalter der
Form `##name##` sowie Contao-Insert-Tags stehen. Zur Verfügung stehen unter
anderem:

| Platzhalter | Inhalt |
| --- | --- |
| `##content##` | Der eingegebene Nachrichtentext |
| `##signatur##` | Die Signatur des Backend-Benutzers |
| `##spieler_nachname##`, `##spieler_vorname##` | Name des Spielers |
| `##spieler_titel##`, `##spieler_anrede##`, `##spieler_briefanrede##` | Anrede und Titel |
| `##spieler_geschlecht##` | Geschlecht |
| `##spieler_geburtstag##`, `##spieler_geburtsort##` | Geburtsdaten |
| `##spieler_verstorben##`, `##spieler_sterbetag##`, `##spieler_sterbeort##` | Sterbedaten |

Die Vorschaufunktion im Bearbeitungsformular zeigt die fertige Nachricht mit
aufgelösten Platzhaltern.

Über *Serienmail-Empfänger setzen* wird die Empfängerliste des in den
Einstellungen gewählten Newsletter-Archivs aus den Spielerdaten aufgebaut. Ein
`parseTemplate`-Hook ergänzt beim Newsletterversand die spielerbezogenen
Platzhalter.

## Beitrittsformular

Wird in den Einstellungen ein Contao-Formular als Beitrittsformular hinterlegt,
legt jede Absendung dieses Formulars automatisch einen veröffentlichten
Spielerdatensatz an. Ausgewertet werden die Formularfelder `nachname`,
`vorname`, `strasse`, `plz`, `ort`, `telefon`, `email` und `mitgliedsnummer`.
Alle weiteren bekannten Felder — `geburtstag`, `staat`, `bdf_mitglied`,
`fernschach_erfolge`, `nahschach_erfolge`, `elo`, `dwz`, `beitrittsmonat`,
`beitrittszustimmung` — sammelt die Erweiterung als Fließtext im Feld
*Informationen zum Beitritt*. Der Vorgang wird im Systemprotokoll vermerkt.

## Datenbanktabellen

| Tabelle | Inhalt |
| --- | --- |
| `tl_fernschach_spieler` | Spieler- und Mitgliederstammdaten |
| `tl_fernschach_spieler_konto` | Hauptkonto |
| `tl_fernschach_spieler_konto_beitrag` | Beitragskonto |
| `tl_fernschach_spieler_konto_nenngeld` | Nenngeldkonto |
| `tl_fernschach_spieler_titel` | Verliehene Titel |
| `tl_fernschach_spieler_mails` | Verschickte E-Mails |
| `tl_fernschach_spieler_mailtemplates` | E-Mail-Vorlagen |
| `tl_fernschach_turniere` | Turnierkategorien und Turniere |
| `tl_fernschach_turniere_meldungen` | Anmeldungen |
| `tl_fernschach_turniere_bewerbungen` | Bewerbungen |
| `tl_fernschach_turniere_spieler` | Teilnehmerzuordnung |
| `tl_fernschach_turniere_mannschaften` | Gemeldete Mannschaften |
| `tl_fernschach_turniere_mannschaften_spieler` | Aufstellung je Mannschaft, ein Satz pro Brett |
| `tl_fernschach_konten` | Kontenrahmen (Entwicklungsversion) |
| `tl_fernschach_konten_buchungen` | Buchungen des Kontenrahmens |
| `tl_fernschach_mitgliederstatistik` | Altersstrukturen |
| `tl_fernschach_iccf_ratinglists` | ICCF-Wertungslisten |
| `tl_fernschach_iccf_players` | ICCF-Spieler |
| `tl_fernschach_iccf_ratings` | ICCF-Wertungszahlen |

## Umstieg von 1.9.x auf 2.0.0

Version 2.0.0 läuft unter Contao 4.13 **und** Contao 5 sowie unter PHP 8. An der
Datenbank ändert sich nichts, an zwei Stellen aber am Verhalten:

* **codefog/contao-haste entfällt.** Die Erweiterung braucht Haste nicht mehr.
  Wird Haste von keiner anderen Erweiterung benutzt, kann es entfernt werden.
* **Der ICCF-Import läuft über eine Route.** Die direkt aufrufbare Datei
  `bundles/contaofernschach/Import_ICCF_Rating.php` ist entfallen; an ihre Stelle
  tritt `/contao/fernschach/iccf-import`. Nach dem Update ist ein
  Cache-Neuaufbau nötig, damit die Route bekannt wird:

  ```bash
  vendor/bin/contao-console cache:clear
  vendor/bin/contao-console cache:warmup
  ```

* **Das Auswahlfeld für das Notification Center ist entfallen.** Es war ohne
  Funktion — das Meldeformular verschickt seine E-Mails selbst.
* **Die Importprotokolle liegen jetzt unter `var/logs/`.** Sie hießen und heißen
  `fernschach-verwaltung.log`, `fernschachverwaltung.log` und
  `fernschachverwaltung_buchungen.log`; nur der Weg dorthin ist ein anderer,
  weil die Contao-Funktion `log_message()` entfallen ist.

Der vollständige Änderungsstand steht in der [CHANGELOG.md](CHANGELOG.md).

## Entwicklung

Die Erweiterung bringt kein eigenes `vendor/`-Verzeichnis mit. Die Unit-Tests
laufen mit einem separat installierten PHPUnit 9; die Contao-Klassen kommen über
die Umgebungsvariable `CONTAO_AUTOLOAD` aus einer beliebigen
Contao-Installation:

```bash
CONTAO_AUTOLOAD=/pfad/zur/contao-installation/vendor/autoload.php vendor/bin/phpunit
```

Ohne `CONTAO_AUTOLOAD` überspringen sich die Tests, die Contao brauchen, statt
mit einem Fehler abzubrechen.

## Weiterführende Dokumentation

* [Überblick](docs/README.md)
* [Turnierarten](docs/TURNIERARTEN.md)
* [Anmeldungen zu Einzelturnieren](docs/TURNIERANMELDUNGEN_EINZEL.md)
* [Anmeldungen zu Mannschaftsturnieren](docs/TURNIERANMELDUNGEN_MANNSCHAFT.md)
* [Meldungen zuweisen](docs/MELDUNGEN_ZUWEISEN.md)
* [Wartungsarbeiten](docs/WARTUNG.md)

## Lizenz

LGPL-3.0-or-later — siehe [LICENSE](LICENSE).

**Frank Hoppe**
