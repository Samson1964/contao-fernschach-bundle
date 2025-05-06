# Turnierarten in der Turnierverwaltung

Contao unterscheidet drei Turnierarten bzw. Datensatztypen:

* category <img src="../src/Resources/public/images/turnier_kategorie.png" alt="Kategorie oberste Ebene" width="12"/> <img src="../src/Resources/public/images/turnier_hauptklasse.png" alt="Kategorie andere Ebenen" width="12"/> = Ordner (nur Platzhalter für Turniere)
* tournament <img src="../src/Resources/public/images/turnier_meldungen.png" alt="Turnier" width="12"/> = Meldefähiges Turnier / Turnierklasse
* group <img src="../src/Resources/public/images/turnier_gruppe.png" alt="Gruppe" width="12"/> = Turniergruppe

Die Turnierart **category** kann auf jeder Ebene, außer unterhalb von Turnieren verwendet werden. Als Symbol wird ein gelbes Ordner-Icon verwendet. Nur auf der obersten Ebene wird ein anderes Icon verwendet.
Die Turnierart **tournament** kann nur unterhalb der Kategorien/Ordner (category) verwendet werden. Diese Turnierart kann als einzige Anmeldungen aufnehmen und diese optional als Teilnehmer zugewiesen werden.
Die Turnierart **group** kann nur unterhalb von Turnieren (tournament) verwendet werden. Anmeldungen aus Turnieren können dieser Turnierart als Teilnehmer zugewiesen werden.
