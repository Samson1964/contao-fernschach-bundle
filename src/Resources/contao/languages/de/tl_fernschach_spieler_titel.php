<?php

/**
 * Backend-Modul: Übersetzungen im Eingabeformular
 */
$GLOBALS['TL_LANG']['tl_fernschach_spieler_titel']['titel_legend'] = 'Titel';
$GLOBALS['TL_LANG']['tl_fernschach_spieler_titel']['datum'] = array('Datum', 'Datum der Titelverleihung im Format JJJJ, MM.JJJJ oder TT.MM.JJJJ');
$GLOBALS['TL_LANG']['tl_fernschach_spieler_titel']['titel'] = array('Titel', 'Titel auswählen');

$GLOBALS['TL_LANG']['tl_fernschach_spieler_titel']['extras_legend'] = 'Besondere Einstellungen';
$GLOBALS['TL_LANG']['tl_fernschach_spieler_titel']['markierung'] = array('Markieren', 'Titel markieren');
$GLOBALS['TL_LANG']['tl_fernschach_spieler_titel']['saldoReset'] = array('Saldo-Reset', 'Der Saldo wird ab dieser Titel mit dem obigen Betrag weitergeführt.');

$GLOBALS['TL_LANG']['tl_fernschach_spieler_titel']['turnier_legend'] = 'Zugeordnetes Turnier';
$GLOBALS['TL_LANG']['tl_fernschach_spieler_titel']['turnier'] = array('Turnier wählen', 'Die Titel einem Turnier zuordnen, um das Nenngeld zu übernehmen.');

$GLOBALS['TL_LANG']['tl_fernschach_spieler_titel']['comment_legend'] = 'Kommentar';
$GLOBALS['TL_LANG']['tl_fernschach_spieler_titel']['comment'] = array('Kommentar', 'Interner Kommentar');

$GLOBALS['TL_LANG']['tl_fernschach_spieler_titel']['connection_legend'] = 'Verknüpfungen';
$GLOBALS['TL_LANG']['tl_fernschach_spieler_titel']['meldungId'] = array('Titel &harr; ID Meldung', 'Wenn Sie diesen Wert ändern, geht die Verbindung zur ID der zur Titel gehörenden Meldung verloren. Der Wert 0 hebt die Verbindung auf, jeder andere Wert verbindet die Titel mit einem anderen, möglicherweise nichtexistenten Meldungs-Datensatz!');

$GLOBALS['TL_LANG']['tl_fernschach_spieler_titel']['publish_legend'] = 'Aktivieren';
$GLOBALS['TL_LANG']['tl_fernschach_spieler_titel']['published'] = array('Aktiv', 'Aktivieren oder deaktivieren Sie hier die Titel');

/**
 * Buttons für Operationen
 */

$GLOBALS['TL_LANG']['tl_fernschach_spieler_titel']['new'] = array('Neuer Titel', 'Neuen Titel anlegen');
$GLOBALS['TL_LANG']['tl_fernschach_spieler_titel']['edit'] = array('Titel bearbeiten', 'Titel %s bearbeiten');
$GLOBALS['TL_LANG']['tl_fernschach_spieler_titel']['copy'] = array('Titel kopieren', 'Titel %s kopieren');
$GLOBALS['TL_LANG']['tl_fernschach_spieler_titel']['delete'] = array('Titel löschen', 'Titel %s löschen');
$GLOBALS['TL_LANG']['tl_fernschach_spieler_titel']['toggle'] = array('Titel aktivieren/deaktivieren', 'Titel %s aktivieren/deaktivieren');
$GLOBALS['TL_LANG']['tl_fernschach_spieler_titel']['show'] = array('Titeldetails anzeigen', 'Details des Titels %s anzeigen');

/**
 * Buttons für globale Operationen
 */
$GLOBALS['TL_LANG']['tl_fernschach_spieler_titel']['importTitelen'] = array('Import Titelen', 'Globaler Import von neuen Titelen bzw. um alte Titelen zu ergänzen.');

$GLOBALS['TL_LANG']['tl_fernschach_spieler_titel']['titel_options'] = array
(
	'FSGM' => 'Großmeister (Fernschach)',
	'SIM'  => 'Internationaler Seniorenmeister (SIM)',
	'FSIM' => 'Internationaler Meister (Fernschach)',
	'CCM'  => 'Fernschachmeister (CCM)',
	'CCE'  => 'Fernschachexperte (CCE)',
	'LGM'  => 'Großmeisterin (Fernschach)',
	'LIM'  => 'Internationale Meisterin (Fernschach)',
	'GM'   => 'Großmeister',
	'IM'   => 'Internationaler Meister',
	'FM'   => 'FIDE-Meister',
	'CM'   => 'Kandidatenmeister (CM)',
	'WGM'  => 'Großmeisterin',
	'WIM'  => 'Internationale Meisterin',
	'WFM'  => 'FIDE-Meisterin',
	'WCM'  => 'Kandidatenmeisterin (WCM)',
);
