# Technischer Ablauf bei Anmeldungen zu Turnieren

Hier wird dargestellt wie der interne Programmablauf bei Anmeldungen zu Turnieren ist.

## Aufbau des Anmeldeformulars

Das Formular wird in einem geschützten Bereich der Website angezeigt, der nur für BdF-Mitglieder zugänglich ist. Die Mitgliedsdaten stehen dadurch bereits zur Verfügung und müssen im Formular nicht abgefragt werden.

Die Pflichtfelder im Formular sind:

* Turnier (Auswahl aus Dropdown: Turniername, Nenngeld, Meldeschluss)

Zusätzlich gibt es optionale Eingabefelder für Qualifikationen und Bemerkungen.

### Voraussetzungen einer Turnieranmeldung

* SEPA-Mandat Beitrag liegt vor oder Beitragskonto ist nicht im Minus. Trifft beides nicht zu, ist keine Anmeldung möglich.
* SEPA-Mandat Nenngeld liegt vor oder Nenngeldkonto ist nicht im Minus. Im ersten Fall stehen alle Turniere zur Auswahl. Im zweiten Fall stehen nur Turniere zur Auswahl, wo das Nenngeld das Guthaben auf dem Nenngeldkonto nicht übersteigt.
* Die Turnierparameter (Teilnehmeranzahl, Qualifikation, Geschlecht, Mindest- oder Maximalalter) passen zum meldewilligen Spieler

#### Berechnung Mindest- und Maximalalter

Für ein Mindest- oder Maximalalter wird nicht der Anmeldetag und auch nicht der Meldeschluss des Turniers berücksichtigt. Für die Prüfung des Mindestalters wird der 31.12. des aktuellen Jahres zugrunde gelegt. Für die Prüfung des Maximalalters wird der 01.01. des aktuellen Jahres zugrunde gelegt. Dabei gilt: Das Mindestalter muß größer/gleich dem Alter sein. Das Maximalalter muß kleiner/gleich dem Alter sein. Ein paar Beispiele:

 1. Im Turnier ist ein Mindestalter von 20 Jahren eingetragen. Ein 19-jähriger Spieler (* 01.11.2005) will sich am 01.10.2025 anmelden, also einen Monat vor seinem 20. Geburtstag. Das Programm rechnet jetzt (20251231 - 20051101) / 10000 = 20,013. Das Mindestalter ist größer/gleich dem Alter. Damit ist der Spieler spielberechtigt.
 2. Im Turnier ist ein Mindestalter von 20 Jahren eingetragen. Ein 20-jähriger Spieler (* 01.07.2005) will sich am 01.10.2025 anmelden, also drei Monate nach seinem 20. Geburtstag. Das Programm rechnet jetzt (20251231 - 20050701) / 10000 = 20,053. Das Mindestalter ist größer/gleich dem Alter. Damit ist der Spieler spielberechtigt.
 3. Im Turnier ist ein Maximalalter von 60 Jahren eingetragen. Ein 60-jähriger Spieler (* 01.07.1965) will sich am 01.10.2025 anmelden, also drei Monate nach seinem 60. Geburtstag. Das Programm rechnet jetzt (20250101 - 19650701) / 10000 = 59,94. Das Maximalalter ist kleiner/gleich dem Alter. Damit ist der Spieler spielberechtigt.

## Verarbeitung des Anmeldeformulars

 1. Prüfung der vom Formular versendeten Daten.
 2. Die Meldung wird in die Tabelle tl_fernschach_turniere_meldungen eingetragen. 
 3. Die Meldung wird einem Spieler aus der Tabelle tl_fernschach_spieler zugeordnet. Wird der Spieler dort nicht gefunden, wird ein neuer Datensatz angelegt.
 4. Es wird eine Sollbuchung ("Nenngeld-Forderung") in der Tabelle tl_fernschach_spieler_konto_nenngeld erzeugt. Die Checkbox "vom Guthaben abziehen" wird dabei nur im Buchungsdatensatz mit erwähnt, z.B. "Nenngeld-Forderung (vom Guthaben abziehen)".
 5. Der meldende Spieler bekommt eine Bestätigung seiner Anmeldung.
 6. Der Turnierleiter (des Turniers, für das gemeldet wird) und alle übergeordneten Turnierleiter bekommen eine Info über die neue Anmeldung.

## Verschieben von Anmeldungen im Backend

Gibt es für ein Turnier genügend Meldungen, kann ein neues Turnier angelegt werden und die Meldungen können verschoben werden. Nachfolgend ein Beispiel mit einem Turnier der Meisterklasse:

 1. "Meisterklasse (Server)" ist ein "Meldefähiges Turnier / Turnierklasse". Es gibt genügend Anmeldungen und jetzt sollen diese verschoben werden.
 2. Unterhalb von "Meisterklasse (Server)" ein neues Turnier (z.B. MS-360) anlegen vom einzig möglichen Typ "Turniergruppe".
 3. Anmeldungen via "Mehrere bearbeiten" verschieben aus "Meisterklasse (Server)" und einfügen in "MS-360".
 