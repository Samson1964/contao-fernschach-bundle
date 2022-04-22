<?php

/**
 * Paletten
 */
$GLOBALS['TL_DCA']['tl_content']['palettes']['turnierbewerbungen_zusagen'] = '{type_legend},type,headline;{fernschachverwaltung_legend},fernschachverwaltung_id;{template_legend:hide},customTpl;{protected_legend:hide},protected;{expert_legend:hide},cssID;{invisible_legend:hide},invisible,start,stop';

/**
 * Felder
 */

$GLOBALS['TL_DCA']['tl_content']['fields']['fernschachverwaltung_id'] = array
(
	'label'                   => &$GLOBALS['TL_LANG']['tl_content']['fernschachverwaltung_id'],
	'exclude'                 => true,
	'options_callback'        => array('tl_content_fernschachverwaltung', 'getTournaments'),
	'inputType'               => 'select',
	'eval'                    => array
	(
		'mandatory'           => false,
		'multiple'            => false,
		'chosen'              => true,
		'submitOnChange'      => false,
		'includeBlankOption'  => true,
		'tl_class'            => 'long',
	),
	'sql'                     => "int(10) unsigned NOT NULL default '0'"
);

/*****************************************
 * Klasse tl_content_mitgliederverwaltung
 *****************************************/

class tl_content_fernschachverwaltung extends \Backend
{

	/**
	 * Import the back end user object
	 */
	public function __construct()
	{
		parent::__construct();
		$this->import('BackendUser', 'User');
	}

	/**
	 * Liefert die Liste der registrierten Einladungsturniere zurück
	 */
	public static function getTournaments()
	{
		$objRegister = \Database::getInstance()->prepare("SELECT * FROM tl_fernschach_turniere WHERE typ = ? ORDER BY startDate DESC")
		                                       ->execute('e');
		$array = array();
		while($objRegister->next())
		{
			$array[$objRegister->id] = $objRegister->titel . ' [Start: ' . date('d.m.Y', $objRegister->startDate) . '] '.$objRegister->id;
		}
		return $array;
	}


}
