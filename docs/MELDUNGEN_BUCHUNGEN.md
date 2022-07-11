# Technischer Ablauf Synchronisierung Meldungen <-> Buchungen

Hier wird dargestellt wie der interne Programmablauf der automatischen Erstellung von Buchungen bei Meldungen zu Turnieren ist.

## Über das Formular im Frontend

* Der Besucher schickt das Anmeldeformular ab.
* Für jede Meldung zu einem Turnier wird ein Datensatz in tl_fernschach_meldungen angelegt. Möchte der Besucher das Turnier 3 mal spielen, werden auch 3 Datensätze angelegt.
* Das angegebene Nenngeld wird nur in einem Datensatz eingetragen.
* Mit den Anmeldedaten wird ein Spieler in tl_fernschach_spieler gesucht. Nachname, Vorname und Mitgliedsnummer im BdF muß dabei gleich sein, damit die Meldungen diesem Spieler zugeordnet werden können.
* Wird der Spieler in tl_fernschach_spieler nicht gefunden, wird ein neuer Datensatz in tl_fernschach_spieler angelegt und der Spieler den Meldungen zugeordnet.
