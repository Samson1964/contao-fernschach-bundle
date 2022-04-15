<?php

/**
 * Backend-Modul: Übersetzungen im Eingabeformular
 */
$GLOBALS['TL_LANG']['tl_fernschach_konto']['buchung_legend'] = 'Buchung';
$GLOBALS['TL_LANG']['tl_fernschach_konto']['betrag'] = array('Betrag', 'Positiven (Haben-Buchung) oder negativen Betrag (Soll-Buchung) eingeben');
$GLOBALS['TL_LANG']['tl_fernschach_konto']['datum'] = array('Datum', 'Buchungsdatum im Format TT.MM.JJJJ');
$GLOBALS['TL_LANG']['tl_fernschach_konto']['art'] = array('Art', 'Art der Buchung');
$GLOBALS['TL_LANG']['tl_fernschach_konto']['verwendungszweck'] = array('Verwendungszweck', 'Verwendungszweck, Turniername oder Turnierkennzeichen');

$GLOBALS['TL_LANG']['tl_fernschach_konto']['turnier_legend'] = 'Zugeordnetes Turnier';
$GLOBALS['TL_LANG']['tl_fernschach_konto']['turnier'] = array('Turnier wählen', 'Die Buchung einem Turnier zuordnen, um das Nenngeld zu übernehmen.');

$GLOBALS['TL_LANG']['tl_fernschach_konto']['comment_legend'] = 'Kommentar';
$GLOBALS['TL_LANG']['tl_fernschach_konto']['comment'] = array('Kommentar', 'Interner Kommentar');

$GLOBALS['TL_LANG']['tl_fernschach_konto']['publish_legend'] = 'Aktivieren';
$GLOBALS['TL_LANG']['tl_fernschach_konto']['published'] = array('Aktiv', 'Aktivieren oder deaktivieren Sie hier die Buchung');

/**
 * Buttons für Operationen
 */

$GLOBALS['TL_LANG']['tl_fernschach_konto']['new'] = array('Neue Buchung', 'Neue Buchung anlegen');
$GLOBALS['TL_LANG']['tl_fernschach_konto']['edit'] = array('Buchung bearbeiten', 'Buchung %s bearbeiten');
$GLOBALS['TL_LANG']['tl_fernschach_konto']['copy'] = array('Buchung kopieren', 'Buchung %s kopieren');
$GLOBALS['TL_LANG']['tl_fernschach_konto']['delete'] = array('Buchung löschen', 'Buchung %s löschen');
$GLOBALS['TL_LANG']['tl_fernschach_konto']['toggle'] = array('Buchung aktivieren/deaktivieren', 'Buchung %s aktivieren/deaktivieren');
$GLOBALS['TL_LANG']['tl_fernschach_konto']['show'] = array('Buchungsdetails anzeigen', 'Details der Buchung %s anzeigen');

/**
 * Buttons für globale Operationen
 */
$GLOBALS['TL_LANG']['tl_fernschach_konto']['importBuchungen'] = array('Import Buchungen', 'Globaler Import von neuen Buchungen bzw. um alte Buchungen zu ergänzen.');

$GLOBALS['TL_LANG']['tl_fernschach_konto']['art_options'] = array
(
	//''  => '-',
	'b' => 'Beitrag',
	'g' => 'Guthaben',
	'n' => 'BdF-Turnier',
	'i' => 'ICCF-Turnier',
);
