# Technischer Ablauf bei Wartungen

Wartungsarbeiten laufen im Hintergrund (als Cronjob) in festen Abständen beim Aufruf einer Seite im Front- oder Backend ab.

## Automatische Archivierung von ehemaligen Mitgliedern

* Wartungsintervall: einmal täglich um 0 Uhr

Es werden alle veröffentlichten Spieler geprüft, die noch nicht archiviert sind. Ist einer dieser Spieler kein Mitglied mehr, wird er archiviert (Feld archived auf true). Im System-Log wird diese Archivierung protokolliert. Eine Datensatzversionierung findet bei einem Cronjob nicht statt.
