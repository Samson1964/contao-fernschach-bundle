# Programmablauf beim Zuweisen von Meldungen

## tl_fernschach_turniere_meldungen (M)

- id         = automatische Datensatznummer der Meldung
- pid        = id des Turniers aus tl_fernschach_turniere, zu der die Meldung gehört
- teilnehmer = id der Turniergruppe, der die Meldung zugeordnet wurde

## tl_fernschach_turniere_spieler (S)

- id         = automatische Datensatznummer der Meldung
- pid        = id der Turniergruppe aus tl_fernschach_turniere, zu der die Meldung gehört
- meldungId  = id der Meldung aus tl_fernschach_turniere_meldungen

## Verknüpfungen

M.id = S.meldungId
M.teilnehmer = S.pid

## Programmablauf

* Meldung in M eintragen
* M.teilnehmer ist 0, da keine Turniergruppe verknüpft ist

* Spieler in S eintragen
* Folgende Spieler werden vorgeschlagen für S.meldungId:
** M.teilnehmer = 0 (noch keiner Gruppe zugeordnet)


