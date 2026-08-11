# Technischer Ablauf bei Anmeldungen zu Mannschaftsturnieren

Hier wird dargestellt wie der interne Programmablauf bei Anmeldungen zu Mannschaftsturnieren ist.

## Aufbau des Anmeldeformulars

Das Formular wird in einem geschützten Bereich der Website angezeigt, der nur für BdF-Mitglieder zugänglich ist. Die Mitgliedsdaten stehen dadurch bereits zur Verfügung und müssen im Formular nicht abgefragt werden. Das Mitglied ist zugleich Mannschaftsführer der zu meldenden Mannschaft.

### Voraussetzungen einer Turnieranmeldung für den Mannschaftsführer

* Das SEPA-Mandat Beitrag liegt vor oder das Beitragskonto ist nicht im Minus. Trifft beides nicht zu, ist keine Anmeldung möglich.
* Das SEPA-Mandat Nenngeld liegt vor oder das Nenngeldkonto ist nicht im Minus. Im ersten Fall stehen alle Turniere zur Auswahl. Im zweiten Fall stehen nur Turniere zur Auswahl, wo das Nenngeld das Guthaben auf dem Nenngeldkonto nicht übersteigt.

### Voraussetzungen für Spieler der zu meldenden Mannschaft

* Das SEPA-Mandat Beitrag liegt vor oder das Beitragskonto ist nicht im Minus. Trifft beides nicht zu, ist keine Anmeldung möglich.

## Verarbeitung des Anmeldeformulars

Seit Version 2.6.0 wird die Meldung gespeichert. Der Ablauf:

1. Prüfung der vom Formular versendeten Daten (Pflichtfelder, keine doppelten Spieler, Mitgliedschaft und Beitragskonto jedes Spielers).
2. Die Mannschaft wird in `tl_fernschach_turniere_mannschaften` eingetragen — Untertabelle der Turniere. Gespeichert werden Verein, alter Vereinsname, Mannschaftsbezeichnung, Mannschaftsleiter mit Mitgliedsnummer und E-Mail, Meldedatum, Nenngeld und Bemerkungen.
3. Die Aufstellung wird in `tl_fernschach_turniere_mannschaften_spieler` eingetragen, ein Datensatz je Brett — eine eigene Tabelle, weil die Zahl der Bretter je Turnier verschieden ist. Neben der Spielernummer werden Name, BdF-Mitgliedsnummer und ICCF-ID mitgeschrieben, damit die Aufstellung lesbar bleibt, auch wenn der Spielerdatensatz später geändert wird.
4. Für den **Mannschaftsleiter** entsteht eine Sollbuchung über das Nenngeld des Turniers in `tl_fernschach_spieler_konto_nenngeld`, verknüpft über `mannschaftId`.
5. Für **jeden aufgestellten Spieler** entsteht ein Nenngeld-Datensatz über **0 EUR**, verknüpft über `mannschaftId` und `mannschaftSpielerId`. Er belastet nichts und hält nur fest, dass der Spieler zu diesem Turnier gemeldet ist.
6. Der Mannschaftsleiter bekommt eine Bestätigung, der Turnierdirektor eine Info.

Im Backend sind die Mannschaften über die Schaltfläche **Mannschaften bearbeiten** am Turnier erreichbar; sie erscheint nur bei Turnieren vom Typ *Mannschaftsturnier*.

### Löschen

Wird eine Mannschaft gelöscht, verschwinden mit ihr die Aufstellung (Kindtabelle) und alle Nenngeldsätze, die über `mannschaftId` mit ihr verknüpft sind — die Sollbuchung des Leiters ebenso wie die 0-EUR-Sätze der Spieler. Wird nur ein einzelnes Brett gelöscht, geht ausschließlich dessen 0-EUR-Satz mit.
