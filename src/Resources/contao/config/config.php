<?php

/**
 * Backend-Bereich fernschach (an erster Stelle) anlegen
 */
if(!isset($GLOBALS['BE_MOD']['fernschach'])) 
{
	$fernschach = array(
		'fernschach' => array()
	);
	array_insert($GLOBALS['BE_MOD'], 0, $fernschach);
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
	'fernschach-dokumentation'   => array
	(
		'callback'            => Schachbulle\ContaoFernschachBundle\Modules\Dokumentation::class,
	),
);

if(TL_MODE == 'BE')
{
	$GLOBALS['TL_CSS'][] = 'bundles/contaofernschach/css/backend.css';
	//$GLOBALS['TL_JAVASCRIPT'][] = 'assets/jquery/js/jquery.min.js'; 
	$GLOBALS['TL_JAVASCRIPT'][] = 'bundles/contaofernschach/js/backend.js'; 
}

/**
 * Frontend-Module
 */
$GLOBALS['FE_MOD']['fernschachverwaltung'] = array
(
	'fernschachverwaltung_meldeformular'     => 'Schachbulle\ContaoFernschachBundle\Modules\Meldeformular',
	'fernschachverwaltung_titelnormen'       => 'Schachbulle\ContaoFernschachBundle\Modules\TitelNormen',
	'fernschachverwaltung_titelnormen_liste' => 'Schachbulle\ContaoFernschachBundle\Modules\TitelNormenLast',
	'fernschachverwaltung_kontoauszug'       => 'Schachbulle\ContaoFernschachBundle\Modules\Kontoauszug',
);

/**
 * Notification-Center
 */
$GLOBALS['NOTIFICATION_CENTER']['NOTIFICATION_TYPE']['fernschach'] = array
(
	'meldeformular'     => array
	(
		'recipients'    => array('admin_email', 'form_*', 'member_*'),
		'email_subject' => array('form_*', 'member_*'),
		'email_text'    => array('form_*', 'member_*'),
		'email_html'    => array('form_*', 'member_*'),
	),
);

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

/**
 * -------------------------------------------------------------------------
 * Models registrieren                                  
 * -------------------------------------------------------------------------
 */

$GLOBALS['TL_MODELS']['tl_fernschach_spieler_konto'] = \Schachbulle\ContaoFernschachBundle\Models\HauptkontoModel::class;
