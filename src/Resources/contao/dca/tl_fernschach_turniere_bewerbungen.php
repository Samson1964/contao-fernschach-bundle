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
		'label'                       => &$GLOBALS['TL_LANG']['tl_fernschach_turniere_bewerbungen']['mainTitle'],
		'dataContainer'               => 'Table',
		'ptable'                      => 'tl_fernschach_turniere',
		'switchToEdit'                => true,
		'enableVersioning'            => true,
		//'onsubmit_callback'             => array
		//(
		//	array('tl_fernschach_turniere_bewerbungen', 'setSpielername')
		//),
		'sql' => array
		(
			'keys' => array
			(
				'id'            => 'primary',
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
				'icon'                => 'visible.gif',
				'attributes'          => 'onclick="Backend.getScrollOffset();return AjaxRequest.toggleVisibility(this,%s)"',
				'button_callback'     => array('tl_fernschach_turniere_bewerbungen', 'toggleIcon')
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
		'default'                     => '{application_legend},vorname,nachname,applicationDate;{player_legend:hide},spielerId;{promise_legend:hide},state,stateOrganizer,promiseDate,comment;{publish_legend},published'
	),

	// Fields
	'fields' => array
	(
		'id' => array
		(
			'search'                  => true,
			'sql'                     => "int(10) unsigned NOT NULL auto_increment"
		),
		'pid' => array
		(
			'foreignKey'              => 'tl_mitgliederverwaltung.nachname',
			'sql'                     => "int(10) unsigned NOT NULL default '0'",
			'relation'                => array('type'=>'belongsTo', 'load'=>'eager')
		),
		'tstamp' => array
		(
			'sorting'                 => true,
			'flag'                    => 6,
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
			'load_callback'           => array
			(
				array('tl_fernschach_turniere_bewerbungen','getVorname')
			),
			'eval'                    => array
			(
				'alwaysSave'          => true,
				'mandatory'           => true, 
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
			'load_callback'           => array
			(
				array('tl_fernschach_turniere_bewerbungen','getNachname')
			),
			'eval'                    => array
			(
				'alwaysSave'          => true,
				'mandatory'           => true, 
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

	public function toggleIcon($row, $href, $label, $title, $icon, $attributes)
	{
		$this->import('BackendUser', 'User');

		if (strlen($this->Input->get('tid')))
		{
			$this->toggleVisibility($this->Input->get('tid'), ($this->Input->get('state') == 0));
			$this->redirect($this->getReferer());
		}

		// Check permissions AFTER checking the tid, so hacking attempts are logged
		if (!$this->User->isAdmin && !$this->User->hasAccess('tl_fernschach_turniere_bewerbungen::published', 'alexf'))
		{
			return '';
		}

		$href .= '&amp;id='.$this->Input->get('id').'&amp;tid='.$row['id'].'&amp;state='.$row[''];

		if (!$row['published'])
		{
			$icon = 'invisible.gif';
		}

		return '<a href="'.$this->addToUrl($href).'" title="'.specialchars($title).'"'.$attributes.'>'.$this->generateImage($icon, $label).'</a> ';
	}

	public function toggleVisibility($intId, $blnPublished)
	{
		// Check permissions to publish
		if (!$this->User->isAdmin && !$this->User->hasAccess('tl_fernschach_turniere_bewerbungen::published', 'alexf'))
		{
			$this->log('Kein Zugriffsrecht für Aktivierung Datensatz ID "'.$intId.'"', 'tl_fernschach_turniere_bewerbungen toggleVisibility', TL_ERROR);
			// Zurücklink generieren, ab C4 ist das ein symbolischer Link zu "contao"
			if (version_compare(VERSION, '4.0', '>='))
			{
				$backlink = \System::getContainer()->get('router')->generate('contao_backend');
			}
			else
			{
				$backlink = 'contao/main.php';
			}
			$this->redirect($backlink.'?act=error');
		}

		$this->createInitialVersion('tl_fernschach_turniere_bewerbungen', $intId);

		// Trigger the save_callback
		if (is_array($GLOBALS['TL_DCA']['tl_fernschach_turniere_bewerbungen']['fields']['published']['save_callback']))
		{
			foreach ($GLOBALS['TL_DCA']['tl_fernschach_turniere_bewerbungen']['fields']['published']['save_callback'] as $callback)
			{
				$this->import($callback[0]);
				$blnPublished = $this->$callback[0]->$callback[1]($blnPublished, $this);
			}
		}

		// Update the database
		$this->Database->prepare("UPDATE tl_fernschach_turniere_bewerbungen SET tstamp=". time() .", published='" . ($blnPublished ? '' : '1') . "' WHERE id=?")
		               ->execute($intId);
		$this->createNewVersion('tl_fernschach_turniere_bewerbungen', $intId);
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
	 * load_callback: Wird bei der Initialisierung eines Formularfeldes ausgeführt.
	 * @param $varValue
	 * @param $dc
	 * @return var
	 */
	public function getVorname($varValue, DataContainer $dc) 
	{               
		if(!$varValue && $dc->activeRecord->spielerId)
		{
			// Kein Vorname, dann Vorname aus Spielertabelle holen
			$varValue = \Schachbulle\ContaoFernschachBundle\Classes\Helper::getSpieler($dc->activeRecord->spielerId, 'vorname');
		}
		return $varValue;
	}

	public function getNachname($varValue, DataContainer $dc) 
	{
		if(!$varValue && $dc->activeRecord->spielerId)
		{
			// Kein Nachname, dann Nachname aus Spielertabelle holen
			$varValue = \Schachbulle\ContaoFernschachBundle\Classes\Helper::getSpieler($dc->activeRecord->spielerId, 'nachname');
		}
		return $varValue;
	}

}
