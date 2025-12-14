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
$GLOBALS['TL_DCA']['tl_settings']['palettes']['default'] .= ';{fernschach_legend:hide},fernschach_beitrittsformular,fernschach_resetActive,fernschach_memberDefault,fernschach_memberFernschach,fernschach_newsletter,fernschach_emailVon,fernschach_emailAdresse,fernschach_hinweis_kontoauszug';
$GLOBALS['TL_DCA']['tl_settings']['subpalettes']['fernschach_resetActive'] = 'fernschach_resetRecords';

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

$GLOBALS['TL_DCA']['tl_settings']['fields']['fernschach_beitrittsformular'] = array
(
	'label'                   => &$GLOBALS['TL_LANG']['tl_settings']['fernschach_beitrittsformular'],
	'exclude'                 => true,
	'inputType'               => 'select',
	'foreignKey'              => 'tl_form.title',
	'eval'                    => array
	(
		'includeBlankOption'  => true,
		'tl_class'            => 'w50'
	),
);

$GLOBALS['TL_DCA']['tl_settings']['fields']['fernschach_resetRecords'] = array
(
	'label'                               => &$GLOBALS['TL_LANG']['tl_settings']['fernschach_resetRecords'],
	'exclude'                             => true,
	'inputType'                           => 'multiColumnWizard',
	'eval'                                => array
	(
		'tl_class'                        => 'long clr',
		'buttonPos'                       => 'middle',
		'buttons'                         => array
		(
			'copy'                        => false,
			'delete'                      => 'system/themes/flexible/icons/delete.svg',
			'move'                        => false,
			'up'                          => false,
			'down'                        => false
		),
		'columnFields'                    => array
		(
			'nummer' => array
			(
				'label'                   => &$GLOBALS['TL_LANG']['tl_settings']['fernschach_resetNumber'],
				'exclude'                 => true,
				'inputType'               => 'text',
				'eval'                    => array
				(
					'rgxp'                => 'digit', 
					'tl_class'            => 'w50', 
					'style'               => 'width:90%',
					'maxlength'           => 1,
					'mandatory'           => true
				),
			),
			'datum' => array
			(
				'label'                   => &$GLOBALS['TL_LANG']['tl_settings']['fernschach_resetDate'],
				'exclude'                 => true,
				'inputType'               => 'text',
				'eval'                    => array
				(
					'rgxp'                => 'date', 
					'datepicker'          => true, 
					'tl_class'            => 'w50 wizard', 
					'style'               => 'width:90%',
					'mandatory'           => true
				),
			),
			'saldo' => array
			(
				'label'                   => &$GLOBALS['TL_LANG']['tl_settings']['fernschach_resetSaldo'],
				'exclude'                 => true,
				'inputType'               => 'text',
				'eval'                    => array
				(
					'rgxp'                => 'digit', 
					'mandatory'           => false, 
					'style'               => 'width:90%', 
					'maxlength'           => 6,
					'mandatory'           => true
				),
				'load_callback'           => array
				(
					array('tl_settings_fernschach', 'getBetrag')
				),
				'save_callback' => array
				(
					array('tl_settings_fernschach', 'putBetrag')
				),
			),
			'konten' => array
			(
				'label'                   => &$GLOBALS['TL_LANG']['tl_settings']['fernschach_resetKonten'],
				'exclude'                 => true,
				'inputType'               => 'select',
				'options'                 => array('h', 'b', 'n'),
				'reference'               => &$GLOBALS['TL_LANG']['tl_settings']['fernschach_resetKontenOptions'],
				'eval'                    => array
				(
					'multiple'            => true,
					'tl_class'            => '', 
					'style'               => 'width:100%',
					'mandatory'           => true
				)
			),
		)
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

// Globaler E-Mail-Absendername
$GLOBALS['TL_DCA']['tl_settings']['fields']['fernschach_emailVon'] = array
(
	'label'                   => &$GLOBALS['TL_LANG']['tl_settings']['fernschach_emailVon'],
	'inputType'               => 'text',
	'eval'                    => array
	(
		'mandatory'           => true, 
		'tl_class'            => 'w50 clr', 
	),
);

// Globale E-Mail-Absenderadresse
$GLOBALS['TL_DCA']['tl_settings']['fields']['fernschach_emailAdresse'] = array
(
	'label'                   => &$GLOBALS['TL_LANG']['tl_settings']['fernschach_emailAdresse'],
	'inputType'               => 'text',
	'eval'                    => array
	(
		'rgxp'                => 'email', 
		'mandatory'           => true, 
		'tl_class'            => 'w50', 
	),
);

// Hinweistext im Kontoauszug, wenn Benutzer kein BdF-Mitglied ist
$GLOBALS['TL_DCA']['tl_settings']['fields']['fernschach_hinweis_kontoauszug'] = array
(
	'label'                   => &$GLOBALS['TL_LANG']['tl_settings']['fernschach_hinweis_kontoauszug'],
	'inputType'               => 'textarea',
	'eval'                    => array
	(
		'tl_class'            => 'long clr', 
		'rte'                 => 'ace|html', 
		'cols'                => 80,
		'rows'                => 10
	)
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
