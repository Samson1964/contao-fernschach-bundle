<?php

use Contao\CoreBundle\DataContainer\PaletteManipulator;

$GLOBALS['TL_DCA']['tl_member']['fields']['fernschach_memberId'] = array
(
	'label'                   => &$GLOBALS['TL_LANG']['tl_member']['fernschach_memberId'],
	'exclude'                 => true,
	'inputType'               => 'select',
	'foreignKey'              => 'tl_fernschach_spieler.CONCAT(nachname,", ",vorname," (",memberId,")")',
	'relation'                => array('type'=>'belongsToMany', 'load'=>'lazy'),
	'eval'                    => array
	(
		'includeBlankOption'  => true,
		'chosen'              => true,
		'mandatory'           => false, 
		'tl_class'            => 'w50'
	),
	'sql'                     => "int(10) unsigned NOT NULL default '0'"
);

// Speichert den Timestamp der letzten Prüfung der Zuordnung BdF-Mitglied
$GLOBALS['TL_DCA']['tl_member']['fields']['fernschach_memberbridgeTime'] = array
(
	'label'                   => &$GLOBALS['TL_LANG']['tl_member']['fernschach_memberbridgeTime'],
	'sql'                     => "int(10) unsigned NOT NULL default '0'"
);

PaletteManipulator::create()
    ->addLegend('fernschach_legend', 'personal_legend', PaletteManipulator::POSITION_AFTER)
    ->addField('fernschach_memberId', 'fernschach_legend', PaletteManipulator::POSITION_APPEND)
    ->applyToPalette('default', 'tl_member');
