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
			'tl_fernschach_konto',
			'tl_fernschach_turnierbewerbungen',
		),
		'importSpieler'     => array('Schachbulle\ContaoFernschachBundle\Classes\ImportSpieler', 'run'),
		'importBuchungen'   => array('Schachbulle\ContaoFernschachBundle\Classes\ImportBuchungen', 'run'),
	),
	'fernschach-turniere'   => array
	(
		'tables'            => array
		(
			'tl_fernschach_turniere',
		),
		'importTurniere'    => array('Schachbulle\ContaoFernschachBundle\Classes\ImportTurniere', 'run'),
	),
	'fernschach-meldungen'  => array
	(
		'tables'            => array
		(
			'tl_fernschach_meldungen',
		),
	),
);

if(TL_MODE == 'BE')
{
	$GLOBALS['TL_CSS'][] = 'bundles/contaofernschach/css/backend.css';
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

///**
// * Inhaltselemente
// */
//
//$GLOBALS['TL_CTE']['correspondence_chess']['mitgliederverwaltung_zusagen'] = 'Schachbulle\ContaoMitgliederverwaltungBundle\ContentElements\Zusagen';
