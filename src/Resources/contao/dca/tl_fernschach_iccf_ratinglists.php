<?php

/**
 * Contao Open Source CMS
 *
 * Copyright (c) 2005-2015 Leo Feyer
 *
 * @package   Elo
 * @author    Frank Hoppe
 * @license   GNU/LPGL
 * @copyright Frank Hoppe 2016
 */


/**
 * Table tl_fernschach_iccf
 */
$GLOBALS['TL_DCA']['tl_fernschach_iccf_ratinglists'] = array
(

	// Config
	'config' => array
	(
		'dataContainer'                 => 'Table',
		'enableVersioning'              => true,
		'sql' => array
		(
			'keys' => array
			(
				'id'                    => 'primary',
			)
		)
	),

	// List
	'list' => array
	(
		'sorting' => array
		(
			'mode'                      => 1,
			'fields'                    => array('fromDate', 'toDate'),
			'panelLayout'               => 'filter,sort;search,limit',
			'flag'                      => 12,
			'disableGrouping'           => true,
		),
		'label' => array
		(
			'fields'                    => array('title', 'fromDate', 'toDate'),
			'showColumns'               => true,
		),
		'global_operations' => array
		(
			'spieler' => array
			(
				'label'               => &$GLOBALS['TL_LANG']['tl_fernschach_iccf_ratinglists']['spieler'],
				'href'                => 'table=tl_fernschach_iccf_players',
				'icon'                => 'bundles/contaofernschach/images/spieler.png',
				'attributes'          => 'onclick="Backend.getScrollOffset();"'
			),
			'all' => array
			(
				'label'                 => &$GLOBALS['TL_LANG']['MSC']['all'],
				'href'                  => 'act=select',
				'class'                 => 'header_edit_all',
				'attributes'            => 'onclick="Backend.getScrollOffset();" accesskey="e"'
			)
		),
		'operations' => array
		(
			'editheader' => array
			(
				'label'                 => &$GLOBALS['TL_LANG']['tl_fernschach_iccf_ratinglists']['editheader'],
				'href'                  => 'act=edit',
				'icon'                  => 'header.gif',
			),
			'delete' => array
			(
				'label'                 => &$GLOBALS['TL_LANG']['tl_fernschach_iccf_ratinglists']['delete'],
				'href'                  => 'act=delete',
				'icon'                  => 'delete.gif',
				'attributes'            => 'onclick="if(!confirm(\'' . $GLOBALS['TL_LANG']['MSC']['deleteConfirm'] . '\'))return false;Backend.getScrollOffset()"'
			),
			'toggle' => array
			(
				'label'                => &$GLOBALS['TL_LANG']['tl_fernschach_iccf_ratinglists']['toggle'],
				'attributes'           => 'onclick="Backend.getScrollOffset()"',
				'haste_ajax_operation' => array
				(
					'field'            => 'published',
					'options'          => array
					(
						array('value' => '', 'icon' => 'invisible.svg'),
						array('value' => '1', 'icon' => 'visible.svg'),
					),
				),
			),
			'show' => array
			(
				'label'                 => &$GLOBALS['TL_LANG']['tl_fernschach_iccf_ratinglists']['show'],
				'href'                  => 'act=show',
				'icon'                  => 'show.gif'
			),
			'import' => array
			(
				'label'                 => &$GLOBALS['TL_LANG']['tl_fernschach_iccf_ratinglists']['import'],
				'href'                  => 'key=importCSV',
				'icon'                  => 'bundles/contaofernschach/images/import.png',
				'attributes'            => 'onclick="Backend.getScrollOffset();" accesskey="e"'
			), 
		)
	),

	// Palettes
	'palettes' => array
	(
		'__selector__'                  => array(''),
		'default'                       => '{title_legend},title,fromDate,toDate;{publish_legend},published'
	),

	// Subpalettes
	'subpalettes' => array
	(
		''                              => ''
	),

	// Fields
	'fields' => array
	(
		'id' => array
		(
			'sql'                       => "int(10) unsigned NOT NULL auto_increment"
		),
		'tstamp' => array
		(
			'sql'                       => "int(10) unsigned NOT NULL default '0'"
		),
		'title' => array
		(
			'label'                     => &$GLOBALS['TL_LANG']['tl_fernschach_iccf_ratinglists']['title'],
			'exclude'                   => true,
			'inputType'                 => 'text',
			'search'                    => true,
			'eval'                      => array
			(
				'mandatory'             => true,
				'maxlength'             => 64,
				'tl_class'              => 'w50'
			),
			'sql'                       => "varchar(64) NOT NULL default ''"
		),
		'fromDate' => array
		(
			'label'                     => &$GLOBALS['TL_LANG']['tl_fernschach_iccf_ratinglists']['fromDate'],
			'exclude'                   => true,
			'filter'                    => true,
			'inputType'                 => 'text',
			'flag'                      => 8,
			'eval'                      => array
			(
				'rgxp'                  => 'date',
				'datepicker'            => true,
				'mandatory'             => true,
				'maxlength'             => 10,
				'tl_class'              => 'w50 clr widget'
			),
			'sql'                       => "int(10) unsigned NOT NULL default '0'"
		),
		'toDate' => array
		(
			'label'                     => &$GLOBALS['TL_LANG']['tl_fernschach_iccf_ratinglists']['toDate'],
			'exclude'                   => true,
			'filter'                    => true,
			'inputType'                 => 'text',
			'flag'                      => 8,
			'eval'                      => array
			(
				'rgxp'                  => 'date',
				'datepicker'            => true,
				'mandatory'             => true,
				'maxlength'             => 10,
				'tl_class'              => 'w50 widget'
			),
			'sql'                       => "int(10) unsigned NOT NULL default '0'"
		),
		'published' => array
		(
			'label'                     => &$GLOBALS['TL_LANG']['tl_fernschach_iccf_ratinglists']['published'],
			'exclude'                   => true,
			'search'                    => false,
			'sorting'                   => false,
			'filter'                    => true,
			'inputType'                 => 'checkbox',
			'eval'                      => array('tl_class' => 'w50','isBoolean' => true),
			'sql'                       => "char(1) NOT NULL default ''"
		),
	)
);


/**
 * Class tl_fernschach_iccf_ratinglists
 *
 * Provide miscellaneous methods that are used by the data configuration array.
 * @copyright  Leo Feyer 2005-2014
 * @author     Leo Feyer <https://contao.org>
 * @package    News
 */
class tl_fernschach_iccf_ratinglists extends Backend
{

	/**
	 * Import the back end user object
	 */
	public function __construct()
	{
		parent::__construct();
		$this->import('BackendUser', 'User');
	}

}
