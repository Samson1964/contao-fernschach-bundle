<?php

$GLOBALS['TL_LANG']['CTE']['correspondence_chess'] = 'Fernschach-Elemente';
$GLOBALS['TL_LANG']['CTE']['fernschachverwaltung_zusagen'] = array('Turnierzusagen anzeigen', 'Teilnehmer mit Zusagen für ein Turnier anzeigen.');


$GLOBALS['TL_LANG']['tl_fernschach_spieler_import']['headline'] = 'Spielerdaten aus einer CSV-Datei importieren';
$GLOBALS['TL_LANG']['tl_fernschach_spieler_import']['format'] = 
'Die hochgeladenen CSV-Dateien müssen im UTF-8-Format vorliegen. Je Zeile steht ein Datensatz in der Datei. 
Die 1. Zeile ist die Kopfzeile mit der Definition der Spalten. Die Spalten werden mit einem Semikolon voneinander getrennt.
Die Reihenfolge der Spalten ist frei wählbar.<br><br>
Eindeutiges Kriterium der Zuordnung zu vorhandenen Datensätzen ist die Spalte <b>mitgliednr</b>. Ist ein Datensatz mit <b>mitgliednr</b> bereits
vorhanden, wird dieser mit den importierten Spalten überschrieben. Nichtimportierte Spalten bleiben erhalten. 
Folgende Spaltenarten werden unterstützt:
<table class="tl_fernschach_tabelle">
<tr>
	<th>Name der Spalte<br>(1. Zeile)</th>
	<th>Wert der Spalte<br>(2. - x. Zeile)</th>
</tr>
<tr>
	<td>mitgliednr</td>
	<td>nationale Mitgliedsnummer, Zahl oder Text mit bis zu 20 Stellen</td>
</tr>
<tr>
	<td>mitgliednr_int</td>
	<td>internationale Mitgliedsnummer, Zahl oder Text mit bis zu 20 Stellen</td>
</tr>
<tr>
	<td>vorname</td>
	<td>Vorname des Spielers</td>
</tr>
<tr>
	<td>nachname</td>
	<td>Nachname des Spielers</td>
</tr>
<tr>
	<td>strasse</td>
	<td>Straße des Wohnortes</td>
</tr>
<tr>
	<td>plz</td>
	<td>Postleitzahl des Wohnortes</td>
</tr>
<tr>
	<td>ort</td>
	<td>Name des Wohnortes</td>
</tr>
<tr>
	<td>mitgliedbeginn</td>
	<td>Beginn der Mitgliedschaft als Datum im Format TT.MM.JJJJ, MM.JJJJ oder JJJJ</td>
</tr>
<tr>
	<td>mitgliedende</td>
	<td>Ende der Mitgliedschaft als Datum im Format TT.MM.JJJJ, MM.JJJJ oder JJJJ</td>
</tr>
<tr>
	<td>status</td>
	<td>Status der Mitgliedschaft (beliebiger Text)</td>
</tr>
<tr>
	<td>aktiv</td>
	<td>1, Mitglied aktivieren - leerlassen, wenn inaktiv (ausgetreten)</td>
</tr>
</table>
<p style="margin:18px"><i>Für den Import von Spalten mit Titeln gilt:</i><br><br>
PREFIX_title = 1, wenn vorhanden - leerlassen, wenn nicht vorhanden<br>
PREFIX_date = Datum im Format TT.MM.JJJJ, MM.JJJJ oder JJJJ oder leerlassen<br><br>
<i>Mögliche Prefixe: gm, im, wgm, fm, wim, cm, wfm, wcm, fgm, sim, fim, ccm, lgm, cce, lim</i></p>
';

$GLOBALS['TL_LANG']['tl_fernschach_turniere_import']['headline'] = 'Turniere aus einer CSV-Datei importieren';
$GLOBALS['TL_LANG']['tl_fernschach_turniere_import']['format'] = 
'Die hochgeladenen CSV-Dateien müssen im UTF-8-Format vorliegen. Je Zeile steht ein Datensatz in der Datei. 
Die 1. Zeile ist die Kopfzeile mit der Definition der Spalten. Die Spalten werden mit einem Semikolon voneinander getrennt.
Die Reihenfolge der Spalten ist frei wählbar.<br><br>
Eindeutiges Kriterium der Zuordnung zu vorhandenen Datensätzen ist die Spalte <b>titel</b>. Ist ein Datensatz mit dem Wert aus der Spalte <b>titel</b> bereits
vorhanden, wird dieser mit den importierten Daten überschrieben. Nichtimportierte Spalten bleiben erhalten.<br><br>
Folgende Spaltenarten werden unterstützt:
<table class="tl_fernschach_tabelle">
<tr>
	<th>Name der Spalte<br>(1. Zeile)</th>
	<th>Wert der Spalte<br>(2. - x. Zeile)</th>
</tr>
<tr>
	<td>titel</td>
	<td>Name des Turniers. Er muß eindeutig sein, da ein vorhandenes Turnier anhand des Titels überschrieben bzw. ergänzt wird. Hat ein zu importierender Datensatz keinen Titel (Leereintrag), wird der Datensatz beim Import ignoriert.</td>
</tr>
<tr>
	<td>art</td>
	<td>i = international, n = national, e = Einladungsturnier</td>
</tr>
<tr>
	<td>kennziffer</td>
	<td>Turnier-Kennziffer (max. 255 Zeichen)</td>
</tr>
<tr>
	<td>registrationDate</td>
	<td>Meldedatum bzw. Registrierungsschluß im Format TT.MM.JJJJ</td>
</tr>
<tr>
	<td>startDate</td>
	<td>Startdatum im Format TT.MM.JJJJ</td>
</tr>
<tr>
	<td>nenngeld</td>
	<td>Betrag in Euro, z.B. -12,05 oder 13.23</td>
</tr>
<tr>
	<td>turnierleiterName</td>
	<td>Name des Turnierleiters (max. 255 Zeichen)</td>
</tr>
<tr>
	<td>turnierleiterEmail</td>
	<td>E-Mail-Adresse des Turnierleiters (max. 255 Zeichen)</td>
</tr>
<tr>
	<td>published</td>
	<td>1 = Turnier veröffentlicht, bei Nichtveröffentlichung leerlassen. Wenn das Feld <b>published</b> fehlt, wird das Turnier automatisch veröffentlicht!
</td>
</tr>
<tr>
	<td>id</td>
	<td>ID des Datensatzes in der Datenbank. Ein bereits vorhandenes Turnier wird überschrieben bzw. ergänzt. Damit läßt sich auch das Feld <b>titel</b> überschreiben! Ist das Feld <b>id</b> leer, wird wieder nach dem Feld <b>titel</b> verfahren.</td>
</tr>
</table>
';

$GLOBALS['TL_LANG']['tl_fernschach_buchungen_import']['headline'] = 'Buchungen aus einer CSV-Datei importieren';
$GLOBALS['TL_LANG']['tl_fernschach_buchungen_import']['format'] = 
'Die hochgeladenen CSV-Dateien müssen im UTF-8-Format vorliegen. Je Zeile steht ein Datensatz in der Datei. 
Die 1. Zeile ist die Kopfzeile mit der Definition der Spalten. Die Spalten werden mit einem Semikolon voneinander getrennt.
Die Reihenfolge der Spalten ist frei wählbar.<br><br>
Folgende Spaltenarten werden unterstützt:
<table class="tl_fernschach_tabelle">
<tr>
	<th>Name der Spalte<br>(1. Zeile)</th>
	<th>Wert der Spalte<br>(2. - x. Zeile)</th>
</tr>
<tr>
	<td>betrag</td>
	<td>Betrag in Euro, z.B. -12,05 oder 13.23</td>
</tr>
<tr>
	<td>art</td>
	<td>b = Mitgliedsbeitrag, g = Guthaben, n = BdF-Turnier, i = ICCF-Turnier</td>
</tr>
<tr>
	<td>datum</td>
	<td>Datum der Buchung im Format TT.MM.JJJJ</td>
</tr>
<tr>
	<td>verwendungszweck</td>
	<td>Buchungstext (max. 255 Zeichen), kann auch für den Turniernamen verwendet werden, wenn das Feld <b>turnier</b> nicht gesetzt wurde.</td>
</tr>
<tr>
	<td>turnier</td>
	<td>Name des Turniers, für das die Buchung ist. Ist das Feld leer, wird kein Turnier zugeordnet. Ist das Feld gefüllt, wird die Buchung einem vorhandenem Turnier mit diesem Namen zugeordnet oder es wird ein neues Turnier dafür angelegt. Ist das Feld <b>turnier</b> nicht definiert, wird der Wert aus dem Feld <b>verwendungszweck</b> verwendet!</td>
</tr>
<tr>
	<td>comment</td>
	<td>Kommentar zur Buchung</td>
</tr>
<tr>
	<td>memberId*</td>
	<td>Mitgliedsnummer im BdF. Ist das Feld gesetzt, wird die Buchung diesem Spieler zugeordnet. Es wird ein neuer Spieler (deaktiviert) angelegt, wenn die Mitgliedsnummer noch nicht existiert.</td>
</tr>
<tr>
	<td>nachname*</td>
	<td>Nachname des Spielers</td>
</tr>
<tr>
	<td>vorname*</td>
	<td>Vorname des Spielers</td>
</tr>
<tr>
	<td>published</td>
	<td>1 = Buchung aktiviert, bei Nichtaktivierung leerlassen. Wenn das Feld <b>published</b> fehlt, wird die Buchung automatisch aktiviert!</td>
</tr>
<tr>
	<td>id</td>
	<td>ID des Datensatzes in der Datenbank. Eine bereits vorhandene Buchung wird überschrieben bzw. ergänzt. Ist das Feld <b>id</b> leer, wird eine neue Buchung angelegt.</td>
</tr>
</table>
<p style="margin:18px">*Die Felder <b>memberId</b> oder/und <b>nachname</b> und <b>vorname</b> werden für die Zuordnung der Buchung zu einem Spieler benötigt. Ist <b>memberId</b> gesetzt, wird der entsprechende Spieler verwendet und ggfs. neu angelegt. Ist <b>memberId</b> nicht gesetzt, wird mit Hilfe der Felder <b>nachname</b> und <b>vorname</b> nach einem Spieler gesucht. Dem ersten gefundenen Spieler wird die Buchung zugeordnet. Wird kein Spieler gefunden, wird ein neuer Spieler (deaktiviert) angelegt.</p>
';

$GLOBALS['TL_LANG']['tl_fernschachverwaltung']['normen_titel'] = array
(
	'fgm' => 'GM',
	'sim' => 'SIM',
	'fim' => 'IM',
	'ccm' => 'CCM',
	'lgm' => 'LGM',
	'cce' => 'CCE',
	'lim' => 'LIM',
);
