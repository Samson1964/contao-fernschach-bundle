# Meldeverwaltung für Turniere

## Module im Backend

* Spielerverwaltung
* Turnierverwaltung
* Meldeverwaltung

### Backend-Modul Spielerverwaltung

* Erfassung von Mitgliedern und Nichtmitgliedern mit Geburtsdatum und Kontaktdaten
* Verknüpfung mit Tabelle tl_member für Zuordnung von Frontend-Mitgliedern
* Unterscheidung BdF-Mitglied oder nicht

### Backend-Modul Turnierverwaltung

* Verwalten von Turnieren, für die gemeldet werden kann
* Turniertypen: Klassenturniere, Einladungsturniere, Pokalturniere
* Bearbeitungsformular zeigt die Meldungen für das Turnier an

### Backend-Modul Meldeverwaltung

* Verarbeitung der Daten aus dem Meldeformular
* Checkboxen (mit Datum) für Nenngeld bezahlt, Spielberechtigung okay
	* Status wird vom Turnierleiter, Turnierdirektor, Schatzmeister verwaltet
* Automatische E-Mail-Bernachrichtigung bei Änderungen an einer Meldung
* Meldung wird verknüpft mit einem Spieler-Datensatz
* Meldung wird verknüpft mit einem Turnier-Datensatz
* Automatische Prüfung, ob der meldende Spieler berechtigt ist, an dem Turnier teilzunehmen

## Module im Frontend

* Meldeformular für Turniere
* Ausgabe von Meldelisten

### Frontend-Modul Meldeformular

* Select-Box mit den Turnieren, für die gemeldet werden kann
* Select-Box mit den Spielern, für die gemeldet werden kann

### Frontend-Modul Meldelisten

* Gibt die Meldeliste eines Turniers aus
