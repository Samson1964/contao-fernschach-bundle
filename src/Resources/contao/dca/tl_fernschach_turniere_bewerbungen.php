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
 * Table tl_fernschach_turniere_bewerbungen
 */
$GLOBALS['TL_DCA']['tl_fernschach_turniere_bewerbungen'] = array
(

	// Config
	'config' => array
	(
		'dataContainer'               => 'Table',
		'ptable'                      => 'tl_fernschach_turniere',
		'enableVersioning'            => true,
		'onsubmit_callback'           => array
		(
			array('tl_fernschach_turniere_bewerbungen', 'setSpielername')
		),
		'sql' => array
		(
			'keys' => array
			(
				'id'                  => 'primary',
				'pid'                 => 'index',
			)
		)
	),

	// List
	'list' => array
	(
		'sorting' => array
		(
			'mode'                    => 4,
			'fields'                  => array('id'),
			'flag'                    => 3,
			'headerFields'            => array('title', 'registrationDate', 'startDate'),
			'panelLayout'             => 'filter;sort;search,limit',
			'child_record_callback'   => array('tl_fernschach_turniere_bewerbungen', 'listBewerbungen'),
			'disableGrouping'         => true
		),
		'global_operations' => array
		(
			'all' => array
			(
				'label'               => &$GLOBALS['TL_LANG']['MSC']['all'],
				'href'                => 'act=select',
				'class'               => 'header_edit_all',
				'attributes'          => 'onclick="Backend.getScrollOffset()" accesskey="e"'
			)
		),
		'operations' => array
		(
			'edit' => array
			(
				'label'               => &$GLOBALS['TL_LANG']['tl_fernschach_turniere_bewerbungen']['edit'],
				'href'                => 'act=edit',
				'icon'                => 'edit.gif',
			),
			'copy' => array
			(
				'label'               => &$GLOBALS['TL_LANG']['tl_fernschach_turniere_bewerbungen']['copy'],
				'href'                => 'act=copy',
				'icon'                => 'copy.gif',
				//'button_callback'     => array('tl_fernschach_turniere_bewerbungen', 'copyArchive')
			),
			'delete' => array
			(
				'label'               => &$GLOBALS['TL_LANG']['tl_fernschach_turniere_bewerbungen']['delete'],
				'href'                => 'act=delete',
				'icon'                => 'delete.gif',
				'attributes'          => 'onclick="if(!confirm(\'' . $GLOBALS['TL_LANG']['MSC']['deleteConfirm'] . '\'))return false;Backend.getScrollOffset()"',
				//'button_callback'     => array('tl_fernschach_turniere_bewerbungen', 'deleteArchive')
			),
			'toggle' => array
			(
				'label'               => &$GLOBALS['TL_LANG']['tl_fernschach_turniere_bewerbungen']['toggle'],
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
				'label'               => &$GLOBALS['TL_LANG']['tl_fernschach_turniere_bewerbungen']['show'],
				'href'                => 'act=show',
				'icon'                => 'show.gif'
			)
		)
	),

	// Palettes
	'palettes' => array
	(
		'default'                     => '{application_legend},vorname,nachname,applicationDate;{player_legend:hide},spielerId;{info_legend:hide},infoQualifikation,bemerkungen;{promise_legend:hide},state,stateOrganizer,promiseDate,comment;{publish_legend},published'
	),

	// Fields
	'fields' => array
	(
		'id' => array
		(
			'sql'                     => "int(10) unsigned NOT NULL auto_increment"
		),
		'pid' => array
		(
			'foreignKey'              => 'tl_fernschach_turniere.id',
			'sql'                     => "int(10) unsigned NOT NULL default '0'",
			'relation'                => array('type'=>'belongsTo', 'load'=>'eager')
		),
		'tstamp' => array
		(
			'sql'                     => "int(10) unsigned NOT NULL default '0'"
		),
		'vorname' => array
		(
			'label'                   => &$GLOBALS['TL_LANG']['tl_fernschach_turniere_bewerbungen']['vorname'],
			'exclude'                 => true,
			'sorting'                 => true,
			'search'                  => true,
			'flag'                    => 1,
			'inputType'               => 'text',
			'eval'                    => array
			(
				'alwaysSave'          => true,
				'mandatory'           => false,
				'maxlength'           => 40,
				'tl_class'            =>'w50'
			),
			'sql'                     => "varchar(40) NOT NULL default ''"
		),
		'nachname' => array
		(
			'label'                   => &$GLOBALS['TL_LANG']['tl_fernschach_turniere_bewerbungen']['nachname'],
			'exclude'                 => true,
			'sorting'                 => true,
			'search'                  => true,
			'flag'                    => 1,
			'inputType'               => 'text',
			'eval'                    => array
			(
				'alwaysSave'          => true,
				'mandatory'           => false,
				'maxlength'           => 40,
				'tl_class'            =>'w50'
			),
			'sql'                     => "varchar(40) NOT NULL default ''"
		),
		'applicationDate' => array
		(
			'label'                   => &$GLOBALS['TL_LANG']['tl_fernschach_turniere_bewerbungen']['applicationDate'],
			'default'                 => time(),
			'exclude'                 => true,
			'filter'                  => true,
			'sorting'                 => true,
			'flag'                    => 6,
			'inputType'               => 'text',
			'eval'                    => array('rgxp'=>'date', 'mandatory'=>true, 'doNotCopy'=>true, 'datepicker'=>true, 'tl_class'=>'w50 wizard'),
			'load_callback' => array
			(
				array('tl_fernschach_turniere_bewerbungen', 'loadDate')
			),
			'sql'                     => "int(10) unsigned NOT NULL default 0"
		),
		'spielerId' => array
		(
			'label'                   => &$GLOBALS['TL_LANG']['tl_fernschach_turniere_bewerbungen']['spielerId'],
			'exclude'                 => true,
			'inputType'               => 'select',
			'foreignKey'              => 'tl_fernschach_spieler.CONCAT(nachname,", ",vorname," (",memberId,")")',
			'relation'                => array('type'=>'belongsToMany', 'load'=>'lazy'),
			'eval'                    => array
			(
				'includeBlankOption'  => true,
				'chosen'              => true,
				'mandatory'           => false,
				'submitOnChange'      => true,
				'tl_class'            => 'w50'
			),
			'sql'                     => "int(10) unsigned NOT NULL default '0'"
		),
		'infoQualifikation' => array
		(
			'label'                   => &$GLOBALS['TL_LANG']['tl_fernschach_turniere_bewerbungen']['infoQualifikation'],
			'inputType'               => 'textarea',
			'exclude'                 => true,
			'sorting'                 => true,
			'flag'                    => 1,
			'search'                  => true,
			'eval'                    => array('mandatory'=>false, 'maxlength'=>255, 'tl_class'=>'long'),
			'sql'                     => "text NULL"
		),
		'bemerkungen' => array
		(
			'label'                   => &$GLOBALS['TL_LANG']['tl_fernschach_turniere_bewerbungen']['bemerkungen'],
			'inputType'               => 'textarea',
			'exclude'                 => true,
			'sorting'                 => true,
			'flag'                    => 1,
			'search'                  => true,
			'eval'                    => array('mandatory'=>false, 'tl_class'=>'long'),
			'sql'                     => "text NULL"
		),
		'state' => array
		(
			'label'                   => &$GLOBALS['TL_LANG']['tl_fernschach_turniere_bewerbungen']['state'],
			'exclude'                 => true,
			'filter'                  => true,
			'inputType'               => 'radio',
			'default'                 => 0,
			'options'                 => array('0', '1', '2'),
			'eval'                    => array('tl_class'=>'w50'),
			'reference'               => &$GLOBALS['TL_LANG']['tl_fernschach_turniere_bewerbungen']['state_options'],
			'sql'                     => "varchar(1) NOT NULL default '0'"
		),
		'stateOrganizer' => array
		(
			'label'                   => &$GLOBALS['TL_LANG']['tl_fernschach_turniere_bewerbungen']['stateOrganizer'],
			'inputType'               => 'checkbox',
			'default'                 => '',
			'filter'                  => true,
			'eval'                    => array
			(
				'tl_class'            => 'w50',
				'isBoolean'           => true
			),
			'sql'                     => "char(1) NOT NULL default ''"
		),
		'promiseDate' => array
		(
			'label'                   => &$GLOBALS['TL_LANG']['tl_fernschach_turniere_bewerbungen']['promiseDate'],
			'exclude'                 => true,
			'filter'                  => true,
			'sorting'                 => true,
			'flag'                    => 6,
			'inputType'               => 'text',
			'eval'                    => array('rgxp'=>'date', 'mandatory'=>false, 'doNotCopy'=>true, 'datepicker'=>true, 'tl_class'=>'clr w50 wizard'),
			'load_callback' => array
			(
				array('tl_fernschach_turniere_bewerbungen', 'loadDate')
			),
			'sql'                     => "int(10) unsigned NOT NULL default 0"
		),
		'comment' => array
		(
			'exclude'                 => true,
			'search'                  => true,
			'inputType'               => 'textarea',
			'eval'                    => array
			(
				'tl_class'            => 'w50 noresize',
			),
			'sql'                     => "text NULL"
		),
		'published' => array
		(
			'label'                   => &$GLOBALS['TL_LANG']['tl_fernschach_turniere_bewerbungen']['published'],
			'inputType'               => 'checkbox',
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
 * Class tl_fernschach_turniere_bewerbungen
 *
 * Provide miscellaneous methods that are used by the data configuration array.
 * @copyright  Leo Feyer 2005-2014
 * @author     Leo Feyer <https://contao.org>
 * @package    News
 */
class tl_fernschach_turniere_bewerbungen extends Backend
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
	 * Datensätze auflisten
	 * @param array
	 * @return string
	 */
	public function listBewerbungen($arrRow)
	{
		$spieler = \Schachbulle\ContaoFernschachBundle\Classes\Helper::getSpieler();
		// Vor- und Nachname
		if($arrRow['state'] == 0) $temp = '<b>'.$arrRow['vorname'].' '.$arrRow['nachname'].'</b>';
		elseif($arrRow['state'] == 1) $temp = '<b style="color:green">'.$arrRow['vorname'].' '.$arrRow['nachname'].'</b>';
		else $temp = '<b style="color:red">'.$arrRow['vorname'].' '.$arrRow['nachname'].'</b>';
		// Zuordnung
		if($arrRow['spielerId'])
		{
			$temp .= ' - zugeordnet: '.$spieler[$arrRow['spielerId']]['vorname'].' '.$spieler[$arrRow['spielerId']]['nachname'];
		}
		else $temp .= ' - nicht zugeordnet';
		// Bewerbungsdatum
		$temp .= ' - Bewerbung am: <b>'.date('d.m.Y', $arrRow['applicationDate']).'</b>';
		// Status
		if($arrRow['state'] == 0) $temp .= ' - ohne Entscheidung';
		elseif($arrRow['state'] == 1) $temp .= ' - <span style="color:green">Zusage</span>';
		else $temp .= ' - <span style="color:red">Absage</span>';
		// Datum
		if($arrRow['promiseDate']) $temp .= ' am <b>'.date('d.m.Y', $arrRow['promiseDate']).'</b>';
		return $temp;
	}

	/**
	 * Set the timestamp to 00:00:00 (see #26)
	 *
	 * @param integer $value
	 *
	 * @return integer
	 */
	public function loadDate($value)
	{
		if($value) return strtotime(date('Y-m-d', $value) . ' 00:00:00');
		else return '';
	}

	/**
	 * Setzt die Felder Vorname und Nachname, wenn diese nicht gefüllt sind
	 * @param mixed
	 * @param \DataContainer
	 * @return string
	 * @throws \Exception
	 */
	public function setSpielername(\DataContainer $dc)
	{
		$nachname = $dc->activeRecord->nachname;
		$vorname = $dc->activeRecord->vorname;

		if(!$nachname && $dc->activeRecord->spielerId)
		{
			// Kein Nachname, dann Nachname aus Spielertabelle holen
			$nachname = \Schachbulle\ContaoFernschachBundle\Classes\Helper::getSpieler($dc->activeRecord->spielerId, 'nachname');
			\Database::getInstance()->prepare("UPDATE tl_fernschach_turniere_bewerbungen SET nachname = ? WHERE id = ?")
			                        ->execute($nachname, $dc->id);
			$this->createNewVersion('tl_fernschach_turniere_bewerbungen', $dc->id);
		}

		if(!$vorname && $dc->activeRecord->spielerId)
		{
			// Kein Vorname, dann Vorname aus Spielertabelle holen
			$vorname = \Schachbulle\ContaoFernschachBundle\Classes\Helper::getSpieler($dc->activeRecord->spielerId, 'vorname');
			\Database::getInstance()->prepare("UPDATE tl_fernschach_turniere_bewerbungen SET vorname = ? WHERE id = ?")
			                        ->execute($vorname, $dc->id);
			$this->createNewVersion('tl_fernschach_turniere_bewerbungen', $dc->id);
		}

	}

}
