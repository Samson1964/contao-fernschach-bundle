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
 