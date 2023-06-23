# Technischer Ablauf bei Anmeldungen zu Turnieren

Hier wird dargestellt wie der interne Programmablauf bei Anmeldungen zu Turnieren ist.

## Aufbau des Anmeldeformulars

Die Pflichtfelder sind:

* Mitgliedsnummer im BdF (memberid)
* Vorname, Nachname, Adresse
* Turnier (Auswahl aus Dropdown: Turniername, Nenngeld, Meldeschluss)
* Anzahl der Meldungen für dieses Turnier (Standard: 1)
* Nenngeld-Überweisung: Datum und Betrag - oder
* ... alternativ vom Guthaben abziehen (Checkbox)
* E-Mail-Adresse

Beachte: Für Spieler wird z.Z. nur ein Buchungskonto geführt, weswegen die Option "vom Guthaben abziehen" irrelevant ist.
Für eine spätere Version der Fernschach-Verwaltung sind beliebig viele Konten für Spieler geplant, d.h. mindestens ein Beitragskonto und ein Nenngeldkonto. Auf dem Nenngeldkonto darf sich ein Guthaben bilden, was dann für "vom Guthaben abziehen" gilt.

## Verarbeitung des Anmeldeformulars

 1. Prüfung der vom Formular versendeten Daten.
 2. Die Meldung wird in die Tabelle tl_fernschach_turniere_meldungen eingetragen. Wenn der Spieler mehr als eine Meldung für dieses Turnier abgibt, werden automatisch weitere Datensätze angelegt.
 3. Die Meldung wird einem Spieler aus der Tabelle tl_fernschach_spieler zugeordnet. Wird der Spieler dort nicht gefunden, wird ein neuer Datensatz angelegt.
 4. Es wird eine Sollbuchung ("Nenngeld-Forderung") in der Tabelle tl_fernschach_spieler_konto erzeugt. Die Checkbox "vom Guthaben abziehen" wird dabei nur im Buchungsdatensatz mit erwähnt, z.B. "Nenngeld-Forderung (vom Guthaben abziehen)". Für die Anzahl der Datensätze gilt Punkt 2. Das Nenngeld wird nur in den ersten angelegten Datensatz eingetragen.
 5. Der meldende Spieler bekommt eine Bestätigung seiner Anmeldung.
 6. Der Turnierleiter (des Turniers) bekommt eine Info über die neue Anmeldung.
