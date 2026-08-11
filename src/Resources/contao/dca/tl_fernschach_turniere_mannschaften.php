<?php

/**
 * Fernschach-Verwaltung für Contao Open Source CMS
 *
 * @author    Frank Hoppe
 * @license   LGPL-3.0-or-later
 */

use Contao\Backend;
use Contao\BackendUser;
use Contao\DC_Table;
use Contao\DataContainer;
use Contao\Database;
use Contao\Image;
use Contao\StringUtil;
use Schachbulle\ContaoFernschachBundle\Classes\Helper;

/**
 * Table tl_fernschach_turniere_mannschaften
 *
 * Eine gemeldete Mannschaft. Untertabelle der Turniere, mit der Aufstellung in
 * tl_fernschach_turniere_mannschaften_spieler als eigener Untertabelle — die
 * Zahl der Bretter ist je Turnier verschieden und lässt sich nicht in feste
 * Spalten gießen.
 */
$GLOBALS['TL_DCA']['tl_fernschach_turniere_mannschaften'] = array
(

	// Config
	'config' => array
	(
		'dataContainer'               => DC_Table::class,
		'ptable'                      => 'tl_fernschach_turniere',
		'ctable'                      => array('tl_fernschach_turniere_mannschaften_spieler'),
		'enableVersioning'            => true,
		'ondelete_callback'           => array
		(
			array('tl_fernschach_turniere_mannschaften', 'loescheNenngeldsaetze')
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
			'fields'                  => array('mannschaftsname'),
			'flag'                    => 1,
			'headerFields'            => array('title', 'registrationDate', 'startDate'),
			'panelLayout'             => 'filter;sort;search,limit',
			'child_record_callback'   => array('tl_fernschach_turniere_mannschaften', 'listMannschaften'),
			'disableGrouping'         => true
		),
		// Ohne diesen Block wirft Contao 5 in vsprintf einen ValueError, sobald
		// die Elternansicht geöffnet wird.
		'label' => array
		(
			'fields'                  => array('mannschaftsname'),
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
				'label'               => &$GLOBALS['TL_LANG']['tl_fernschach_turniere_mannschaften']['edit'],
				'href'                => 'table=tl_fernschach_turniere_mannschaften_spieler',
				'icon'                => 'edit.gif',
			),
			'editheader' => array
			(
				'label'               => &$GLOBALS['TL_LANG']['tl_fernschach_turniere_mannschaften']['editheader'],
				'href'                => 'act=edit',
				'icon'                => 'header.gif',
			),
			'delete' => array
			(
				'label'               => &$GLOBALS['TL_LANG']['tl_fernschach_turniere_mannschaften']['delete'],
				'href'                => 'act=delete',
				'icon'                => 'delete.gif',
				'attributes'          => 'onclick="if(!confirm(\'' . ($GLOBALS['TL_LANG']['MSC']['deleteConfirm'] ?? null).' '.($GLOBALS['TL_LANG']['tl_fernschach_turniere_mannschaften']['deleteConfirm'] ?? null) . '\'))return false;Backend.getScrollOffset()"',
			),
			'toggle' => array
			(
				'label'               => &$GLOBALS['TL_LANG']['tl_fernschach_turniere_mannschaften']['toggle'],
				'attributes'          => 'onclick="Backend.getScrollOffset()"',
				'href'                => 'act=toggle&amp;field=published',
				'icon'                => 'visible.gif',
			),
			'show' => array
			(
				'label'               => &$GLOBALS['TL_LANG']['tl_fernschach_turniere_mannschaften']['show'],
				'href'                => 'act=show',
				'icon'                => 'show.gif',
			)
		)
	),

	// Palettes
	'palettes' => array
	(
		'default'                     => '{mannschaft_legend},vereinsname,vereinsnameAlt,mannschaftsname;{fuehrer_legend},spielerId,memberId,email;{meldung_legend},meldungDatum,nenngeld,bemerkungen;{publish_legend},published'
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
			'foreignKey'              => 'tl_fernschach_turniere.title',
			'sql'                     => "int(10) unsigned NOT NULL default '0'",
			'relation'                => array('type' => 'belongsTo', 'load' => 'eager')
		),
		'tstamp' => array
		(
			'sql'                     => "int(10) unsigned NOT NULL default '0'"
		),
		'vereinsname' => array
		(
			'label'                   => &$GLOBALS['TL_LANG']['tl_fernschach_turniere_mannschaften']['vereinsname'],
			'exclude'                 => true,
			'search'                  => true,
			'sorting'                 => true,
			'inputType'               => 'text',
			'eval'                    => array('mandatory' => true, 'maxlength' => 255, 'tl_class' => 'w50'),
			'sql'                     => "varchar(255) NOT NULL default ''"
		),
		'vereinsnameAlt' => array
		(
			'label'                   => &$GLOBALS['TL_LANG']['tl_fernschach_turniere_mannschaften']['vereinsnameAlt'],
			'exclude'                 => true,
			'search'                  => true,
			'inputType'               => 'text',
			'eval'                    => array('maxlength' => 255, 'tl_class' => 'w50'),
			'sql'                     => "varchar(255) NOT NULL default ''"
		),
		'mannschaftsname' => array
		(
			'label'                   => &$GLOBALS['TL_LANG']['tl_fernschach_turniere_mannschaften']['mannschaftsname'],
			'exclude'                 => true,
			'search'                  => true,
			'sorting'                 => true,
			'flag'                    => 1,
			'inputType'               => 'text',
			'eval'                    => array('mandatory' => true, 'maxlength' => 255, 'tl_class' => 'w50'),
			'sql'                     => "varchar(255) NOT NULL default ''"
		),
		'spielerId' => array
		(
			'label'                   => &$GLOBALS['TL_LANG']['tl_fernschach_turniere_mannschaften']['spielerId'],
			'exclude'                 => true,
			'search'                  => true,
			'inputType'               => 'select',
			'options_callback'        => array('tl_fernschach_turniere_mannschaften', 'getSpieler'),
			'eval'                    => array('includeBlankOption' => true, 'chosen' => true, 'tl_class' => 'w50'),
			'sql'                     => "int(10) unsigned NOT NULL default '0'"
		),
		'memberId' => array
		(
			'label'                   => &$GLOBALS['TL_LANG']['tl_fernschach_turniere_mannschaften']['memberId'],
			'exclude'                 => true,
			'search'                  => true,
			'inputType'               => 'text',
			'eval'                    => array('rgxp' => 'digit', 'maxlength' => 10, 'tl_class' => 'w50'),
			'sql'                     => "int(10) unsigned NOT NULL default '0'"
		),
		'email' => array
		(
			'label'                   => &$GLOBALS['TL_LANG']['tl_fernschach_turniere_mannschaften']['email'],
			'exclude'                 => true,
			'search'                  => true,
			'inputType'               => 'text',
			'eval'                    => array('rgxp' => 'email', 'maxlength' => 255, 'tl_class' => 'w50'),
			'sql'                     => "varchar(255) NOT NULL default ''"
		),
		'meldungDatum' => array
		(
			'label'                   => &$GLOBALS['TL_LANG']['tl_fernschach_turniere_mannschaften']['meldungDatum'],
			'exclude'                 => true,
			'sorting'                 => true,
			'flag'                    => 8,
			'inputType'               => 'text',
			'eval'                    => array('rgxp' => 'datim', 'datepicker' => true, 'tl_class' => 'w50 wizard'),
			'sql'                     => "int(10) unsigned NOT NULL default '0'"
		),
		'nenngeld' => array
		(
			'label'                   => &$GLOBALS['TL_LANG']['tl_fernschach_turniere_mannschaften']['nenngeld'],
			'exclude'                 => true,
			'inputType'               => 'text',
			'eval'                    => array('maxlength' => 10, 'tl_class' => 'w50'),
			'sql'                     => "decimal(9,2) NOT NULL default '0.00'"
		),
		'bemerkungen' => array
		(
			'label'                   => &$GLOBALS['TL_LANG']['tl_fernschach_turniere_mannschaften']['bemerkungen'],
			'exclude'                 => true,
			'search'                  => true,
			'inputType'               => 'textarea',
			'eval'                    => array('tl_class' => 'clr'),
			'sql'                     => "text NULL"
		),
		'published' => array
		(
			'label'                   => &$GLOBALS['TL_LANG']['tl_fernschach_turniere_mannschaften']['published'],
			'exclude'                 => true,
			'filter'                  => true,
			'inputType'               => 'checkbox',
			'eval'                    => array('doNotCopy' => true),
			'toggle'                  => true,
			'sql'                     => "char(1) NOT NULL default ''"
		)
	)
);

/**
 * Rückrufe der Tabelle tl_fernschach_turniere_mannschaften.
 */
class tl_fernschach_turniere_mannschaften extends Backend
{
	/**
	 * Lädt das Backend-Benutzerobjekt, das für die Rechteprüfung gebraucht wird.
	 */
	public function __construct()
	{
		parent::__construct();
		$this->import(BackendUser::class, 'User');
	}

	/**
	 * Baut die Zeile einer Mannschaft in der Elternansicht.
	 *
	 * Gezeigt werden Mannschaftsbezeichnung, Verein, Mannschaftsleiter und die
	 * Zahl der eingetragenen Bretter — damit auf einen Blick zu sehen ist, ob
	 * eine Aufstellung vollständig ist.
	 *
	 * @param array $arrRow Der Datensatz der Mannschaft
	 *
	 * @return string Die fertige HTML-Zeile
	 */
	public function listMannschaften($arrRow)
	{
		$objSpieler = Database::getInstance()->prepare('SELECT COUNT(*) AS anzahl FROM tl_fernschach_turniere_mannschaften_spieler WHERE pid = ?')
		                                      ->execute($arrRow['id']);

		$strFuehrer = '';

		if ($arrRow['spielerId'])
		{
			$objFuehrer = Helper::getSpielerdatensatz($arrRow['spielerId']);

			if ($objFuehrer && $objFuehrer->numRows)
			{
				$strFuehrer = $objFuehrer->nachname.', '.$objFuehrer->vorname;
			}
		}

		$strTemp = '<div class="tl_content_left">';
		$strTemp .= '<b>'.StringUtil::specialchars($arrRow['mannschaftsname']).'</b>';
		$strTemp .= ' <span style="color:#999">('.StringUtil::specialchars($arrRow['vereinsname']).')</span>';
		$strTemp .= '<br><span style="color:#999">';
		$strTemp .= 'Mannschaftsleiter: '.StringUtil::specialchars($strFuehrer ?: '—');
		$strTemp .= ' | Bretter: '.$objSpieler->anzahl;

		if ($arrRow['meldungDatum'])
		{
			$strTemp .= ' | gemeldet am '.date('d.m.Y H:i', (int) $arrRow['meldungDatum']);
		}

		$strTemp .= '</span></div>';

		return $strTemp;
	}

	/**
	 * Liefert die Spieler für die Auswahlliste des Mannschaftsleiters.
	 *
	 * @return array Spielernummer => "Nachname, Vorname (BdF-Nr. X)"
	 */
	public function getSpieler()
	{
		$arrSpieler = array();

		$objSpieler = Database::getInstance()->execute("SELECT id, nachname, vorname, memberId FROM tl_fernschach_spieler WHERE published = '1' ORDER BY nachname ASC, vorname ASC");

		while ($objSpieler->next())
		{
			$arrSpieler[$objSpieler->id] = $objSpieler->nachname.', '.$objSpieler->vorname.' (BdF-Nr. '.$objSpieler->memberId.')';
		}

		return $arrSpieler;
	}

	/**
	 * Entfernt die Nenngeldsätze der Mannschaft, wenn sie gelöscht wird.
	 *
	 * Für den Mannschaftsleiter steht das Nenngeld als Sollbuchung auf dem
	 * Konto, für jeden gemeldeten Spieler ein Satz über 0 €. Beide tragen die
	 * Nummer der Mannschaft in `meldungId` und werden hier abgeräumt. Die
	 * Spielerzeilen selbst löscht Contao über die Kindtabelle mit.
	 *
	 * @param DataContainer $dc Die Mannschaft, die gerade gelöscht wird
	 *
	 * @return void Seiteneffekt ist das Löschen in
	 *              tl_fernschach_spieler_konto_nenngeld
	 */
	public function loescheNenngeldsaetze(DataContainer $dc)
	{
		if (!$dc->activeRecord)
		{
			return;
		}

		Database::getInstance()->prepare('DELETE FROM tl_fernschach_spieler_konto_nenngeld WHERE mannschaftId = ?')
		                        ->execute($dc->activeRecord->id);
	}
}
