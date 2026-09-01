# Technischer Ablauf bei Wartungen

Wartungsarbeiten laufen im Hintergrund (als Cronjob) in festen Abständen beim Aufruf einer Seite im Front- oder Backend ab.

## Automatische Archivierung von ehemaligen Mitgliedern

* Wartungsintervall: einmal täglich um 0 Uhr

Es werden alle veröffentlichten Spieler geprüft, die noch nicht archiviert sind. Ist einer dieser Spieler kein Mitglied mehr, wird er archiviert (Feld archived auf true). Im System-Log wird diese Archivierung protokolliert. Eine Datensatzversionierung findet bei einem Cronjob nicht statt.

## Mitgliedschaften prüfen (von Hand)

Backend-Modul **Spieler**, Schaltfläche **Mitgliedschaften prüfen** über der Liste.

Gespeichert werden die Datumsangaben einer Mitgliedschaft als `JJJJMMTT`. Im Bestand stehen aber auch Datensätze in der Anzeigeform `TT.MM.JJJJ`. Auffallen können sie nicht: Die Anzeige reicht sie unverändert durch, und ein Speichern im Backend wandelt sie stillschweigend um. Beim Vergleich richten sie Schaden an — PHP vergleicht `'31.12.2025'` mit `20260831` als Zeichenketten, sodass eine längst beendete Mitgliedschaft als aktiv gilt.

Seit Version 2.9.2 werden sie beim Lesen richtig ausgewertet. Diese Routine räumt sie zusätzlich in der Datenbank auf:

1. Der erste Aufruf **sucht nur** und listet die betroffenen Spieler mit dem alten und dem künftigen Wert auf. Es wird nichts verändert.
2. Die Schaltfläche *Jetzt bereinigen* schreibt sie nach einer Rückfrage um. Vor jeder Änderung entsteht eine Version, der alte Stand lässt sich also zurückholen. Jede Änderung steht im Systemprotokoll.
3. Lässt sich für einen Datensatz keine Version anlegen, wird er übersprungen und am Ende genannt — ohne Rückweg wird nichts geändert.

Geprüft werden alle Spieler, auch archivierte und nicht veröffentlichte.
