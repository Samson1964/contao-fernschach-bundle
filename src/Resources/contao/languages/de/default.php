<?php

// Globale Variablen
$GLOBALS['TL_LANG']['fernschachverwaltung']['botname'] = 'Fernschach-Bot';

// Bestätigungen in Popups
$GLOBALS['TL_LANG']['tl_fernschach_spieler']['setNewsletter_confirm'] = 'Wollen Sie die E-Mail-Adressen der aktuellen Spielerliste wirklich in das Serienmail-Newsletter-Archiv importieren?';

$GLOBALS['TL_LANG']['CTE']['correspondence_chess'] = 'Fernschach-Elemente';
$GLOBALS['TL_LANG']['CTE']['fernschachverwaltung'] = array('Fernschach-Verwaltung', 'Teilnehmer mit Zusagen für ein Turnier anzeigen.');
$GLOBALS['TL_LANG']['CTE']['fernschachverwaltung_zusagen'] = array('Turnierzusagen anzeigen', 'Teilnehmer mit Zusagen für ein Turnier anzeigen.');

$GLOBALS['TL_LANG']['tl_fernschach_spieler_import']['headline'] = 'Spielerdaten aus einer CSV-Datei importieren';
$GLOBALS['TL_LANG']['tl_fernschach_spieler_import']['format'] = 
'Die hochgeladenen CSV-Dateien müssen im UTF-8-Format vorliegen. Je Zeile steht ein Datensatz in der Datei. 
Die 1. Zeile ist die Kopfzeile mit der Definition der Spalten. Die Spalten werden mit einem | voneinander getrennt.
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
	<td>geschlecht</td>
	<td>Geschlecht. Folgende Werte sind möglich: m, w, d</td>
</tr>
<tr>
	<td>anrede</td>
	<td>Anrede. Erlaubt sind: Herr, Herrn, Frau, Mr., Mrs. oder das Feld leerlassen.</td>
</tr>
<tr>
	<td>briefanrede</td>
	<td>Briefanrede. Erlaubt sind: Lieber Fernschachfreund, Liebe Fernschachfreundin, Lieber Schachfreund, Liebe Schachfreundin, Dear chessfriend oder das Feld leerlassen.</td>
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
	<td>zusatz</td>
	<td>Adresszusatz</td>
</tr>
<tr>
	<td>telefon</td>
	<td>1. Telefonnummer</td>
</tr>
<tr>
	<td>telefon1</td>
	<td>1. Telefonnummer</td>
</tr>
<tr>
	<td>telefon2</td>
	<td>2. Telefonnummer</td>
</tr>
<tr>
	<td>telefax</td>
	<td>1. Telefaxnummer</td>
</tr>
<tr>
	<td>telefax1</td>
	<td>1. Telefaxnummer</td>
</tr>
<tr>
	<td>telefax2</td>
	<td>2. Telefaxnummer</td>
</tr>
<tr>
	<td>email</td>
	<td>1. E-Mail-Adresse</td>
</tr>
<tr>
	<td>email1</td>
	<td>1. E-Mail-Adresse</td>
</tr>
<tr>
	<td>email2</td>
	<td>2. E-Mail-Adresse</td>
</tr>
<tr>
	<td>verein</td>
	<td>Verein</td>
</tr>
<tr>
	<td>status</td>
	<td>Status</td>
</tr>
<tr>
	<td>mitgliedbeginn</td>
	<td>Beginn der Mitgliedschaft als Datum im Format TT.MM.JJJJ, MM.JJJJ oder JJJJ. Eine vorhandene Mitgliedschaft wird ergänzt, ansonsten eine neue Mitgliedschaft angelegt.</td>
</tr>
<tr>
	<td>mitgliedende</td>
	<td>Ende der Mitgliedschaft als Datum im Format TT.MM.JJJJ, MM.JJJJ oder JJJJ. Eine vorhandene Mitgliedschaft wird ergänzt, ansonsten eine neue Mitgliedschaft angelegt.</td>
</tr>
<tr>
	<td>mitgliedstatus</td>
	<td>Status der Mitgliedschaft (beliebiger Text). Eine vorhandene Mitgliedschaft wird ergänzt, ansonsten eine neue Mitgliedschaft angelegt.</td>
</tr>
<tr>
	<td>klassenberechtigung</td>
	<td>Klassenberechtigung. Mögliche Werte sind: M, H, O oder das Feld leerlassen.</td>
</tr>
<tr>
	<td>gast</td>
	<td>Gast-Nummer</td>
</tr>
<tr>
	<td>servertester</td>
	<td>Servertester-Nummer</td>
</tr>
<tr>
	<td>fremdspieler</td>
	<td>Fremdspieler-Nummer</td>
</tr>
<tr>
	<td>streichung</td>
	<td>Streichungsdatum im Format TT.MM.JJJJ, MM.JJJJ oder JJJJ</td>
</tr>
<tr>
	<td>zuzug</td>
	<td>Zuzug als Datum im Format TT.MM.JJJJ, MM.JJJJ oder JJJJ</td>
</tr>
<tr>
	<td>info</td>
	<td>Interne Bemerkungen</td>
</tr>
<tr>
	<td>titelhalter</td>
	<td>Mit dem Inhalt wird das Feld <i>titelinfo</i> ergänzt.</td>
</tr>
<tr>
	<td>iccftitel</td>
	<td>Mit dem Inhalt wird das Feld <i>titelinfo</i> ergänzt.</td>
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
	<td>typ</td>
	<td>Möglich sind folgende Werte: category, tournament, group</td>
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
	<td>1 = Turnier veröffentlicht, bei Nichtveröffentlichung leerlassen. Wenn das Feld <b>published</b> fehlt, wird das Turnier automatisch veröffentlicht!</td>
</tr>
<tr>
	<td>id</td>
	<td>ID des Datensatzes in der Datenbank. Ein bereits vorhandenes Turnier wird überschrieben bzw. ergänzt. Damit läßt sich auch das Feld <b>titel</b> überschreiben! Ist das Feld <b>id</b> leer, wird wieder nach dem Feld <b>titel</b> verfahren.</td>
</tr>
<tr>
	<td>pid</td>
	<td>ID des Eltern-Datensatzes in der Datenbank. Das importierte Turnier wird in diese Kategorie/dieses Turnier eingefügt. Bei gesetztem Feld <b>id</b> wird das Feld <b>pid</b> ignoriert!</td>
</tr>
</table>
';

$GLOBALS['TL_LANG']['tl_fernschach_buchungen_verschieben']['hinweis'] = array
(
	'Buchungen global verschieben', 
	'<p class="fernschach-verwaltung">Hier können Sie Buchungen global über alle Buchungskonten (Hauptkonto, Beitragskonto, Nenngeld) bei allen Spielern verschieben. Wenn Sie kein Zielkonto auswählen, werden die Buchungen anhand der Kategorie verschoben: "Nenngeld" in das Nenngeldkonto, "Beitrag" in das Beitragskonto, alle anderen Kategorien in das Hauptkonto. Es werden maximal 2000 Buchungen verschoben, um fertig zu sein, bevor der Server das Skript abbricht. Führen Sie das Formular in dem Fall ggfs. mehrmals aus.<br><br><span style="color:red; font-weight:bold;">Mit dem Klick auf "Verschiebung starten" beginnt ohne Sicherheitsfrage die Verschiebung. Bitte sind Sie achtsam beim Füllen der Formularfelder!</span><br><br>Alle Ausführungen der Funktion werden in fernschachverwaltung_buchungen.log protokolliert. Fragen Sie den Webmaster für den Zugriff darauf.
	<ul>
		<li><b>Buchungsdatum</b>: Es werden nur Buchungen mit diesem Datum verschoben. Lassen Sie das Feld frei, wenn Buchungen unabhängig vom Datum verschoben werden sollen.</li>
		<li><b>Verwendungszweck suchen</b>: Es werden nur Buchungen berücksichtigt, die im Verwendungszweck diesen Text zu stehen haben. Groß- und Kleinschreibung wird nicht unterschieden. Wenn Sie das Feld leerlassen, wird es nicht berücksichtigt.</li>
		<li><b>Verwendungszweck auswählen</b>: Die Auswahlliste zeigt die Verwendungszwecke aus allen Buchungen, abwärts sortiert nach Vorkommen dieses Verwendungszwecks, wobei immer das komplette Feld berücksichtigt wird. Wenn Sie einen Verwendungszweck hier auswählen, werden nur Buchungen damit berücksichtigt. Groß- und Kleinschreibung wird nicht unterschieden.<br>Die Suche nach einem leeren Verwendungszweck (Standardeinstellung oder nur eine Anzahl in Klammern) ist nicht möglich. In dem Fall bleibt das Feld unberücksichtigt.</li>
		<li><b>Zielkonto auswählen</b>: Buchungen werden in das ausgewählte Konto verschoben. Die Kategorie der Buchung wird dabei nicht berücksichtigt!</li>
	</ul>
	<br>
	<h3>Beispiele:</h3>
	<ul>
		<li><b>Variante 1</b>: Buchungsdatum "12.01.2024", Verwendungszweckssuche "Nenngeld", Verwendungszweckauswahl "Nenngeld (x mal)", kein Zielkonto &raquo; Verschiebt alle Buchungen entsprechend ihrer Kategorie in das richtige Zielkonto. Es werden nur Buchungen berücksichtigt, die mit den Parametern übereinstimmen.</li>
		<li><b>Variante 2</b>: Buchungsdatum leer, Verwendungszweckssuche leer, Verwendungszweckauswahl "Nenngeld (x mal)", Zielkonto "Nenngeldkonto" &raquo; Verschiebt alle Buchungen mit dem Verwendungszweck "Nenngeld" in das Nenngeldkonto.</li>
		<li><b>Variante 3</b>: Buchungsdatum leer, Verwendungszweckssuche leer, Verwendungszweckauswahl leer, Zielkonto leer &raquo; Hier werden alle Buchungen entsprechend ihrer Kategorie in das richtige Zielkonto verschoben.</li>
		<li><b>Variante 4</b>: Buchungsdatum leer, Verwendungszweckssuche leer, Verwendungszweckauswahl leer, Zielkonto ausgewählt &raquo; <b>Hier wird nichts ausgeführt, da alle Buchungen in das gewählte Zielkonto verschoben würden.</b></li>
	</ul></p>'
);
$GLOBALS['TL_LANG']['tl_fernschach_buchungen_verschieben']['datum'] = array('Buchungsdatum', 'Datum der Buchung(en), die verschoben werden sollen. Leerlassen, wenn das Datum unberücksichtigt bleiben soll.');
$GLOBALS['TL_LANG']['tl_fernschach_buchungen_verschieben']['select_verwendungszweck'] = array('Verwendungszweck auswählen', 'Zusammenfassung aller Verwendungszwecke in allen Buchungskonten. Technisch bedingt wird der Verwendungszweck in Kleinschreibung angezeigt. In Klammern steht die Anzahl der Vorkommen.');
$GLOBALS['TL_LANG']['tl_fernschach_buchungen_verschieben']['search_verwendungszweck'] = array('Verwendungszweck suchen', 'Es wird im ganzen Verwendungszweck nach dem Begriff gesucht.');
$GLOBALS['TL_LANG']['tl_fernschach_buchungen_verschieben']['zielkonto'] = array('Zielkonto auswählen', 'Legen Sie hier fest in welches Konto die Buchungen verschoben werden sollen. Wenn Sie kein Zielkonto auswählen, werden die Buchungen anhand der zugeordneten Kategorien verschoben.');

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
	<td>konto</td>
	<td>Mögliche Werte: h, b, n - h (Hauptkonto) ist Standard, wenn nichts angegeben wird. Möglich sind auch noch b (Beitrag) und n (Nenngeld).</td>
</tr>
<tr>
	<td>betrag</td>
	<td>Betrag in Euro (ggfs. mit Vorzeichen, siehe <i>typ</i>), z.B. -12,05 oder 13.23</td>
</tr>
<tr>
	<td>typ</td>
	<td>s = Sollbuchung (Ausgabe, Rechnungstellung Nenngeld), h = Habenbuchung (Zahlung vom Spieler). Wenn diese Spalte nicht angegeben ist, wird das Vorzeichen aus der Spalte <i>betrag</i> entsprechend als Soll oder Haben verwendet.</td>
</tr>
<tr>
	<td>art</td>
	<td>n = BdF-Turnier, i = ICCF-Turnier</td>
</tr>
<tr>
	<td>kategorie</td>
	<td>b = Beitrag, g = Guthaben</td>
</tr>
<tr>
	<td>datum*</td>
	<td>Datum der Buchung im Format TT.MM.JJJJ</td>
</tr>
<tr>
	<td>verwendungszweck</td>
	<td>Buchungstext (max. 255 Zeichen), kann auch für den Turniernamen verwendet werden, wenn das Feld <b>turnier</b> nicht gesetzt wurde.</td>
</tr>
<tr>
	<td>markierung</td>
	<td>1 = Buchung markieren, ansonsten leerlassen</td>
</tr>
<tr>
	<td>reset</td>
	<td>1 = Saldo vor der Buchung auf 0 setzen, ansonsten leerlassen</td>
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
	<td>memberid*</td>
	<td>Mitgliedsnummer im BdF. Ist das Feld gesetzt, wird die Buchung diesem Spieler zugeordnet. Es wird ein neuer Spieler (deaktiviert) angelegt, wenn die Mitgliedsnummer noch nicht existiert.</td>
</tr>
<tr>
	<td>iccfid</td>
	<td>Mitgliedsnummer im ICCF. Ist das Feld <i>memberid</i> gesetzt, wird die Buchung diesem Spieler zugeordnet. Es wird ein neuer Spieler (deaktiviert) angelegt, wenn die Mitgliedsnummer noch nicht existiert.</td>
</tr>
<tr>
	<td>nachname*</td>
	<td>Nachname des Spielers. Sind die Felder <i>memberid</i> und <i>iccfid</i> nicht gesetzt, wird für die Buchung ein neuer Spieler mit <i>nachname</i> und <i>vorname</i> (deaktiviert) angelegt.</td>
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
<p style="margin:18px">*Pflichtfelder<br>Die Felder <b>memberid</b> oder/und <b>nachname</b> und <b>vorname</b> werden für die Zuordnung der Buchung zu einem Spieler benötigt. Ist <b>memberid</b> gesetzt, wird der entsprechende Spieler verwendet und ggfs. neu angelegt. Ist <b>memberid</b> nicht gesetzt, wird mit Hilfe der Felder <b>nachname</b> und <b>vorname</b> nach einem Spieler gesucht. Dem ersten gefundenen Spieler wird die Buchung zugeordnet. Wird kein Spieler gefunden, wird ein neuer Spieler (deaktiviert) angelegt.</p>
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
	'im'  => 'IM',
);

$GLOBALS['TL_LANG']['tl_fernschach_mitgliederstatistik']['headline'] = 'Mitgliederstatistik erstellen';
$GLOBALS['TL_LANG']['tl_fernschach_mitgliederstatistik']['stichtag'] = array('Stichtag', 'Geben Sie einen Stichtag ein.');
$GLOBALS['TL_LANG']['tl_fernschach_mitgliederstatistik']['altersstruktur'] = array('Altersstruktur auswählen', 'Wählen Sie die Altersstruktur für die Mitgliederstatistik aus.');
$GLOBALS['TL_LANG']['tl_fernschach_mitgliederstatistik']['aktiviert'] = array('Nur aktivierte Spieler', 'Statistik nur mit den aktivierten Spielern erstellen. Entfernen Sie das Häkchen, wenn Sie alle Spieler berücksichtigen möchten.');
$GLOBALS['TL_LANG']['tl_fernschach_mitgliederstatistik']['start'] = 'Export starten';

$GLOBALS['TL_LANG']['tl_fernschach_exportexcel']['headline'] = 'Spieler nach Excel exportieren';
$GLOBALS['TL_LANG']['tl_fernschach_exportexcel']['kenncode_stichtag'] = array('Stichtag Kenncode', 'Geben Sie den Stichtag für den Kenncode ein. Mit dem Stichtag wird ein eindeutiger Kenncode für den Spieler generiert, der als Passwort für andere Projekte verwendet werden kann. Wiederholen Sie einen Excel-Export, dann verwenden Sie bitte immer denselben Stichtag!');
$GLOBALS['TL_LANG']['tl_fernschach_exportexcel']['saldo_stichtag'] = array('Stichtag Saldo', 'Geben Sie den Stichtag für den Saldo ein.');
$GLOBALS['TL_LANG']['tl_fernschach_exportexcel']['start'] = array('Export starten', 'Der Download des Exports erfolgt im Hintergrund. Klicken Sie danach auf den Zurück-Link.');

$GLOBALS['TL_LANG']['tl_fernschach_spieler_konto']['hinweis'] = '
<ul>
<li>&bull; grüne Schrift = Habenbuchung (Beitragszahlung, Guthabenzahlung, Nenngeldzahlung u.a.)</li>
<li>&bull; rote Schrift = Sollbuchung (Rechnung an Spieler, Nenngeldforderung u.a.)</li>
<li>&bull; rot unterstrichen = globale Resetbuchung für alle Konten (setzt den Saldo auf 0,00 €)</li>
<li>&bull; schwarz unterstrichen = Resetbuchung nur in diesem Konto (setzt den Saldo auf 0,00 €)</li>
<li>&bull; rötlicher Hintergrund = markierte Buchung</li>
</ul>
';

$GLOBALS['TL_LANG']['tl_fernschach_spieler_konto']['verschieben'] = '
<ul>
<li>&bull; Buchungen der Kategorie Beitrag werden verschoben in das Beitragskonto.</li>
<li>&bull; Buchungen der Kategorie Nenngeld werden verschoben in das Nenngeldkonto.</li>
<li>&bull; Buchungen anderer Kategorien werden verschoben in das Hauptkonto.</li>
</ul>
';


$GLOBALS['TL_LANG']['tl_module']['fernschachverwaltung_konten_options'] = array
(
	'h'           => 'Hauptkonto',
	'b'           => 'Beitragskonto',
	'n'           => 'Nenngeldkonto'
);
