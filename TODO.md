# Fernschach-Verwaltung ToDo

## Migration von Mitgliederverwaltung nach Fernschach-Verwaltung

* Alle 3 Tabellen exportieren und entsprechend �ndern
tl_mitgliederverwaltung -> tl_fernschach_spieler (alle Felder identisch)
tl_mitgliederverwaltung_applications -> tl_fernschach_turnierbewerbungen
tl_mitgliederverwaltung_tournaments -> tl_fernschach_turniere
- Feld date -> startDate

## Noch bearbeiten

/src/Classes/ImportBuchungen.php
/src/Classes/ImportTournaments.php
/src/ContentElements/Zusagen.php   
/src/Modules/TitelNormenLast.php
/src/Modules/TitelNormen.php    


