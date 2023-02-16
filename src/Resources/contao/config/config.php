<?php

/**
 * Backend-Bereich fernschach (an erster Stelle) anlegen
 */
if(!$GLOBALS['BE_MOD']['fernschach']) 
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
	'fernschach-spieler'    => array
	(
		'tables'            => array
		(
			'tl_fernschach_spieler',
			'tl_fernschach_spieler_konto',
			'tl_fernschach_turnierbewerbungen',
		),
		'exportXLS'         => array('Schachbulle\ContaoFernschachBundle\Classes\Export', 'exportXLS'),
		'importSpieler'     => array('Schachbulle\ContaoFernschachBundle\Classes\ImportSpieler', 'run'),
		'importBuchungen'   => array('Schachbulle\ContaoFernschachBundle\Classes\ImportBuchungen', 'run'),
	),
	'fernschach-turniere'   => array
	(
		'tables'            => array
		(
			'tl_fernschach_turniere',
			'tl_fernschach_turniere_meldungen',
			'tl_fernschach_turniere_spieler',
			'tl_fernschach_turniere_bewerbungen',
		),
		'importTurniere'    => array('Schachbulle\ContaoFernschachBundle\Classes\ImportTurniere', 'run'),
	),
	'fernschach-mitgliederstatistik'   => array
	(
		'tables'            => array
		(
			'tl_fernschach_mitgliederstatistik',
		),
		'statistik'         => array('Schachbulle\ContaoFernschachBundle\Classes\Statistik', 'run'),
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
