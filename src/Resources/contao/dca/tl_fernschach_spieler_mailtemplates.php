<?php

/**
 * Contao Open Source CMS
 *
 * Copyright (c) 2005-2014 Leo Feyer
 *
 * @package News
 * @link    https://contao.org
 * @license http://www.gnu.org/licenses/lgpl-3.0.html LGPL
 */


/**
 * Table tl_fernschach_spieler_mailtemplates
 */
$GLOBALS['TL_DCA']['tl_fernschach_spieler_mailtemplates'] = array
(

	// Config
	'config' => array
	(
		'dataContainer'               => 'Table',
		'switchToEdit'                => true, 
		'enableVersioning'            => true,
		'sql' => array
		(
			'keys' => array
			(
				'id'                 => 'primary',
			)
		)
	),

	// List
	'list' => array
	(
		'sorting' => array
		(
			'mode'                    => 1,
			'fields'                  => array('name'),
			'flag'                    => 1,
			'panelLayout'             => 'filter,sort;search,limit',
			'disableGrouping'         => true,
		),
		'label' => array
		(
			'fields'                  => array('name', 'description'),
			'format'                  => '%s %s',
			'showColumns'             => true,
		),
		'global_operations' => array
		(
			'all' => array
			(
				'label'               => &$GLOBALS['TL_LANG']['MSC']['all'],
				'href'                => 'act=select',
				'class'               => 'header_edit_all',
				'attributes'          => 'onclick="Backend.getScrollOffset()" accesskey="e"'
			),
			'back' => array
			(
				'label'               => &$GLOBALS['TL_LANG']['MSC']['backBT'], // z.B. "Zurück"
				'href'                => 'table=tl_fernschach_spieler_mails', // Ziel-Tabelle
				'class'               => 'header_back',
				'attributes'          => 'onclick="Backend.getScrollOffset();"'
			),
		),
		'operations' => array
		(
			'edit' => array
			(
				'label'               => &$GLOBALS['TL_LANG']['tl_fernschach_spieler_mailtemplates']['edit'],
				'href'                => 'act=edit',
				'icon'                => 'edit.gif',
			),
			'copy' => array
			(
				'label'               => &$GLOBALS['TL_LANG']['tl_fernschach_spieler_mailtemplates']['copy'],
				'href'                => 'act=copy',
				'icon'                => 'copy.gif',
			),
			'cut' => array(
				'label'               => &$GLOBALS['TL_LANG']['tl_fernschach_spieler_mailtemplates']['cut'],
				'href'                => 'act=paste&amp;mode=cut',
				'icon'                => 'cut.gif',
			),
			'toggle' => array
			(
				'label'                => &$GLOBALS['TL_LANG']['tl_fernschach_spieler_mailtemplates']['toggle'],
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
				'label'               => &$GLOBALS['TL_LANG']['tl_fernschach_spieler_mailtemplates']['show'],
				'href'                => 'act=show',
				'icon'                => 'show.gif'
			),
		)
	),

	// Palettes
	'palettes' => array
	(
		'default'                     => '{name_legend},name,description,template;{publish_legend},published'
	),

	// Fields
	'fields' => array
	(
		'id' => array
		(
			'sql'                     => "int(10) unsigned NOT NULL auto_increment"
		),
		'tstamp' => array
		(
			'sql'                     => "int(10) unsigned NOT NULL default '0'"
		),
		'name' => array
		(
			'label'                   => &$GLOBALS['TL_LANG']['tl_fernschach_spieler_mailtemplates']['name'],
			'exclude'                 => true,
			'search'                  => true,
			'inputType'               => 'text',
			'eval'                    => array
			(
				'mandatory'           => true,
				'maxlength'           => 255, 
				'tl_class'            => 'long'
			),
			'sql'                     => "varchar(255) NOT NULL default ''"
		),
		'description' => array
		(
			'label'                   => &$GLOBALS['TL_LANG']['tl_fernschach_spieler_mailtemplates']['description'],
			'exclude'                 => true,
			'search'                  => true,
			'inputType'               => 'text',
			'eval'                    => array
			(
				'mandatory'           => false,
				'maxlength'           => 255, 
				'tl_class'            => 'long'
			),
			'sql'                     => "varchar(255) NOT NULL default ''"
		),
		'template' => array
		(
			'label'                   => &$GLOBALS['TL_LANG']['tl_fernschach_spieler_mailtemplates']['template'],
			'exclude'                 => true,
			'search'                  => true,
			'inputType'               => 'textarea',
			'default'                 => &$GLOBALS['TL_LANG']['tl_fernschach_spieler_mailtemplates']['default_template'],
			'eval'                    => array
			(
				'mandatory'           => true, 
				'preserveTags'        => true, 
				'decodeEntities'      => true, 
				'class'               => 'monospace', 
				'rte'                 => 'ace|php', 
				'helpwizard'          => true, 
				'tl_class'            => 'long'
			),
			'explanation'             => 'fernschach_mailtemplates',
			'sql'                     => "mediumtext NULL"
		),
		'published' => array
		(
			'label'                   => &$GLOBALS['TL_LANG']['tl_fernschach_spieler_mailtemplates']['published'],
			'inputType'               => 'checkbox',
			'exclude'                 => true,
			'default'                 => 1,
			'filter'                  => true,
			'eval'                    => array
			(
				'tl_class'            => 'w50',
				'isBoolean'           => true
			),
			'sql'                     => "char(1) NOT NULL default '1'"
		),
	)
);


/**
 * Class tl_fernschach_spieler_mailtemplates
 *
 * Provide miscellaneous methods that are used by the data configuration array.
 * @copyright  Leo Feyer 2005-2014
 * @author     Leo Feyer <https://contao.org>
 * @package    News
 */
class tl_fernschach_spieler_mailtemplates extends Backend
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
