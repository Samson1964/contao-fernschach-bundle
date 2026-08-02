<?php

/**
 * Grundkonfiguration der Fernschach-Verwaltung
 *
 * Diese Datei wird von Contao beim Aufbau des Caches eingelesen — einmal für das
 * Backend und einmal für das Frontend. Sie darf deshalb nichts enthalten, was
 * eine laufende Anfrage voraussetzt.
 */

use Contao\ArrayUtil;
use Schachbulle\ContaoFernschachBundle\Classes\Scope;

if(!isset($GLOBALS['BE_MOD']['fernschach']))
{
	$fernschach = array(
		'fernschach' => array()
	);
	ArrayUtil::arrayInsert($GLOBALS['BE_MOD'], 0, $fernschach);
}

/**
 * Backend-Module definieren
 */

$GLOBALS['BE_MOD']['fernschach'] = array
(
	'fernschach-spieler'      => array
	(
		'tables'              => array
		(
			'tl_fernschach_spieler',
			'tl_fernschach_spieler_mails',
			'tl_fernschach_spieler_mailtemplates',
			'tl_fernschach_spieler_konto',
			'tl_fernschach_spieler_konto_beitrag',
			'tl_fernschach_spieler_konto_nenngeld',
			'tl_fernschach_spieler_titel',
		),
		'exportXLS'           => array('Schachbulle\ContaoFernschachBundle\Classes\Export', 'exportXLS'),
		'importSpieler'       => array('Schachbulle\ContaoFernschachBundle\Classes\ImportSpieler', 'run'),
		'importBuchungen'     => array('Schachbulle\ContaoFernschachBundle\Classes\ImportBuchungen', 'run'),
		'verschiebeBuchungen' => array('Schachbulle\ContaoFernschachBundle\Classes\VerschiebeBuchungen', 'run'),
		'move'                => array('Schachbulle\ContaoFernschachBundle\Classes\MoveBuchungen', 'run'),
		'setNewsletter'       => array('Schachbulle\ContaoFernschachBundle\Classes\Newsletter', 'setNewsletter'),
		'initAccounts'        => array('Schachbulle\ContaoFernschachBundle\Classes\Accounts\Init', 'run'),
		'moveBuchung'         => array('Schachbulle\ContaoFernschachBundle\Classes\Konto\MoveBuchung', 'run'),
		'send'                => array('Schachbulle\ContaoFernschachBundle\Classes\Mailer', 'send'), 
	),
	'fernschach-turniere'     => array
	(
		'tables'              => array
		(
			'tl_fernschach_turniere',
			'tl_fernschach_turniere_meldungen',
			'tl_fernschach_turniere_spieler',
			'tl_fernschach_turniere_bewerbungen',
		),
		'statistik'           => array('Schachbulle\ContaoFernschachBundle\Classes\Turnierstatistik', 'Statistik'),
	),
	'fernschach-turniere-spieler'     => array
	(
		'tables'              => array
		(
			'tl_fernschach_turniere_meldungen',
		),
		'callback'            => \Schachbulle\ContaoFernschachBundle\Modules\ZeigeTurniere::class,
		'hideInNavigation'    => true,
	),
	'fernschach-turniere-teilnehmer'     => array
	(
		'tables'              => array
		(
			'tl_fernschach_turniere_meldungen',
		),
		'callback'            => \Schachbulle\ContaoFernschachBundle\Modules\ZeigeTeilnehmer::class,
		'hideInNavigation'    => true,
	),
	'fernschach-mitgliederstatistik'   => array
	(
		'tables'              => array
		(
			'tl_fernschach_mitgliederstatistik',
		),
		'statistik'           => array('Schachbulle\ContaoFernschachBundle\Classes\Statistik', 'run'),
	),
	'fernschach-konten'   => array
	(
		'tables'              => array
		(
			'tl_fernschach_konten',
			'tl_fernschach_konten_buchungen',
		),
	),
	'fernschach-iccf'     => array
	(
		'tables'              => array
		(
			'tl_fernschach_iccf_ratinglists',
			'tl_fernschach_iccf_players',
			'tl_fernschach_iccf_ratings',
		),
		'importCSV'              => array('\Schachbulle\ContaoFernschachBundle\Classes\ImportRating', 'importCSV'), 
		'importProgress'         => array('\Schachbulle\ContaoFernschachBundle\Classes\ImportProgress', 'importProgress'), 
	),
	'fernschach-dokumentation'   => array
	(
		'callback'            => Schachbulle\ContaoFernschachBundle\Modules\Dokumentation::class,
	),
);

/**
 * Eigene Stylesheets und Skripte einbinden
 *
 * Die frühere Fallunterscheidung über die Konstante TL_MODE ist entfallen, weil
 * es sie in Contao 5 nicht mehr gibt. Die Auskunft, ob gerade das Backend läuft,
 * kommt jetzt vom Routing-Dienst; im Cronjob und auf der Kommandozeile liefert
 * er "kein Backend", was richtig ist, weil dort ohnehin nichts ausgegeben wird.
 */
if(Scope::isBackendRequest())
{
	$GLOBALS['TL_CSS'][] = 'bundles/contaofernschach/css/backend.css';
	$GLOBALS['TL_JAVASCRIPT'][] = 'bundles/contaofernschach/js/backend.js';
}
else
{
	$GLOBALS['TL_CSS'][] = 'bundles/contaofernschach/css/frontend.css';
}

/**
 * Frontend-Module
 */
$GLOBALS['FE_MOD']['fernschachverwaltung'] = array
(
	'fernschachverwaltung_meldeformular'        => 'Schachbulle\ContaoFernschachBundle\Modules\Meldeformular_Spieler',
	'fernschachverwaltung_meldeformular_team'   => 'Schachbulle\ContaoFernschachBundle\Modules\Meldeformular_Mannschaft',
	'fernschachverwaltung_titel'                => 'Schachbulle\ContaoFernschachBundle\Modules\Titel',
	'fernschachverwaltung_titelnormen'          => 'Schachbulle\ContaoFernschachBundle\Modules\TitelNormen',
	'fernschachverwaltung_titelnormen_liste'    => 'Schachbulle\ContaoFernschachBundle\Modules\TitelNormenLast',
	'fernschachverwaltung_kontoauszug'          => 'Schachbulle\ContaoFernschachBundle\Modules\Kontoauszug',
);

/*
 * Hinweis zur Version 2.0.0: Hier war ein Benachrichtigungstyp
 * "fernschach/meldeformular" für das Notification Center angemeldet und im
 * Meldeformular-Modul stand das zugehörige Auswahlfeld nc_notification. Beides
 * ist entfallen: Die Methode, die die Benachrichtigung hätte verschicken
 * sollen, wurde nie aufgerufen — das Meldeformular verschickt seine E-Mails
 * selbst. Das Bundle setzt terminal42/notification_center folgerichtig auch
 * nicht voraus.
 */

/**
 * Inhaltselemente
 */

$GLOBALS['TL_CTE']['fernschachverwaltung']['fernschachverwaltung_zusagen'] = 'Schachbulle\ContaoFernschachBundle\ContentElements\Zusagen';

/**
 * Add permissions
 */
$GLOBALS['TL_PERMISSIONS'][] = 'fernschach_spieler'; // Spieler-Rechte
$GLOBALS['TL_PERMISSIONS'][] = 'fernschach_konto'; // Buchungen-Rechte

/**
 * -------------------------------------------------------------------------
 * Eigener inputType
 * -------------------------------------------------------------------------
 */
$GLOBALS['BE_FFL']['tournamentTree'] = 'Schachbulle\ContaoFernschachBundle\Widgets\TournamentTree';

/**
 * Hooks
 */
// Newsletter modifizieren, falls Serienmail Fernschach-Verwaltung
$GLOBALS['TL_HOOKS']['parseTemplate'][] = array('Schachbulle\ContaoFernschachBundle\Hooks\Newsletter', 'NewsletterTags');
$GLOBALS['TL_HOOKS']['parseBackendTemplate'][] = array('Schachbulle\ContaoFernschachBundle\Hooks\Template', 'BackendTemplate');
// Formularversendung prüfen: Beitrittserklärung verarbeiten
$GLOBALS['TL_HOOKS']['processFormData'][] = array('Schachbulle\ContaoFernschachBundle\EventListener\Beitrittsformularpuefung', '__invoke');

/**
 * -------------------------------------------------------------------------
 * Voreinstellungen
 * -------------------------------------------------------------------------
 */

$GLOBALS['TL_CONFIG']['fernschach_resetActive'] = false;
$GLOBALS['TL_CONFIG']['fernschach_hinweis_kontoauszug'] = 'Kein BdF-Mitglied';
$GLOBALS['TL_CONFIG']['fernschach_resetUpdate_time'] = 86400;
$GLOBALS['TL_CONFIG']['fernschach_membershipUpdate_time'] = 86400;
$GLOBALS['TL_CONFIG']['fernschach_maintenanceUpdate_time'] = 43200;
$GLOBALS['TL_CONFIG']['fernschach_intervall_memberbridgeCheck'] = 86400;
$GLOBALS['TL_CONFIG']['fernschach_intervall_membershipsCheck'] = 86400;

/**
 * -------------------------------------------------------------------------
 * Models registrieren
 * -------------------------------------------------------------------------
 */

$GLOBALS['TL_MODELS']['tl_fernschach_spieler'] = \Schachbulle\ContaoFernschachBundle\Models\Spieler::class;
$GLOBALS['TL_MODELS']['tl_fernschach_spieler_konto'] = \Schachbulle\ContaoFernschachBundle\Models\Hauptkonto::class;
$GLOBALS['TL_MODELS']['tl_fernschach_spieler_konto_beitrag'] = \Schachbulle\ContaoFernschachBundle\Models\Beitragskonto::class;
$GLOBALS['TL_MODELS']['tl_fernschach_spieler_konto_nenngeld'] = \Schachbulle\ContaoFernschachBundle\Models\Nenngeldkonto::class;
