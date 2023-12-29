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
 * Table tl_fernschach_spieler_titel
 */
$GLOBALS['TL_DCA']['tl_fernschach_spieler_titel'] = array
(

	// Config
	'config' => array
	(
		'dataContainer'               => 'Table',
		'ptable'                      => 'tl_fernschach_spieler',
		'enableVersioning'            => true,
		//'onload_callback'             => array
		//(
		//	array('tl_fernschach_spieler_titel', 'checkPermission'),
		//),
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
			'fields'                  => array('datum'),
			'flag'                    => 12,
			'headerFields'            => array('nachname', 'vorname', 'memberId'),
			'panelLayout'             => 'filter;sort;search,limit',
			'child_record_callback'   => array('tl_fernschach_spieler_titel', 'listTitel'),
			'disableGrouping'         => true
		),
		'label' => array
		(
			'fields'                  => array('titel'),
			'format'                  => '%s',
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
				'label'               => &$GLOBALS['TL_LANG']['tl_fernschach_spieler_titel']['edit'],
				'href'                => 'act=edit',
				'icon'                => 'edit.svg',
				//'button_callback'     => array('tl_fernschach_spieler_titel', 'generateEditButton')
			),
			'copy' => array
			(
				'label'               => &$GLOBALS['TL_LANG']['tl_fernschach_spieler_titel']['copy'],
				'href'                => 'act=copy',
				'icon'                => 'copy.svg',
				//'button_callback'     => array('tl_fernschach_spieler_titel', 'generateCopyButton')
			),
			'delete' => array
			(
				'label'               => &$GLOBALS['TL_LANG']['tl_fernschach_spieler_titel']['delete'],
				'href'                => 'act=delete',
				'icon'                => 'delete.svg',
				'attributes'          => 'onclick="if(!confirm(\'' . $GLOBALS['TL_LANG']['MSC']['deleteConfirm'] . '\'))return false;Backend.getScrollOffset()"',
				//'button_callback'     => array('tl_fernschach_spieler_titel', 'generateDeleteButton')
			),
			'toggle' => array
			(
				'label'               => &$GLOBALS['TL_LANG']['tl_fernschach_spieler_titel']['toggle'],
				'attributes'          => 'onclick="Backend.getScrollOffset()"',
				'haste_ajax_operation' => array
				(
					'field'           => 'published',
					'options'         => array
					(
						array('value' => '', 'icon' => 'invisible.svg'),
						array('value' => '1', 'icon' => 'visible.svg'),
					),
				),
			),
			'show' => array
			(
				'label'               => &$GLOBALS['TL_LANG']['tl_fernschach_spieler_titel']['show'],
				'href'                => 'act=show',
				'icon'                => 'bundles/contaofernschach/images/show.svg',
			),
		)
	),

	// Palettes
	'palettes' => array
	(
		'default'                     => '{titel_legend},datum,titel;{publish_legend},published'
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
			'foreignKey'              => 'tl_fernschach_spieler.id',
			'sql'                     => "int(10) unsigned NOT NULL default '0'",
			'relation'                => array('type'=>'belongsTo', 'load'=>'eager')
		),
		'tstamp' => array
		(
			'flag'                    => 6,
			'sql'                     => "int(10) unsigned NOT NULL default '0'"
		),
		'datum' => array
		(
			'label'                   => &$GLOBALS['TL_LANG']['tl_fernschach_spieler_titel']['datum'],
			'exclude'                 => true,
			'search'                  => false,
			'sorting'                 => true,
			'flag'                    => 12,
			'inputType'               => 'text',
			'eval'                    => array
			(
				'maxlength'           => 10,
				'tl_class'            => 'w50',
				'rgxp'                => 'alnum'
			),
			'load_callback'           => array
			(
				array('\Schachbulle\ContaoHelperBundle\Classes\Helper', 'getDate')
			),
			'save_callback' => array
			(
				array('\Schachbulle\ContaoHelperBundle\Classes\Helper', 'putDate')
			),
			'sql'                     => "int(8) unsigned NOT NULL default '0'"
		),
		'titel' => array
		(
			'label'                   => &$GLOBALS['TL_LANG']['tl_fernschach_spieler_titel']['titel'],
			'inputType'               => 'select',
			'options'                 => array('FSGM', 'SIM', 'FSIM', 'CCM', 'CCE', 'LGM', 'LIM', 'GM', 'IM', 'FM', 'CM', 'WGM', 'WIM', 'WFM', 'WCM'),
			'reference'               => &$GLOBALS['TL_LANG']['tl_fernschach_spieler_titel']['titel_options'],
			'exclude'                 => true,
			'sorting'                 => true,
			'flag'                    => 1,
			'filter'                  => true,
			'search'                  => true,
			'eval'                    => array
			(
				'includeBlankOption'  => true,
				'mandatory'           => false,
				'tl_class'            => 'w50',
			),
			'sql'                     => "varchar(4) NOT NULL default ''"
		),
		'published' => array
		(
			'label'                   => &$GLOBALS['TL_LANG']['tl_fernschach_spieler_titel']['published'],
			'exclude'                 => true,
			'inputType'               => 'checkbox',
			'default'                 => 1,
			'filter'                  => true,
			'eval'                    => array('tl_class' => 'w50','isBoolean' => true),
			'sql'                     => "char(1) NOT NULL default '1'"
		),
	)
);


/**
 * Class tl_fernschach_spieler_titel
 *
 * Provide miscellaneous methods that are used by the data configuration array.
 * @copyright  Leo Feyer 2005-2014
 * @author     Leo Feyer <https://contao.org>
 * @package    News
 */
class tl_fernschach_spieler_titel extends \Backend
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
	 * Prüfe Zugangsrechte für tl_fernschach_spieler_titel
	 *
	 * @throws AccessDeniedException
	 */
	public function checkPermission()
	{
		if($this->User->isAdmin)
		{
			return;
		}

		// Zugriff auf globale Operationen prüfen
		if(!$this->User->hasAccess('import', 'fernschach_konto')) unset($GLOBALS['TL_DCA']['tl_fernschach_spieler_titel']['list']['global_operations']['importBuchungen']);
		if(!$this->User->hasAccess('all', 'fernschach_konto')) unset($GLOBALS['TL_DCA']['tl_fernschach_spieler_titel']['list']['global_operations']['all']);
		if(!$this->User->hasAccess('create', 'fernschach_konto')) $GLOBALS['TL_DCA']['tl_fernschach_spieler_titel']['config']['closed'] = true;

		// Aktuelle Aktion von act prüfen
		switch (Input::get('act'))
		{
			case 'create': // Buchung anlegen
				if(!$this->User->hasAccess('create', 'fernschach_konto'))
				{
					$this->log('Fernschach-Verwaltung: Keine Rechte, um eine neue Buchung anzulegen.', __METHOD__, TL_ERROR);
					$this->redirect('contao/main.php?act=error');
				}
				break;

			case 'copy': // Buchung kopieren
				if(!$this->User->hasAccess('copy', 'fernschach_konto'))
				{
					$this->log('Fernschach-Verwaltung: Keine Rechte, um eine Buchung zu kopieren.', __METHOD__, TL_ERROR);
					$this->redirect('contao/main.php?act=error');
				}
				break;

			case 'toggle': // Buchung aktivieren/deaktivieren
				if(!$this->User->hasAccess('toggle', 'fernschach_konto'))
				{
					$this->log('Fernschach-Verwaltung: Keine Rechte, um eine Buchung zu (de)aktivieren.', __METHOD__, TL_ERROR);
					$this->redirect('contao/main.php?act=error');
				}
				break;

			case 'show': // Infobox
				if(!$this->User->hasAccess('show', 'fernschach_konto'))
				{
					$this->log('Fernschach-Verwaltung: Keine Rechte, um eine Buchung-Infobox anzuzeigen.', __METHOD__, TL_ERROR);
					$this->redirect('contao/main.php?act=error');
				}
				break;

			case 'edit': // Buchung bearbeiten
				if(!$this->User->hasAccess('edit', 'fernschach_konto'))
				{
					$this->log('Fernschach-Verwaltung: Keine Rechte, um eine Buchung zu bearbeiten.', __METHOD__, TL_ERROR);
					$this->redirect('contao/main.php?act=error');
				}
				break;

			default:

				// Aktuelle Aktion von key prüfen
				switch (Input::get('key'))
				{
					case 'import': // Buchungen importieren
						if(!$this->User->hasAccess('import', 'fernschach_konto'))
						{
							$this->log('Fernschach-Verwaltung: Keine Rechte, um Buchungen zu importieren.', __METHOD__, TL_ERROR);
							$this->redirect('contao/main.php?act=error');
						}
						break;

					default:
				}
		}

	}

	/**
	 * Datensätze auflisten
	 * @param array
	 * @return string
	 */
	public function listTitel($arrRow)
	{
		$temp = \Schachbulle\ContaoHelperBundle\Classes\Helper::getDate($arrRow['datum']);
		$temp .= ' '.@$GLOBALS['TL_LANG']['tl_fernschach_spieler_titel']['titel_options'][$arrRow['titel']];
		return $temp;
	}

}