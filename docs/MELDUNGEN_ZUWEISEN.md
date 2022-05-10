# Programmablauf beim Zuweisen von Meldungen

## tl_fernschach_turniere_meldungen (M)

- id         = automatische Datensatznummer der Meldung
- pid        = id des Turniers aus tl_fernschach_turniere, zu der die Meldung geh�rt
- teilnehmer = id der Turniergruppe, der die Meldung zugeordnet wurde

## tl_fernschach_turniere_spieler (S)

- id         = automatische Datensatznummer der Meldung
- pid        = id der Turniergruppe aus tl_fernschach_turniere, zu der die Meldung geh�rt
- meldungId  = id der Meldung aus tl_fernschach_turniere_meldungen

## Verkn�pfungen

M.id = S.meldungId
M.teilnehmer = S.pid

## Programmablauf

* Meldung in M eintragen
* M.teilnehmer ist 0, da keine Turniergruppe verkn�pft ist

* Spieler in S eintragen
* Folgende Spieler werden vorgeschlagen f�r S.meldungId:
** M.teilnehmer = 0 (noch keiner Gruppe zugeordnet)


