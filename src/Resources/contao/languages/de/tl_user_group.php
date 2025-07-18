<?php

/**
 * Contao Open Source CMS
 *
 * Copyright (c) 2005-2023 Leo Feyer
 *
 * @package   fernschach
 * @author    Frank Hoppe
 * @license   GNU/LPGL
 * @copyright Frank Hoppe 2023
 */

/**
 * Backend-Modul Übersetzungen
 */

$GLOBALS['TL_LANG']['tl_user_group']['fernschach_legend'] = 'Fernschach-Verwaltung';
$GLOBALS['TL_LANG']['tl_user_group']['fernschach_turniere_meldungen'] = array('Turniermeldungen-Rechte', 'Hier können Sie die Turniermeldungen-Rechte festlegen.');
$GLOBALS['TL_LANG']['tl_user_group']['fernschach_spieler'] = array('Spielerrechte', 'Hier können Sie den Zugriff auf die Spieler festlegen.');
$GLOBALS['TL_LANG']['tl_user_group']['fernschach_konto'] = array('Kontorechte', 'Hier können Sie den Zugriff auf das Konto der Spieler festlegen.');

$GLOBALS['TL_LANG']['tl_user_group']['fernschach_turniere_meldungen_optionen'] = array
(
	'create'     => 'Anlegen',
	'delete'     => 'Löschen',
);

$GLOBALS['TL_LANG']['tl_user_group']['fernschach_spieler_optionen'] = array
(
	'create'         => 'Global: Spieler neu anlegen',
	'import'         => 'Global: Spieler importieren',
	'export'         => 'Global: Spieler exportieren',
	'all'            => 'Global: Mehrere Spieler bearbeiten',
	'edit'           => 'Datensatz: Spieler bearbeiten',
	'copy'           => 'Datensatz: Spieler kopieren',
	'delete'         => 'Datensatz: Spieler löschen',
	'konto'          => 'Datensatz: Buchungen anzeigen',
	'toggle'         => 'Datensatz: Spieler Veröffentlichen-Status setzen',
	'show'           => 'Datensatz: Spieler Infobox zeigen',
	'fertig'         => 'Datensatz: Spieler Fertig-Status setzen',
	'saldo'          => 'Auflistungen/Exporte: Saldo anzeigen',
	'accountChecked' => 'Auflistungen/Exporte: Konto geprüft anzeigen',
	'viewNegative'   => 'Info: Nenngeldkonten negativ anzeigen',
);

$GLOBALS['TL_LANG']['tl_user_group']['fernschach_konto_optionen'] = array
(
	'import'     => 'Buchungen importieren',
	'create'     => 'Buchung anlegen',
	'edit'       => 'Buchung bearbeiten',
	'delete'     => 'Buchung löschen',
	'copy'       => 'Buchung kopieren',
	'toggle'     => 'Buchung Veröffentlichen-Status setzen',
	'show'       => 'Buchung Infobox zeigen',
	'fertig'     => 'Buchung Fertig-Status setzen',
	'all'        => 'Mehrere Buchungen bearbeiten',
);
