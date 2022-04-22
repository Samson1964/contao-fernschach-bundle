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

$GLOBALS['TL_DCA']['tl_module']['palettes']['fernschachverwaltung_meldeformular'] = '{title_legend},name,headline,type;{options_legend},nc_notification;{template_legend:hide},customTpl;{protected_legend:hide},protected;{expert_legend:hide},guests,cssID';
$GLOBALS['TL_DCA']['tl_module']['palettes']['fernschachverwaltung_titelnormen'] = '{title_legend},name,headline,type;{options_legend},fernschachverwaltung_zeitraum;{template_legend:hide},customTpl;{protected_legend:hide},protected;{expert_legend:hide},guests,cssID';
$GLOBALS['TL_DCA']['tl_module']['palettes']['fernschachverwaltung_titelnormen_liste'] = '{title_legend},name,headline,type;{options_legend},fernschachverwaltung_zeitraum,fernschachverwaltung_anzahl;{template_legend:hide},customTpl;{protected_legend:hide},protected;{expert_legend:hide},guests,cssID';

$GLOBALS['TL_DCA']['tl_module']['fields']['fernschachverwaltung_zeitraum'] = array
(
	'label'                   => &$GLOBALS['TL_LANG']['tl_module']['fernschachverwaltung_zeitraum'],
	'exclude'                 => true,
	'default'                 => '-2 months',
	'inputType'               => 'select',
	'options'                 => $GLOBALS['TL_LANG']['tl_module']['fernschachverwaltung_zeitraum_options'],
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
