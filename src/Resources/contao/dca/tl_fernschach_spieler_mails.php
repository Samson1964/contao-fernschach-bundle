<?php

//$this->Template->headline = 'Hallo';

/**
 * Contao Open Source CMS
 *
 * Copyright (c) 2005-2015 Leo Feyer
 *
 * @license LGPL-3.0+
 */

/**
 * Table tl_fernschach_spieler_mails
 */
$GLOBALS['TL_DCA']['tl_fernschach_spieler_mails'] = array
(

	// Config
	'config' => array
	(
		'label'                       => &$GLOBALS['TL_LANG']['tl_fernschach_spieler_mails']['main'],
		'dataContainer'               => 'Table',
		'ptable'                      => 'tl_fernschach_spieler',
		'enableVersioning'            => true,
		'markAsCopy'                  => 'subject',
		'sql' => array
		(
			'keys' => array
			(
				'id' => 'primary',
				'pid' => 'index'
			)
		)
	),

	// List
	'list' => array
	(
		'sorting' => array
		(
			'mode'                    => 4,
			'fields'                  => array('sent_state ASC', 'sent_date DESC'),
			'headerFields'            => array('nachname', 'vorname', 'memberId'),
			'panelLayout'             => 'filter;sort,search,limit',
			'child_record_callback'   => array('tl_fernschach_spieler_mails', 'listEmails')
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
				'label'               => &$GLOBALS['TL_LANG']['tl_fernschach_spieler_mails']['edit'],
				'href'                => 'act=edit',
				'icon'                => 'edit.gif'
			),
			'copy' => array
			(
				'label'               => &$GLOBALS['TL_LANG']['tl_fernschach_spieler_mails']['copy'],
				'href'                => 'act=paste&amp;mode=copy',
				'icon'                => 'copy.gif'
			),
			'cut' => array
			(
				'label'               => &$GLOBALS['TL_LANG']['tl_fernschach_spieler_mails']['cut'],
				'href'                => 'act=paste&amp;mode=cut',
				'icon'                => 'cut.gif'
			),
			'delete' => array
			(
				'label'               => &$GLOBALS['TL_LANG']['tl_fernschach_spieler_mails']['delete'],
				'href'                => 'act=delete',
				'icon'                => 'delete.gif',
				'attributes'          => 'onclick="if(!confirm(\'' . $GLOBALS['TL_LANG']['MSC']['deleteConfirm'] . '\'))return false;Backend.getScrollOffset()"'
			),
			'show' => array
			(
				'label'               => &$GLOBALS['TL_LANG']['tl_fernschach_spieler_mails']['show'],
				'href'                => 'act=show',
				'icon'                => 'show.gif'
			),
			'send' => array
			(
				'label'               => &$GLOBALS['TL_LANG']['tl_fernschach_spieler_mails']['send'],
				'href'                => 'key=send',
				'icon'                => 'bundles/contaolizenzverwaltung/images/email_senden.png'
			)
		)
	),

	// Palettes
	'palettes' => array
	(
		'__selector__'                => array('signatur'),
		'default'                     => '{text_legend},subject,content;{signatur_legend:hide},signatur;{mail_legend},copyBenutzer,send'
	),

	// Subpalettes
	'subpalettes' => array
	(
		'signatur'                    => 'signatur_text',
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
			'foreignKey'              => 'tl_lizenzverwaltung.id',
			'sql'                     => "int(10) unsigned NOT NULL default '0'",
			'relation'                => array('type'=>'belongsTo', 'load'=>'eager')
		),
		'tstamp' => array
		(
			'flag'                    => 11,
			'sql'                     => "int(10) unsigned NOT NULL default '0'"
		),
		'subject' => array
		(
			'label'                   => &$GLOBALS['TL_LANG']['tl_fernschach_spieler_mails']['subject'],
			'exclude'                 => true,
			'search'                  => true,
			'sorting'                 => true,
			'default'                 => '',
			'flag'                    => 1,
			'inputType'               => 'text',
			'eval'                    => array
			(
				'mandatory'           => true, 
				'decodeEntities'      => true, 
				'maxlength'           => 255, 
				'tl_class'            =>'long clr'
			),
			'sql'                     => "varchar(255) NOT NULL default ''"
		),
		'content' => array
		(
			'label'                   => &$GLOBALS['TL_LANG']['tl_fernschach_spieler_mails']['content'],
			'exclude'                 => true,
			'search'                  => true,
			'inputType'               => 'textarea',
			'eval'                    => array
			(
				'mandatory'           => true, 
				'rte'                 => 'tinyMCE',
				'helpwizard'          => true,
				'tl_class'            => 'long clr',
			),
			'explanation'             => 'insertTags',
			'sql'                     => "mediumtext NULL"
		),
		'signatur' => array
		(
			'label'                   => &$GLOBALS['TL_LANG']['tl_fernschach_spieler_mails']['signatur'],
			'exclude'                 => true,
			'inputType'               => 'checkbox',
			'filter'                  => true,
			'eval'                    => array
			(
				'submitOnChange'      => true,
				'tl_class'            => 'clr'
			),
			'sql'                     => "char(1) NOT NULL default ''"
		),
		'signatur_text' => array
		(
			'label'                   => &$GLOBALS['TL_LANG']['tl_fernschach_spieler_mails']['signatur_text'],
			'exclude'                 => true,
			'search'                  => true,
			'inputType'               => 'textarea',
			'load_callback'           => array
			(
				array('tl_fernschach_spieler_mails', 'getSignatur')
			),
			'eval'                    => array
			(
				'mandatory'           => true, 
				'rte'                 => 'tinyMCE',
				'helpwizard'          => true,
				'tl_class'            => 'long clr',
			),
			'explanation'             => 'insertTags',
			'sql'                     => "mediumtext NULL"
		),
		'copyBenutzer' => array
		(
			'label'                   => &$GLOBALS['TL_LANG']['tl_fernschach_spieler_mails']['copyBenutzer'],
			'inputType'               => 'checkbox',
			'default'                 => true,
			'exclude'                 => true,
			'eval'                    => array
			(
				'tl_class'            => 'w50',
				'isBoolean'           => false
			),
			'sql'                     => "char(1) NOT NULL default '1'"
		),
		'sent_state' => array
		(
			'label'                   => &$GLOBALS['TL_LANG']['tl_fernschach_spieler_mails']['sent_state'],
			'eval'                    => array
			(
				'doNotCopy'           => true,
				'isBoolean'           => true,
			),
			'sql'                     => "char(1) NOT NULL default ''"
		),
		'sent_date' => array
		(
			'label'                   => &$GLOBALS['TL_LANG']['tl_fernschach_spieler_mails']['sent_date'],
			'sorting'                 => true,
			'flag'                    => 6,
			'eval'                    => array
			(
				'rgxp'                => 'date',
				'doNotCopy'           => true,
			),
			'sql'                     => "int(10) unsigned NOT NULL default '0'"
		),
		'sent_text' => array
		(
			'label'                   => &$GLOBALS['TL_LANG']['tl_fernschach_spieler_mails']['sent_text'],
			'eval'                    => array
			(
				'doNotCopy'           => true,
			),
			'sql'                     => "mediumtext NULL"
		),
	)
);


/**
 * Provide miscellaneous methods that are used by the data configuration array.
 *
 * @author Leo Feyer <https://github.com/leofeyer>
 */
class tl_fernschach_spieler_mails extends Backend
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
	 * List records
	 *
	 * @param array $arrRow
	 *
	 * @return string
	 */
	public function listEmails($arrRow)
	{
		$content = \StringUtil::insertTagToSrc($arrRow['content']);
		
		return '
<div class="cte_type '.(($arrRow['sent_state'] && $arrRow['sent_date']) ? 'published' : 'unpublished').'"><strong>'.$arrRow['subject'].'</strong> - '.(($arrRow['sent_state'] && $arrRow['sent_date']) ? 'Versendet am '.Date::parse(Config::get('datimFormat'), $arrRow['sent_date']) : 'Nicht versendet'). '</div>
<div class="limit_height'.(!Config::get('doNotCollapse') ? ' h128' : '').'">'.$content.'<hr></div>'."\n";

	}

	/**
	 * Funktion getSignatur
	 * ============================================
	 * Signatur aus Profil laden, wenn das Feld leer ist
	 *
	 * @param array $arrRow
	 *
	 * @return string
	 */
	public function getSignatur($varValue)
	{
		if($varValue)
		{
			return $varValue;
		}
		else
		{
			return $this->User->fernschach_signatur;
		}
	}

}
