<?php

/**
 * Contao Open Source CMS
 *
 * Copyright (c) 2005-2013 Leo Feyer
 *
 * @package Core
 * @link    https://contao.org
 * @license http://www.gnu.org/licenses/lgpl-3.0.html LGPL
 */

use Contao\Backend;
use Contao\BackendUser;
use Contao\Config;
use Contao\Controller;
use Contao\CoreBundle\Exception\AccessDeniedException;
use Contao\DC_Table;
use Contao\DataContainer;
use Contao\Database;
use Contao\Environment;
use Contao\Image;
use Contao\Input;
use Contao\Message;
use Contao\PageModel;
use Contao\StringUtil;
use Contao\System;
use Schachbulle\ContaoFernschachBundle\Classes\Scope;



/**
 * Table tl_fernschach_turniere
 */
$GLOBALS['TL_DCA']['tl_fernschach_turniere'] = array
(

	// Config
	'config' => array
	(
		'label'                       => &$GLOBALS['TL_LANG']['tl_fernschach_turniere']['mainTitle'],
		'dataContainer'               => DC_Table::class,
		'ctable'                      => array('tl_fernschach_turniere_meldungen', 'tl_fernschach_turniere_spieler', 'tl_fernschach_turniere_bewerbungen', 'tl_fernschach_turniere_mannschaften'),
		'enableVersioning'            => true,
		'onload_callback'             => array
		(
			//array('tl_fernschach_turniere', 'checkPermission'),
			array('tl_fernschach_turniere', 'addBreadcrumb')
		),
		'sql'                         => array
		(
			'keys'                    => array
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
			'mode'                    => 5,
			'fields'                  => array('sorting'),
			'icon'                    => 'pagemounts.gif',
			'paste_button_callback'   => array('tl_fernschach_turniere', 'pasteTournament'),
			'panelLayout'             => 'filter;sort,search',
		),
		'label' => array
		(
			'fields'                  => array('title'),
			'format'                  => '%s',
			'label_callback'          => array('tl_fernschach_turniere', 'addIcon')
		),
		'global_operations' => array
		(
			'toggleNodes' => array
			(
				'label'               => &$GLOBALS['TL_LANG']['MSC']['toggleAll'],
				'href'                => 'ptg=all',
				'class'               => 'header_toggle',
				'showOnSelect'        => true 
			),
			'importTurniere' => array
			(
				'label'               => &$GLOBALS['TL_LANG']['tl_fernschach_turniere']['importTurniere'],
				'href'                => 'key=importTurniere',
				'icon'                => 'bundles/contaofernschach/images/import.png'
			),
			'statistik' => array
			(
				'label'               => &$GLOBALS['TL_LANG']['tl_fernschach_turniere']['statistik'],
				'href'                => 'key=statistik',
				'icon'                => 'bundles/contaofernschach/images/chart.png'
			),
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
			'infoBewerbungen' => array
			(
				'button_callback'     => array('tl_fernschach_turniere', 'infoBewerbungen')
			),
			'editHeader' => array
			(
				'label'               => &$GLOBALS['TL_LANG']['tl_fernschach_turniere']['editHeader'],
				'href'                => 'act=edit',
				'icon'                => 'header.gif',
			), 
			'editBewerbungen' => array
			(
				'label'               => &$GLOBALS['TL_LANG']['tl_fernschach_turniere']['editBewerbungen'],
				'href'                => 'table=tl_fernschach_turniere_bewerbungen',
				'icon'                => 'bundles/contaofernschach/images/turnier_bewerbungen.png',
				'button_callback'     => array('tl_fernschach_turniere', 'bewerbungenIcon')
			),
			'editMeldungen' => array
			(
				'label'               => &$GLOBALS['TL_LANG']['tl_fernschach_turniere']['editMeldungen'],
				'href'                => 'table=tl_fernschach_turniere_meldungen',
				'icon'                => 'bundles/contaofernschach/images/turnier_meldungen.png',
				'button_callback'     => array('tl_fernschach_turniere', 'meldungenIcon')
			),
			'editMannschaften' => array
			(
				'label'               => &$GLOBALS['TL_LANG']['tl_fernschach_turniere']['editMannschaften'],
				'href'                => 'table=tl_fernschach_turniere_mannschaften',
				'icon'                => 'bundles/contaofernschach/images/turnier_mannschaften.png',
				'button_callback'     => array('tl_fernschach_turniere', 'mannschaftenIcon')
			),
			'editTeilnehmer' => array
			(
				'label'               => &$GLOBALS['TL_LANG']['tl_fernschach_turniere']['editTeilnehmer'],
				'href'                => 'do=fernschach-turniere-teilnehmer',
				'icon'                => 'bundles/contaofernschach/images/turnier_gruppe.png',
				'button_callback'     => array('tl_fernschach_turniere', 'spielerIcon')
			),
			'cut' => array
			(
				'label'               => &$GLOBALS['TL_LANG']['tl_fernschach_turniere']['cut'],
				'href'                => 'act=paste&amp;mode=cut',
				'icon'                => 'cut.gif',
				'attributes'          => 'onclick="Backend.getScrollOffset()"',
			),
			'delete' => array
			(
				'label'               => &$GLOBALS['TL_LANG']['tl_fernschach_turniere']['delete'],
				'href'                => 'act=delete',
				'icon'                => 'delete.gif',
				'attributes'          => 'onclick="if(!confirm(\'' . ($GLOBALS['TL_LANG']['MSC']['deleteConfirm'] ?? null) . '\'))return false;Backend.getScrollOffset()"',
			),
			'toggle' => array
			(
				'label'                => &$GLOBALS['TL_LANG']['tl_fernschach_turniere']['toggle'],
				'attributes'           => 'onclick="Backend.getScrollOffset()"',
				'href'                => 'act=toggle&amp;field=published',
				'icon'                => 'visible.svg',
			),
			'show' => array
			(
				'label'               => &$GLOBALS['TL_LANG']['tl_fernschach_turniere']['show'],
				'href'                => 'act=show',
				'icon'                => 'show.gif'
			)
		)
	),

	// Palettes
	'palettes' => array
	(
		'__selector__'                => array('type', 'titleView', 'bewerbungErlaubt', 'nenngeldActive'), 
		'default'                     => '{title_legend},title,type;{publish_legend},published',
		'category'                    => '{title_legend},title,type,titleView;{turnierleiter_legend},turnierleiterName,turnierleiterEmail,turnierleiterUserId,turnierleiterInfo;{nenngeld_legend},nenngeldView,nenngeldActive;{publish_legend},archived,published',
		'tournament'                  => '{title_legend},title,type;{tournament_legend},kennziffer,klassenzuordnung,registrationDate,startDate,typ,bretter,art,artInfo,spielerMax,maxMeldungen,spielerGeschlecht,spielerAlterMin,spielerAlterMax;{nenngeld_legend},nenngeldView,nenngeldActive;{meldung_legend},onlineAnmeldung;{meldestand_legend:hide},onlineMeldestaende,versteckeNamen;{turnierleiter_legend},turnierleiterName,turnierleiterEmail,turnierleiterUserId,turnierleiterInfo;{applications_legend},bewerbungErlaubt;{publish_legend},archived,published',
		'group'                       => '{title_legend},title,type;{tournament_legend},kennziffer;{turnierleiter_legend},turnierleiterName,turnierleiterEmail,turnierleiterUserId,turnierleiterInfo;{publish_legend},archived,published',
	), 

	// Subpalettes
	'subpalettes' => array
	(
		'titleView'                   => 'titleAlternate',
		'bewerbungErlaubt'            => 'applications,applicationText',
		'nenngeldActive'              => 'nenngeld'
	), 
	
	// Fields
	'fields' => array
	(
		'id' => array
		(
			'label'                   => array('ID'),
			'search'                  => true,
			'sql'                     => "int(10) unsigned NOT NULL auto_increment"
		),
		'pid' => array
		(
			'sql'                     => "int(10) unsigned NOT NULL default '0'"
		),
		'sorting' => array
		(
			'sql'                     => "int(10) unsigned NOT NULL default '0'"
		),
		'tstamp' => array
		(
			'sql'                     => "int(10) unsigned NOT NULL default '0'"
		),
		'title' => array
		(
			'label'                   => &$GLOBALS['TL_LANG']['tl_fernschach_turniere']['title'],
			'exclude'                 => true,
			'inputType'               => 'text',
			'search'                  => true,
			'eval'                    => array
			(
				'mandatory'           => true,
				'maxlength'           => 255,
				'decodeEntities'      => true,
				'tl_class'            => 'w50'
			),
			'sql'                     => "varchar(255) NOT NULL default ''"
		),
		'type' => array
		(
			'label'                   => &$GLOBALS['TL_LANG']['tl_fernschach_turniere']['type'],
			'exclude'                 => true,
			'filter'                  => true,
			'inputType'               => 'select',
			'explanation'             => 'fernschach_turniere_type',
			'eval'                    => array
			(
				'includeBlankOption'  => true,
				'mandatory'           => true,
				'helpwizard'          => true,
				'submitOnChange'      => true,
				'tl_class'            => 'w50'
			),
			'options_callback'        => array('tl_fernschach_turniere', 'getTypen'),
			'save_callback'           => array
			(
				array('tl_fernschach_turniere', 'pruefeTyp')
			),
			'sql'                     => "varchar(64) NOT NULL default ''"
		),
		'titleView' => array
		(
			'label'                   => &$GLOBALS['TL_LANG']['tl_fernschach_turniere']['titleView'],
			'exclude'                 => true,
			'default'                 => '',
			'inputType'               => 'checkbox',
			'eval'                    => array
			(
				'tl_class'            => 'long clr', 
				'isBoolean'           => true,
				'submitOnChange'      => true
			),
			'sql'                     => "char(1) NOT NULL default ''"
		),
		'titleAlternate' => array
		(
			'label'                   => &$GLOBALS['TL_LANG']['tl_fernschach_turniere']['titleAlternate'],
			'exclude'                 => true,
			'inputType'               => 'text',
			'search'                  => true,
			'eval'                    => array
			(
				'mandatory'           => false,
				'maxlength'           => 255,
				'decodeEntities'      => true,
				'tl_class'            => 'w50'
			),
			'sql'                     => "varchar(255) NOT NULL default ''"
		),
		'kennziffer' => array
		(
			'label'                   => &$GLOBALS['TL_LANG']['tl_fernschach_turniere']['kennziffer'],
			'inputType'               => 'text',
			'exclude'                 => true,
			'sorting'                 => true,
			'search'                  => true,
			'flag'                    => 1,
			'eval'                    => array
			(
				'mandatory'           => false, 
				'maxlength'           => 255, 
				'tl_class'            => 'w50'
			),
			'sql'                     => "varchar(255) NOT NULL default ''"
		),
		'klassenzuordnung' => array
		(
			'label'                   => &$GLOBALS['TL_LANG']['tl_fernschach_turniere']['klassenzuordnung'],
			'exclude'                 => true,
			'inputType'               => 'select',
			'filter'                  => true,
			'options'                 => &$GLOBALS['TL_LANG']['tl_fernschach_turniere']['klassenzuordnung_options'],
			'eval'                    => array
			(
				'includeBlankOption'  => true,
				'mandatory'           => false,
				'tl_class'            => 'w50'
			),
			'sql'                     => "varchar(5) NOT NULL default ''"
		),
		'registrationDate' => array
		(
			'label'                   => &$GLOBALS['TL_LANG']['tl_fernschach_turniere']['registrationDate'],
			'default'                 => time(),
			'exclude'                 => true,
			'sorting'                 => true,
			'flag'                    => 6,
			'inputType'               => 'text',
			'eval'                    => array('rgxp'=>'date', 'mandatory'=>false, 'doNotCopy'=>true, 'datepicker'=>true, 'tl_class'=>'clr w50 wizard'),
			'load_callback' => array
			(
				array('tl_fernschach_turniere', 'loadDate')
			),
			'sql'                     => "int(10) unsigned NOT NULL default 0"
		),
		'startDate' => array
		(
			'label'                   => &$GLOBALS['TL_LANG']['tl_fernschach_turniere']['startDate'],
			'default'                 => time(),
			'exclude'                 => true,
			'sorting'                 => true,
			'flag'                    => 6,
			'inputType'               => 'text',
			'eval'                    => array('rgxp'=>'date', 'mandatory'=>false, 'doNotCopy'=>true, 'datepicker'=>true, 'tl_class'=>'w50 wizard'),
			'load_callback' => array
			(
				array('tl_fernschach_turniere', 'loadDate')
			),
			'sql'                     => "int(10) unsigned NOT NULL default 0"
		),
		'typ' => array(
			'label'                   => &$GLOBALS['TL_LANG']['tl_fernschach_turniere']['typ'],
			'exclude'                 => true,
			'filter'                  => true,
			'inputType'               => 'select',
			'options'                 => &$GLOBALS['TL_LANG']['tl_fernschach_turniere']['typ_options'],
			'eval'                    => array
			(
				'tl_class'            => 'w50', 
				'includeBlankOption'  => true
			),
			'sql'                     => "varchar(1) NOT NULL default ''"
		),
		// Information anzeigen, welches FE-Mitglied dem Spieler zugeordnet ist
		'nenngeldView' => array
		(
			'label'                   => &$GLOBALS['TL_LANG']['tl_fernschach_turniere']['nenngeldView'],
			'input_field_callback'    => array('tl_fernschach_turniere', 'getNenngeld'),
			'exclude'                 => true
		),
		'nenngeldActive' => array
		(
			'label'                   => &$GLOBALS['TL_LANG']['tl_fernschach_turniere']['nenngeldActive'],
			'exclude'                 => true,
			'filter'                  => true,
			'default'                 => '1',
			'inputType'               => 'checkbox',
			'eval'                    => array
			(
				'tl_class'            => 'w50', 
				'isBoolean'           => true,
				'submitOnChange'      => true
			),
			'sql'                     => "char(1) NOT NULL default '1'"
		),
		'nenngeld' => array
		(
			'label'                   => &$GLOBALS['TL_LANG']['tl_fernschach_turniere']['nenngeld'],
			'exclude'                 => true,
			'sorting'                 => true,
			'inputType'               => 'text',
			'eval'                    => array('rgxp'=>'digit', 'mandatory'=>false, 'tl_class'=>'w50', 'maxlength'=>6),
			'sql'                     => "varchar(6) NOT NULL default ''"
		),
		'art' => array(
			'label'                   => &$GLOBALS['TL_LANG']['tl_fernschach_turniere']['art'],
			'exclude'                 => true,
			'filter'                  => true,
			'inputType'               => 'select',
			'options'                 => &$GLOBALS['TL_LANG']['tl_fernschach_turniere']['art_options'],
			'eval'                    => array
			(
				'tl_class'            => 'clr w50',
				'includeBlankOption'  => true
			),
			'sql'                     => "varchar(1) NOT NULL default ''"
		),
		'artInfo' => array
		(
			'label'                   => &$GLOBALS['TL_LANG']['tl_fernschach_turniere']['artInfo'],
			'inputType'               => 'text',
			'exclude'                 => true,
			'sorting'                 => false,
			'search'                  => true,
			'flag'                    => 1,
			'eval'                    => array
			(
				'mandatory'           => false, 
				'maxlength'           => 255, 
				'tl_class'            => 'w50'
			),
			'sql'                     => "varchar(255) NOT NULL default ''"
		),
		'spielerMax' => array
		(
			'label'                   => &$GLOBALS['TL_LANG']['tl_fernschach_turniere']['spielerMax'],
			'exclude'                 => true,
			'sorting'                 => false,
			'default'                 => 0,
			'inputType'               => 'text',
			'eval'                    => array
			(
				'rgxp'                => 'digit', 
				'mandatory'           => false, 
				'tl_class'            => 'w50', 
				'maxlength'           => 6
			),
			'sql'                     => "int(6) unsigned NOT NULL default 0"
		),
		'maxMeldungen' => array
		(
			'label'                   => &$GLOBALS['TL_LANG']['tl_fernschach_turniere']['maxMeldungen'],
			'exclude'                 => true,
			'sorting'                 => false,
			'default'                 => 1,
			'inputType'               => 'text',
			'eval'                    => array
			(
				'rgxp'                => 'digit',
				'mandatory'           => false,
				'tl_class'            => 'w50',
				'maxlength'           => 3
			),
			// Voreinstellung 1: Ein Spieler darf sich genau einmal melden. Beim
			// Anlegen der Spalte bekommen auch alle vorhandenen Turniere diesen
			// Wert — genau so gewollt, weil Mehrfachmeldungen bisher überall
			// möglich waren und unterbunden werden sollen.
			'sql'                     => "int(3) unsigned NOT NULL default 1"
		),
		'bretter' => array
		(
			'label'                   => &$GLOBALS['TL_LANG']['tl_fernschach_turniere']['bretter'],
			'exclude'                 => true,
			'sorting'                 => false,
			'default'                 => 4,
			'inputType'               => 'text',
			'eval'                    => array
			(
				'rgxp'                => 'digit', 
				'mandatory'           => false, 
				'tl_class'            => 'w50', 
				'maxlength'           => 4
			),
			'sql'                     => "int(4) unsigned NOT NULL default 4"
		),
		'spielerGeschlecht' => array
		(
			'label'                   => &$GLOBALS['TL_LANG']['tl_fernschach_turniere']['spielerGeschlecht'],
			'exclude'                 => true,
			'filter'                  => true,
			'inputType'               => 'select',
			'options'                 => &$GLOBALS['TL_LANG']['tl_fernschach_turniere']['spielerGeschlecht_options'],
			'eval'                    => array
			(
				'tl_class'            => 'w50',
				'includeBlankOption'  => true
			),
			'sql'                     => "varchar(1) NOT NULL default ''"
		),
		'spielerAlterMin' => array
		(
			'label'                   => &$GLOBALS['TL_LANG']['tl_fernschach_turniere']['spielerAlterMin'],
			'exclude'                 => true,
			'sorting'                 => false,
			'default'                 => 0,
			'inputType'               => 'text',
			'eval'                    => array
			(
				'rgxp'                => 'digit', 
				'mandatory'           => false, 
				'tl_class'            => 'w50', 
				'maxlength'           => 3
			),
			'sql'                     => "int(3) unsigned NOT NULL default 0"
		),
		'spielerAlterMax' => array
		(
			'label'                   => &$GLOBALS['TL_LANG']['tl_fernschach_turniere']['spielerAlterMax'],
			'exclude'                 => true,
			'sorting'                 => false,
			'default'                 => 0,
			'inputType'               => 'text',
			'eval'                    => array
			(
				'rgxp'                => 'digit', 
				'mandatory'           => false, 
				'tl_class'            => 'w50', 
				'maxlength'           => 3
			),
			'sql'                     => "int(3) unsigned NOT NULL default 0"
		),
		'onlineAnmeldung' => array
		(
			'label'                   => &$GLOBALS['TL_LANG']['tl_fernschach_turniere']['onlineAnmeldung'],
			'exclude'                 => true,
			'filter'                  => true,
			'default'                 => 1,
			'inputType'               => 'checkbox',
			'eval'                    => array
			(
				'tl_class'            => 'w50', 
				'isBoolean'           => true,
			),
			'sql'                     => "char(1) NOT NULL default '1'"
		),
		'onlineMeldestaende' => array
		(
			'label'                   => &$GLOBALS['TL_LANG']['tl_fernschach_turniere']['onlineMeldestaende'],
			'exclude'                 => true,
			'filter'                  => true,
			'default'                 => 1,
			'inputType'               => 'checkbox',
			'eval'                    => array
			(
				'tl_class'            => 'w50', 
				'isBoolean'           => true,
			),
			'sql'                     => "char(1) NOT NULL default '1'"
		),
		'versteckeNamen' => array
		(
			'label'                   => &$GLOBALS['TL_LANG']['tl_fernschach_turniere']['versteckeNamen'],
			'exclude'                 => true,
			'filter'                  => true,
			'inputType'               => 'checkbox',
			'eval'                    => array
			(
				'tl_class'            => 'w50', 
				'isBoolean'           => true,
			),
			'sql'                     => "char(1) NOT NULL default ''"
		),
		'turnierleiterName' => array
		(
			'label'                   => &$GLOBALS['TL_LANG']['tl_fernschach_turniere']['turnierleiterName'],
			'inputType'               => 'text',
			'exclude'                 => true,
			'filter'                  => true,
			'sorting'                 => true,
			'search'                  => true,
			'flag'                    => 1,
			'eval'                    => array
			(
				'mandatory'           => false, 
				'maxlength'           => 255, 
				'tl_class'            => 'w50'
			),
			'sql'                     => "varchar(255) NOT NULL default ''"
		),
		'turnierleiterEmail' => array
		(
			'label'                   => &$GLOBALS['TL_LANG']['tl_fernschach_turniere']['turnierleiterEmail'],
			'exclude'                 => true,
			'search'                  => true,
			'filter'                  => true,
			'inputType'               => 'text',
			'eval'                    => array
			(
				'mandatory'           => false,
				'maxlength'           => 255, 
				'rgxp'                => 'email', 
				'decodeEntities'      => true, 
				'tl_class'            => 'w50'
			),
			'sql'                     => "varchar(255) NOT NULL default ''"
		),
		'turnierleiterUserId' => array
		(
			'label'                   => &$GLOBALS['TL_LANG']['tl_fernschach_turniere']['turnierleiterUserId'],
			'exclude'                 => true,
			'inputType'               => 'select',
			'foreignKey'              => 'tl_user.CONCAT(username," (",name,")")',
			'relation'                => array('type'=>'belongsToMany', 'load'=>'lazy'),
			'eval'                    => array
			(
				'includeBlankOption'  => true,
				'chosen'              => true,
				'mandatory'           => false, 
				'tl_class'            => 'w50'
			),
			'sql'                     => "int(10) unsigned NOT NULL default '0'"
		),
		'turnierleiterInfo' => array
		(
			'label'                   => &$GLOBALS['TL_LANG']['tl_fernschach_turniere']['turnierleiterInfo'],
			'exclude'                 => true,
			'default'                 => 1,
			'inputType'               => 'checkbox',
			'eval'                    => array
			(
				'boolean'             => true,
				'tl_class'            => 'w50 m12'
			),
			'sql'                     => "char(1) NOT NULL default '1'"
		), 
		'bewerbungErlaubt' => array
		(
			'label'                   => &$GLOBALS['TL_LANG']['tl_fernschach_turniere']['bewerbungErlaubt'],
			'exclude'                 => true,
			'filter'                  => true,
			'default'                 => '',
			'inputType'               => 'checkbox',
			'eval'                    => array
			(
				'tl_class'            => 'w50', 
				'isBoolean'           => true,
				'submitOnChange'      => true
			),
			'sql'                     => "char(1) NOT NULL default ''"
		),
		// Gibt die Liste der Bewerbungen aus
		'applications' => array
		(
			'label'                   => &$GLOBALS['TL_LANG']['tl_fernschach_turniere']['applications'],
			'input_field_callback'    => array('tl_fernschach_turniere', 'getApplications'),
		),
		'applicationText' => array
		(
			'exclude'                 => true,
			'search'                  => true,
			'inputType'               => 'textarea',
			'eval'                    => array('mandatory'=>false, 'rte'=>'tinyMCE', 'helpwizard'=>true),
			'explanation'             => 'insertTags',
			'sql'                     => "mediumtext NULL"
		),
		'bewerbungen' => array
		(
			'label'                   => &$GLOBALS['TL_LANG']['tl_fernschach_turniere']['bewerbungen'],
		),
		'zusagen' => array
		(
			'label'                   => &$GLOBALS['TL_LANG']['tl_fernschach_turniere']['zusagen'],
		),
		'archived' => array
		(
			'label'                   => &$GLOBALS['TL_LANG']['tl_fernschach_turniere']['archived'],
			'exclude'                 => true,
			'filter'                  => true,
			'default'                 => '',
			'inputType'               => 'checkbox',
			'eval'                    => array
			(
				'boolean'             => true,
			),
			'sql'                     => "char(1) NOT NULL default ''"
		), 
		'published' => array
		(
			'label'                   => &$GLOBALS['TL_LANG']['tl_fernschach_turniere']['published'],
			'toggle'                  => true, // Aktiviert den Contao-eigenen Schnellschalter in der Übersicht
			'exclude'                 => true,
			'filter'                  => true,
			'default'                 => 1,
			'inputType'               => 'checkbox',
			'eval'                    => array
			(
				'doNotCopy'           => true,
				'boolean'             => true,
			),
			'sql'                     => "char(1) NOT NULL default '1'"
		), 
	)
);

/**
 * Class tl_fernschach_turniere
 *
 * Provide miscellaneous methods that are used by the data configuration array.
 * @copyright  Leo Feyer 2005-2013
 * @author     Leo Feyer <https://contao.org>
 * @package    Core
 */
class tl_fernschach_turniere extends Backend
{

	var $bewerbungen = array(); // Nimmt die Daten aus tl_fernschach_turniere_bewerbungen auf
	
	/**
	 * Import the back end user object
	 */
	public function __construct()
	{
		parent::__construct();
		$this->import(BackendUser::class, 'User');

		$objBewerbungen = Database::getInstance()->prepare("SELECT * FROM tl_fernschach_turniere_bewerbungen WHERE published=?")
		                                          ->execute(1);
		if($objBewerbungen->numRows)
		{
			while($objBewerbungen->next())
			{
				// Zähler-Array anlegen, wenn noch nicht vorhanden
				if(!isset($this->bewerbungen[$objBewerbungen->pid])) $this->bewerbungen[$objBewerbungen->pid] = array('anzahl' => 0, 'unklar' => 0, 'zusagen' => 0, 'absagen' => 0);
				// Zähler inkrementieren
				$this->bewerbungen[$objBewerbungen->pid]['anzahl']++;
				switch($objBewerbungen->state)
				{
					case 0: // ohne Entscheidung
						$this->bewerbungen[$objBewerbungen->pid]['unklar']++;
						break;
					case 1: // Zusage
						$this->bewerbungen[$objBewerbungen->pid]['zusagen']++;
						break;
					case 2: // Absage
						$this->bewerbungen[$objBewerbungen->pid]['absagen']++;
						break;
				}
			}
		}
		//print_r($this->bewerbungen);

	}

	/**
	 * Check permissions to edit table tl_page
	 *
	 * @throws AccessDeniedException
	 */
	public function checkPermission()
	{
		if ($this->User->isAdmin)
		{
			return;
		}

		$objSession = System::getContainer()->get('request_stack')->getSession();
		$session = $objSession->all();

		// Set the default page user and group
		//$GLOBALS['TL_DCA']['tl_page']['fields']['cuser']['default'] = (int) Config::get('defaultUser') ?: $this->User->id;
		//$GLOBALS['TL_DCA']['tl_page']['fields']['cgroup']['default'] = (int) Config::get('defaultGroup') ?: (int) $this->User->groups[0];

		// Restrict the page tree
		if (empty($this->User->fernschach_turnierzugriff) || !is_array($this->User->fernschach_turnierzugriff))
		{
			$root = array(0);
		}
		else
		{
			$root = $this->User->fernschach_turnierzugriff;
		}

		$GLOBALS['TL_DCA']['tl_fernschach_turniere']['list']['sorting']['rootPaste'] = false;
		$GLOBALS['TL_DCA']['tl_fernschach_turniere']['list']['sorting']['root'] = $root;
		$security = System::getContainer()->get('security.helper');

		return;
		
		// Set allowed page IDs (edit multiple)
		if (is_array($session['CURRENT']['IDS'] ?? null))
		{
			$edit_all = array();
			$delete_all = array();

			foreach ($session['CURRENT']['IDS'] as $id)
			{
				$objPage = Database::getInstance()->prepare("SELECT id, pid, type, includeChmod, chmod, cuser, cgroup FROM tl_page WHERE id=?")
										  ->limit(1)
										  ->execute($id);

				if ($objPage->numRows < 1 || !$security->isGranted(ContaoCorePermissions::USER_CAN_ACCESS_PAGE_TYPE, $objPage->type))
				{
					continue;
				}

				$row = $objPage->row();

				if ($security->isGranted(ContaoCorePermissions::USER_CAN_EDIT_PAGE, $row))
				{
					$edit_all[] = $id;
				}

				// Mounted pages cannot be deleted
				if ($security->isGranted(ContaoCorePermissions::USER_CAN_DELETE_PAGE, $row) && !$this->User->hasAccess($id, 'pagemounts'))
				{
					$delete_all[] = $id;
				}
			}

			$session['CURRENT']['IDS'] = (Input::get('act') == 'deleteAll') ? $delete_all : $edit_all;
		}

		// Set allowed clipboard IDs
		if (is_array($session['CLIPBOARD']['tl_page']['id'] ?? null))
		{
			$clipboard = array();

			foreach ($session['CLIPBOARD']['tl_page']['id'] as $id)
			{
				$objPage = Database::getInstance()->prepare("SELECT id, pid, type, includeChmod, chmod, cuser, cgroup FROM tl_page WHERE id=?")
										  ->limit(1)
										  ->execute($id);

				if ($objPage->numRows < 1 || !$security->isGranted(ContaoCorePermissions::USER_CAN_ACCESS_PAGE_TYPE, $objPage->type))
				{
					continue;
				}

				if ($security->isGranted(ContaoCorePermissions::USER_CAN_EDIT_PAGE_HIERARCHY, $objPage->row()))
				{
					$clipboard[] = $id;
				}
			}

			$session['CLIPBOARD']['tl_page']['id'] = $clipboard;
		}

		// Overwrite session
		$objSession->replace($session);

		// Check permissions to save and create new
		if (Input::get('act') == 'edit')
		{
			$objPage = Database::getInstance()->prepare("SELECT * FROM tl_page WHERE id=(SELECT pid FROM tl_page WHERE id=?)")
									  ->limit(1)
									  ->execute(Input::get('id'));

			if ($objPage->numRows && !$security->isGranted(ContaoCorePermissions::USER_CAN_EDIT_PAGE_HIERARCHY, $objPage->row()))
			{
				$GLOBALS['TL_DCA']['tl_page']['config']['closed'] = true;
			}
		}

		// Check current action
		if (Input::get('act') && Input::get('act') != 'paste')
		{
			$permission = null;
			$cid = Input::get('id');
			$ids = $cid ? array($cid) : array();

			// Set permission
			switch (Input::get('act'))
			{
				case 'edit':
				case 'toggle':
					$permission = ContaoCorePermissions::USER_CAN_EDIT_PAGE;
					break;

				case 'create':
				case 'copy':
				case 'copyAll':
				case 'cut':
				case 'cutAll':
					$permission = ContaoCorePermissions::USER_CAN_EDIT_PAGE_HIERARCHY;

					// Check the parent page in "paste into" mode
					if (Input::get('mode') == 2)
					{
						$ids[] = Input::get('pid');
					}
					// Check the parent's parent page in "paste after" mode
					else
					{
						$objPage = Database::getInstance()->prepare("SELECT pid FROM tl_page WHERE id=?")
												  ->limit(1)
												  ->execute(Input::get('pid'));

						$ids[] = $objPage->pid;
					}
					break;

				case 'delete':
					$permission = ContaoCorePermissions::USER_CAN_DELETE_PAGE;
					break;
			}

			// Check user permissions
			$pagemounts = array();

			// Get all allowed pages for the current user
			foreach ($this->User->pagemounts as $root)
			{
				if (Input::get('act') != 'delete')
				{
					$pagemounts[] = array($root);
				}

				$pagemounts[] = Database::getInstance()->getChildRecords($root, 'tl_page');
			}

			if (!empty($pagemounts))
			{
				$pagemounts = array_merge(...$pagemounts);
			}

			$pagemounts = array_unique($pagemounts);

			// Do not allow pasting after pages on the root level (pagemounts)
			if (Input::get('mode') == 1 && (Input::get('act') == 'cut' || Input::get('act') == 'cutAll') && in_array(Input::get('pid'), $this->eliminateNestedPages($this->User->pagemounts)))
			{
				throw new AccessDeniedException('Not enough permissions to paste page ID ' . Input::get('id') . ' after mounted page ID ' . Input::get('pid') . ' (root level).');
			}

			$error = false;

			// Check each page
			foreach ($ids as $i=>$id)
			{
				if (!in_array($id, $pagemounts))
				{
					System::getContainer()->get('monolog.logger.contao.error')->error('Page ID ' . $id . ' was not mounted');

					$error = true;
					break;
				}

				// Get the page object
				$objPage = PageModel::findById($id);

				if ($objPage === null)
				{
					continue;
				}

				// Check whether the current user is allowed to access the current page
				if (Input::get('act') != 'show' && ($permission === null || !$security->isGranted($permission, $objPage->row())))
				{
					$error = true;
					break;
				}

				// Check the type of the first page (not the following parent pages)
				// In "edit multiple" mode, $ids contains only the parent ID, therefore check $id != $_GET['pid'] (see #5620)
				if ($i == 0 && $id != Input::get('pid') && Input::get('act') != 'create' && !$security->isGranted(ContaoCorePermissions::USER_CAN_ACCESS_PAGE_TYPE, $objPage->type))
				{
					System::getContainer()->get('monolog.logger.contao.error')->error('Not enough permissions to  ' . Input::get('act') . ' ' . $objPage->type . ' pages');

					$error = true;
					break;
				}
			}

			// Redirect if there is an error
			if ($error)
			{
				throw new AccessDeniedException('Not enough permissions to ' . Input::get('act') . ' page ID ' . $cid . ' or paste after/into page ID ' . Input::get('pid') . '.');
			}
		}
	}

	/**
	 * Add the breadcrumb menu
	 */
	public function addBreadcrumb()
	{

		// Knoten in Session speichern
		if (isset($_GET['node']))
		{
			Scope::setBackendSessionValue('tl_fernschach_turniere_node', Input::get('node'));
			$this->redirect(preg_replace('/&node=[^&]*/', '', Environment::get('request')));
		}
		$cat = Scope::getBackendSessionValue('tl_fernschach_turniere_node');

		// Breadcrumb-Navigation erstellen
		$breadcrumb = array();
		if($cat) // Nur bei Unterkategorien
		{
			// Kategorienbaum einschränken
			$GLOBALS['TL_DCA']['tl_fernschach_turniere']['list']['sorting']['root'] = array($cat);
		
			// Infos zur aktuellen Kategorie laden
			$objActual = Database::getInstance()->prepare('SELECT * FROM tl_fernschach_turniere WHERE published = ? AND id = ?')
			                                     ->execute(1, $cat);
			$breadcrumb[] = '<img src="bundles/contaofernschach/images/ordner_gelb.png" width="18" height="18" alt=""> ' . $objActual->title;
			
			// Navigation vervollständigen
			$pid = $objActual->pid;
			while($pid > 0)
			{
				$objTemp = Database::getInstance()->prepare('SELECT * FROM tl_fernschach_turniere WHERE published = ? AND id = ?')
				                                   ->execute(1, $pid);
				$breadcrumb[] = '<img src="bundles/contaofernschach/images/ordner_gelb.png" width="18" height="18" alt=""> <a href="' . Controller::addToUrl('node='.$objTemp->id) . '" title="'.StringUtil::specialchars($GLOBALS['TL_LANG']['MSC']['selectNode']).'">' . $objTemp->title . '</a>';
				$pid = $objTemp->pid;
			}
			// Image::getHtml() ermittelt den Pfad zum Symbol des aktuellen
			// Backend-Themes selbst. Bis Version 2.1.0 stand hier die Konstante
			// TL_FILES_URL, die es in Contao 5 nicht mehr gibt — der Aufruf war
			// dort ein Fatal Error, sobald man einen Knoten geöffnet hat.
			$breadcrumb[] = Image::getHtml('pagemounts.svg', '', 'width="18" height="18"') . ' <a href="' . Controller::addToUrl('node=0') . '" title="'.StringUtil::specialchars($GLOBALS['TL_LANG']['MSC']['selectAllNodes']).'">' . $GLOBALS['TL_LANG']['MSC']['filterAll'] . '</a>';
		}
		$breadcrumb = array_reverse($breadcrumb);

		// Insert breadcrumb menu
		if($breadcrumb)
		{
			@$GLOBALS['TL_DCA']['tl_fernschach_turniere']['list']['sorting']['breadcrumb'] .= '
			<ul id="tl_breadcrumb">
				<li>' . implode(' &gt; </li><li>', $breadcrumb) . '</li>
			</ul>';
		}
	}

	/**
	 * Add an image to each page in the tree
	 *
	 * @param array         $row
	 * @param string        $label
	 * @param DataContainer $dc
	 * @param string        $imageAttribute
	 * @param boolean       $blnReturnImage
	 * @param boolean       $blnProtected
	 *
	 * @return string
	 */
	public function addIcon($row, $label, DataContainer $dc=null, $imageAttribute='', $blnReturnImage=false, $blnProtected=false)
	{
		if ($blnProtected)
		{
			$row['protected'] = true;
		}

		// Kategorie-Icon und Icon-Attribute zuweisen
		// turnier_hauptklasse.png / turnier_hauptklasse_inaktiv.png
		$image = $row['published'] ? 'bundles/contaofernschach/images/turnier_hauptklasse.png' : 'bundles/contaofernschach/images/turnier_hauptklasse_inaktiv.png';
		$imageAttribute = trim($imageAttribute . ' data-icon="bundles/contaofernschach/images/turnier_hauptklasse.png" data-icon-disabled="bundles/contaofernschach/images/turnier_hauptklasse_inaktiv.png"');

		// Return the image only
		if ($blnReturnImage)
		{
			return Image::getHtml($image, '', $imageAttribute);
		}

		// Markiere Root-Kategorien
		if($row['pid'] == '0')
		{
			// turnier_kategorie.png / turnier_kategorie_inaktiv.png
			$image = $row['published'] ? 'bundles/contaofernschach/images/turnier_kategorie.png' : 'bundles/contaofernschach/images/turnier_kategorie_inaktiv.png';
			$imageAttribute = trim($imageAttribute . ' data-icon="bundles/contaofernschach/images/turnier_kategorie.png" data-icon-disabled="bundles/contaofernschach/images/turnier_kategorie_inaktiv.png"');
			$label = '<strong>' . $label . '</strong>';
		}

		if($row['type'] == 'tournament')
		{
			// turnier_meldungen.png / turnier_meldungen_inaktiv.png
			$image = $row['published'] ? 'bundles/contaofernschach/images/turnier_meldungen.png' : 'bundles/contaofernschach/images/turnier_meldungen_inaktiv.png';
			$imageAttribute = trim($imageAttribute . ' data-icon="bundles/contaofernschach/images/turnier_meldungen.png" data-icon-disabled="bundles/contaofernschach/images/turnier_meldungen_inaktiv.png"');
		}

		if($row['type'] == 'group')
		{
			// turnier_gruppe.png / turnier_gruppe_inaktiv.png
			$image = $row['published'] ? 'bundles/contaofernschach/images/turnier_gruppe.png' : 'bundles/contaofernschach/images/turnier_gruppe_inaktiv.png';
			$imageAttribute = trim($imageAttribute . ' data-icon="bundles/contaofernschach/images/turnier_gruppe.png" data-icon-disabled="bundles/contaofernschach/images/turnier_gruppe_inaktiv.png"');
		}

		// Rückgabe der Zeile
		return Image::getHtml($image, '', $imageAttribute) . '<a href="' . Controller::addToUrl('node='.$row['id']) . '" title="'.StringUtil::specialchars($GLOBALS['TL_LANG']['MSC']['selectNode']).'"> ' . $label . '</a>'; 

	}

	public function getApplications(DataContainer $dc)
	{

		// Der Backend-Einstieg heißt seit Contao 4 "contao" und wird über den
		// Router ermittelt. Die frühere Fallunterscheidung über die Konstante
		// VERSION ist entfallen — es gibt sie in Contao 5 nicht mehr.
		$linkprefix = System::getContainer()->get('router')->generate('contao_backend');
		$imageEdit = Image::getHtml('edit.svg', 'Bewerbung des Mitglieds bearbeiten');

		$turnier_id = $dc->activeRecord->id;

		$objApplications = Database::getInstance()->prepare("SELECT m.id AS mitglied_id, m.nachname AS nachnameM, m.vorname AS vornameM, b.nachname AS nachname, b.vorname AS vorname, b.id AS bewerbung_id, b.applicationDate AS bewerbungsdatum, b.state AS status, b.promiseDate AS zusagedatum FROM tl_fernschach_turniere_bewerbungen AS b LEFT JOIN tl_fernschach_spieler AS m ON b.spielerId = m.id WHERE b.pid=?")
		                                           ->execute($turnier_id);
		$ausgabe = '<div class="long widget" style="margin-top:10px;">'; // Wichtig damit das Auf- und Zuklappen funktioniert
		$ausgabe .= '<table class="tl_listing showColumns">';
		$ausgabe .= '<tbody><tr>';
		$ausgabe .= '<th class="tl_folder_tlist">'.$GLOBALS['TL_LANG']['tl_fernschach_turniere']['name'][0].'</th>';
		$ausgabe .= '<th class="tl_folder_tlist">'.$GLOBALS['TL_LANG']['tl_fernschach_turniere']['applicationDate'][0].'</th>';
		$ausgabe .= '<th class="tl_folder_tlist">'.$GLOBALS['TL_LANG']['tl_fernschach_turniere']['state'][0].'</th>';
		$ausgabe .= '<th class="tl_folder_tlist">'.$GLOBALS['TL_LANG']['tl_fernschach_turniere']['promiseDate'][0].'</th>';
		$ausgabe .= '<th class="tl_folder_tlist tl_right_nowrap">&nbsp;</th>';
		$ausgabe .= '</tr>';
		$oddeven = 'odd';
		while($objApplications->next())
		{
			// Farbmarkierung festlegen
			if($objApplications->status == 0) $style = '';
			elseif($objApplications->status == 1) $style = ' style="color:green"';
			else $style = ' style="color:red"';
			$oddeven = $oddeven == 'odd' ? 'even' : 'odd';
			$ausgabe .= '<tr class="'.$oddeven.'" onmouseover="Theme.hoverRow(this,1)" onmouseout="Theme.hoverRow(this,0)">';
			if($objApplications->nachname || $objApplications->vorname)
			{
				$ausgabe .= '<td class="tl_file_list"'.$style.'>'.$objApplications->nachname.','.$objApplications->vorname.'</td>';
			}
			else
			{
				$ausgabe .= '<td class="tl_file_list"'.$style.'>'.$objApplications->nachnameM.','.$objApplications->vornameM.'</td>';
			}
			$ausgabe .= '<td class="tl_file_list"'.$style.'>'.($objApplications->bewerbungsdatum ? date('d.m.Y', $objApplications->bewerbungsdatum) : '-').'</td>';
			$ausgabe .= '<td class="tl_file_list"'.$style.'>'.($objApplications->status == 0 ? 'ohne Entscheidung' : ($objApplications->status == 1 ? 'Zusage' : 'Absage')).'</td>';
			$ausgabe .= '<td class="tl_file_list"'.$style.'>'.($objApplications->zusagedatum ? date('d.m.Y', $objApplications->zusagedatum) : '-').'</td>';
			$ausgabe .= '<td class="tl_file_list tl_right_nowrap">';
			$ausgabe .= '<a href="'.$linkprefix.'?do=fernschach-turniere&amp;table=tl_fernschach_turniere_bewerbungen&amp;act=edit&amp;id='.$objApplications->bewerbung_id.'&amp;popup=1&amp;rt='.Scope::getRequestToken().'" onclick="Backend.openModalIframe({\'width\':768,\'title\':\'Eintrag in Bewerbungen bearbeiten\',\'url\':this.href});return false">'.$imageEdit.'</a>';
			$ausgabe .= '</td>';
			$ausgabe .= '</tr>';
		}
		$ausgabe .= '</tbody></table>';
		$ausgabe .= '<p style="margin: 18px 0 0 5px;">'.$objApplications->numRows.' Bewerbung(en) gefunden</p>';
		$ausgabe .= '</div>';
		return $ausgabe;

	}

	/**
	 * Setzt die Uhrzeit eines Datums auf 0:00 Uhr.
	 *
	 * Contao speichert Datumsfelder als Zeitstempel. Ohne diese Umwandlung
	 * enthielte ein am Nachmittag gespeichertes Datum auch die Uhrzeit, und
	 * Vergleiche auf Tagesgrenzen gingen schief.
	 *
	 * @param int|string $value Der gespeicherte Zeitstempel; 0 oder leer bleibt unverändert
	 *
	 * @return int|string Der Zeitstempel zur Mitternacht desselben Tages
	 */
	public function loadDate($value)
	{
		if($value) return strtotime(date('Y-m-d', $value) . ' 00:00:00');
		else return '';
	}


	/**
	 * Liefert die an dieser Stelle im Baum zulässigen Turnierarten.
	 *
	 * Bis Version 2.0.0 hat diese Methode die unerwünschten Einträge mit unset()
	 * direkt aus $GLOBALS['TL_LANG'] entfernt. Damit waren sie für den Rest der
	 * Anfrage weg: Wurde die Auswahlliste einmal für einen Datensatz unterhalb
	 * eines Turniers aufgebaut, kannte Contao die Art "tournament" anschließend
	 * nicht mehr und zeigte sie als unbekannt an. Gearbeitet wird deshalb jetzt
	 * auf einer Kopie.
	 *
	 * @param DataContainer $dc Der Data Container des bearbeiteten Datensatzes
	 *
	 * @return array Turnierart => Beschriftung. Die Art, die der Datensatz
	 *               bereits hat, bleibt immer enthalten — sonst stünde beim
	 *               Bearbeiten eines Altbestands ein leeres Auswahlfeld da und
	 *               das Pflichtfeld ließe sich nicht mehr speichern
	 */
	public function getTypen(DataContainer $dc)
	{
		$arrAlle = $GLOBALS['TL_LANG']['tl_fernschach_turniere']['type_options'] ?? array();
		$arrErlaubt = self::getErlaubteTypen(self::getElternTyp($dc->id));

		$arrTypen = array_intersect_key($arrAlle, array_flip($arrErlaubt));

		// Den eigenen Wert immer anbieten
		$objDatensatz = Database::getInstance()->prepare("SELECT type FROM tl_fernschach_turniere WHERE id = ?")
		                                       ->execute($dc->id);

		if($objDatensatz->numRows && $objDatensatz->type && isset($arrAlle[$objDatensatz->type]))
		{
			$arrTypen[$objDatensatz->type] = $arrAlle[$objDatensatz->type];
		}

		return $arrTypen;
	}

	/**
	 * Ermittelt die Turnierart des übergeordneten Datensatzes.
	 *
	 * @param int|string|null $intId ID des Datensatzes, dessen Elternteil gesucht wird
	 *
	 * @return string Die Art des Elternteils, oder eine leere Zeichenkette, wenn
	 *                der Datensatz auf der obersten Ebene liegt, es ihn nicht
	 *                gibt oder das Elternteil verwaist ist
	 */
	public static function getElternTyp($intId)
	{
		if(!$intId)
		{
			return '';
		}

		$objDatensatz = Database::getInstance()->prepare("SELECT pid FROM tl_fernschach_turniere WHERE id = ?")
		                                       ->execute($intId);

		if(!$objDatensatz->numRows || !$objDatensatz->pid)
		{
			return '';
		}

		$objEltern = Database::getInstance()->prepare("SELECT type FROM tl_fernschach_turniere WHERE id = ?")
		                                    ->execute($objDatensatz->pid);

		return $objEltern->numRows ? (string) $objEltern->type : '';
	}

	/**
	 * Legt fest, welche Turnierarten unterhalb einer bestimmten Art erlaubt sind.
	 *
	 * Der Baum ist bewusst flach gehalten: Kategorien dürfen weitere Kategorien
	 * und Turniere aufnehmen, ein Turnier nur noch Gruppen, eine Gruppe gar
	 * nichts mehr. Insbesondere darf unter einem Turnier **kein** weiteres
	 * Turnier liegen — die Turnierauswahl im Frontend und die Meldelisten gehen
	 * davon aus, dass ein Turnier ein Blatt des Baumes ist.
	 *
	 * @param string $strElternTyp Die Art des übergeordneten Datensatzes; eine
	 *                             leere Zeichenkette steht für die oberste Ebene
	 *
	 * @return array Liste der erlaubten Arten, gegebenenfalls leer
	 */
	public static function getErlaubteTypen($strElternTyp)
	{
		switch($strElternTyp)
		{
			// Oberste Ebene: nur Kategorien
			case '':
				return array('category');

			case 'category':
				return array('category', 'tournament');

			case 'tournament':
				return array('group');

			// Unterhalb einer Gruppe geht es nicht weiter
			default:
				return array();
		}
	}

	/**
	 * Weist eine unzulässige Turnierart beim Speichern zurück.
	 *
	 * Die Auswahlliste allein reicht nicht: Ein Datensatz kann auch durch
	 * Kopieren oder Verschieben an eine Stelle geraten, an der seine Art nicht
	 * erlaubt ist. Genau so sind die Turniere unterhalb von Turnieren
	 * entstanden, die im Backend als unbekannte Art erschienen.
	 *
	 * Ein Altbestand, der bereits eine unzulässige Art hat, lässt sich weiterhin
	 * bearbeiten und speichern — sonst käme man an den Titel eines solchen
	 * Datensatzes gar nicht mehr heran. In dem Fall gibt es nur einen Hinweis.
	 *
	 * @param mixed         $varValue Die gewählte Turnierart
	 * @param DataContainer $dc       Der Data Container des Datensatzes
	 *
	 * @return mixed Die unveränderte Turnierart
	 *
	 * @throws \Exception Wenn die Art an dieser Stelle im Baum nicht erlaubt ist
	 *                    und der Datensatz sie noch nicht hatte
	 */
	public function pruefeTyp($varValue, DataContainer $dc)
	{
		$arrErlaubt = self::getErlaubteTypen(self::getElternTyp($dc->id));

		if(!$varValue || in_array($varValue, $arrErlaubt, true))
		{
			return $varValue;
		}

		$objDatensatz = Database::getInstance()->prepare("SELECT type FROM tl_fernschach_turniere WHERE id = ?")
		                                       ->execute($dc->id);

		// Unveränderter Altbestand: durchlassen, aber darauf hinweisen
		if($objDatensatz->numRows && $objDatensatz->type === $varValue)
		{
			Message::addInfo(sprintf($GLOBALS['TL_LANG']['tl_fernschach_turniere']['type_altbestand'] ?? 'Dieser Datensatz hat die an dieser Stelle unzulässige Turnierart "%s".', $varValue));

			return $varValue;
		}

		throw new \Exception(sprintf($GLOBALS['TL_LANG']['tl_fernschach_turniere']['type_nicht_erlaubt'] ?? 'Die Turnierart "%s" ist an dieser Stelle nicht erlaubt.', $varValue));
	}

	/**
	 * Return the paste page button
	 *
	 * @param DataContainer $dc
	 * @param array         $row
	 * @param string        $table
	 * @param boolean       $cr
	 * @param array         $arrClipboard
	 *
	 * @return string
	 */
	public function pasteTournament(DataContainer $dc, $row, $table, $cr, $arrClipboard=null)
	{
		$disablePA = false;
		$disablePI = false;

		if(isset($row['type']) && $row['type'] == 'group')
		{
			$disablePI = true;
		}

		$return = '';

		if(!isset($GLOBALS['TL_LANG'][$table]['pasteafter'])) $GLOBALS['TL_LANG'][$table]['pasteafter'] = array('', '');
		if(!isset($GLOBALS['TL_LANG'][$table]['pasteinto'])) $GLOBALS['TL_LANG'][$table]['pasteinto'] = array('', '');

		// Return the buttons

		$imagePasteAfter = Image::getHtml('pasteafter.svg', sprintf($GLOBALS['TL_LANG'][$table]['pasteafter'][1], $row['id']));
		$imagePasteInto = Image::getHtml('pasteinto.svg', sprintf($GLOBALS['TL_LANG'][$table]['pasteinto'][1], $row['id']));

		if ($row['id'] > 0)
		{
			$return = $disablePA ? Image::getHtml('pasteafter_.svg') . ' ' : '<a href="' . $this->addToUrl('act=' . $arrClipboard['mode'] . '&amp;mode=1&amp;pid=' . $row['id'] . (!is_array($arrClipboard['id']) ? '&amp;id=' . $arrClipboard['id'] : '')) . '" title="' . StringUtil::specialchars(sprintf($GLOBALS['TL_LANG'][$table]['pasteafter'][1], $row['id'])) . '" onclick="Backend.getScrollOffset()">' . $imagePasteAfter . '</a> ';
		}

		return $return . ($disablePI ? Image::getHtml('pasteinto_.svg') . ' ' : '<a href="' . $this->addToUrl('act=' . $arrClipboard['mode'] . '&amp;mode=2&amp;pid=' . $row['id'] . (!is_array($arrClipboard['id']) ? '&amp;id=' . $arrClipboard['id'] : '')) . '" title="' . StringUtil::specialchars(sprintf($GLOBALS['TL_LANG'][$table]['pasteinto'][$row['id'] > 0 ? 1 : 0], $row['id'])) . '" onclick="Backend.getScrollOffset()">' . $imagePasteInto . '</a> ');
	}

	/**
	 * Gibt den Button für die Bearbeitung der Bewerbungen zurück
	 *
	 * @param mixed $row
	 * @param mixed $href
	 * @param mixed $label
	 * @param mixed $title
	 * @param mixed $icon
	 * @param mixed $attributes
	 *
	 * @return string
	 */
	public function bewerbungenIcon($row, $href, $label, $title, $icon, $attributes)
	{

		if($row['bewerbungErlaubt'] && $row['type'] == 'tournament')
		{
			// Bewerbungen können bearbeitet werden
			return '<a href="'.$this->addToUrl($href).'&id='.$row["id"].'" title="'.StringUtil::specialchars($title).'"'.$attributes.'>'.Image::getHtml($icon, $label).'</a> '; 
		}
		else
		{
			// Keine Bearbeitung von Bewerbungen
			$icon = 'bundles/contaofernschach/images/turnier_bewerbungen_inaktiv.png';
			$title = 'Keine Bewerbungen möglich bei diesem Eintrag';
			return '<span>'.Image::getHtml($icon, $label).'</span> ';
		}

	}

	/**
	 * Gibt den Button für die Bearbeitung der Bewerbungen zurück
	 *
	 * @param mixed $row
	 * @param mixed $href
	 * @param mixed $label
	 * @param mixed $title
	 * @param mixed $icon
	 * @param mixed $attributes
	 *
	 * @return string
	 */
	public function infoBewerbungen($row, $href, $label, $title, $icon, $attributes)
	{

		if($row['bewerbungErlaubt'] && $row['type'] == 'tournament')
		{
			// Bewerbungen können bearbeitet werden, deshalb Anzahl Bewerbungen anzeigen
			$temp = '';
			if(isset($this->bewerbungen[$row['id']]))
			{
				$temp = '<span style="color:#9F9F9F;">Bewerbungen: <b>'.$this->bewerbungen[$row['id']]['anzahl'].'</b> [';
				if($this->bewerbungen[$row['id']]['unklar']) $temp .= '<span title="Anzahl der nicht geklärten Bewerbungen">'.$this->bewerbungen[$row['id']]['unklar'].Image::getHtml('bundles/contaofernschach/images/fragezeichen.png', 'ohne Entscheidung', 'width="12" height="12"').'</span> ';
				if($this->bewerbungen[$row['id']]['zusagen']) $temp .= '<span title="Anzahl der Zusagen">'.$this->bewerbungen[$row['id']]['zusagen'].Image::getHtml('bundles/contaofernschach/images/ja.png', 'Zusagen', 'width="12" height="12"').'</span> ';
				if($this->bewerbungen[$row['id']]['absagen']) $temp .= '<span title="Anzahl der Absagen">'.$this->bewerbungen[$row['id']]['absagen'].Image::getHtml('bundles/contaofernschach/images/nein.png', 'Absagen', 'width="12" height="12"').'</span> ';
				$temp = rtrim($temp).']</span><span style="width:20px; display:inline-block;"></span>';
			}
			return $temp;
		}
		else
		{
			// Keine Bearbeitung von Bewerbungen
			return '';
		}

	}

	/**
	 * Gibt den Button für die Bearbeitung der Meldungen zurück
	 *
	 * @param mixed $row
	 * @param mixed $href
	 * @param mixed $label
	 * @param mixed $title
	 * @param mixed $icon
	 * @param mixed $attributes
	 *
	 * @return string
	 */
	public function meldungenIcon($row, $href, $label, $title, $icon, $attributes)
	{
		if($row['type'] == 'category' || $row['type'] == 'group')
		{
			// Keine Bearbeitung von Meldungen bei Kategorien und Gruppen
			$icon = 'bundles/contaofernschach/images/turnier_meldungen_inaktiv.png';
			$title = 'Keine Meldungen möglich bei diesem Eintrag';
			return '<span title="'.StringUtil::specialchars($title).'">'.Image::getHtml($icon, $label).'</span> ';
		}

		// Bei einem Mannschaftsturnier melden nicht einzelne Spieler, sondern der
		// Mannschaftsleiter meldet eine Mannschaft; das steht in den beiden
		// Mannschaftstabellen und nicht in den Anmeldungen. Die Schaltfläche wird
		// deshalb abgeblendet — aber nur, solange dort wirklich nichts liegt.
		// Aus der Zeit vor den Mannschaftstabellen gibt es Turniere mit
		// Altbestand, und den darf das Abblenden nicht unerreichbar machen.
		if($row['typ'] == 'm')
		{
			$objMeldungen = Database::getInstance()->prepare('SELECT COUNT(*) AS anzahl FROM tl_fernschach_turniere_meldungen WHERE pid = ?')
			                                        ->execute($row['id']);

			if(!$objMeldungen->anzahl)
			{
				$icon = 'bundles/contaofernschach/images/turnier_meldungen_inaktiv.png';
				$title = 'Bei einem Mannschaftsturnier stehen die Meldungen unter Mannschaften bearbeiten';

				return '<span title="'.StringUtil::specialchars($title).'">'.Image::getHtml($icon, $label).'</span> ';
			}

			$title = $objMeldungen->anzahl.' Anmeldung(en) aus der Zeit vor den Mannschaftstabellen bearbeiten';
		}

		// Meldungen können bearbeitet werden
		return '<a href="'.$this->addToUrl($href).'&id='.$row["id"].'" title="'.StringUtil::specialchars($title).'"'.$attributes.'>'.Image::getHtml($icon, $label).'</a> ';
	}

	/**
	 * Gibt die Schaltfläche für die gemeldeten Mannschaften zurück.
	 *
	 * Angeboten wird sie nur bei Turnieren vom Typ „Mannschaftsturnier" — bei
	 * Kategorien, Gruppen und Einzelturnieren gibt es keine Mannschaften, dort
	 * erscheint das Symbol abgeblendet.
	 *
	 * @param array  $row        Der Turnierdatensatz
	 * @param string $href       Das Ziel aus der DCA-Definition
	 * @param string $label      Beschriftung der Schaltfläche
	 * @param string $title      Titel für das title-Attribut
	 * @param string $icon       Pfad des Symbols
	 * @param string $attributes Zusätzliche Attribute aus der DCA-Definition
	 *
	 * @return string Der fertige Verweis, oder ein abgeblendetes Symbol ohne Ziel
	 */
	public function mannschaftenIcon($row, $href, $label, $title, $icon, $attributes)
	{
		if ($row['type'] != 'tournament' || $row['typ'] != 'm')
		{
			$icon = 'bundles/contaofernschach/images/turnier_mannschaften_inaktiv.png';
			$title = 'Mannschaften gibt es nur bei Turnieren vom Typ Mannschaftsturnier';

			// Der Titel gehört an das span. Ohne ihn erfährt niemand, wofür das
			// abgeblendete Symbol steht — auch nicht beim Überfahren mit der Maus.
			return '<span title="'.StringUtil::specialchars($title).'">'.Image::getHtml($icon, $label).'</span> ';
		}

		return '<a href="'.$this->addToUrl($href).'&id='.$row['id'].'" title="'.StringUtil::specialchars($title).'"'.$attributes.'>'.Image::getHtml($icon, $label).'</a> ';
	}

	/**
	 * Gibt den Button für die Bearbeitung der Spieler zurück
	 *
	 * @param mixed $row
	 * @param mixed $href
	 * @param mixed $label
	 * @param mixed $title
	 * @param mixed $icon
	 * @param mixed $attributes
	 *
	 * @return string
	 */
	public function spielerIcon($row, $href, $label, $title, $icon, $attributes)
	{

		if($row['type'] == 'category' || $row['type'] == 'tournament')
		{
			// Keine Bearbeitung von Spielern bei Kategorien und meldefähigen Turnieren
			$icon = 'bundles/contaofernschach/images/turnier_gruppe_inaktiv.png';
			$title = 'Keine Spieler möglich bei diesem Eintrag';
			return '<span>'.Image::getHtml($icon, $label).'</span> ';
		}
		else
		{
			// Spieler können bearbeitet werden
			return '<a href="'.$this->addToUrl($href).'&id='.$row["id"].'" title="'.StringUtil::specialchars($title).'"'.$attributes.'>'.Image::getHtml($icon, $label).'</a> '; 
		}

	}

	/**
	 * Zugeordnetes Nenngeld anzeigen
	 * @param DataContainer $dc
	 *
	 * @return string HTML-Code
	 */
	public function getNenngeld(DataContainer $dc)
	{
		if($dc->activeRecord->id)
		{
			$daten = \Schachbulle\ContaoFernschachBundle\Classes\Turnier::getNenngeld($dc->activeRecord->id);

			if($daten && $daten['parent'])
			{
				return '
				<div class="tl_listing_container list_view" id="tl_listing">
					<table class="tl_listing">
						<tbody>
							<tr class="even click2edit toggle_select hover-row">
								<td class="tl_file_list" width="50%">Nenngeld übergeordnet festgelegt:</td>
								<td class="tl_file_list">'.$daten['amount'].' €</td>
							</tr>
							<tr class="even click2edit toggle_select hover-row">
								<td class="tl_file_list" width="50%">im Turnier:</td>
								<td class="tl_file_list">'.$daten['name'].'</td>
							</tr>
						</tbody>
					</table>
				</div>';
			}
			else
			{
				return '';
			}
			
		}

	}

}

