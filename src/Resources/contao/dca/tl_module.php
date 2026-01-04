<?php
/**
 * Avatar for Contao Open Source CMS
 *
 * Copyright (C) 2013 Kirsten Roschanski
 * Copyright (C) 2013 Tristan Lins <http://bit3.de>
 *
 * @package    DeWIS
 * @license    http://opensource.org/licenses/lgpl-3.0.html LGPL
 */

/**
 * Add palette to tl_module
 */

$GLOBALS['TL_DCA']['tl_module']['palettes']['fernschachverwaltung_meldeformular_player'] = '{title_legend},name,headline,type;{options_legend},fernschachverwaltung_linkingMembers,fernschachverwaltung_tournamentRoot,fernschachverwaltung_tournamentText,fernschachverwaltung_bewerbung,fernschachverwaltung_radio,nc_notification;{template_legend:hide},customTpl;{protected_legend:hide},protected;{expert_legend:hide},guests,cssID';
$GLOBALS['TL_DCA']['tl_module']['palettes']['fernschachverwaltung_meldeformular_team'] = '{title_legend},name,headline,type;{options_legend},fernschachverwaltung_linkingMembers;{template_legend:hide},customTpl;{protected_legend:hide},protected;{expert_legend:hide},guests,cssID';
$GLOBALS['TL_DCA']['tl_module']['palettes']['fernschachverwaltung_titelnormen'] = '{title_legend},name,headline,type;{options_legend},fernschachverwaltung_zeitraum;{template_legend:hide},customTpl;{protected_legend:hide},protected;{expert_legend:hide},guests,cssID';
$GLOBALS['TL_DCA']['tl_module']['palettes']['fernschachverwaltung_titelnormen_liste'] = '{title_legend},name,headline,type;{options_legend},fernschachverwaltung_zeitraum,fernschachverwaltung_anzahl;{template_legend:hide},customTpl;{protected_legend:hide},protected;{expert_legend:hide},guests,cssID';
$GLOBALS['TL_DCA']['tl_module']['palettes']['fernschachverwaltung_kontoauszug'] = '{title_legend},name,headline,type;{fernschachverwaltungKontoauszug_legend},fernschachverwaltung_minBuchungen,fernschachverwaltung_maxBuchungen,fernschachverwaltung_maxDatum,fernschachverwaltung_kontostand,fernschachverwaltung_isReset,fernschachverwaltung_konten,fernschachverwaltung_hauptkonto;{template_legend:hide},customTpl;{protected_legend:hide},protected;{expert_legend:hide},guests,cssID';

// Formular mit Mitgliederdaten als Voreinstellung
$GLOBALS['TL_DCA']['tl_module']['fields']['fernschachverwaltung_linkingMembers'] = array
(
	'label'                   => &$GLOBALS['TL_LANG']['tl_module']['fernschachverwaltung_linkingMembers'],
	'exclude'                 => true,
	'flag'                    => 1,
	'inputType'               => 'checkbox',
	'eval'                    => array
	(
		'tl_class'            =>'w50 m12',
	),
	'sql'                     => "char(1) NOT NULL default ''"
);

$GLOBALS['TL_DCA']['tl_module']['fields']['fernschachverwaltung_tournamentRoot'] = array
(
	'label'                   => &$GLOBALS['TL_LANG']['tl_module']['fernschachverwaltung_tournamentRoot'],
	'exclude'                 => true,
	'inputType'               => 'select',
	'options_callback'        => array('tl_module_fernschach', 'getTurniere'),
	'eval'                    => array
	(
		'tl_class'            => 'long clr',
		'includeBlankOption'  => true,
		'chosen'              => true,
	),
	'sql'                     => "int(10) unsigned NOT NULL default '0'",
);

$GLOBALS['TL_DCA']['tl_module']['fields']['fernschachverwaltung_tournamentText'] = array
(
	'label'                   => &$GLOBALS['TL_LANG']['tl_module']['fernschachverwaltung_tournamentText'],
	'exclude'                 => true,
	'default'                 => 'Hiermit melde ich mich zu folgendem Fernschachturnier an:',
	'inputType'               => 'textarea',
	'eval'                    => array
	(
		'tl_class'            => 'long clr',
		'mandatory'           => true, 
	),
	'sql'                     => "mediumtext NULL default 'Hiermit melde ich mich zu folgendem Fernschachturnier an:'"
);

$GLOBALS['TL_DCA']['tl_module']['fields']['fernschachverwaltung_bewerbung'] = array
(
	'label'                   => &$GLOBALS['TL_LANG']['tl_module']['fernschachverwaltung_bewerbung'],
	'exclude'                 => true,
	'flag'                    => 1,
	'inputType'               => 'checkbox',
	'eval'                    => array
	(
		'tl_class'            =>'w50',
	),
	'sql'                     => "char(1) NOT NULL default ''"
);

$GLOBALS['TL_DCA']['tl_module']['fields']['fernschachverwaltung_radio'] = array
(
	'label'                   => &$GLOBALS['TL_LANG']['tl_module']['fernschachverwaltung_radio'],
	'exclude'                 => true,
	'flag'                    => 1,
	'inputType'               => 'checkbox',
	'eval'                    => array
	(
		'tl_class'            =>'w50',
	),
	'sql'                     => "char(1) NOT NULL default ''"
);

$GLOBALS['TL_DCA']['tl_module']['fields']['fernschachverwaltung_zeitraum'] = array
(
	'label'                   => &$GLOBALS['TL_LANG']['tl_module']['fernschachverwaltung_zeitraum'],
	'exclude'                 => true,
	'default'                 => '-2 months',
	'inputType'               => 'select',
	'options'                 => array('-1 month' ,'-2 months', '-3 months', '-4 months', '-5 months', '-6 months', '-1 year', '-100 years'), 
	'reference'               => &$GLOBALS['TL_LANG']['tl_module']['fernschachverwaltung_zeitraum_options'],
	'eval'                    => array
	(
		'mandatory'           => false, 
		'tl_class'            => 'w50'
	),
	'sql'                     => "varchar(10) NOT NULL default ''"
);

$GLOBALS['TL_DCA']['tl_module']['fields']['fernschachverwaltung_anzahl'] = array
(
	'label'                   => &$GLOBALS['TL_LANG']['tl_module']['fernschachverwaltung_anzahl'],
	'exclude'                 => true,
	'default'                 => '3',
	'inputType'               => 'text',
	'eval'                    => array
	(
		'maxlength'           => 4,
		'mandatory'           => false,
		'tl_class'            => 'w50'
	),
	'sql'                     => "int(4) unsigned NOT NULL default '3'"
);

$GLOBALS['TL_DCA']['tl_module']['fields']['fernschachverwaltung_minBuchungen'] = array
(
	'label'                   => &$GLOBALS['TL_LANG']['tl_module']['fernschachverwaltung_minBuchungen'],
	'exclude'                 => true,
	'default'                 => '1',
	'inputType'               => 'text',
	'eval'                    => array
	(
		'maxlength'           => 3,
		'mandatory'           => false,
		'tl_class'            => 'w50'
	),
	'sql'                     => "int(3) unsigned NOT NULL default '1'"
);

$GLOBALS['TL_DCA']['tl_module']['fields']['fernschachverwaltung_maxBuchungen'] = array
(
	'label'                   => &$GLOBALS['TL_LANG']['tl_module']['fernschachverwaltung_maxBuchungen'],
	'exclude'                 => true,
	'default'                 => '0',
	'inputType'               => 'text',
	'eval'                    => array
	(
		'maxlength'           => 3,
		'mandatory'           => false,
		'tl_class'            => 'w50'
	),
	'sql'                     => "int(3) unsigned NOT NULL default '0'"
);

$GLOBALS['TL_DCA']['tl_module']['fields']['fernschachverwaltung_maxDatum'] = array
(
	'label'                   => &$GLOBALS['TL_LANG']['tl_module']['fernschachverwaltung_maxDatum'],
	'default'                 => time(),
	'exclude'                 => true,
	'filter'                  => true,
	'sorting'                 => true,
	'flag'                    => 8,
	'inputType'               => 'text',
	'load_callback'           => array
	(
		array('Schachbulle\ContaoFernschachBundle\Classes\Helper', 'loadDate')
	),
	'eval'                    => array
	(
		'rgxp'                => 'date',
		'datepicker'          => true, 
		'tl_class'            =>'w50 wizard'
	),
	'sql'                     => "int(10) unsigned NOT NULL default '0'"
);

$GLOBALS['TL_DCA']['tl_module']['fields']['fernschachverwaltung_kontostand'] = array
(
	'label'                   => &$GLOBALS['TL_LANG']['tl_module']['fernschachverwaltung_kontostand'],
	'exclude'                 => true,
	'flag'                    => 1,
	'inputType'               => 'checkbox',
	'eval'                    => array
	(
		'tl_class'            =>'w50 clr m12',
		'submitOnChange'      => false,
	),
	'sql'                     => "char(1) NOT NULL default ''"
);

$GLOBALS['TL_DCA']['tl_module']['fields']['fernschachverwaltung_isReset'] = array
(
	'label'                   => &$GLOBALS['TL_LANG']['tl_module']['fernschachverwaltung_isReset'],
	'exclude'                 => true,
	'flag'                    => 1,
	'inputType'               => 'checkbox',
	'eval'                    => array
	(
		'tl_class'            =>'w50 m12',
		'boolean'             => true
	),
	'sql'                     => "char(1) NOT NULL default ''"
);

$GLOBALS['TL_DCA']['tl_module']['fields']['fernschachverwaltung_konten'] = array
(
	'label'                   => &$GLOBALS['TL_LANG']['tl_module']['fernschachverwaltung_konten'],
	'exclude'                 => true,
	'inputType'               => 'checkboxWizard',
	'options'                 => array('h', 'b', 'n'),
	'reference'               => &$GLOBALS['TL_LANG']['tl_module']['fernschachverwaltung_konten_options'],
	'eval'                    => array
	(
		'multiple'            => true, 
		'tl_class'            =>'w50',
		'mandatory'           => false
	),
	'sql'                     => "blob NULL"
);

$GLOBALS['TL_DCA']['tl_module']['fields']['fernschachverwaltung_hauptkonto'] = array
(
	'label'                   => &$GLOBALS['TL_LANG']['tl_module']['fernschachverwaltung_hauptkonto'],
	'exclude'                 => true,
	'flag'                    => 1,
	'inputType'               => 'checkbox',
	'eval'                    => array
	(
		'tl_class'            =>'w50',
		'boolean'             => true
	),
	'sql'                     => "char(1) NOT NULL default ''"
);

class tl_module_fernschach extends \Backend
{
	/**
	 * options_callback: Ermöglicht das Befüllen eines Drop-Down-Menüs oder einer Checkbox-Liste mittels einer individuellen Funktion.
	 * @param  $dc
	 * @return array
	 */
	public function getTurniere(\DataContainer $dc)
	{
		$objTurniere = \Database::getInstance()->prepare("SELECT * FROM tl_fernschach_turniere WHERE titleView = ? ORDER BY title ASC")
		                                       ->execute(1);

		$arr = array();
		if($objTurniere->numRows)
		{
			while($objTurniere->next())
			{
				$titel = $objTurniere->titleAlternate ? $objTurniere->title.' (alt: '.$objTurniere->titleAlternate.')' : $objTurniere->title;
				$arr[$objTurniere->id] = $titel;
			}
		}
		return $arr;
	}
}
