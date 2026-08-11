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
use Contao\StringUtil;
use Schachbulle\ContaoFernschachBundle\Classes\Helper;

/**
 * Table tl_fernschach_turniere_mannschaften_spieler
 *
 * Die Aufstellung einer gemeldeten Mannschaft, ein Datensatz je Brett. Eine
 * eigene Tabelle, weil die Zahl der Bretter je Turnier verschieden ist — vier,
 * sechs oder acht — und feste Spalten das nicht abbilden.
 */
$GLOBALS['TL_DCA']['tl_fernschach_turniere_mannschaften_spieler'] = array
(

	// Config
	'config' => array
	(
		'dataContainer'               => DC_Table::class,
		'ptable'                      => 'tl_fernschach_turniere_mannschaften',
		'enableVersioning'            => true,
		'onsubmit_callback'           => array
		(
			array('tl_fernschach_turniere_mannschaften_spieler', 'uebernimmSpielerdaten')
		),
		'ondelete_callback'           => array
		(
			array('tl_fernschach_turniere_mannschaften_spieler', 'loescheNenngeldsatz')
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
			'fields'                  => array('brett'),
			'flag'                    => 11,
			'headerFields'            => array('mannschaftsname', 'vereinsname', 'meldungDatum'),
			'panelLayout'             => 'search,limit',
			'child_record_callback'   => array('tl_fernschach_turniere_mannschaften_spieler', 'listSpieler'),
			'disableGrouping'         => true
		),
		'label' => array
		(
			'fields'                  => array('brett'),
			'format'                  => 'Brett %s',
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
				'label'               => &$GLOBALS['TL_LANG']['tl_fernschach_turniere_mannschaften_spieler']['edit'],
				'href'                => 'act=edit',
				'icon'                => 'edit.gif',
			),
			'delete' => array
			(
				'label'               => &$GLOBALS['TL_LANG']['tl_fernschach_turniere_mannschaften_spieler']['delete'],
				'href'                => 'act=delete',
				'icon'                => 'delete.gif',
				'attributes'          => 'onclick="if(!confirm(\'' . ($GLOBALS['TL_LANG']['MSC']['deleteConfirm'] ?? null).' '.($GLOBALS['TL_LANG']['tl_fernschach_turniere_mannschaften_spieler']['deleteConfirm'] ?? null) . '\'))return false;Backend.getScrollOffset()"',
			),
			'show' => array
			(
				'label'               => &$GLOBALS['TL_LANG']['tl_fernschach_turniere_mannschaften_spieler']['show'],
				'href'                => 'act=show',
				'icon'                => 'show.gif',
			)
		)
	),

	// Palettes
	'palettes' => array
	(
		'default'                     => '{brett_legend},brett,spielerId;{spieler_legend},vorname,nachname,memberId,memberInternationalId'
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
			'foreignKey'              => 'tl_fernschach_turniere_mannschaften.mannschaftsname',
			'sql'                     => "int(10) unsigned NOT NULL default '0'",
			'relation'                => array('type' => 'belongsTo', 'load' => 'eager')
		),
		'tstamp' => array
		(
			'sql'                     => "int(10) unsigned NOT NULL default '0'"
		),
		'brett' => array
		(
			'label'                   => &$GLOBALS['TL_LANG']['tl_fernschach_turniere_mannschaften_spieler']['brett'],
			'exclude'                 => true,
			'sorting'                 => true,
			'flag'                    => 11,
			'inputType'               => 'text',
			'eval'                    => array('mandatory' => true, 'rgxp' => 'natural', 'maxlength' => 3, 'tl_class' => 'w50'),
			'sql'                     => "int(3) unsigned NOT NULL default '0'"
		),
		'spielerId' => array
		(
			'label'                   => &$GLOBALS['TL_LANG']['tl_fernschach_turniere_mannschaften_spieler']['spielerId'],
			'exclude'                 => true,
			'search'                  => true,
			'inputType'               => 'select',
			'options_callback'        => array('tl_fernschach_turniere_mannschaften_spieler', 'getSpieler'),
			'eval'                    => array('includeBlankOption' => true, 'chosen' => true, 'tl_class' => 'w50'),
			'sql'                     => "int(10) unsigned NOT NULL default '0'"
		),
		'vorname' => array
		(
			'label'                   => &$GLOBALS['TL_LANG']['tl_fernschach_turniere_mannschaften_spieler']['vorname'],
			'exclude'                 => true,
			'search'                  => true,
			'inputType'               => 'text',
			'eval'                    => array('maxlength' => 255, 'tl_class' => 'w50'),
			'sql'                     => "varchar(255) NOT NULL default ''"
		),
		'nachname' => array
		(
			'label'                   => &$GLOBALS['TL_LANG']['tl_fernschach_turniere_mannschaften_spieler']['nachname'],
			'exclude'                 => true,
			'search'                  => true,
			'inputType'               => 'text',
			'eval'                    => array('maxlength' => 255, 'tl_class' => 'w50'),
			'sql'                     => "varchar(255) NOT NULL default ''"
		),
		'memberId' => array
		(
			'label'                   => &$GLOBALS['TL_LANG']['tl_fernschach_turniere_mannschaften_spieler']['memberId'],
			'exclude'                 => true,
			'search'                  => true,
			'inputType'               => 'text',
			'eval'                    => array('rgxp' => 'digit', 'maxlength' => 10, 'tl_class' => 'w50'),
			'sql'                     => "int(10) unsigned NOT NULL default '0'"
		),
		'memberInternationalId' => array
		(
			'label'                   => &$GLOBALS['TL_LANG']['tl_fernschach_turniere_mannschaften_spieler']['memberInternationalId'],
			'exclude'                 => true,
			'search'                  => true,
			'inputType'               => 'text',
			'eval'                    => array('rgxp' => 'digit', 'maxlength' => 10, 'tl_class' => 'w50'),
			'sql'                     => "int(10) unsigned NOT NULL default '0'"
		)
	)
);

/**
 * Rückrufe der Tabelle tl_fernschach_turniere_mannschaften_spieler.
 */
class tl_fernschach_turniere_mannschaften_spieler extends Backend
{
	/**
	 * Lädt das Backend-Benutzerobjekt.
	 */
	public function __construct()
	{
		parent::__construct();
		$this->import(BackendUser::class, 'User');
	}

	/**
	 * Baut die Zeile eines Bretts in der Elternansicht.
	 *
	 * @param array $arrRow Der Datensatz des Bretts
	 *
	 * @return string Die fertige HTML-Zeile
	 */
	public function listSpieler($arrRow)
	{
		$strName = trim($arrRow['nachname'].', '.$arrRow['vorname'], ', ');

		$strTemp = '<div class="tl_content_left">';
		$strTemp .= '<span style="display:inline-block; width:80px;"><b>Brett '.(int) $arrRow['brett'].'</b></span>';
		$strTemp .= StringUtil::specialchars($strName ?: '— nicht besetzt —');
		$strTemp .= ' <span style="color:#999">(BdF-Nr. '.(int) $arrRow['memberId'];

		if ($arrRow['memberInternationalId'])
		{
			$strTemp .= ' / ICCF-ID '.(int) $arrRow['memberInternationalId'];
		}

		$strTemp .= ')</span></div>';

		return $strTemp;
	}

	/**
	 * Liefert die Spieler für die Auswahlliste.
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
	 * Schreibt Name und Nummern aus dem Spielerregister in den Datensatz.
	 *
	 * Der Name wird mitgeführt, damit die Aufstellung lesbar bleibt, auch wenn
	 * der Spielerdatensatz später umbenannt oder gelöscht wird — genauso wie es
	 * die Anmeldungen halten.
	 *
	 * @param DataContainer $dc Der gespeicherte Datensatz
	 *
	 * @return void Aktualisiert die Zeile in der Datenbank
	 */
	public function uebernimmSpielerdaten(DataContainer $dc)
	{
		if (!$dc->activeRecord || !$dc->activeRecord->spielerId)
		{
			return;
		}

		$objSpieler = Helper::getSpielerdatensatz($dc->activeRecord->spielerId);

		if (!$objSpieler || !$objSpieler->numRows)
		{
			return;
		}

		Database::getInstance()->prepare('UPDATE tl_fernschach_turniere_mannschaften_spieler %s WHERE id = ?')
		                        ->set(array
		                        (
		                        	'vorname'               => $objSpieler->vorname,
		                        	'nachname'              => $objSpieler->nachname,
		                        	'memberId'              => $objSpieler->memberId,
		                        	'memberInternationalId' => $objSpieler->memberInternationalId,
		                        ))
		                        ->execute($dc->activeRecord->id);
	}

	/**
	 * Entfernt den 0-€-Nenngeldsatz des Spielers, wenn das Brett gelöscht wird.
	 *
	 * @param DataContainer $dc Das Brett, das gerade gelöscht wird
	 *
	 * @return void Seiteneffekt ist das Löschen in
	 *              tl_fernschach_spieler_konto_nenngeld
	 */
	public function loescheNenngeldsatz(DataContainer $dc)
	{
		if (!$dc->activeRecord)
		{
			return;
		}

		Database::getInstance()->prepare('DELETE FROM tl_fernschach_spieler_konto_nenngeld WHERE mannschaftSpielerId = ?')
		                        ->execute($dc->activeRecord->id);
	}
}
