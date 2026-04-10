<?php

$GLOBALS['TL_LANG']['XPL']['fernschach_turniere_type'] = array
(
	array('colspan', 'Folgende Typen lassen sich einstellen:'),
	array('Ordner', 'Ordner werden nur über ihren Titel (Namen) definiert. Ein Ordner kann weitere Ordner, meldefähige Turniere und Turniergruppen enthalten. Ein Ordner kann keine Meldungen und keine Spieler enthalten.<br><br><i>Beispiel: Ordner sind "Einzelmeisterschaften" (Turnierordner) und "Deutsche Fernschachmeisterschaft" (Turnierhauptklasse). Siehe dazu auch den Turnierüberblick auf dem <a href="https://www.bdf-schachserver.de/de/tour/index" target="_blank">BdF-Schachserver</a>.</i>'),
	array('Meldefähiges Turnier', 'Ein meldefähiges Turnier kann nur in einem Ordner angelegt werden. In diesem Typ werden die Stammdaten eines Turniers definiert. Innerhalb dieses Turniers werden die Meldungen erfaßt.<br><br><i>Beispiel: Ein meldefähiges Turnier ist die "54. Deutsche Fernschachmeisterschaft". Hier werden Meldungen entgegengenommen und die Meldungen später auf die Turniergruppen aufgeteilt. Siehe dazu auch die Turnierklassen auf dem <a href="https://www.bdf-schachserver.de/de/tour/tourclass?mcl=13" target="_blank">BdF-Schachserver</a>.</i>'),
	array('Turniergruppe', 'Eine Turniergruppe kann nur in einem meldefähigen Turnier angelegt werden. Dieser Typ enthält alle Spieler.<br><br><i>Beispiel: Eine Turniergruppe ist "54. DFM/V01-S", eine Gruppe der "54. Deutschen Fernschachmeisterschaft". Siehe dazu auch die Übersicht über 54. Deutsche Fernschachmeisterschaft auf dem <a href="https://www.bdf-schachserver.de/de/tour/tourtable?tcl=307" target="_blank">BdF-Schachserver</a>.</i>'),
);

$GLOBALS['TL_LANG']['XPL']['fernschach_spieler_bdfnummer'] = array
(
	array('colspan', 'Die BdF-Mitgliedsnummer unterliegt folgenden Regeln:'),
	array('1 bis 89999', '1- bis 5-stellig bis max. 89999 = Mitglieder'),
	array('90000 bis 99999', '5-stellig beginnend mit 9 = Tester (keine Mitglieder)'),
	array('600000 bis 699999', '6-stellig beginnend mit 6 = Gastnummer (keine Mitglieder)'),
	array('900000 bis 999999', '6-stellig beginnend mit 9 = DDR-Spieler (keine Mitglieder)')
);

$GLOBALS['TL_LANG']['XPL']['fernschach_mailtemplates'] = array
(
	array('Inserttags', 'Weitere Informationen zu Inserttags finden Sie unter <a href="https://docs.contao.org/books/manual/current/de/04-inhalte-verwalten/inserttags.html" title="Contao Online-Dokumentation" target="_blank" rel="noreferrer noopener">docs.contao.org/books/manual/current/de/04-inhalte-verwalten/inserttags.html</a>.'),
	array('', '<b>Verwendbare Platzhalter</b>'),
	array('##content##', 'Wird mit dem Text der E-Mail ersetzt.'),
	array('##signatur##', 'Wird mit der Signatur der E-Mail ersetzt.'),
	array('##spieler_vorname##', 'Vorname des Spielers, für den die E-Mail bestimmt ist.'),
	array('##spieler_nachname##', 'Nachname des Spielers, für den die E-Mail bestimmt ist.'),
	array('##spieler_titel##', 'Titel des Spielers, für den die E-Mail bestimmt ist.'),
	array('##spieler_anrede##', 'Anrede des Spielers, für den die E-Mail bestimmt ist.'),
	array('##spieler_briefanrede##', 'Briefanrede des Spielers, für den die E-Mail bestimmt ist.'),
	array('##spieler_geschlecht##', 'Geschlecht des Spielers (m, w, d), für den die E-Mail bestimmt ist.'),
	array('##spieler_geburtstag##', 'Geburtstag des Spielers, für den die E-Mail bestimmt ist.'),
	array('##spieler_geburtsort##', 'Geburtsort des Spielers, für den die E-Mail bestimmt ist.'),
	array('##spieler_verstorben##', 'Verstorbenstatus des Spielers (wahr oder falsch), für den die E-Mail bestimmt ist.'),
	array('##spieler_sterbetag##', 'Sterbedatum des Spielers, für den die E-Mail bestimmt ist.'),
	array('##spieler_sterbeort##', 'Sterbeort des Spielers, für den die E-Mail bestimmt ist.'),
);
