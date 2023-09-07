<?php

/**
 * Contao Open Source CMS
 *
 * Copyright (C) 2005-2022 Leo Feyer
 *
 * @package   Fernschach-Verwaltung
 * @author    Frank Hoppe
 * @license   GNU/LGPL
 * @copyright Frank Hoppe 2022
 */

/**
 * Paletten
 */
$GLOBALS['TL_DCA']['tl_settings']['palettes']['__selector__'][] = 'fernschach_resetActive';
$GLOBALS['TL_DCA']['tl_settings']['palettes']['default'] .= ';{fernschach_legend:hide},fernschach_resetActive,fernschach_resetUpdate,fernschach_membershipUpdate,fernschach_maintenanceUpdate,fernschach_memberDefault,fernschach_memberFernschach,fernschach_newsletter';
$GLOBALS['TL_DCA']['tl_settings']['subpalettes']['fernschach_resetActive'] = 'fernschach_resetDate,fernschach_resetSaldo';

/**
 * Felder
 */

// Globale Reset-Buchung aktivieren/deaktivieren
$GLOBALS['TL_DCA']['tl_settings']['fields']['fernschach_resetActive'] = array
(
	'label'                   => &$GLOBALS['TL_LANG']['tl_settings']['fernschach_resetActive'],
	'inputType'               => 'checkbox',
	'eval'                    => array
	(
		'tl_class'            => 'w50',
		'submitOnChange'      => true
	)
);

$GLOBALS['TL_DCA']['tl_settings']['fields']['fernschach_resetDate'] = array
(
	'label'                   => &$GLOBALS['TL_LANG']['tl_settings']['fernschach_resetDate'],
	'inputType'               => 'text',
	'eval'                    => array
	(
		'rgxp'                => 'datim', 
		'datepicker'          => true, 
		'tl_class'            => 'w50 wizard'
	),
);

$GLOBALS['TL_DCA']['tl_settings']['fields']['fernschach_resetSaldo'] = array
(
	'label'                   => &$GLOBALS['TL_LANG']['tl_settings']['fernschach_resetSaldo'],
	'inputType'               => 'text',
	'eval'                    => array
	(
		'rgxp'                => 'digit', 
		'mandatory'           => false, 
		'tl_class'            => 'w50', 
		'maxlength'           => 6
	),
	'load_callback'           => array
	(
		array('tl_settings_fernschach', 'getBetrag')
	),
	'save_callback' => array
	(
		array('tl_settings_fernschach', 'putBetrag')
	),
);

// Speichert den Zeitpunkt der letzten Aktualisierung von tl_fernschach_spieler_konto
$GLOBALS['TL_DCA']['tl_settings']['fields']['fernschach_resetUpdate'] = array
(
	'label'                   => &$GLOBALS['TL_LANG']['tl_settings']['fernschach_resetUpdate'],
	'inputType'               => 'text',
	'eval'                    => array
	(
		'rgxp'                => 'datim', 
		'datepicker'          => true, 
		'tl_class'            => 'w50 wizard'
	),
);

// Speichert den Zeitpunkt der letzten Prüfung von tl_fernschach_spieler.status
$GLOBALS['TL_DCA']['tl_settings']['fields']['fernschach_membershipUpdate'] = array
(
	'label'                   => &$GLOBALS['TL_LANG']['tl_settings']['fernschach_membershipUpdate'],
	'inputType'               => 'text',
	'eval'                    => array
	(
		'rgxp'                => 'datim', 
		'datepicker'          => true, 
		'tl_class'            => 'w50 wizard'
	),
);

// Speichert den Zeitpunkt der letzten Prüfung von tl_fernschach_spieler
$GLOBALS['TL_DCA']['tl_settings']['fields']['fernschach_maintenanceUpdate'] = array
(
	'label'                   => &$GLOBALS['TL_LANG']['tl_settings']['fernschach_maintenanceUpdate'],
	'inputType'               => 'text',
	'eval'                    => array
	(
		'rgxp'                => 'datim', 
		'datepicker'          => true, 
		'tl_class'            => 'w50 wizard'
	),
);

$GLOBALS['TL_DCA']['tl_settings']['fields']['fernschach_memberDefault'] = array
(
	'label'                   => &$GLOBALS['TL_LANG']['tl_settings']['fernschach_memberDefault'],
	'exclude'                 => true,
	'inputType'               => 'select',
	'foreignKey'              => 'tl_member_group.name',
	'eval'                    => array
	(
		'includeBlankOption'  => true,
		'tl_class'            => 'w50 clr'
	),
);

$GLOBALS['TL_DCA']['tl_settings']['fields']['fernschach_memberFernschach'] = array
(
	'label'                   => &$GLOBALS['TL_LANG']['tl_settings']['fernschach_memberFernschach'],
	'exclude'                 => true,
	'inputType'               => 'select',
	'foreignKey'              => 'tl_member_group.name',
	'eval'                    => array
	(
		'includeBlankOption'  => true,
		'tl_class'            => 'w50'
	),
);

// Auswahl eines Newsletter-Archivs für die Serienmailfunktion
$GLOBALS['TL_DCA']['tl_settings']['fields']['fernschach_newsletter'] = array
(
	'label'                   => &$GLOBALS['TL_LANG']['tl_settings']['fernschach_newsletter'],
	'exclude'                 => true,
	'inputType'               => 'select',
	'foreignKey'              => 'tl_newsletter_channel.title',
	'eval'                    => array
	(
		'includeBlankOption'  => true,
		'tl_class'            => 'w50'
	),
);

class tl_settings_fernschach extends Backend
{

	/**
	 * Betrag für Datenbank umwandeln
	 * @param $varValue       string      z.B. 9,12
	 * @return                float       z.B. 9.12
	 */
	public function putBetrag($varValue)
	{
		$temp = str_replace(',', '.', $varValue); // Komma in Punkt umwandeln
		return $temp;
	}

	/**
	 * Betrag aus der Datenbank umwandeln
	 * @param $varValue       string      z.B. 9,12
	 * @return                float       z.B. 9.12
	 */
	public function getBetrag($varValue)
	{
		$temp = sprintf("%01.2f", $varValue); // In Wert mit zwei Nachkommastellen umwandeln
		$temp = str_replace('.', ',', $temp); // Punkt in Komma umwandeln
		return $temp;
	}
}
