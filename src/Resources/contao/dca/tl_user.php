<?php

/**
 * Contao Open Source CMS
 *
 * Copyright (c) 2005-2015 Leo Feyer
 *
 * @license LGPL-3.0+
 */


/**
 * Extend default palette
 */
$GLOBALS['TL_DCA']['tl_user']['palettes']['extend'] = str_replace('fop;', 'fop;{fernschach_legend},fernschach_spieler,fernschach_konto;', $GLOBALS['TL_DCA']['tl_user']['palettes']['extend']);
$GLOBALS['TL_DCA']['tl_user']['palettes']['custom'] = str_replace('fop;', 'fop;{fernschach_legend},fernschach_spieler,fernschach_konto;', $GLOBALS['TL_DCA']['tl_user']['palettes']['custom']);

/**
 * Add fields to tl_user
 */
$GLOBALS['TL_DCA']['tl_user']['fields']['fernschach_turnierzugriff'] = array
(
	'label'                   => &$GLOBALS['TL_LANG']['tl_user']['fernschach_turnierzugriff'],
	'inputType'               => 'tournamentTree',
	'eval'                    => array('multiple'=>true, 'fieldType'=>'checkbox'),
	'sql'                     => "blob NULL"
);
$GLOBALS['TL_DCA']['tl_user']['fields']['fernschach_spieler'] = array
(
	'label'                   => &$GLOBALS['TL_LANG']['tl_user']['fernschach_spieler'],
	'exclude'                 => true,
	'inputType'               => 'checkbox',
	'options'                 => &$GLOBALS['TL_LANG']['tl_user']['fernschach_spieler_optionen'],
	'eval'                    => array('multiple'=>true),
	'sql'                     => "blob NULL"
); 

$GLOBALS['TL_DCA']['tl_user']['fields']['fernschach_konto'] = array
(
	'label'                   => &$GLOBALS['TL_LANG']['tl_user']['fernschach_konto'],
	'exclude'                 => true,
	'inputType'               => 'checkbox',
	'options'                 => &$GLOBALS['TL_LANG']['tl_user']['fernschach_konto_optionen'],
	'eval'                    => array('multiple'=>true),
	'sql'                     => "blob NULL"
); 
