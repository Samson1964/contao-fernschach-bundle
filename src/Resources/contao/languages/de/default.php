<?php

$GLOBALS['TL_LANG']['CTE']['correspondence_chess'] = 'Fernschach-Elemente';
$GLOBALS['TL_LANG']['CTE']['mitgliederverwaltung_zusagen'] = array('Turnierzusagen anzeigen', 'Teilnehmer mit Zusagen für ein Turnier anzeigen.');


$GLOBALS['TL_LANG']['tl_mitgliederverwaltung_import']['headline'] = 'Mitgliederdaten aus einer CSV-Datei importieren';
$GLOBALS['TL_LANG']['tl_mitgliederverwaltung_import']['format'] = 
'Die hochgeladenen CSV-Dateien müssen im UTF-8-Format vorliegen. Je Zeile steht ein Datensatz in der Datei. 
Die 1. Zeile ist die Kopfzeile mit der Definition der Spalten. Die Spalten werden mit einem Semikolon voneinander getrennt.
Die Reihenfolge der Spalten ist frei wählbar.<br><br>
Eindeutiges Kriterium der Zuordnung zu vorhandenen Datensätzen ist die Spalte mitgliednr. Ist ein Datensatz mit mitgliednr bereits
vorhanden, wird dieser mit den importierten Spalten überschrieben. Nichtimportierte Spalten bleiben erhalten. 
Folgende Spaltenarten sind möglich:<br><br>
<b>Spaltenart (1. Zeile) = Wert (2. - x. Zeile)</b><br>
mitgliednr = nationale Mitgliedsnummer, Zahl oder Text mit bis zu 20 Stellen<br>
mitgliednr_int = internationale Mitgliedsnummer, Zahl oder Text mit bis zu 20 Stellen<br>
vorname = Vorname des Mitglieds<br>
nachname = Nachname des Mitglieds<br>
strasse = <br>
plz = <br>
ort = <br>
mitgliedbeginn = Datum im Format TT.MM.JJJJ, MM.JJJJ oder JJJJ<br>
mitgliedende = Datum im Format TT.MM.JJJJ, MM.JJJJ oder JJJJ<br>
status = Status der Mitgliedschaft (beliebiger Text)<br>
email = E-Mail-Adresse<br>
aktiv = 1, Mitglied aktivieren - leerlassen, wenn inaktiv (ausgetreten)<br><br>
<i>Für den Import von Spalten mit Titeln gilt:</i><br><br>
PREFIX_title = 1, wenn vorhanden - leerlassen, wenn nicht vorhanden<br>
PREFIX_date = Datum im Format TT.MM.JJJJ, MM.JJJJ oder JJJJ oder leerlassen<br><br>
<i>Mögliche Prefixe: gm, im, wgm, fm, wim, cm, wfm, wcm, fgm, sim, fim, ccm, lgm, cce, lim</i>';

$GLOBALS['TL_LANG']['tl_mitgliederverwaltung']['normen_titel'] = array
(
	'fgm' => 'GM',
	'sim' => 'SIM',
	'fim' => 'IM',
	'ccm' => 'CCM',
	'lgm' => 'LGM',
	'cce' => 'CCE',
	'lim' => 'LIM',
);
