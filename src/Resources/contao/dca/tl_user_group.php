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
$GLOBALS['TL_DCA']['tl_user_group']['palettes']['default'] = str_replace('fop;', 'fop;{fernschach_legend},fernschach_spieler,fernschach_konto;', $GLOBALS['TL_DCA']['tl_user_group']['palettes']['default']);

/**
 * Add fields to tl_user_group
 */
$GLOBALS['TL_DCA']['tl_user_group']['fields']['fernschach_spieler'] = array
(
	'label'                   => &$GLOBALS['TL_LANG']['tl_user_group']['fernschach_spieler'],
	'exclude'                 => true,
	'inputType'               => 'checkbox',
	'options'                 => &$GLOBALS['TL_LANG']['tl_user_group']['fernschach_spieler_optionen'],
	'eval'                    => array('multiple'=>true),
	'sql'                     => "blob NULL"
); 

$GLOBALS['TL_DCA']['tl_user_group']['fields']['fernschach_konto'] = array
(
	'label'                   => &$GLOBALS['TL_LANG']['tl_user_group']['fernschach_konto'],
	'exclude'                 => true,
	'inputType'               => 'checkbox',
	'options'                 => &$GLOBALS['TL_LANG']['tl_user_group']['fernschach_konto_optionen'],
	'eval'                    => array('multiple'=>true),
	'sql'                     => "blob NULL"
); 
