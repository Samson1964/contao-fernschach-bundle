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

## Todesfall eines Mitglieds

* Wartungsintervall: einmal täglich

Wird bei einem Spieler *Verstorben* gesetzt, ist der **Todestag Pflicht** — ohne ihn endet die Mitgliedschaft nicht und der Beitrag läuft weiter. Der Datensatz lässt sich deshalb nicht mehr ohne Datum speichern; ist das genaue Datum unbekannt, gehört ein ungefähres hinein.

Beim ersten Eintrag des Todestages wird der **Schatzmeister benachrichtigt**. Empfänger und Absender stehen in *System → Einstellungen*. Ist dort keine Adresse hinterlegt, unterbleibt die Nachricht und es entsteht ein Eintrag im Systemprotokoll. Eine spätere Korrektur des Datums löst keine zweite Nachricht aus, ebenso wenig ein über den Import angelegter Datensatz.

Der tägliche Cronjob *Todesfallprüfung* trägt anschließend den Todestag als **Ende der laufenden Mitgliedschaft** ein, mit dem Status *Verstorben*. Angefaßt wird nur eine Mitgliedschaft ohne Enddatum: Wer Jahre nach seinem Austritt stirbt, war zum Todeszeitpunkt kein Mitglied mehr; dessen Mitgliedschaft bleibt, wie sie ist. Ein zweiter Lauf ändert deshalb nichts mehr. Eine Datensatzversionierung findet bei einem Cronjob nicht statt.

Todesvermerke **ohne** Todestag kann keine Routine heilen — das Datum kennt nur ein Mensch. Der Cronjob meldet sie deshalb nur, und zwar in einer einzigen Protokollzeile für alle: Die Meldung wiederholt sich jeden Tag, solange niemand die Daten nachträgt.

## Verstorbene prüfen (von Hand)

Backend-Modul **Spieler**, Schaltfläche **Verstorbene prüfen** über der Liste.

Der Bericht zeigt zwei Listen und **ändert nichts**:

1. **Verstorben, aber ohne Todestag** — Altbestand aus der Zeit, bevor der Todestag Pflichtfeld war. Diese Datensätze müssen von Hand ergänzt werden; jeder Name führt direkt zum Datensatz.
2. **Todestag vorhanden, Mitgliedschaft noch offen** — das erledigt der Cronjob beim nächsten Lauf. Bis dahin gelten diese Spieler noch als Mitglied.

Geprüft werden alle Spieler, auch archivierte und nicht veröffentlichte.
