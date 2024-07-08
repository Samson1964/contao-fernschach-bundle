<?php

/**
 * Backend-Modul: Übersetzungen im Eingabeformular
 */
$GLOBALS['TL_LANG'][$strTable]['buchung_legend'] = 'Buchung';
$GLOBALS['TL_LANG'][$strTable]['betrag'] = array('Betrag', 'Betrag ohne Vorzeichen eingeben.');
$GLOBALS['TL_LANG'][$strTable]['typ'] = array('Soll/Haben', 'Betrag als Soll- oder Haben-Buchung berechnen.');
$GLOBALS['TL_LANG'][$strTable]['datum'] = array('Datum', 'Buchungsdatum im Format TT.MM.JJJJ');
$GLOBALS['TL_LANG'][$strTable]['sortierung'] = array('Sortierung', 'Hilfsfeld für eine benutzerdefinierte Sortierung bei gleichem Datum bei mehreren Buchungen. Geben Sie bis zu 5-stellige Zahlen ein. Eine höhere Zahl bedeutet eine jüngere Buchung. Standard ist 0.');
$GLOBALS['TL_LANG'][$strTable]['kategorie'] = array('Kategorie', 'Kategorie der Buchung');
$GLOBALS['TL_LANG'][$strTable]['art'] = array('Art', 'Art der Buchung');
$GLOBALS['TL_LANG'][$strTable]['verwendungszweck'] = array('Verwendungszweck', 'Verwendungszweck, Turniername oder Turnierkennzeichen');

$GLOBALS['TL_LANG'][$strTable]['extras_legend'] = 'Besondere Einstellungen';
$GLOBALS['TL_LANG'][$strTable]['markierung'] = array('Markieren', 'Buchung markieren');
$GLOBALS['TL_LANG'][$strTable]['saldoReset'] = array('Saldo-Reset', 'Der Saldo wird ab dieser Buchung mit dem obigen Betrag weitergeführt.');

$GLOBALS['TL_LANG'][$strTable]['turnier_legend'] = 'Zugeordnetes Turnier';
$GLOBALS['TL_LANG'][$strTable]['turnier'] = array('Turnier wählen', 'Die Buchung einem Turnier zuordnen, um das Nenngeld zu übernehmen.');

$GLOBALS['TL_LANG'][$strTable]['comment_legend'] = 'Kommentar';
$GLOBALS['TL_LANG'][$strTable]['comment'] = array('Kommentar', 'Interner Kommentar');

$GLOBALS['TL_LANG'][$strTable]['connection_legend'] = 'Verknüpfungen';
$GLOBALS['TL_LANG'][$strTable]['meldungId'] = array('Buchung &harr; ID Meldung', 'Wenn Sie diesen Wert ändern, geht die Verbindung zur ID der zur Buchung gehörenden Meldung verloren. Der Wert 0 hebt die Verbindung auf, jeder andere Wert verbindet die Buchung mit einem anderen, möglicherweise nichtexistenten Meldungs-Datensatz!');

$GLOBALS['TL_LANG'][$strTable]['publish_legend'] = 'Aktivieren';
$GLOBALS['TL_LANG'][$strTable]['published'] = array('Aktiv', 'Aktivieren oder deaktivieren Sie hier die Buchung');

$GLOBALS['TL_LANG'][$strTable]['saldo'] = array('Saldo', 'Der Saldo wird aus allen veröffentlichten, ggfs. gefilterten Datensätzen berechnet.');

/**
 * Buttons für Operationen
 */

$GLOBALS['TL_LANG'][$strTable]['new'] = array('Neue Buchung', 'Neue Buchung anlegen');
$GLOBALS['TL_LANG'][$strTable]['edit'] = array('Buchung bearbeiten', 'Buchung %s bearbeiten');
$GLOBALS['TL_LANG'][$strTable]['copy'] = array('Buchung kopieren', 'Buchung %s kopieren');
$GLOBALS['TL_LANG'][$strTable]['delete'] = array('Buchung löschen', 'Buchung %s löschen');
$GLOBALS['TL_LANG'][$strTable]['toggle'] = array('Buchung aktivieren/deaktivieren', 'Buchung %s aktivieren/deaktivieren');
$GLOBALS['TL_LANG'][$strTable]['show'] = array('Buchungsdetails anzeigen', 'Details der Buchung %s anzeigen');
$GLOBALS['TL_LANG'][$strTable]['markiertIcon'] = array('Spieler markiert/nicht markiert', 'Spieler %s markieren - aktivieren oder deaktivieren');

/**
 * Buttons für globale Operationen
 */
$GLOBALS['TL_LANG'][$strTable]['verschiebeBuchungen'] = array('Buchungen verschieben', 'Buchungen bei allen Spielern verschieben.');
$GLOBALS['TL_LANG'][$strTable]['importBuchungen'] = array('Buchungen importieren', 'Globaler Import von neuen Buchungen bzw. um alte Buchungen zu ergänzen.');

$GLOBALS['TL_LANG'][$strTable]['typ_options'] = array
(
	'h' => 'Haben-Buchung (Zahlung durch Spieler)',
	's' => 'Soll-Buchung (Forderung an Spieler)'
);

$GLOBALS['TL_LANG'][$strTable]['kategorie_options'] = array
(
	'b' => 'Beitrag',
	'g' => 'Guthaben',
	's' => 'Nenngeld',
);

$GLOBALS['TL_LANG'][$strTable]['art_options'] = array
(
	'n' => 'BdF-Turnier',
	'i' => 'ICCF-Turnier',
	'g' => 'Guthaben',
);

/**
 * Infofelder
 */
$GLOBALS['TL_LANG'][$strTable]['message_resetRecord'] = 'Dieser Datensatz ist als globaler Reset definiert! Die Felder "Betrag", "Soll/Haben" und "Datum" dürfen nicht geändert werden. Bei einer Änderung wird der Datensatz gelöscht und neu angelegt mit den voreingestellten Werten.';
