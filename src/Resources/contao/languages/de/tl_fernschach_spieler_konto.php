<?php

/**
 * Backend-Modul: Übersetzungen im Eingabeformular
 */
$GLOBALS['TL_LANG']['tl_fernschach_spieler_konto']['buchung_legend'] = 'Buchung';
$GLOBALS['TL_LANG']['tl_fernschach_spieler_konto']['betrag'] = array('Betrag', 'Betrag ohne Vorzeichen eingeben.');
$GLOBALS['TL_LANG']['tl_fernschach_spieler_konto']['typ'] = array('Soll/Haben', 'Betrag als Soll- oder Haben-Buchung berechnen.');
$GLOBALS['TL_LANG']['tl_fernschach_spieler_konto']['datum'] = array('Datum', 'Buchungsdatum im Format TT.MM.JJJJ');
$GLOBALS['TL_LANG']['tl_fernschach_spieler_konto']['kategorie'] = array('Kategorie', 'Kategorie der Buchung');
$GLOBALS['TL_LANG']['tl_fernschach_spieler_konto']['art'] = array('Art', 'Art der Buchung');
$GLOBALS['TL_LANG']['tl_fernschach_spieler_konto']['verwendungszweck'] = array('Verwendungszweck', 'Verwendungszweck, Turniername oder Turnierkennzeichen');

$GLOBALS['TL_LANG']['tl_fernschach_spieler_konto']['turnier_legend'] = 'Zugeordnetes Turnier';
$GLOBALS['TL_LANG']['tl_fernschach_spieler_konto']['turnier'] = array('Turnier wählen', 'Die Buchung einem Turnier zuordnen, um das Nenngeld zu übernehmen.');

$GLOBALS['TL_LANG']['tl_fernschach_spieler_konto']['comment_legend'] = 'Kommentar';
$GLOBALS['TL_LANG']['tl_fernschach_spieler_konto']['comment'] = array('Kommentar', 'Interner Kommentar');

$GLOBALS['TL_LANG']['tl_fernschach_spieler_konto']['connection_legend'] = 'Verknüpfungen';
$GLOBALS['TL_LANG']['tl_fernschach_spieler_konto']['meldungId'] = array('Buchung &harr; ID Meldung', 'Wenn Sie diesen Wert ändern, geht die Verbindung zur ID der zur Buchung gehörenden Meldung verloren. Der Wert 0 hebt die Verbindung auf, jeder andere Wert verbindet die Buchung mit einem anderen, möglicherweise nichtexistenten Meldungs-Datensatz!');

$GLOBALS['TL_LANG']['tl_fernschach_spieler_konto']['publish_legend'] = 'Aktivieren';
$GLOBALS['TL_LANG']['tl_fernschach_spieler_konto']['published'] = array('Aktiv', 'Aktivieren oder deaktivieren Sie hier die Buchung');

/**
 * Buttons für Operationen
 */

$GLOBALS['TL_LANG']['tl_fernschach_spieler_konto']['new'] = array('Neue Buchung', 'Neue Buchung anlegen');
$GLOBALS['TL_LANG']['tl_fernschach_spieler_konto']['edit'] = array('Buchung bearbeiten', 'Buchung %s bearbeiten');
$GLOBALS['TL_LANG']['tl_fernschach_spieler_konto']['copy'] = array('Buchung kopieren', 'Buchung %s kopieren');
$GLOBALS['TL_LANG']['tl_fernschach_spieler_konto']['delete'] = array('Buchung löschen', 'Buchung %s löschen');
$GLOBALS['TL_LANG']['tl_fernschach_spieler_konto']['toggle'] = array('Buchung aktivieren/deaktivieren', 'Buchung %s aktivieren/deaktivieren');
$GLOBALS['TL_LANG']['tl_fernschach_spieler_konto']['show'] = array('Buchungsdetails anzeigen', 'Details der Buchung %s anzeigen');

/**
 * Buttons für globale Operationen
 */
$GLOBALS['TL_LANG']['tl_fernschach_spieler_konto']['importBuchungen'] = array('Import Buchungen', 'Globaler Import von neuen Buchungen bzw. um alte Buchungen zu ergänzen.');

$GLOBALS['TL_LANG']['tl_fernschach_spieler_konto']['typ_options'] = array
(
	'h' => 'Haben-Buchung (Zahlung durch Spieler)',
	's' => 'Soll-Buchung (Forderung an Spieler)'
);

$GLOBALS['TL_LANG']['tl_fernschach_spieler_konto']['kategorie_options'] = array
(
	'b' => 'Beitrag',
	'g' => 'Guthaben',
);

$GLOBALS['TL_LANG']['tl_fernschach_spieler_konto']['art_options'] = array
(
	'n' => 'BdF-Turnier',
	'i' => 'ICCF-Turnier',
);
