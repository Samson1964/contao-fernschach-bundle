<?php

/**
 * Backend-Modul: Übersetzungen im Eingabeformular
 */
$GLOBALS['TL_LANG']['tl_fernschach_spieler_konto_nenngeld']['buchung_legend'] = 'Buchung';
$GLOBALS['TL_LANG']['tl_fernschach_spieler_konto_nenngeld']['betrag'] = array('Betrag', 'Betrag ohne Vorzeichen eingeben.');
$GLOBALS['TL_LANG']['tl_fernschach_spieler_konto_nenngeld']['typ'] = array('Soll/Haben', 'Betrag als Soll- oder Haben-Buchung berechnen.');
$GLOBALS['TL_LANG']['tl_fernschach_spieler_konto_nenngeld']['datum'] = array('Datum', 'Buchungsdatum im Format TT.MM.JJJJ');
$GLOBALS['TL_LANG']['tl_fernschach_spieler_konto_nenngeld']['sortierung'] = array('Sortierung', 'Hilfsfeld für eine benutzerdefinierte Sortierung bei gleichem Datum bei mehreren Buchungen. Geben Sie bis zu 5-stellige Zahlen ein. Eine höhere Zahl bedeutet eine jüngere Buchung. Standard ist 0.');
$GLOBALS['TL_LANG']['tl_fernschach_spieler_konto_nenngeld']['art'] = array('Art', 'Art der Buchung');
$GLOBALS['TL_LANG']['tl_fernschach_spieler_konto_nenngeld']['verwendungszweck'] = array('Verwendungszweck', 'Verwendungszweck, Turniername oder Turnierkennzeichen');

$GLOBALS['TL_LANG']['tl_fernschach_spieler_konto_nenngeld']['extras_legend'] = 'Besondere Einstellungen';
$GLOBALS['TL_LANG']['tl_fernschach_spieler_konto_nenngeld']['markierung'] = array('Markieren', 'Buchung markieren');
$GLOBALS['TL_LANG']['tl_fernschach_spieler_konto_nenngeld']['saldoReset'] = array('Saldo-Reset', 'Der Saldo wird ab dieser Buchung mit dem obigen Betrag weitergeführt.');

$GLOBALS['TL_LANG']['tl_fernschach_spieler_konto_nenngeld']['turnier_legend'] = 'Zugeordnetes Turnier';
$GLOBALS['TL_LANG']['tl_fernschach_spieler_konto_nenngeld']['turnier'] = array('Turnier wählen', 'Die Buchung einem Turnier zuordnen, um das Nenngeld zu übernehmen.');

$GLOBALS['TL_LANG']['tl_fernschach_spieler_konto_nenngeld']['comment_legend'] = 'Kommentar';
$GLOBALS['TL_LANG']['tl_fernschach_spieler_konto_nenngeld']['comment'] = array('Kommentar', 'Interner Kommentar');

$GLOBALS['TL_LANG']['tl_fernschach_spieler_konto_nenngeld']['connection_legend'] = 'Verknüpfungen';
$GLOBALS['TL_LANG']['tl_fernschach_spieler_konto_nenngeld']['meldungId'] = array('Buchung &harr; ID Meldung', 'Wenn Sie diesen Wert ändern, geht die Verbindung zur ID der zur Buchung gehörenden Meldung verloren. Der Wert 0 hebt die Verbindung auf, jeder andere Wert verbindet die Buchung mit einem anderen, möglicherweise nichtexistenten Meldungs-Datensatz!');

$GLOBALS['TL_LANG']['tl_fernschach_spieler_konto_nenngeld']['publish_legend'] = 'Aktivieren';
$GLOBALS['TL_LANG']['tl_fernschach_spieler_konto_nenngeld']['published'] = array('Aktiv', 'Aktivieren oder deaktivieren Sie hier die Buchung');

/**
 * Buttons für Operationen
 */

$GLOBALS['TL_LANG']['tl_fernschach_spieler_konto_nenngeld']['new'] = array('Neue Buchung', 'Neue Buchung anlegen');
$GLOBALS['TL_LANG']['tl_fernschach_spieler_konto_nenngeld']['edit'] = array('Buchung bearbeiten', 'Buchung %s bearbeiten');
$GLOBALS['TL_LANG']['tl_fernschach_spieler_konto_nenngeld']['copy'] = array('Buchung kopieren', 'Buchung %s kopieren');
$GLOBALS['TL_LANG']['tl_fernschach_spieler_konto_nenngeld']['delete'] = array('Buchung löschen', 'Buchung %s löschen');
$GLOBALS['TL_LANG']['tl_fernschach_spieler_konto_nenngeld']['toggle'] = array('Buchung aktivieren/deaktivieren', 'Buchung %s aktivieren/deaktivieren');
$GLOBALS['TL_LANG']['tl_fernschach_spieler_konto_nenngeld']['show'] = array('Buchungsdetails anzeigen', 'Details der Buchung %s anzeigen');
$GLOBALS['TL_LANG']['tl_fernschach_spieler_konto_nenngeld']['markiertIcon'] = array('Spieler markiert/nicht markiert', 'Spieler %s markieren - aktivieren oder deaktivieren');

/**
 * Buttons für globale Operationen
 */
$GLOBALS['TL_LANG']['tl_fernschach_spieler_konto_nenngeld']['importBuchungen'] = array('Import Buchungen', 'Globaler Import von neuen Buchungen bzw. um alte Buchungen zu ergänzen.');

$GLOBALS['TL_LANG']['tl_fernschach_spieler_konto_nenngeld']['typ_options'] = array
(
	'h' => 'Haben-Buchung (Zahlung durch Spieler)',
	's' => 'Soll-Buchung (Forderung an Spieler)'
);

$GLOBALS['TL_LANG']['tl_fernschach_spieler_konto_nenngeld']['art_options'] = array
(
	'n' => 'BdF-Turnier',
	'i' => 'ICCF-Turnier',
	'g' => 'Guthaben',
);
